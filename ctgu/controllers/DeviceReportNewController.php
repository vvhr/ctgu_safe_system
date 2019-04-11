<?php

namespace app\controllers;

use app\customLibrary\ActionTool;
use app\models\DeviceReportNew;
use Yii;
use app\controllers\parent\ParentController;

class DeviceReportNewController extends ParentController
{
    public function actionIndex(){
        $params = \Yii::$app->request->getQueryParams();
        $query = DeviceReportNew::find()->joinWith(['device'])->joinWith(['user'])->where(['device.enable'=>1]);
        ActionTool::addGroup3UserIdFilter($query);
        ActionTool::addAddressFilter($query, $params);

        if(isset($params['username']) && !empty($params['username']))
            $query->andWhere(['like', 'user.username', trim($params['username'])]);
        if(isset($params['uuid']) && !empty($params['uuid']))
            $query->andWhere(['like', 'device_report_new.uuid', trim($params['uuid'])]);
        if(isset($params['treatment_result']))
            $query->andFilterWhere(['device_report_new.treatment_result'=>$params['treatment_result']]);
        if(isset($params['eType']))
            $query->andFilterWhere(['device_report_new.eType'=>$params['eType']]);
        if(isset($params['searchType'])) {
            // 当上报时间 < 当前时间 - 10分钟 时，判断为故障断线
            if($params['searchType'] == 1)
                $query->andFilterWhere(['<', 'device_report_new.reportTime' , date("Y-m-d H:i:s" ,strtotime("- 600 seconds"))]);
            // 当功率大于预定功率，判断为开启了违章电器
            elseif($params['searchType'] == 2)
                $query->andFilterWhere(['>', 'device_report_new.p', $params['illegal']]);
            // 当温度和漏电流大于预定阈值，判断报警
            elseif($params['searchType'] == 3)
                $query->andFilterWhere(['OR', ['>', 'lc', (float)$params['lc']], ['>', 't', (float)$params['t']*10]]);
        }
        //return $query->createCommand()->getRawSql();
        return ActionTool::createActiveDataProvider($query, $params, 'uuid');
    }


    public function actionView(){
        $params = \Yii::$app->request->getQueryParams();
        $realReportOne = DeviceReportNew::findOne(['uuid'=>$params['uuid']]);
        // 临时增加关闭超过实时功率的电器
//        ReportRealTimeOneObjTab2::closeOverPowerAppliance($params['uuid'], $realReportOne);
        return $realReportOne;
    }

    // actionCloseOverPowerAppliance
    public function actionCpa(){
        $params = \Yii::$app->request->getQueryParams();
        $realReportOne = DeviceReportNew::findOne(['uuid'=>$params['uuid']]);
        // 临时增加关闭超过实时功率的电器
        DeviceReportNew::closeOverPowerAppliance($params['uuid'], $realReportOne);
    }

    /**
     * 获取设备总数
     * @return int
     */
    public function actionTotal(){
        $params = \Yii::$app->request->getQueryParams();
        // 必须是已启用的设备
        $query = DeviceReportNew::find()->andWhere(['device_report_new.enable'=>1])
            ->select('COUNT(*) as total')
            ->leftJoin('device','device_report_new.uuid = device.uuid');
        ActionTool::addGroup3UserIdFilter($query);
        ActionTool::addAddressFilter($query, $params);
        // 当上报时间 < 当前时间 - 10分钟 时，判断为故障断线
        if(isset($params['unWork']))
            $query->andFilterWhere(['<', 'device_report_new.reportTime' , date("Y-m-d H:i:s" ,strtotime("- 600 seconds"))]);
        // 当功率大于预定功率，判断为开启了违章电器
        if(isset($params['illegal']))
            $query->andFilterWhere(['>', 'device_report_new.p', $params['illegal']]);
        // 当温度和漏电流大于预定阈值，判断报警
        if(isset($params['alarm']))
            $query->andFilterWhere(['OR', ['>', 'lc', (float)$params['lc']], ['>', 't', (float)$params['t']*10]]);
        return (int)($query->count());
    }
}
