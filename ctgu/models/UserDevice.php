<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_device".
 *
 * @property int $device_id
 * @property int $user_id
 * @property string $imei
 * @property int $channel_id
 * @property int $home_id
 */
class UserDevice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_device';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['device_id', 'user_id', 'home_id'], 'required'],
            [['device_id', 'user_id', 'channel_id', 'home_id'], 'integer'],
            [['imei'], 'string', 'max' => 36],
        ];
    }

    public function extraFields()
    {
        $extraFields = [
            'device'=>function($model){
                return $model->device;
            },
            'user'=>function($model){
                return $model->user;
            }
        ];
        return $extraFields;
    }

    public function getDevice(){
        return $this->hasOne(Device::class,['id'=>'device_id']);
    }
    public function getUser(){
        return $this->hasOne(User::class,['id'=>'user_id']);
    }

}
