<?php

namespace app\controllers;

use app\models\Device;
use Yii;
use app\controllers\parent\ParentController;
use app\models\ExceptionReportTab;
use yii\data\ActiveDataProvider;

class ExceptionReportTabController extends ParentController
{
    /**
     * 获取异常类型为1/2, 项目id为1 的实时异常记录
     */
    public function actionIndex(){
        $params = \Yii::$app->request->getQueryParams();
        $query = Device::find();
//         user_group_id === 3 代表是个人用户，这时，只搜索与user_id相关联的设备
        if(Yii::$app->user->identity->user_group_id === 3) {
            $query->rightJoin('user_device as d','d.device_id = device.id');
            $query->andWhere('d.user_id = '.Yii::$app->user->identity->getId());
        }
        if(isset($params['project_id']) && $params['project_id'] !== '')
            $query->andWhere(['project_id'=>$params['project_id']]);
        if(isset($params['province'])  && $params['province'] !== '')
            $query->andWhere(['province'=>$params['province']]);
        if(isset($params['city'])  && $params['city'] !== '')
            $query->andWhere(['city'=>$params['city']]);
        if(isset($params['district'])  && $params['district'] !== '')
            $query->andWhere(['district'=>$params['district']]);
        if(isset($params['township'])  && $params['township'] !== '')
            $query->andWhere(['township'=>$params['township']]);
        $devices = $query->all();

        $imeis = array_column($devices,'imei');
        $res = ExceptionReportTab::find()->where(['imei'=>$imeis])->orderBy('imei')->all();
        $tempArr = [];
        foreach ($res as $report){
            $tempArr[$report['imei']] = $report->toArray();
        }
        foreach ($devices as $device){
            if(isset($tempArr[$device['imei']])){
                // 因为device表与mongodb中的exception表有字段重复，所以在数合并的时候，将exception表记录放在后面，这样就以exception表为主
                $tempArr[$device['imei']] = array_merge($device->toArray(), $tempArr[$device['imei']]);
            }
        }
        return $tempArr;
    }
}
