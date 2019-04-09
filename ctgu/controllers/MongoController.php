<?php

namespace app\controllers;

use app\controllers\parent\ParentController;
use app\models\MongoDBUtils;

class MongoController extends ParentController
{
    public function actionGetDeviceReportNews(){
        $params = \Yii::$app->request->getQueryParams();
        if(isset($params['uuid']) && isset($params['day']) && isset($params['timeRange'])){
            $day = trim($params['day']);
            $timeRange = $params['timeRange'];
            $uuid = trim($params['uuid']);
            $startTime = $day.' '.trim($timeRange[0]);
            $endTime = $day.' '.trim($timeRange[1]);
            return MongoDBUtils::findDeviceReportNews($uuid,$startTime,$endTime);
        }else
        {
            return [];
        }
    }
}
