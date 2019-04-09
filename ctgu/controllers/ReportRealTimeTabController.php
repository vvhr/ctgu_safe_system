<?php

namespace app\controllers;

use app\controllers\parent\ParentController;
use app\models\ReportRealTimeTab2;
use yii\mongodb\Exception;

class ReportRealTimeTabController extends ParentController
{
    public function actionIndex(){
//        $params = \Yii::$app->request->getQueryParams();
        $query = ReportRealTimeTab2::find();
//        if(isset($params['uuid']))
//            $query->andFilterWhere(['uuid'=>$params['uuid']]);
//        if(isset($params['reportTime']))
//            $query->andWhere(['>','reportTime',$params['reportTime']]);
        $query->andWhere(['>','leakageCurrent',80]);
        try {
            return $query->count();
        } catch (Exception $e) {
        }
        // return ActionTool::createActiveDataProviderMongo($query,$params);
    }
}
