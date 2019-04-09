<?php

namespace app\controllers;

use app\config\Status;
use app\models\Device;
use app\models\HomeUser;
use app\models\User;
use app\models\UserDevice;
use Yii;
use app\controllers\parent\ParentController;
use yii\data\ActiveDataProvider;

class UserDeviceController extends ParentController
{
    public function actionIndex(){
        $params = \Yii::$app->request->getQueryParams();
        $query = UserDevice::find()->joinWith('device');
        if(Yii::$app->user->identity->user_group_id === 3) {
            $query->andWhere('user_device.user_id = '.Yii::$app->user->getId());
        }

        if(isset($params['user_id']) && $params['user_id'] !== ''){
            $query->where(['user_device.user_id'=>$params['user_id']]);
        }
        if(isset($params['imei']) && $params['imei'] !== '')
            $query->where(['user_device.imei'=>$params['imei']]);
        if(isset($params['province'])  && $params['province'] !== '')
            $query->andWhere(['province'=>$params['province']]);
        if(isset($params['city'])  && $params['city'] !== '')
            $query->andWhere(['city'=>$params['city']]);
        if(isset($params['district'])  && $params['district'] !== '')
            $query->andWhere(['district'=>$params['district']]);
        if(isset($params['township'])  && $params['township'] !== '')
            $query->andWhere(['township'=>$params['township']]);

        if(isset($params['expand'])) {
            $expandFields = explode(',', $params['expand']);
            if(in_array('user', $expandFields)){
                $query->with('user');
            }
        }

        // 设备默认每页多少条
        $pageSize = 10;
        if(isset($params['pageSize'])){
            $pageSize = $params['pageSize'];
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
                    'user_device_id' => SORT_ASC,
                ]
            ],
        ]);
    }

    public function actionAutoFillHomeId(){
        $res = UserDevice::findAll(['home_id'=>null]);
        foreach ($res as $item){
            $homeUser = HomeUser::findOne(['imei'=>$item->imei, 'channel'=>$item->channel_id]);
            $item->home_id = $homeUser->id;
            $item->save();
        }
        return 'complete';
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate(){
        $params = \Yii::$app->request->getBodyParams();
        if(isset($params['user_id']) && isset($params['device_id']) && isset($params['channel_id'])){
            if(UserDevice::findOne(['channel_id'=>$params['channel_id'],'device_id'=>$params['device_id']])){
                return [
                    'bCode' => Status::FAIL,
                    'bData' => '该设备通道已经被绑定过'
                ];
            }
        }
        $model = new UserDevice();
        $model->load($params, '');
        $model->channel_id = $params['channel_id'];
        $homeUser = HomeUser::findOne(['imei'=>$params['imei'],'channel'=>$params['channel_id']]);
        if(!$homeUser){
            return [
                'bCode' => Status::FAIL,
                'bData' => '该设备尚未在home_user表中新增4个通道记录，点击设备管理，自动导入，自动新增通道尝试修复'
            ];
        }
        $model->home_id = $homeUser['id'];
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
     * 接收user_id，home_id使设备与用户进行绑定（即：在user_device表中创建记录）
     * 1、尊循一个通道只允许绑定一次的原则，所以表中存在参数中home_id，都无法进行绑定
     */
    public function actionCreateByUserIdAndHomeId(){
        $params = \Yii::$app->request->getBodyParams();
        $res = UserDevice::find()->where(['home_id'=>$params['home_id']])->limit(1)->one();
        if($res){
            $return = [
                'bCode' => Status::FAIL,
                'bData' => '该通道已经绑定过，一个通道只能绑定给一个用户'
            ];
        }
        else{
            $home_user = HomeUser::findOne($params['home_id']);
            $user = User::findOne($params['user_id']);

            if($home_user && $user){
                $device = Device::findOne(['imei'=>$home_user['imei']]);
                $data = [
                    'device_id'=>$device['id'],
                    'user_id'=>$params['user_id'],
                    'imei'=>$device['imei'],
                    'home_id'=>$params['home_id'],
                    'channel_id'=>$home_user['channel']
                ];
                $model = new UserDevice();
                $model->load($data, '');
                if($model->save()){
                    $return = [
                        'bCode' => Status::SUCCESS,
                        'bData' => $user
                    ];
                }else{
                    $return = [
                        'bCode' => Status::FAIL,
                        'bData' => $model->errors
                    ];
                }
            }else{
                $return = [
                    'bCode' => Status::FAIL,
                    'bData' => '该设备通道或用户不存在'
                ];
            }
        }
        return $return;
    }

    /**
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete(){
        $params = \Yii::$app->request->getBodyParams();
        if(isset($params['user_id']) && isset($params['device_id'])){
            $model = UserDevice::findOne(['user_id'=>$params['user_id'],'device_id'=>$params['device_id']]);
            if($model){
                $model->delete();
                return [
                    'bCode' => Status::SUCCESS,
                    'bData' => $model
                ];
            }else{
                return [
                    'bCode' => Status::FAIL,
                    'bData' => '未找到此记录:user_id:'.$params['user_id'].', device_id:'.$params['device_id']
                ];
            }
        }else{
            return [
                'bCode' => Status::FAIL,
                'bData' => '缺少参数：user_id 或 device_id'
            ];
        }
    }

    /**
     * @return array
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteByUserIdAndHomeId(){
        $params = \Yii::$app->request->getBodyParams();
        if(isset($params['user_id']) && isset($params['home_id'])){
            $model = UserDevice::findOne(['user_id'=>$params['user_id'],'home_id'=>$params['home_id']]);
            if($model){
                $model->delete();
                return [
                    'bCode' => Status::SUCCESS,
                    'bData' => $model
                ];
            }else{
                return [
                    'bCode' => Status::FAIL,
                    'bData' => '未找到此记录:user_id:'.$params['user_id'].', home_id:'.$params['home_id']
                ];
            }
        }else{
            return [
                'bCode' => Status::FAIL,
                'bData' => '缺少参数：user_id 或 home_id'
            ];
        }
    }

    /**
     * 维护数据完整，清除用户绑定的不存在的home_id(谁识删了home_id?)
     * @return array
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionClearWrongHomeId(){
        $res = [];
        $userDevices = UserDevice::find()
            ->leftJoin('home_user','user_device.home_id=home_user.id')
            ->where('ISNULL(home_user.id)')
            ->all();
        foreach ($userDevices as $userDevice){
            $userDevice->delete();
            array_push($res, $userDevice);
        }
        return [
            'bCode' => Status::SUCCESS,
            'bData' => $res
        ];
    }
}
