<?php

namespace app\models;

use app\customLibrary\ActionTool;
use Yii;

/**
 * This is the model class for table "analysis_results".
 *
 * @property int $id
 * @property int $channel 通道
 * @property string $imei 设备imei
 * @property string $eventTime
 * @property int $eventType
 * @property string $appliances 电器名称
 * @property string $createTime 创建时间
 * @property int $homeId 家庭用户id
 * @property int $typeId
 * @property mixed appliance
 * @property mixed homeUser
 * @property mixed device
 * @property int $pid
 * @property int $uuid
 */
class AnalysisResults extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'analysis_results';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['channel', 'homeId', 'typeId', 'eventType', 'pid'], 'integer'],
            [['eventTime', 'createTime'], 'safe'],
            [['imei'], 'string', 'max' => 30],
            [['uuid'], 'string', 'max' => 30],
            [['appliances'], 'string', 'max' => 50],
        ];
    }

    public function extraFields()
    {
        $fields = [
            'homePortrait'=>function($model){
                return $model->homePortrait;
            },
            'homeUser'=>function($model){
                return $model->homeUser;
            },
            'device'=>function($model){
                return $model->device;
            },
            'user'=>function($model){
                return $model->user;
            },
        ];
        return $fields;
    }

    public function getHomePortrait(){
        return $this->hasOne(HomePortrait::class, ['id'=>'pid']);
    }

    public function getHomeUser(){
        return $this->hasOne(HomeUser::class, ['id'=>'homeId']);
    }

    public function getDevice(){
        return $this->hasOne(Device::class, ['uuid'=>'uuid']);
    }

    public function getUser(){
        return $this->hasOne(User::class, ['id'=>'user_id'])->via('device');
    }

    /** 转换数组中数字的数据类型
     * @param $array
     * @return mixed
     */
    public static function setArray($array) {
        foreach ($array as $key => $value) {
            $array[$key]['value'] = (int)$value['value'];
        }
        return $array;
    }
}
