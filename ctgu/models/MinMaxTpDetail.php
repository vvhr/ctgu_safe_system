<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "min_max_tp_detail".
 *
 * @property int $id
 * @property string $imei
 * @property int $channel
 * @property int $temperature
 * @property int $voltage
 * @property int $electricity
 * @property int $leakageCurrent
 * @property string $reportRealTimeTabId
 * @property string $reportTime
 * @property string $date
 * @property int $created_at
 * @property int $min_temperature
 * @property int $home_id
 * @property int $user_id
 */
class MinMaxTpDetail extends \yii\db\ActiveRecord
{
    public static $channelField = [
        'voltage'=>['voltage1','voltage2','voltage3','voltage4'],
        'leakageCurrent'=>['leakageCurrent','leakageCurrent2','leakageCurrent3','leakageCurrent4'],
        'electricity'=>['electricity','electricity2','electricity3','electricity4'],
        'temperature'=>['temperatureA','temperatureB','temperatureC','temperatureN']
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'min_max_tp_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['channel', 'temperature', 'voltage', 'electricity', 'leakageCurrent', 'created_at', 'min_temperature', 'home_id','user_id'], 'integer'],
            [['reportTime', 'date'], 'safe'],
            [['imei', 'reportRealTimeTabId'], 'string', 'max' => 255],
        ];
    }

    public function extraFields()
    {
        $fields['device'] = function ($model){
            return $model->device; //$model是当前活动记录实例
        };
        $fields['user'] = function ($model){
            return $model->user; //$model是当前活动记录实例
        };
        $fields['homeUser'] = function ($model){
            return $model->homeUser; //$model是当前活动记录实例
        };
        return $fields;
    }

    public function getDevice(){
        return $this->hasOne(Device::class, ['imei'=>'imei']);
    }

    public function getHomeUser(){
        return $this->hasOne(HomeUser::class, ['id'=>'home_id']);
    }

    public function getUserDevice(){
        return $this->hasOne(UserDevice::class, ['home_id'=>'id'])->via('homeUser');
    }

    public function getUser(){
        return $this->hasOne(User::class, ['id'=>'user_id']);
    }


    /**
     * 按某一天将该天的最大值对应的记录查出来(只查一条)，并存储到本模型对应的表
     * @param $date
     * @return array
     * 前置方法
     * @see \app\models\MinMaxLc::insertMinMaxLcByDate()
     */
    public static function getAndInsertMaxMinTPRelatedRecordsByDate($date){
        set_time_limit(180);
        $success = 0;
        $fail = 0;
        $one = MinMaxTpDetail::find()->where(['date'=>$date])->limit(1)->one();
        if(!$one){
            $maxMinTps = MinMaxLc::find()->where(['date'=>$date])->all();
            // 只选择
            $dangerTp = 400;
            //遍历当天所有IMEI对应设备漏电流最大值最小值记录，并查询对应最大值第一条记录存储到表
            /** @var  $item MinMaxLc*/
            foreach ($maxMinTps as $item){
                // 比较每一个通道的漏电最大小最小的差值，超过一定值的进一步查询并存储
                for($i=1;$i<=4;$i++){

                    // 初始化计算变量
                    $tp_max = $item['max_tp'.$i];
                    $tp_min = $item['min_tp'.$i];
                    $channel = $i;

                    // 每个通道在mongoDB的表中对应的电流，电压，漏电流，温度字段名
                    $LCField =  self::$channelField['leakageCurrent'][$i-1];
                    $VTField =  self::$channelField['voltage'][$i-1];
                    $TPField =  self::$channelField['temperature'][$i-1];
                    $ETField =  self::$channelField['electricity'][$i-1];

                    // 当漏电流最大值与最小值差值大于100时，查询第一条等于最大值的实时上报记录，并存储到统计表，计算存储成功条数
                    if($tp_max>=$dangerTp){
                        $insertData = [];
                        $res = ReportRealTimeTab::find()->where(
                            [
                                'imei'=>$item->imei,
                                $TPField=>$tp_max,
                                'reportTime' => ['$gte' => $date.' 00:00:00', '$lte' => $date.' 23:59:59']
                            ]
                        )->limit(1)->one();
                        $insertData['imei']=$item->imei;
                        $insertData['channel']=$channel;
                        //查询home_id
                        /** @var  $homeUser HomeUser*/
                        $homeUser = HomeUser::find()->where(['imei'=>$item->imei, 'channel'=>$channel])->limit(1)->one();
                        if($homeUser){
                            $home_id = $insertData['home_id']= $homeUser->id;
                            $userDevice = UserDevice::find()->where(['home_id'=>$home_id])->limit(1)->one();
                            $insertData['user_id']=$userDevice?$userDevice->user_id:null;
                        }


                        $insertData['leakageCurrent']=$res[$LCField];
                        $insertData['voltage']=$res[$VTField];
                        $insertData['electricity']=$res[$ETField];
                        $insertData['temperature']=$res[$TPField];
                        $insertData['reportRealTimeTabId']=(string)$res['_id'];
                        $insertData['reportTime']=$res['reportTime'];
                        $insertData['date']=$date;
                        $insertData['created_at']=time();
                        $insertData['min_temperature']=$tp_min;
                        $model = new MinMaxTpDetail();
                        $model->load($insertData, '');
                        if($model->save()){
                            $success++;
                        }else{
                            $fail++;
                        }
                    }
                }
            }
        }
        return ['success'=>$success,'fail'=>$fail];
    }
}
