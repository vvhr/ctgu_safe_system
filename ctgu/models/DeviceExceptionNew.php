<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "device_exception_new".
 *
 * @property int $id 主键id
 * @property string $uuid 设备唯一标识符
 * @property string $imei
 * @property int $channel 通道信息
 * @property double $leakage_current 漏电流
 * @property double $temperature 温度
 * @property double $electricity 电流
 * @property double $voltage 电压
 * @property int $type 异常类型;1警报，2设备故障
 * @property int $alarm_type 报警或异常原因：1漏电流2温度3电流4电压
 * @property int $treatment_result 处理结果：1未处理，2已处理
 * @property int $home_id 安装用户id
 * @property string $create_time 创建日期
 * @property Device $device 创建日期
 */
class DeviceExceptionNew extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'device_exception_new';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['channel', 'type', 'alarm_type', 'treatment_result', 'home_id'], 'integer'],
            [['leakage_current', 'temperature', 'electricity', 'voltage'], 'number'],
            [['create_time'], 'safe'],
            [['uuid'], 'string', 'max' => 30],
            [['imei'], 'string', 'max' => 28],
        ];
    }

    public function extraFields()
    {
        $fields = [
            'device'=> function($model){
                return $model->device;
            },
            'user'=> function($model){
                return $model->user;
            },
            'maintainRecord'=> function($model){
                return $model->maintainRecord;
            },
            'wxUsers'=> function($model){
                return $model->wxUsers;
            }
        ];
        return $fields;
    }

    public function getDevice(){
        return $this->hasOne(Device::class, ['uuid'=>'uuid']);
    }

    public function getUser(){
        return $this->hasOne(User::class, ['id'=>'user_id'])->via('device');
    }

    public function getMaintainRecord(){
        return $this->hasMany(MaintainRecord::class, ['device_exception_id'=>'id']);
    }

    public function getWxUsers(){
        return $this->hasMany(WxUser::class, ['user_id'=>'user_id'])->via('device');
    }

    /**
     * @param $uuid
     * @return bool
     * @throws \yii\db\Exception
     * 用于吧异常和故障设备表未处理的记录,改为已处理
     */
    public static function deviceExceptionNewUpdate($uuid) {
        // 把device_exception_new表的中处理状态全改为2
        $res = Yii::$app->db->createCommand()->update('device_exception_new',['treatment_result' => 2],['uuid' => $uuid, 'treatment_result' => 1])->execute();
        if ($res) {
            return true;
        }
        return false;
    }
}
