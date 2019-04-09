<?php

namespace app\controllers;
use app\config\Status;
use app\models\Device;
use app\models\HomePortrait;
use app\models\HomeUser;
use app\models\LeakagecurrentEvent;
use app\models\MaintainRecord;
use app\models\UploadForm;
use app\models\User;
use app\models\UserDevice;
use Yii;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\mongodb\Connection;
use yii\rest\Controller;
use yii\web\UploadedFile;

class RepairController extends Controller
{
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(
            [
                'corsFilter'=>[
                    'class' => Cors::class,
                    'cors'=>[
                        // restrict access to
                        'Origin' => ALLOW_ORIGIN,
                        'Access-Control-Request-Headers' => ['*'],
                    ]
                ]
            ],
            parent::behaviors()
        );
        return $behaviors;
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpload()
    {
//        $data = \Yii::$app->request->getBodyParams();
        $a= [1,2];
        array_shift($a);
        return array_shift($a);
//       return 1 instanceof number;
//        if (!file_exists('uploads')) {
//            mkdir('uploads'); // 按日期创建保存图片的文件夹
//        }
//
//        if (!file_exists('uploads/'.date('Y-m-d', time()))) {
//            mkdir('uploads/'.date('Y-m-d', time())); // 按日期创建保存图片的文件夹
//        }
//        $model = new UploadForm();
//        if (Yii::$app->request->isPost) {
//            $model->setImageFiles('file');
//            if ($model->upload()) {
//                // 文件上传成功
//                return [
//                    'bCode' => Status::SUCCESS,
//                    'bData' => $model
//                ];
//            }
//        }
//
//        return [
//            'bCode' => Status::FAIL,
//            'bData' => $model->errors
//        ];
    }

    /**
     * 把Home_user全拿出来
     * 遍历
     * 把第一条home_user更新到device表对应的记录
     * 每一条home_user新增到device表
     */
    public function actionRepairDevice() {
        $success = [];
        $fail = [];
        $errors = [];
        // 修复Device表
        $devices = Device::find()->all();
        foreach ($devices as $device){
            /** @var  $device Device*/
            if(!$device->uuid){
                $device->uuid = $device->imei.'_1';
                if($device->save()){
                    array_push($success, $device->uuid);
                }else{
                    array_push($fail, $device->uuid);
                    array_push($errors, $device->errors);
                }
            }
            for($i=2;$i<=4;$i++){
                $uuid = $device->imei.'_'.$i;
                if(!Device::find()->where(['uuid'=>$uuid])->limit(1)->one()){
                    $model = new Device();
                    $model->load($device->toArray(), '');
                    $model->uuid = $uuid;
                    if($model->save()){
                        array_push($success, $uuid);
                    }else{
                        array_push($fail, $uuid);
                        array_push($errors, $model->errors);
                    }
                }
            }
        }

        return ['fail'=>$fail, 'errors'=>$errors, 'success'=>$success];
    }
    public function actionRepairDeviceUser() {
        $success = [];
        $fail = [];
        $errors = [];
        // 修复Device表
        $devices = Device::find()->all();
        foreach ($devices as $device){
            /** @var  $device Device*/
            if(!$device->user_id){
                $imei_channel = explode('_', $device->uuid);
                /** @var  $userDevice UserDevice*/
                $userDevice = UserDevice::find()->where(['imei'=>$imei_channel[0],'channel_id'=>$imei_channel[1]])->one();
                if($userDevice){
                    $device->user_id = $userDevice->user_id;
                    if($device->save()){
                        array_push($success, $device->user_id);
                    }else{
                        array_push($fail, $device->user_id);
                        array_push($errors, $device->errors);
                    }
                }

            }
        }

        return ['fail'=>$fail, 'errors'=>$errors, 'success'=>$success];
    }

    public function actionRepairHomeUser(){
        $success = [];
        $fail = [];
        $errors = [];
        $homeUsers = HomeUser::find()->all();
        /** @var  $homeUser HomeUser */
        foreach ($homeUsers as $homeUser){
            if(!$homeUser->uuid){
                /** @var $homeUser HomeUser */
                $homeUser->uuid = $homeUser->imei.'_'.$homeUser->channel;
                if($homeUser->save()){
                    array_push($success, $homeUser->uuid);
                }else{
                    array_push($fail, $homeUser->uuid);
                    array_push($errors, $homeUser->errors);
                }
            }
        }
        return ['fail'=>$fail, 'errors'=>$errors,'success'=>$success];
    }

    public function actionRepairLeakagecurrentEvent(){
        $success = [];
        $fail = [];
        $errors = [];
        $les = LeakagecurrentEvent::find()->all();
        /** @var  $le LeakagecurrentEvent */
        foreach ($les as $le){
            if(!$le->uuid){
                /** @var  $homeUser HomeUser*/
                $homeUser = HomeUser::find()->where(['id'=>$le->homeId])->limit(1)->one();
                if($homeUser){
                    $le->uuid = $homeUser->imei.'_'.$homeUser->channel;
                    if($le->save()){
                        array_push($success, $le->uuid);
                    }else{
                        array_push($fail, $le->uuid);
                        array_push($errors, $le->errors);
                    }
                }
            }

        }
        return ['fail'=>$fail, 'errors'=>$errors, 'success'=>$success,];
    }
    public function actionRepairHomePortrait(){
        $success = [];
        $fail = [];
        $errors = [];
        $les = HomePortrait::find()->all();
        /** @var  $le LeakagecurrentEvent */
        foreach ($les as $le){
            if(!$le->uuid){
                /** @var  $homeUser HomeUser*/
                $homeUser = HomeUser::find()->where(['id'=>$le->homeId])->limit(1)->one();
                if($homeUser){
                    $le->uuid = $homeUser->imei.'_'.$homeUser->channel;
                    if($le->save()){
                        array_push($success, $le->uuid);
                    }else{
                        array_push($fail, $le->uuid);
                        array_push($errors, $le->errors);
                    }
                }
            }

        }
        return ['fail'=>$fail, 'errors'=>$errors, 'success'=>$success,];
    }

    // 修复device表，去重
    public function actionDeleteDupDevice(){
        // return Yii::$app->basePath;
        // 要显示额外字段，必须使用asArray
        $dupDevices = Device::find()->asArray()->select('uuid,count(uuid) as my_count')->groupBy('uuid')->having('my_count>1')->all();
        //return $devices->createCommand()->getRawSql();
        $dupDevices_join = '';
        foreach ($dupDevices as $dupDevice){
            $dupDevices_join.='['.$dupDevice['uuid'].']'."\r\n";
            $devices = Device::find()->where(['uuid'=>$dupDevice['uuid']])->all();
            $flag = 0;
            foreach ($devices as $device){
                 if($flag>0) $device->delete();
                $flag++;
            }
        }
        // 日志记录
        $logPath = Yii::$app->basePath.'/runtime/logs/my_log.log';
        if(!is_file($logPath)){
            file_put_contents($logPath, '该日志文件创建自'.date('Y-m-d H:i:s')."\r\n");
        }
        $logContent = file_get_contents($logPath);
        if(!empty($dupDevices_join)){
            $logContent.= date('Y-m-d H:i:s').' >> 本次删除的重复设备的UUID为'.$dupDevices_join."\r\n";
            file_put_contents($logPath,$logContent);
        }

        $dupDevices = Device::find()->asArray()->select('uuid,count(uuid) as my_count')->groupBy('uuid')->having('my_count>1')->all();
        return $dupDevices;

    }
}
