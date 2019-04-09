<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "wx_user".
 *
 * @property int $id
 * @property string $openid
 * @property int $user_id
 * @property int $subscribe_at
 * @property int $bind_at
 * @property string $tel
 * @property string $nickname
 * @property int $sex
 * @property string $province
 * @property string $city
 * @property string $country
 * @property string $headimgurl
 * @property int $enable_receive_msg
 */
class WxUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wx_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['openid'], 'required'],
            [['user_id', 'subscribe_at', 'bind_at', 'sex', 'enable_receive_msg'], 'integer'],
            [['openid', 'nickname', 'province', 'city', 'country', 'headimgurl'], 'string', 'max' => 255],
            [['tel'], 'string', 'max' => 15],
        ];
    }

    public function getUserInfo(){
        return $this->hasOne(User::class, ['id'=>'user_id']);
    }
}
