<?php

namespace app\models;

use app\config\Status;
use app\customLibrary\ReturnTool;
use Yii;
use yii\mongodb\Query;

/**
 * This is the model class for table "device".
 *
 * @property int $id 设备id
 * @property string $uuid 设备唯一标识符
 * @property string $imei 设备imei
 * @property int $channel 通道号
 * @property int $device_model 设备型号
 * @property string $installed_by_who 安装人员+电话
 * @property string $province 所在省
 * @property string $city 所在市
 * @property int $citycode 高德：城市id
 * @property string $district 高德：地区/具
 * @property string $township 高德：行政街道/乡镇
 * @property int $adcode 高德：地址id，精确到区县
 * @property string $street 高德：哪一条路
 * @property string $address 详细地址
 * @property double $lat 高德：横坐标
 * @property double $lon 高德：纵坐标
 * @property int $state 状态：0，正常；1，报警；2，故障
 * @property int $enable 是否禁用
 * @property int $project_id 项目id
 * @property int $user_id 个人用户
 * @property string $create_time 创建日期
 * @property string $update_time 更新时间
 * @property int $parseMode
 * @property array $wxUsers
 */
class Device extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'device';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['channel', 'device_model', 'citycode', 'adcode', 'state', 'enable', 'project_id', 'user_id','parseMode'], 'integer'],
            [['lat', 'lon'], 'number'],
            [['create_time', 'update_time'], 'safe'],
            [['uuid', 'installed_by_who', 'province', 'city', 'district', 'township', 'street'], 'string', 'max' => 255],
            [['imei'], 'string', 'max' => 36],
            [['address'], 'string', 'max' => 200],
            [['uuid'], 'unique'],
        ];
    }


    public function extraFields()
    {
        $extraFields = [
            'user'=>function($model){
                return $model->user;
            },
            'homePortrait'=>function($model){
                return $model->homePortrait;
            },
            'project'=>function($model){
                return $model->project;
            }
        ];
        return $extraFields;
    }

    // 关系类方法
    public function getUser(){
        return $this->hasOne(User::class, ['id'=>'user_id']);
    }

    public function getHomePortrait(){
        return $this->hasMany(HomePortrait::class, ['uuid'=>'uuid']);
    }

    public function getProject(){
        return $this->hasOne(Project::class, ['id'=>'project_id']);
    }

    public function getWxUsers(){
        return $this->hasMany(WxUser::class, ['user_id'=>'user_id']);
    }

    public static function bulkAddDevice(){
        $res = [
            'successList'=>[],
            'failList'=>[]
        ];
        $newDevices = DeviceReportNew::find()->leftJoin('device','device_report_new.uuid=device.uuid')->where('ISNULL(device.uuid)')->all();
        /** @var  $newDevice DeviceReportNew */
        foreach ($newDevices as $newDevice){
            $device = new Device();
            $device->uuid = $newDevice->uuid;
            $device->imei = $newDevice->imei;
            $device->channel = $newDevice->channel;
            $device->create_time = date('Y-m-d H:i:s');
            $device->enable = 0;
            $device->province = '广东省';
            if($device->save()){
                array_push($res['successList'],$device);
            }else{
                array_push($res['failList'],[$device,$device->errors]);
            }
        }
        return $res;
    }

    public static function bulkBindUser(int $user_id, array $device_ids){
        $success_device_ids = [];
        if (User::findOne($user_id)){
            foreach ($device_ids as $id){
                $model = Device::findOne($id);
                if($model){
                    $model->user_id = $user_id;
                    if($model->save()){
                        array_push($success_device_ids, $id);
                    }
                }
            }
        }
        return $success_device_ids;
    }

    public static function bulkBindProject(int $project_id, array $device_ids){
        $success_device_ids = [];
        if (User::findOne($project_id)){
            foreach ($device_ids as $id){
                $model = Device::findOne($id);
                if($model){
                    $model->project_id = $project_id;
                    if($model->save()){
                        array_push($success_device_ids, $id);
                    }
                }
            }
        }
        return $success_device_ids;
    }

    /** 底层方法 */
    /**
     * @param String $uuid
     * @return Device
     */
    // 将一个uuid插入到device
    private static function insertSingleUuidToDevice(String $uuid){
        // 将uuid拆分成imei与channel
        $splicedUuid = explode('_', $uuid);
        $imei = $splicedUuid[0];
        $channel = $splicedUuid[1];
        // 存储过程
        $model = new Device();
        $model->uuid = $uuid;
        $model->imei = $imei;
        $model->channel = $channel;
        $model->create_time = date('Y-m-d H:i:s');
        // 默认正常
        $model->state = 0;
        // 默认未启用
        $model->enable = 0;
        return $model;
    }
}
