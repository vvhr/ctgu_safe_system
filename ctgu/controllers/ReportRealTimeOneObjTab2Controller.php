<?php

namespace app\controllers;

use app\customLibrary\ActionTool;
use app\models\Device;
use app\models\ReportRealTimeOneObjTab2;
use app\controllers\parent\ParentController;
class ReportRealTimeOneObjTab2Controller extends ParentController
{
    /**
     * 实时设备信息
     * @return array
     */
    public function actionIndex(){
        $params = \Yii::$app->request->getQueryParams();
        $query = Device::find();
        // 不查未启用的设备
        $query->andWhere(['enable'=>1]);
        ActionTool::addGroup3UserIdFilter($query);
        if(isset($params['user_id'])) {
            $query->andFilterWhere(['user_id'=>$params['user_id']]);
        }

        if(isset($params['uuid']) && trim($params['uuid']) !== '')
            $query->andWhere(['like', 'device.uuid', trim($params['uuid'])]);
        if(isset($params['project_id']))
            $query->andFilterWhere(['project_id'=>$params['project_id']]);
        if(isset($params['home_user_info']) && trim($params['home_user_info']) !== '') {
            $query->leftJoin('home_user', 'device.uuid=home_user.uuid');
            $query->andWhere(
                [
                    'or',
                    ['like', 'home_user.contact', trim($params['home_user_info'])],
                    ['like', 'home_user.phone', trim($params['home_user_info'])]
                ]
            );

        }
        ActionTool::addAddressFilter($query, $params);
        ActionTool::addWithRelation($query, $params);

        // 分页：
        $totalCount = $query->count();
        $page = 1;
        $offset = 0;
        $limit = 10;
        if(isset($params['page']))
            $page = $params['page'];
        if(isset($params['pageSize']))
            $limit = $params['pageSize'];
        if($page > 1)
            $offset = ($page-1)*$limit;
        $query->offset($offset)->limit($limit);
        $_meta = [
            'currentPage'=>$page,
            'perPage'=>$limit,
            'totalCount'=>$totalCount
        ];
        /*----------------------*/
        $devices = $query->asArray()->all();

        $uuids = array_column($devices,'uuid');
        $res = ReportRealTimeOneObjTab2::find()->where(['uuid'=>$uuids])->orderBy('uuid')->all();
        $reportArr = [];
        // 以uuid值为键，作映射
        foreach ($res as $report){
            $reportArr[$report['uuid']] = $report->toArray();
        }
        // 二次映射
        foreach ($devices as $device){
            if(isset($reportArr[$device['uuid']])){
                $reportArr[$device['uuid']] = array_merge($device, ['report'=>$reportArr[$device['uuid']]]);
            }else{
                $reportArr[$device['uuid']] = array_merge($device, ['report'=>null]);
            }
        }

        return array_merge(['_items'=>array_values($reportArr),'_meta'=>$_meta]);
    }

    /**
     * @return ReportRealTimeOneObjTab2|null
     * @throws \yii\db\Exception
     */
    public function actionView(){
        $params = \Yii::$app->request->getQueryParams();
        $realReportOne = ReportRealTimeOneObjTab2::findOne(['uuid'=>$params['uuid']]);
        // 临时增加关闭超过实时功率的电器
//        ReportRealTimeOneObjTab2::closeOverPowerAppliance($params['uuid'], $realReportOne);
        return $realReportOne;
    }

    // actionCloseOverPowerAppliance
    public function actionCpa(){
        $params = \Yii::$app->request->getQueryParams();
        $realReportOne = ReportRealTimeOneObjTab2::findOne(['uuid'=>$params['uuid']]);
        // 临时增加关闭超过实时功率的电器
        ReportRealTimeOneObjTab2::closeOverPowerAppliance($params['uuid'], $realReportOne);
    }
}
