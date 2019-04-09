<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "operate_log".
 *
 * @property int $id
 * @property string $content
 * @property string $create_time
 * @property string $log_type
 * @property string $uuid
 */
class OperateLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'operate_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['create_time'], 'safe'],
            [['log_type','uuid'], 'string', 'max' => 255],
            [['content'], 'string'],
        ];
    }
}
