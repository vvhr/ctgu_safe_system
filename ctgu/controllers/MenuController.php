<?php

namespace app\controllers;

use Yii;
use app\controllers\parent\ParentController;
use yii\data\ActiveDataProvider;
use app\models\Menu;

class MenuController extends ParentController
{
    public function actionIndex(){
        $params = Yii::$app->request->getQueryParams();
        $query = Menu::find();
        if(isset($params['level'])){
            $query->andWhere(['level'=>$params['level']]);
        }
        $pageSize = isset($params['pageSize'])?$params['pageSize']:100;
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => $pageSize],
            'sort' => ['defaultOrder' => ['id'=>SORT_ASC]]
        ]);
    }
}
