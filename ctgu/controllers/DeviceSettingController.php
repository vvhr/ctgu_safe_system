<?php

namespace app\controllers;

use app\config\Status;
use app\models\DeviceSetting;
use Yii;
use app\controllers\parent\ParentController;
use yii\data\ActiveDataProvider;
use linslin\yii2\curl;

class DeviceSettingController extends ParentController
{
    public function actionIndex(){
        $params = Yii::$app->request->getQueryParams();
        $query = DeviceSetting::find();
        if (!empty($params['imei'])) {
            $query->andWhere(['imei' => $params['imei']]);
        }
        // 设备默认每页多少条
        $pageSize = 10;
        if(isset($params['pageSize'])){
            $pageSize = (int)$params['pageSize'];
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => $pageSize],
            'sort' => ['defaultOrder' => ['id'=>SORT_ASC]]
        ]);
    }

    public function actionView(){
        $params = Yii::$app->request->getQueryParams();
        $model = DeviceSetting::find()->where(['imei'=>$params['imei']])->one();
        return $model;
    }

    /**
     * 用于向设备发送查询指令，发送成功后，设备将按发送数据更改参数
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSendUpdateInstruction(){
        $params = Yii::$app->request->getBodyParams();
        $model = DeviceSetting::findOne(['imei'=>$params['imei']]);
        $timeValue = time() - strtotime($model['update_time']);
        if (($model['verified'] === 0 && $timeValue < 60*3)) {
            return [
                'bCode' => Status::FAIL,
                'bData' => '友情提示每次修改请三分钟之后才能修改'
            ];
        }
        if($model){
            $model->load($params, '');
            $model->verified = 0;
            $model->save();
            if($model->validate()){
                // 参数合规，发送指令
                $params = $model->toArray();
                unset($params['update_time'], $params['id']);
                $apiUrl = 'http://api.hjdlwl.com/zhydpro/device/setDeviceParameter';
                $curl = new curl\Curl();
                $params = json_encode($params);
                $response = $curl->setRequestBody($params)
                    ->setHeaders([
                        'Content-Type' => 'application/json',
                        'Content-Length' => strlen($params)
                    ])
                    ->post($apiUrl);
                $response = json_decode($response, true);
                if($response['code']===1){
                    return [
                        'bCode' => Status::SUCCESS,
                        'bData' => json_decode($params)
                    ];
                }else{
                    return [
                        'bCode' => Status::FAIL,
                        'bData' => '修改设备参数接口调用失败'
                    ];
                }
            }else{
                return [
                    'bCode' => Status::FAIL,
                    'bData' => $model->errors
                ];
            }
        }else{
            return [
                'bCode' => Status::SUCCESS,
                'bData' => '该ID在设备参数表不存在'
            ];
        }
    }
}
