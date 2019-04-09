<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "fingerlibrary".
 *
 * @property int $id
 * @property int $typeId 电器类型id
 * @property double $apAve 有功功率均值
 * @property double $rpAve 无功均值
 * @property double $h3Ave 3次谐波均值
 * @property double $h5Ave 5次谐波均值
 * @property double $h7Ave 7次谐波均值
 * @property double $h9Ave 9次谐波均值
 * @property double $voltage 电压
 * @property double $electricity 电流
 * @property double $timeUse 持续时间
 * @property string $appName 电器名称
 */
class Fingerlibrary extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fingerlibrary';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['typeId'], 'integer'],
            [['apAve', 'rpAve', 'h3Ave', 'h5Ave', 'h7Ave', 'h9Ave', 'voltage', 'electricity', 'timeUse'], 'number'],
            [['appName'], 'string', 'max' => 50],
        ];
    }
}
