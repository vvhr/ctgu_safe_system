<?php

namespace app\controllers;

use app\config\Status;
use app\models\UserGroupApi;
use Yii;
use app\controllers\parent\ParentController;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;

class UserGroupApiController extends ParentController
{
    public function actionIndex(){
        $params = \Yii::$app->request->getQueryParams();
        $query = UserGroupApi::find();

        if(isset($params['group_id'])){
            $query->andWhere(['group_id'=>$params['group_id']]);
        }

        $pageSize = 1000;
        if(isset($params['pageSize'])){
            $pageSize = (int)$params['pageSize'];
        }
        return new ActiveDataProvider([
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
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate() {
        $params = \Yii::$app->request->getBodyParams();
        $group_id = $params['group_id'];
        $api_ids = $params['api_ids'];
        $res = [
          'delete_success'=>[],
          'delete_fail'=>[],
          'create_success'=>[],
          'create_fail'=>[],

        ];

        // 第一步删除多余的：删除表中有，而上传权限ID数组中没有的
        $models = UserGroupApi::find()->where(['group_id'=>$group_id])->andWhere(['NOT IN', 'api_id', $api_ids])->all();
        foreach ($models as $model){
            try {
                $model->delete();
                array_push($res['delete_success'], $model->api_id);
            } catch (StaleObjectException $e) {
                array_push($res['delete_fail'], $model->api_id);
            } catch (\Throwable $e) {
            }
        }

        // 第二步增加不存在：创建上传权限ID组中有，而表中没有的
        foreach ($api_ids as $api_id){
            $model = UserGroupApi::findOne(['group_id'=>$group_id, 'api_id'=>$api_id]);
            if($model){
                continue;
            }else{
                $createModel = new UserGroupApi();
                $createModel->group_id = $group_id;
                $createModel->api_id = $api_id;
                if($createModel->save()){
                    array_push($res['create_success'], $api_id);
                }else{
                    array_push($res['create_fail'], $createModel->errors);
                }
            }
        }
        return [
            'bCode' => Status::SUCCESS,
            'bData' => $res
        ];
    }
}
