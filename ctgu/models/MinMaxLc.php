<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "min_max_lc".
 *
 * @property int $id
 * @property string $imei
 * @property int $max_lc1
 * @property int $min_lc1
 * @property int $max_lc2
 * @property int $min_lc2
 * @property int $max_lc3
 * @property int $min_lc3
 * @property int $max_lc4
 * @property int $min_lc4
 * @property int $max_tp1
 * @property int $min_tp1
 * @property int $max_tp2
 * @property int $min_tp2
 * @property int $max_tp3
 * @property int $min_tp3
 * @property int $max_tp4
 * @property int $min_tp4
 * @property int $created_at
 * @property string $date
 */
class MinMaxLc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'min_max_lc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'max_lc1', 'min_lc1', 'max_lc2', 'min_lc2', 'max_lc3', 'min_lc3', 'max_lc4', 'min_lc4', 'created_at',
                    'max_tp1', 'min_tp1', 'max_tp2', 'min_tp2', 'max_tp3', 'min_tp3', 'max_tp4', 'min_tp4',
                ],
                'integer'],
            [['date'], 'safe'],
            [['imei'], 'string', 'max' => 255],
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
        return $fields;
    }

    public function getDevice(){
        return $this->hasOne(Device::class, ['imei'=>'imei']);
    }

    public function getUser(){
        return $this->hasOne(User::class, ['id'=>'user_id'])->viaTable('user_device', ['imei'=>'imei']);
    }

    /**
     * 将某一天的当天漏电流最大值最小值查出来存到本表
     * @param $date
     * @return array
     * @throws \yii\mongodb\Exception
     * 依赖方法
     * @see \app\models\ReportRealTimeTab::getMaxMinLCOfDeviceByDate()
     * 后置方法
     * @see \app\models\MinMaxLcDetail::getAndInsertMaxMinLCRelatedRecordsByDate()
     */
    public static function insertMinMaxLcByDate($date){
        $minMaxLcOfDate = MinMaxLc::find()->where(['date'=>$date])->limit(1)->one();
        $count = 0;
        $fail = 0;
        if(!$minMaxLcOfDate){
            $res = ReportRealTimeTab::getMaxMinLCOfDeviceByDate($date);
            foreach ($res as $item){
                $model = new MinMaxLc();
                $model->load($item, '');
                $model->imei = $item['_id'];
                $model->date = $date;
                $model->created_at = time();
                if($model->save()){
                    $count++;
                }else{
                    $fail++;
                }
            }
        }
        return ['success'=>$count,'fail'=>$fail];
    }
}
