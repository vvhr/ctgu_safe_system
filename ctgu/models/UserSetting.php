<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_setting".
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $default_address 默认地址
 */
class UserSetting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_setting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'default_address'], 'required'],
            [['user_id'], 'integer'],
            [['default_address'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'default_address' => 'Default Address',
        ];
    }
}
