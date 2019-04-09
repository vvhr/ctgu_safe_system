<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "leakagecurrent_event".
 *
 * @property int $id
 * @property string $imei 设备imei
 * @property string $uuid 设备imei
 * @property string $optionTime 操作时间
 * @property int $typeId 电器类型
 * @property int $homeId 安装用户id
 * @property int $leakageCurrent 漏电流值
 * @property mixed appliance
 */
class LeakagecurrentEvent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'leakagecurrent_event';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['typeId', 'homeId', 'leakageCurrent'], 'integer'],
            [['imei'], 'string', 'max' => 64],
            [['uuid'], 'string', 'max' => 30],
            [['optionTime'], 'string', 'max' => 30],
        ];
    }

    /**
     * extraFields只与ActiveDataProvider结合时才被使用。一般在控制器返回数据时，不使用ActiveDataProvider，而真接使用with('appliance')加asArray()->all(),
     * 则会直接将子对数组作为返回数据。无需extraFields的限制
     * @return array|mixed
     */
    public function extraFields()
    {
        $fields['homePortrait'] = function ($model){
          return $model->homePortrait; //$model是当前活动记录实例
        };
        $fields['device'] = function ($model){
            return $model->device; //$model是当前活动记录实例
        };
        $fields['user'] = function ($model){
            return $model->user; //$model是当前活动记录实例
        };
        return $fields;
    }

    public function getHomePortrait(){
        return $this->hasOne(HomePortrait::class,['id'=>'typeId']);
    }
    public function getDevice(){
        return $this->hasOne(Device::class, ['uuid'=>'uuid']);
    }
    public function getUser(){
        return $this->hasOne(User::class,['id'=>'user_id'])->via('device');
    }
}
