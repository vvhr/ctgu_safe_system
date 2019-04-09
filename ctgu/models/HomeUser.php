<?php

namespace app\models;

/**
 * This is the model class for table "home_user".
 *
 * @property int $id 主建id
 * @property string $uuid 设备uuid
 * @property string $imei 设备imei
 * @property int $channel 通道（对应的用户）
 * @property string $unit 单元/楼栋
 * @property string $house_number 房间号
 * @property string $meter_number 电表号
 * @property string $contact 联系人
 * @property string $phone 电话
 * @property string $create_time
 * @property int $scenesOrPortrait
 *
 */
class HomeUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'home_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scenesOrPortrait'], 'integer', 'max' => 4],
            [['channel'], 'integer'],
            [['create_time'], 'safe'],
            [['imei', 'meter_number'], 'string', 'max' => 36],
            [['unit'], 'string', 'max' => 255],
            [['uuid'], 'string', 'max' => 30],
            [['house_number', 'contact', 'phone'], 'string', 'max' => 20],
        ];
    }
    public function extraFields()
    {
        return [
            'appliances'=>function($model){
                return $model->appliances;
            },
            'device'=>function($model){
                return $model->device;
            },
            'user'=>function($model){
                return $model->user;
            }
        ];
    }

    /**
     * 获得当前通道下的用户画像中存在的电器列表
     */
    public function  getAppliances(){
        return $this->hasMany(HomePortrait::class, ['homeId'=>'id']);
    }
    
    public function getDevice(){
        return $this->hasOne(Device::class, ['uuid'=>'uuid']);
    }

    public function getUser(){
        return $this->hasOne(User::class, ['id'=>'user_id'])->via('device');
    }
}
