<?php

namespace app\controllers;

use app\models\SmsLog;
use Yii;
use app\controllers\parent\ParentController;
use yii\data\ActiveDataProvider;

class SmsLogController extends ParentController
{
    public function actionIndex(){
        $params = Yii::$app->request->getQueryParams();
        $query = SmsLog::find();
        $pageSize = isset($params['pageSize'])?$params['pageSize']:10;
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => $pageSize],
            'sort' => ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);
    }
}
