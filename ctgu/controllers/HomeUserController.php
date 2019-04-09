<?php

namespace app\controllers;

use app\config\Status;
use app\models\Device;
use app\models\HomeUser;
use Yii;
use app\controllers\parent\ParentController;
use yii\data\ActiveDataProvider;
use app\customLibrary\ActionTool;

class HomeUserController extends ParentController
{
    /**
     * @return ActiveDataProvider
     */
    public function actionIndex() {
        $params = \Yii::$app->request->getQueryParams();
        $query = HomeUser::find();
        if (isset($params['uuid'])) {
            $query->andWhere(['uuid'=>$params['uuid']]);
        }
        return ActionTool::createActiveDataProvider($query, $params);
    }

    public function actionView(){
        $params = \Yii::$app->request->getQueryParams();
            $id = $params['id'];
            $query = HomeUser::find()->where(['id'=>$id])->limit(1);
        if(isset($params['expand'])){
            $expand = explode(',', $params['expand']);
            if(in_array('appliances', $expand)){
                $query->with('appliances');
            }
            if(in_array('device', $expand)){
                $query->with('device');
            }
            if(in_array('user', $expand)){
                $query->with('user');
            }
        }
        $res = $query->one();
        // 超过8小时未关的电器全关了
        if ($res && ($appliances = $res->appliances) !== null){
            foreach ($appliances as $app){
                if($app){
                    // 超过1个小时没关的都关掉
                    if($app->state === 1 && time() - strtotime($app->updateTime) >= 8*60*60){
                        $app->state=2;
                        $app->save();
                    }
                }
            }
        }
        return $res;
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate(){
        $params = Yii::$app->request->getBodyParams();
        if (isset($params['uuid'])) {
            $homeUser = HomeUser::findOne(['uuid' => $params['uuid']]);
        } else {
            $id = (int)$params['id'];
            $homeUser = HomeUser::findOne($id);
        }
        if($homeUser){
            $homeUser->load($params,'');
            if($homeUser->save()){
                return [
                    'bCode' => Status::SUCCESS,
                    'bData' => $homeUser
                ];
            }else{
                return [
                    'bCode' => Status::FAIL,
                    'bData' => $homeUser->errors
                ];
            }
        }
        return [
            'bCode' => Status::FAIL,
            'bData' => '该频道ID不存在'
        ];
    }

    // 批量添加通道住户
    public function actionBulkAdd(){
        $devices = Device::find()->all();
        foreach ($devices as $device){
            /** @var $device Device */
            if(!HomeUser::findOne(['uuid'=>$device->uuid])){
                $homeUser = new HomeUser();
                $homeUser->load($device->toArray(), '');
                $homeUser->save();
            }
        }
    }

    // 用于获取某个设备的四个通道
    public function actionDeviceChannels(){
        $params = \Yii::$app->request->getQueryParams();
        $query = HomeUser::find();
        if(isset($params['expand'])) {
            $expandFields = explode(',', $params['expand']);
            if(in_array('user', $expandFields)){
                $query->with('user');
            }
        }
        if(isset($params['imei']))
            $query->where(['imei'=>$params['imei']]);
        if(isset($params['channel']))
            $query->andWhere(['channel'=>$params['channel']]);
        $query->limit(4);
        return new ActiveDataProvider([
            // 使用with方法实现贪加载
            'query' => $query,
            // 分页信息
            'pagination' => [
                'pageSize' => 4
            ]
        ]);
    }
}
