<?php

namespace app\controllers;

use app\config\Status;
use app\customLibrary\ActionTool;
use app\controllers\parent\ParentController;
use app\models\ExceptionReportNew;
use Yii;
use yii\db\ActiveQuery;

class ExceptionReportNewController extends ParentController
{
    public function actionIndex(){
        $params = \Yii::$app->request->getQueryParams();
        $query = ExceptionReportNew::find()->joinWith(['device'])->joinWith(['user'])->where(['device.enable'=>1]);
        ActionTool::addGroup3UserIdFilter($query);
        ActionTool::addAddressFilter($query, $params);
        // 如果
        if(isset($params['expand'])) {
            $expandFields = explode(',', $params['expand']);
            if(in_array('maintainRecord', $expandFields)){
                $query->with(['maintainRecord'=> function($sub_query) {
                    /** @var $sub_query ActiveQuery */
                    $sub_query->andWhere(['maintain_record.valid' => 1]);
                }]);
            }
            if(in_array('wxUsers', $expandFields)){
//                $query->with(['wxUsers'=> function($sub_query) {
//                    /** @var $sub_query ActiveQuery */
//                    $sub_query->andWhere(['wx_user.enable_receive_msg' => 1]);
//                }]);
                $query->with('wxUsers');
            }
        }

        if(isset($params['username']) && !empty($params['username']))
            $query->andWhere(['like', 'user.username', trim($params['username'])]);
        if(isset($params['uuid']) && !empty($params['uuid']))
            $query->andWhere(['like', 'exception_report_new.uuid', trim($params['uuid'])]);
        if(isset($params['treatment_result']))
            $query->andFilterWhere(['treatment_result'=>$params['treatment_result']]);
        if(isset($params['start_time']))
            $query->andFilterWhere(['>=', 'exception_report_new.reportTime', $params['start_time']]);
        if(isset($params['end_time']))
            $query->andFilterWhere(['<', 'exception_report_new.reportTime', $params['end_time']]);
        if(isset($params['type']))
            $query->andFilterWhere(['exception_report_new.type'=>$params['type']]);
        //return $query->createCommand()->getRawSql();
        return ActionTool::createActiveDataProvider($query, $params, 'uuid');
    }

    /*按地区与时间范围统计总数*/
    public function actionTotal(){
        $params = \Yii::$app->request->getQueryParams();
        $query = ExceptionReportNew::find()->select('COUNT(*) as total')->leftJoin('device','exception_report_new.uuid = device.uuid');
        if(isset($params['date_from']) && isset($params['date_to']) && $params['date_from'] && $params['date_to']){
            $query->andWhere(['>=', 'exception_report_new.reportTime', $params['date_from']]);
            $query->andWhere(['<', 'exception_report_new.reportTime', $params['date_to']]);
        }
        if(isset($params['type']))
            $query->andFilterWhere(['exception_report_new.type'=>$params['type']]);
        if(isset($params['treatment_result']))
            $query->andFilterWhere(['treatment_result'=>$params['treatment_result']]);
        ActionTool::addAddressFilter($query, $params);
        ActionTool::addGroup3UserIdFilter($query);
        return (int)($query->scalar());
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     * 按区域分组得出每个区域的报警总数
     */
    public function actionTotalGroupByDistrict(){
        $params = \Yii::$app->request->getQueryParams();
        $districtLevel = $params['district_level'];

        $query = ExceptionReportNew::find()->select([$params['district_level'],'COUNT(*) as total'])->leftJoin('device','exception_report_new.uuid = device.uuid');
        if(isset($params['date_from']) && isset($params['date_to']) && $params['date_from'] && $params['date_to']){
            $query->andWhere(['>=', 'exception_report_new.reportTime', $params['date_from']]);
            $query->andWhere(['<', 'exception_report_new.reportTime', $params['date_to']]);
        }
        ActionTool::addAddressFilter($query, $params);
        ActionTool::addGroup3UserIdFilter($query);
        return $query->asArray()->groupBy($districtLevel)->all();
    }

    // 获得当年各月报警总数
    public function actionTotalGroupByMonthOfYear(){
        $params = \Yii::$app->request->getQueryParams();
        $query = ExceptionReportNew::find()->select(['MONTH(exception_report_new.reportTime) as month', 'COUNT(*) as total'])->leftJoin('device','exception_report_new.uuid = device.uuid');
        $query->andWhere(['>=', 'exception_report_new.reportTime', $params['year'].'-01-01']);
        $query->andWhere(['<', 'exception_report_new.reportTime', ($params['year']+1).'-01-01']);
        ActionTool::addAddressFilter($query, $params);
        ActionTool::addGroup3UserIdFilter($query);
        $res = $query->asArray()->groupBy('MONTH(exception_report_new.reportTime)')->all();
        $arr = [];
        foreach ($res as $item){
            $arr[$item['month']] = $item['total'];
        }
        for($i=1; $i<=12;$i++){
            if(!isset($arr[$i])){
                $arr[$i] = 0;
            }
        }
        return $arr;
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionClearAlarm(){
        $params = \Yii::$app->request->getBodyParams();
        $uuid = $params['uuid'];
        $id = $params['id'];

        // 把exception_report_new表的中处理状态全改为2
        $sql = 'UPDATE exception_report_new SET treatment_result = 1 WHERE id = :id';
        $res = Yii::$app->db->createCommand($sql, [':id'=>$id])->execute();

        // 把device表中的state改为0
        $sql = 'UPDATE device SET state = 0 WHERE uuid = :uuid';
        Yii::$app->db->createCommand($sql, [':uuid'=>$uuid])->execute();

//        // 删除mongodb相关记录
//        /** @var  $mongoDb \yii\mongodb\Connection*/
//        $mongoDb = Yii::$app->mongodb;
//        /** @var  $writeResult WriteResult*/
//        $writeResult = $mongoDb->createCommand()->delete('exceptionReportTab2', ['uuid'=>$uuid]);

        return [
            'bCode' => Status::SUCCESS,
            'bData' => 'Mysql中更新了'.$res.'条记录为已处理，并将设备状态更新为正常',
//            'bMongoData' => 'MongoDb从exceptionReportTab中删除了'.$writeResult->getDeletedCount().'条记录'
        ];
    }
}
