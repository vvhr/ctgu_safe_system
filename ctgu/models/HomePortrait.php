<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "home_portrait".
 *
 * @property int $id
 * @property int $uuid
 * @property int $typeId 类型id
 * @property int $appId 电器指纹id
 * @property double $apAve 有功均值
 * @property double $apStd 有功方差
 * @property double $rpAve 无功均值
 * @property double $rpStd 无功方差
 * @property double $durationAve 持续时间均值
 * @property double $durationStd 持续时间方差
 * @property string $timeUse 使用时间
 * @property int $homeId 用户homeid（monitoring_user表id）
 * @property double $voltage 电压
 * @property double $electricity 电流
 * @property double $harmonic3 3次谐波
 * @property double $h3Std
 * @property double $harmonic5 5次谐波
 * @property double $h5Std 5次谐波方差
 * @property double $harmonic7 7次谐波
 * @property double $h7Std
 * @property double $harmonic9 9次谐波
 * @property double $h9Std 9次谐波方差
 * @property double $ap 有功功率
 * @property double $rp 无功功率
 * @property string $updateTime 更新时间
 * @property int $state 电器开关状态：1开，2关
 * @property int $is_public 是否公用画像：0否，1是
 * @property string $appName 电器名称
 * @property int $is_temporary 是否临时生成的画像，1是，0否
 * @property int $appNum 是否临时生成的画像，1是，0否
 * @property int $openCount 是否临时生成的画像，1是，0否
 * @property int $is_high 是否是高危电器
 */
class HomePortrait extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'home_portrait';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['typeId', 'appId', 'homeId', 'state', 'is_public','is_temporary', 'is_high', 'openCount', 'appNum','is_temporary'], 'integer'],
            [['apAve', 'apStd', 'rpAve', 'rpStd', 'durationAve', 'durationStd', 'voltage', 'electricity', 'harmonic3', 'h3Std', 'harmonic5', 'h5Std', 'harmonic7', 'h7Std', 'harmonic9', 'h9Std', 'ap', 'rp'], 'number'],
            [['updateTime'], 'safe'],
            [['timeUse', 'appName'], 'string', 'max' => 200],
            [['appName', 'uuid'], 'string', 'max' => 30],
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
            },
            'analysisResults'=>function($model){
                return $model->analysisResults;
            }
        ];
        return $extraFields;
    }


    public function getDevice(){
        return $this->hasOne(Device::class, ['uuid'=>'uuid']);
    }

    public function getAnalysisResults(){
        return $this->hasOne(AnalysisResults::class, ['pid'=>'id']);
    }

    public function getUser(){
        return $this->hasOne(User::class, ['id'=>'user_id'])->via('device');
    }
}
