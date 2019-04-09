<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "exception_report_new".
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
 * @property int $diffLc 漏电差值
 * @property int $diffT 温度差值
 * @property int $diffP 有功差值
 * @property int $diffNp 无功差值
 * @property int $treatment_result 0-未处理 1-已处理
 */
class ExceptionReportNew extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exception_report_new';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['channel', 'v', 'c', 'lc', 't', 'h1', 'a1', 'h3', 'a3', 'h5', 'a5', 'h7', 'a7', 'h9', 'a9', 'p', 'np', 'rate', 'eType', 'eDetailType', 'enable', 'diffLc', 'diffT', 'diffP', 'diffNp'], 'integer'],
            [['reportTime'], 'safe'],
            [['uuid'], 'string', 'max' => 26],
            [['imei'], 'string', 'max' => 24],
            [['eComment'], 'string', 'max' => 255],
            [['eHexL', 'eHexH', 'aSignHex'], 'string', 'max' => 4],
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
     * 用于把异常和故障设备表未处理的记录,改为已处理
     */
    public static function ExceptionReportNewUpdate($uuid) {
        // 把device_exception_new表的中处理状态全改为2
        $res = Yii::$app->db->createCommand()->update('exception_report_new',['treatment_result' => 2],['uuid' => $uuid, 'treatment_result' => 1])->execute();
        if ($res) {
            return true;
        }
        return false;
    }
}
