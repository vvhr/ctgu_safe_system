<?php

namespace app\controllers;

use app\config\Status;
use app\customLibrary\ActionTool;
use app\models\User;
use app\models\UserSetting;
use Yii;
use app\controllers\parent\ParentController;

class UserController extends ParentController
{
    private $passwordError = null;
    public function actionIndex(){
        $params = \Yii::$app->request->getQueryParams();
        $query = User::find();
        ActionTool::addWithRelation($query,$params);
        // user_group_id === 3 代表是个人用户，这时，只搜索与user_id相关联的设备
        /** @var  $userModel User*/
        $userModel = Yii::$app->user->identity;
        if($userModel->user_group_id === 3) {
            $query->andWhere(['id'=>Yii::$app->user->identity->getId()]);
        }
        if(isset($params['username']))
            $query->andFilterWhere(['like', 'username', $params['username']]);
        if(isset($params['user_group_id']))
            $query->andFilterWhere(['user_group_id'=>$params['user_group_id']]);
        if(isset($params['phone']) && $params['phone'] !== '')
            $query->andWhere(['like', 'phone', $params['phone']]);

        // 设备默认每页多少条
        return ActionTool::createActiveDataProvider($query, $params);
    }

    public function actionView(){
        $params = Yii::$app->request->getQueryParams();
        $id = $params['id'];
        $model = User::findone($id);
        return $model;
    }

    /**
     * @return array
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate(){
        $params = \Yii::$app->request->getBodyParams();
        $model = new User();
        $password = trim($params['password']);
        if(!$this->checkPasswordStrength($password)){
            return [
                'bCode' => Status::FAIL,
                'bData' => $this->passwordError
            ];
        }
        $params['password'] = Yii::$app->getSecurity()->generatePasswordHash($password);
        $model->load($params,'');
        $model->create_time = date('Y-m-d H:i:s', time());
        $model->token = sha1(time().mt_rand(0,1000));
        $model->token_update_at = time();

        if($model->save()){
            $userSetting = new UserSetting();
            $userSetting->user_id = $model->id;
            $userSetting->default_address = '["广东省"]';
            $userSetting->save();
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
     * @return array
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate(){
        $params = \Yii::$app->request->getBodyParams();
        $id = (int)$params['id'];
        $user = User::findOne($id);
        if($user){
            // 如果是修改密码，需要进行密码强度检测
            if(isset($params['password'])){
                if($params['password']!==''){
                    $password = trim($params['password']);
                    if(!$this->checkPasswordStrength($password)){
                        return [
                            'bCode' => Status::FAIL,
                            'bData' => $this->passwordError
                        ];
                    }
                    $params['password'] = Yii::$app->getSecurity()->generatePasswordHash($password);
                }else{
                    unset($params['password']);
                }
            }
            $user->load($params,'');
            if($user->save()){
                return [
                    'bCode' => Status::SUCCESS,
                    'bData' => $user
                ];
            }else{
                return [
                    'bCode' => Status::FAIL,
                    'bData' => $user->errors
                ];
            }
        }
        return [
            'bCode' => Status::FAIL,
            'bData' => '该用户ID不存在'
        ];
    }

    public function actionGetUnbindUsers(){
        //首先用户组必须是个人用户：3
        $params = \Yii::$app->request->getQueryParams();

        $query = User::find()->where(['user_group_id'=>3])->limit(10);
        if(isset($params['username']))
            $query->andWhere(['like','username',$params['username']]);
        return $query->all();
    }

    public function actionTestPassword(){
        $params = \Yii::$app->request->getQueryParams();
        return [$this->checkPasswordStrength($params['password']), $this->passwordError];

    }

    /**
     * @param $password
     * @return bool
     */
    private function checkPasswordStrength($password){
        $str = $password;
        // 必须至少六位
        if(strlen($str)<6)
        {
            $this->passwordError = '密码至少6位';
           return false;
        }
        if(!preg_match("/[a-z]+/",$str))
        {
            $this->passwordError = '密码必须包含大小写字母';
            return false;
        }
        if(!preg_match("/[A-Z]+/",$str))
        {
            $this->passwordError = '密码必须包含大小写字母';
            return false;
        }
        if(!preg_match("/[0-9]+/",$str))
        {
            $this->passwordError = '密码必须包含数字';
            return false;
        }
        return true;
    }
}
