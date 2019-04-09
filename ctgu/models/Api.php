<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "api".
 *
 * @property int $id
 * @property string $api
 * @property string $comment
 * @property string $update_at
 */
class Api extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'api';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['update_at'], 'safe'],
            [['api', 'comment'], 'string', 'max' => 255],
        ];
    }
}
