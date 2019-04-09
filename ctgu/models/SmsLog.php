<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sms_log".
 *
 * @property int $id
 * @property string $receiver 收信人
 * @property string $message 发送的信息
 * @property string $send_time 发送时间
 * @property int $type 信息类型：1：验证码，2：报警通知
 * @property string $imei
 * @property int $send_state 发送状态，1成功，2失败
 * @property string $info 报警信息，重发用
 * @property int $channel 通道
 * @property int $device_exception_new_id 关联的异常报警记录ID
 * @property int $sender_user_id 发送账户 ID
 * @property int $homeId
 * @property int $wx_user_id
 */
class SmsLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sms_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type', 'send_state', 'channel', 'device_exception_new_id', 'sender_user_id', 'homeId','wx_user_id'], 'integer'],
            [['send_time'], 'safe'],
            [['receiver'], 'string', 'max' => 150],
            [['message', 'info'], 'string'],
            [['imei'], 'string', 'max' => 30],
            [['id'], 'unique'],
        ];
    }
}
