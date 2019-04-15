<?php
namespace app\controllers;
use app\config\Status;
use app\controllers\parent\ParentController;
use app\customLibrary\ActionTool;
use app\models\Device;
use app\models\Project;

class ProjectController extends ParentController
{
    public function actionIndex(){
        $params = \Yii::$app->request->getQueryParams();
        $query = Project::find()->where(['is_del' => 0]);

        if(isset($params['project_name']))
            $query->andWhere(['like','project_name',trim($params['project_name'])]);
        if(isset($params['address']))
            $query->andWhere(['like','address',trim($params['address'])]);
        return ActionTool::createActiveDataProvider($query, $params);
    }

    public function actionView(){
        $params = \Yii::$app->request->get('id');
        $id = (int)$params;
        $source = Project::findOne($id);
        return $source;
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate(){
        $params = \Yii::$app->request->getBodyParams();
        $model = new Project();
        $model->load($params,'');
        $model->operator_user_id = \Yii::$app->user->id;
        if($model->save()){
            return [
                'bCode' => Status::SUCCESS,
                'bData' => $model
            ];
        }
        return [
            'bCode' => Status::FAIL,
            'bData' => $model->errors
        ];
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate(){
        $params = \Yii::$app->request->getBodyParams();
        $id = (int)$params['id'];
        if (isset($params['is_del']) && $params['is_del'] === 1) {
            $res = Device::find()->where(['project_id' => $id])->count();
            if ((int)$res !== 0) {
                return [
                    'bCode' => Status::FAIL,
                    'bData' => '请先清空绑定所有设备'
                ];
            }
        }
        $source = Project::findOne($id);
        if($source){
            $source->load($params,'');
            if($source->save()){
                return [
                    'bCode' => Status::SUCCESS,
                    'bData' => $source
                ];
            }else{
                return [
                    'bCode' => Status::FAIL,
                    'bData' => $source->errors
                ];
            }
        }
        return [
            'bCode' => Status::FAIL,
            'bData' => '该项目ID不存在'
        ];
    }
}
