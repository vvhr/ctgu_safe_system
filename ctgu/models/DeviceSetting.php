<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "device_setting".
 *
 * @property int $id
 * @property string $imei
 * @property int $pwd 按键参数配置密码 1-999
 * @property int $pt PT 值 1-9999，变压变比
 * @property int $ct1 电流通道1CT 电流变比 1-9999
 * @property int $ct2 电流通道2CT 电流变比 1-9999
 * @property int $ct3 电流通道3CT 电流变比 1-9999
 * @property int $ct4 电流通道4CT 电流变比 1-9999
 * @property int $v1 电压通道1报警值，单位v： 1-9999
 * @property int $v2 电压通道2报警值，单位v： 1-9999
 * @property int $v3 电压通道3报警值，单位v： 1-9999
 * @property int $v4 电压通道4报警值，单位v： 1-9999
 * @property int $e1 电流通道1报警值，单位A：1-9999
 * @property int $e2 电流通道2报警值，单位A：1-9999
 * @property int $e3 电流通道3报警值，单位A：1-9999
 * @property int $e4 电流通道4报警值，单位A：1-9999
 * @property int $lc1 剩余电流通道1报警值，单位mA：1-1000
 * @property int $lc2 剩余电流通道2报警值，单位mA：1-1000
 * @property int $lc3 剩余电流通道3报警值，单位mA：1-1000
 * @property int $lc4 剩余电流通道4报警值，单位mA：1-1000
 * @property int $t1 温度通道1报警值，单位为℃：0-1650
 * @property int $t2 温度通道2报警值，单位为℃：0-1650
 * @property int $t3 温度通道3报警值，单位为℃：0-1650
 * @property int $t4 温度通道4报警值，单位为℃：0-1650
 * @property int $cef 通道使能标志
 * @property int $epem 设备外设使能标志
 * @property int $ad 报警延迟时间，单位为：s (秒)
 * @property int $rttrc 实时测量值上报周期，单位为：s (秒)
 * @property int $chrp 电流谐波上报周期，单位为：s(秒)
 * @property int $hc 心跳周期，单位为：s（秒）
 * @property int $hcuefl 谐波电流使能标志（底字）
 * @property int $hcuefh 谐波电流使能标志（高字）
 * @property string $update_time 修改日期
 * @property int $verified 设备反馈成功并更新数据参数完成：0未完成1完成
 */
class DeviceSetting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'device_setting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['imei'], 'required'],
            [['pwd', 'pt', 'ct1', 'ct2', 'ct3', 'ct4', 'v1', 'v2', 'v3', 'v4', 'e1', 'e2', 'e3', 'e4', 'lc1', 'lc2', 'lc3', 'lc4', 't1', 't2', 't3', 't4', 'cef', 'epem', 'ad', 'rttrc', 'chrp', 'hc', 'hcuefl', 'hcuefh', 'verified'], 'integer'],
            [['update_time'], 'safe'],
            [['imei'], 'string', 'max' => 36],
            [['imei'], 'unique'],
        ];
    }
}
