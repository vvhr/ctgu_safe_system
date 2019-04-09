<?php

namespace app\controllers;

use app\config\Status;
use app\controllers\parent\ParentController;
use app\models\UserSetting;

class UserSettingController extends ParentController
{
    public function actionGet(){
        $user_id = \Yii::$app->user->getId();
        $model = UserSetting::findOne(['user_id'=>$user_id]);
        return $model;
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSet(){
        $params = \Yii::$app->request->getBodyParams();
        $user_id = \Yii::$app->user->getId();
        if($model = UserSetting::findOne(['user_id'=>$user_id])){
            $model->load($params,'');
        }else{
            $model = new UserSetting();
            $model->load($params,'');
            $model->user_id = $user_id;
        }

        if($model->save()){
            return [
                'bCode' => Status::SUCCESS,
                'bData' => $model
            ];
        }
        return [
            'bCode' => Status::FAIL,
            'bData' => $model->errors
        ];
    }
}
