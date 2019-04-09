<?php

namespace app\controllers;

use app\models\UserGroup;
use Yii;
use app\controllers\parent\ParentController;
use yii\data\ActiveDataProvider;

class UserGroupController extends ParentController
{
    public function actionIndex(){
        $params = \Yii::$app->request->getQueryParams();
        $query = UserGroup::find();

        $pageSize = 100;
        if(isset($params['pageSize'])){
            $pageSize = (int)$params['pageSize'];
        }
        return new ActiveDataProvider([
            // 使用with方法实现贪加载
            'query' => $query,
            // 分页信息
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            // 排序信息
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ]
            ],
        ]);
    }
}
