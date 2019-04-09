<?php

namespace app\controllers;

use app\config\Status;
use app\controllers\parent\ParentController;
use app\customLibrary\ActionTool;
use app\customLibrary\ReturnTool;
use app\models\AnalysisResults;
use app\models\UserDevice;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class AnalysisResultsController extends ParentController
{
    public function actionIndex(){
        $params = \Yii::$app->request->getQueryParams();
        $query = AnalysisResults::find();
        ActionTool::addGroup3UserIdFilter($query);
        ActionTool::addAddressFilter($query, $params);
        if(isset($params['expand'])){
            $expandFields = explode(',', $params['expand']);
            // 如果额外字段要求获取电器名称及相关信息appliance对象，则将关系with进来进行贪加载
            if(in_array('homePortrait', $expandFields)){
                $query->joinWith('homePortrait');
            }
            if(in_array('homeUser', $expandFields)){
                $query->joinWith('homeUser');
            }
            if(in_array('device', $expandFields)){
                $query->joinWith('device');
            }
        }
        // ActionTool::addWithRelation($query,$params);
        if(isset($params['is_high']) && $params['is_high'] !== '')
            $query->andWhere(['home_portrait.is_high'=>(int)$params['is_high']]);
        if(isset($params['eventType']) && $params['eventType'] !== '')
            $query->andWhere(['eventType'=>(int)$params['eventType']]);
        if(isset($params['project_id']) && $params['project_id'] !== '')
            $query->andWhere(['project_id'=>$params['project_id']]);
        if(isset($params['startTime']) && isset($params['endTime'])){
            $query->andWhere(['between','eventTime',$params['startTime'],$params['endTime']]);
        }
        if(isset($params['homeId']))
            $query->where(['homeId'=>$params['homeId']]);
        if(isset($params['pid']))
            $query->where(['pid'=>$params['pid']]);
        $query->orderBy('analysis_results.id DESC');
//        return $query->createCommand()->getRawSql();
        return ActionTool::createActiveDataProvider($query, $params);
    }

    /**
     * 由home_id获取该通道的用户画像的最后一条电器开关事件
     * @return array|null|\yii\db\ActiveRecord[]
     */
    public function actionAppRunStatusesByHomeId()
    {
        $params = \Yii::$app->request->getQueryParams();
        $query = AnalysisResults::find()->select('MAX(id) as id')
            ->where(['homeId'=>$params['home_id']])
            // 必须将实时展示所需的电器开关时间筛选条件必须是当天。前提是假设没有任何一个电器开几天不关
            ->andWhere(['>=','eventTime',date('Y-m-d')])
            ->groupBy('typeId');
        $idsOfLastEvents = array_column($query->all(),'id');
        $result = [];
        if($idsOfLastEvents)
            $result = AnalysisResults::find()->asArray()->where(['id'=>$idsOfLastEvents])->with('appliance')->all();
        return $result;

    }

    /**
     * @deprecated
     * @return int|string
     */
    public function actionGetRunAppTotalCount(){
        $params = \Yii::$app->request->getQueryParams();
        $query = AnalysisResults::find()->select('typeId')->leftJoin('device','analysis_results.imei = device.imei');
        $query->where(['between','eventTime',date('Y-m-d'),date('Y-m-d').' 23:59:59']);
        $query->andWhere('eventType=1');

        if(isset($params['province'])  && $params['province'] !== '')
            $query->andWhere(['device.province'=>$params['province']]);
        if(isset($params['city'])  && $params['city'] !== '')
            $query->andWhere(['device.city'=>$params['city']]);
        if(isset($params['district'])  && $params['district'] !== '')
            $query->andWhere(['device.district'=>$params['district']]);
        if(isset($params['device.township'])  && $params['township'] !== '')
            $query->andWhere(['device.township'=>$params['township']]);
        $query->groupBy('typeId');
        return $query->count();


    }

    /** 获取高危电器开启次数
     * @return array|mixed
     */
    public function actionGetDangerDeviceRunCount() {
        $params = \Yii::$app->request->getQueryParams();
        $query = AnalysisResults::find()->leftJoin('home_portrait','home_portrait.id=analysis_results.pid');
        $query->leftJoin('device','home_portrait.uuid = device.uuid');
        if (isset($params['default_address'])) {
            $query->select([$params['default_address'].' as address,home_portrait.appName as name,COUNT(analysis_results.id) AS value'])->groupBy(['appName',$params['default_address']]);
        } else {
            $query->select('home_portrait.appName as name,COUNT(analysis_results.id) AS value')->groupBy(['appName']);
        }

        ActionTool::addGroup3UserIdFilter($query);
        ActionTool::addAddressFilter($query, $params);
        $query->andWhere(['eventType'=>1]);
        $query->andWhere(['home_portrait.is_high' => 1]);

        if (isset($params['time_type'])){
            switch ($params['time_type']){
                /** 按月统计危险电器 */
                case 1:
                    $query->andWhere(['>=','createTime', date('Y-m-01')]);
                    $query->andWhere(['<=','createTime', date('Y-m-t')]);
                    break;
                /** 按年统计危险电器 */
                case 2:
                    $query->andWhere(['>=','createTime', date('Y-m-01')]);
                    $query->andWhere(['<=','createTime', date('Y-12-31')]);
                    break;
                default:
                    break;
            }
        }
        $seriesData = $query->asArray()->all();

        if ($seriesData) {
            return AnalysisResults::setArray($seriesData);
        }
        return ReturnTool::returnPostMsg(Status::FAIL,$seriesData, '没有数据');
    }

}
