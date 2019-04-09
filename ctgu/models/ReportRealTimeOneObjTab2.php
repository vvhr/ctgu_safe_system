<?php

namespace app\models;

use Yii;
use yii\db\Exception;
use yii\db\Query;
use yii\mongodb\ActiveRecord;
/**
 * This is the model class for table "project".
 *
 * @property int $id
 * @property string $_id
 * @property string $imei
 * @property string $uuid
 * @property string $channel
 * @property string $voltage
 * @property string $electricity
 * @property string $leakageCurrent
 * @property string $temperature
 * @property string $markMalfunctionsL
 * @property string $markMalfunctionsH
 * @property string $warningSigns
 * @property string $reportTime
 */

class ReportRealTimeOneObjTab2 extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return 'reportRealTimeOneObjTab2';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return ['_id', 'imei', 'uuid', 'channel',

            'voltage',
            'electricity',
            'leakageCurrent',
            'temperature',

            'markMalfunctionsL',
            'markMalfunctionsH',
            'warningSigns',

            'reportTime'];
    }

    // 如果当前该通道：对应的开启电器功率和>当前实际功率，则按时间顺序关掉电器，一直到功率<或=当前功率为止

    /**
     * @param $uuid
     * @param ReportRealTimeOneObjTab2 $realReportOne
     * @return array
     */
    public static function closeOverPowerAppliance($uuid, $realReportOne){
        // 实时功率默认为0
        $power = 0;
        // 计算实时功率（当设备上线，实时电流电压不为NaN时）
        if($realReportOne){
            // 求当前的实时功率(向上浮动10%，也就是说，当额定功率为1100，实时运行功率为1000时，也不会关闭电器)：
            $power = $realReportOne->voltage/100 * $realReportOne->electricity/1000 * 1.1;
            $uuid = $realReportOne->uuid;
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
