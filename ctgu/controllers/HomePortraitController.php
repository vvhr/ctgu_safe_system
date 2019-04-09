<?php

namespace app\controllers;

use app\config\Status;
use app\customLibrary\ActionTool;
use app\customLibrary\ReturnTool;
use app\models\HomePortrait;
use app\models\HomeUser;
use app\models\UserDevice;
use Yii;
use app\controllers\parent\ParentController;

class HomePortraitController extends ParentController
{
    public function actionIndex() {
        $params = \Yii::$app->request->getQueryParams();
        $query = HomePortrait::find()->joinWith(['device', 'user']);
        ActionTool::addGroup3UserIdFilter($query);
        ActionTool::addAddressFilter($query, $params);
        // state表示开关，1开，2关
        if(isset($params['state']))
            $query->andFilterWhere(['home_portrait.state'=>$params['state']]);
        if(isset($params['uuid']))
            $query->andFilterWhere(['home_portrait.uuid'=>$params['uuid']]);
        if(isset($params['is_high']))
            $query->andFilterWhere(['home_portrait.is_high'=>$params['is_high']]);
        return ActionTool::createActiveDataProvider($query, $params);
    }
    public function actionGetAppTotalCount() {
        $params = \Yii::$app->request->getQueryParams();
        $query = HomePortrait::find()->joinWith(['device', 'user']);
        ActionTool::addGroup3UserIdFilter($query);
        ActionTool::addAddressFilter($query, $params);
        // state表示开关，1高危，0不是
        if(isset($params['is_high']))
            $query->andFilterWhere(['home_portrait.is_high'=>$params['is_high']]);
        // state表示开关，1开，2关
        if(isset($params['state']))
            $query->andFilterWhere(['home_portrait.state'=>$params['state']]);
        if(isset($params['uuid']))
            $query->andFilterWhere(['home_portrait.uuid'=>$params['uuid']]);
        return $query->count();
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCloseAllApps(){
        $closedApp = [];
        $params = \Yii::$app->request->getBodyParams();
        // $events: 当天该home_id对应的所有事件记录列表。我们需要把它们清掉
        $apps = HomePortrait::find()->where(['uuid'=>$params['uuid']])->all();
        foreach ($apps as $app){
            /** @var  $app HomePortrait */
            $app->state = 2;
            $app->updateTime = date('Y-m-d H:i:s');
            if($app->save()){
                array_push($closedApp, $app->appName);
            }else{
                array_push($closedApp, $app->errors);
            }
        }
        return ReturnTool::returnPostMsg(Status::SUCCESS, $closedApp, $params['uuid'].'通道全部电器已经关闭');
    }
}
