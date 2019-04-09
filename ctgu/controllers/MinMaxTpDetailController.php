<?php

namespace app\controllers;

use app\controllers\parent\ParentController;
use app\models\MinMaxTpDetail;
use Yii;
use yii\data\ActiveDataProvider;

class MinMaxTpDetailController extends ParentController
{
    public function actionIndex(){
        $params = \Yii::$app->request->getQueryParams();
        $query = MinMaxTpDetail::find()->joinWith('device')->joinWith('user')->with('homeUser');
        if(Yii::$app->user->identity->user_group_id === 3) {
            $myId = Yii::$app->user->identity->getId();
            $query->andWhere(['user.id'=>$myId]);
        }
        // 地址过滤
        if(isset($params['province']))
            $query->andFilterWhere(['province'=>$params['province']]);
        if(isset($params['city']))
            $query->andFilterWhere(['city'=>$params['city']]);
        if(isset($params['district']))
            $query->andFilterWhere(['district'=>$params['district']]);
        if(isset($params['township']))
            $query->andFilterWhere(['township'=>$params['township']]);

        if(isset($params['imei']))
            $query->andFilterWhere(['min_max_tp_detail.imei'=>$params['imei']]);
        if(isset($params['username']))
            $query->andFilterWhere(['like', 'username',$params['username']]);
        if(isset($params['date']))
            $query->andFilterWhere(['date'=>$params['date']]);

        // 设备默认每页多少条
        $pageSize = 1000;
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
    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreateMinMaxTpDetailByDate(){
        $params = Yii::$app->request->getBodyParams();
        $date = $params['date'];
        $res = MinMaxTpDetail::getAndInsertMaxMinTPRelatedRecordsByDate($date);
        return $res;
    }
}
