<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "device_exception".
 *
 * @property int $id 主键id
 * @property string $imei
 * @property double $leakage_current 漏电流
 * @property double $temperature 温度
 * @property double $electricity 电流
 * @property double $voltage 电压
 * @property int $alarm_type 报警类型
 * @property int $type 异常类型;1警报，2设备故障
 * @property int $treatment_result 处理结果：1未处理，2已处理
 * @property string $create_time 创建日期
 * @property int $user_id 用户id
 * @property int $project_id 项目id
 * @property double $leakage_current2 2通道漏电流
 * @property double $leakage_current3 3通道漏电流
 * @property double $leakage_current4 4通道漏电流
 * @property double $temperature2 2通道温度
 * @property double $temperature3 3通道温度
 * @property double $temperature4 4通道温度
 * @property double $voltage2 2通道电压
 * @property double $voltage3 3通道电压
 * @property double $voltage4 4通道电压
 * @property int $power 单通道功能
 * @property int $power2 2通道功率
 * @property int $power3 3通道功率
 * @property int $power4 4通道功率
 * @property double $electricity2 2相电流
 * @property double $electricity3 3相电流
 * @property double $electricity4 4相电流
 */
class DeviceException extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'device_exception';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['leakage_current', 'temperature', 'electricity', 'voltage', 'leakage_current2', 'leakage_current3', 'leakage_current4', 'temperature2', 'temperature3', 'temperature4', 'voltage2', 'voltage3', 'voltage4', 'electricity2', 'electricity3', 'electricity4'], 'number'],
            [['alarm_type', 'type', 'treatment_result', 'user_id', 'project_id', 'power', 'power2', 'power3', 'power4'], 'integer'],
            [['create_time'], 'safe'],
            [['imei'], 'string', 'max' => 36],
        ];
    }
    public function extraFields()
    {
        $fields = [
            'device'=> function($model){
                return $model->device;
            },
            'users'=>function($model){
                return $model->users;
            }
        ];
        return $fields;
    }


    public function getDevice(){
        return $this->hasOne(Device::class, ['imei'=>'imei']);
    }

    public function getUsers(){
        return $this->hasMany(User::class, ['id'=>'user_id'])->viaTable('user_device', ['imei'=>'imei']);
    }
}
