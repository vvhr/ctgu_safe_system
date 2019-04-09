<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/4/3
 * Time: 11:47
 */

namespace app\controllers;

use Yii;
use app\controllers\parent\ParentController;
use app\models\EventRecord;
use app\customLibrary\ActionTool;

class EventRecordController extends ParentController
{
    public function actionIndex(){
        $params = Yii::$app->request->getQueryParams();
        $query = EventRecord::find()->joinWith(['device'])->joinWith(['user']);
        ActionTool::addGroup3UserIdFilter($query);
        ActionTool::addAddressFilter($query, $params);
        if(isset($params['uuid']))
            $query->andFilterWhere(['event_record.uuid'=>$params['uuid']]);
        if(isset($params['diffLc']))
            $query->andFilterWhere(['>', 'event_record.diffLc', $params['diffLc']]);
        return ActionTool::createActiveDataProvider($query, $params);
    }
}