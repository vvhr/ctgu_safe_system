<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "device_report_new".
 *
 * @property string $uuid
 * @property string $imei
 * @property int $channel
 * @property int $v 电压
 * @property int $c 电流
 * @property int $lc 漏电流
 * @property int $t 温度
 * @property int $h1 基波
 * @property int $a1 基波相角
 * @property int $h3 3次谐波
 * @property int $a3 3次相角
 * @property int $h5 5次谐波
 * @property int $a5 5次相角
 * @property int $h7 7次谐波
 * @property int $a7 7次相角
 * @property int $h9 9次谐波
 * @property int $a9 9次相角
 * @property int $p 有功
 * @property int $np 无功
 * @property int $rate 功率因素
 * @property int $eType 异常类型
 * @property int $eDetailType 异常具体类型
 * @property string $eComment 异常描述
 * @property string $eHexL 故障低字
 * @property string $eHexH 故障高字
 * @property string $aSignHex 报警标志
 * @property int $enable
 * @property string $reportTime
 */
class DeviceReportNew extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'device_report_new';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['channel', 'v', 'c', 'lc', 't', 'h1', 'a1', 'h3', 'a3', 'h5', 'a5', 'h7', 'a7', 'h9', 'a9', 'p', 'np', 'rate', 'eType', 'eDetailType', 'enable'], 'integer'],
            [['reportTime'], 'safe'],
            [['uuid'], 'string', 'max' => 26],
            [['imei'], 'string', 'max' => 24],
            [['eComment'], 'string', 'max' => 255],
            [['eHexL', 'eHexH', 'aSignHex'], 'string', 'max' => 4],
            [['uuid'], 'unique'],
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

}
