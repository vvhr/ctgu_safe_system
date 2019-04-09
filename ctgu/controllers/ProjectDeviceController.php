<?php
namespace app\controllers;
use app\controllers\parent\ParentController;
use app\models\ProjectDevice;
use yii\mongodb\Query;
use Yii;

class ProjectDeviceController extends ParentController
{
    /**
     * 该方法批量把所有设备不重复地加入到projectDevice的表中（mongoDB数据库）
     * @return array
     */
    public function actionBulkAddDevicesToProject(){
        $count = 0;
        $imeis = (new Query())
            ->from('reportRealTimeTab')
            ->distinct('imei');
        foreach ($imeis as $imei){
            $modelOfImei = ProjectDevice::findOne(['imei'=>$imei]);
            if(!$modelOfImei){
                $model = new ProjectDevice();
                $model->project_id = 1;
                $model->imei = $imei;
                $model->save();
                $count++;
            }
        }
        return ['msg'=>'一共添加了'.$count.'台设备'];
    }
}
