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

    public function getWxUsers(){
        return $this->hasMany(WxUser::class, ['user_id'=>'user_id'])->via('device');
    }

    /**
     * @param $uuid
     * @param DeviceReportNew $deviceReportNew
     * @return array
     */
    public static function closeOverPowerAppliance($uuid, $deviceReportNew){
        // 实时功率默认为0
        $power = 0;
        // 计算实时功率（当设备上线，实时电流电压不为NaN时）
        if($deviceReportNew){
            // 求当前的实时功率(向上浮动10%，也就是说，当额定功率为1100，实时运行功率为1000时，也不会关闭电器)：
            $power = $deviceReportNew->p * 1.1;
            $uuid = $deviceReportNew->uuid;
        }

        // 计算指纹库状态为开启的电器总功率
        $power_sum = HomePortrait::find()->where(['uuid'=>$uuid,'state'=>1])->sum('apAve');

        // 第一步预先处理：先将所有大于总功率的电器全关掉
        if($power_sum>$power){
            // 日志记录需要关闭的所有电器
            $willClosedApps = HomePortrait::find()->where(['uuid'=>$uuid,'state'=>1])->andWhere('apAve>'.$power)->all();
            /** @var  $app HomePortrait*/
            foreach ($willClosedApps as $app){
                // 关闭电器
                $app->state = 2;
                $app->save();
                // 日志记录
                $log = new OperateLog();
                $log->uuid = $app->uuid;
                $log->content = '关闭的电器为：'.$app->appName.',id为'.$app->id.',uuid为'.$app->uuid.',apAve为'.$app->apAve;
                $log->create_time = date('Y-m-d H:i:s');
                $log->log_type = '实时关闭超过实时运行功率的电器';
                $log->save();
            }
        }

        if($power<=0){
            return ['实时功率为0，所有电器已经关闭，不再执行任何操作'];
        }
        // 第二步处理：逐一关闭电器，真至总功率小于实时功率
        // 求当前设备通道对应的开启的电器总功率：
        $homePortraits = HomePortrait::find()->where(['uuid'=>$uuid,'state'=>1])->orderBy('updateTime ASC')->all();
        $powerSum = 0;
        // 如果该uuid存在指纹库，求开启电器功率总和
        if(count($homePortraits)>0){
            // 将所有开启的电器的功率放到一个数组
            $array_power = array_column($homePortraits, 'apAve');
            // 将以上数组复制一个数组
            $array_power_tmp = array_merge([],$array_power);
            // 遍历数组，逐一关闭开启的电器，直到总功率小于或等于$power,就停止关闭
            foreach ($array_power as $key=>$value){
                // $powerSum：开启电器的功率总和
                $powerSum = array_sum($array_power_tmp);
                if($powerSum>$power){
                    //关掉一个电器
                    $homePortraits[$key]->state=2;
                    $homePortraits[$key]->save();
                    array_shift($array_power_tmp);
                    //日志记录关闭的电器
                    $log = new OperateLog();
                    $log->uuid = $homePortraits[$key]->uuid;
                    $log->content = '关闭的电器为：'.$homePortraits[$key]->appName.',id为'.$homePortraits[$key]->id.',uuid为'.$homePortraits[$key]->uuid.',apAve为'.$homePortraits[$key]->apAve;
                    $log->create_time = date('Y-m-d H:i:s');
                    $log->log_type = '实时关闭超过实时运行功率的电器';
                    $log->save();
                }else{
                    break;
                }
            }
        }
        return ['power'=>$power,'powerSum'=>$powerSum];
    }

}
