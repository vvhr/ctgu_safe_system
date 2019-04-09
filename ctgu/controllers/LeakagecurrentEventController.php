<?php

namespace app\controllers;

use app\customLibrary\ActionTool;
use app\models\LeakagecurrentEvent;
use app\controllers\parent\ParentController;
use Yii;

class LeakagecurrentEventController extends ParentController
{

    public function actionIndex(){
        $params = Yii::$app->request->getQueryParams();
        $query = LeakagecurrentEvent::find()->joinWith(['device', 'user']);
        ActionTool::addGroup3UserIdFilter($query);
        ActionTool::addAddressFilter($query, $params);
        if(isset($params['uuid']))
            $query->andFilterWhere(['leakagecurrent_event.uuid'=>$params['uuid']]);
        if(isset($params['username']))
            $query->andFilterWhere(['like', 'username',$params['username']]);
        return ActionTool::createActiveDataProvider($query, $params);
    }
}
