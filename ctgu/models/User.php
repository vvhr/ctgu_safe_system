<?php

namespace app\models;

use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property int $user_group_id
 * @property string $username 用户名/账号
 * @property string $password 密码
 * @property string $phone 联系电话
 * @property int $state 用户状态
 * @property string $login_time 登录日期
 * @property int $project_id 项目id
 * @property int $user_type 用户类型
 * @property string $id_number 身份证号
 * @property int $parent_id 上一级id
 * @property string $token 用户token
 * @property string $create_time 创建日期
 * @property string $realname 真实姓名
 * @property int $token_update_at 真实姓名
 * @property int $try_login_at 试密码的时间
 * @property int $try_login_count 试密码的次数
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['state', 'project_id', 'user_type', 'parent_id', 'user_group_id', 'token_update_at'], 'integer'],
            [['login_time', 'create_time'], 'safe'],
            [['username','phone','password'], 'required'],
            [['username', 'password'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 15],
            [['id_number'], 'string', 'max' => 18],
            [['token'], 'string', 'max' => 255],
            [['realname'], 'string', 'max' => 20],
            [['username', 'phone'], 'unique'],
        ];
    }

    /**
     * 模型字段过滤器。当它配合数据处理器对象时，会起效
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();
        unset($fields['password'],$fields['token']);
        //以下两句自定义输出字段依赖于关联关系方法：getRole()与getDepartment()
        return $fields;
    }

    public function extraFields()
    {
        $fields = [
            'groupInfo'=>function($model){
                return $model->groupInfo;
            },
            'wxUsers'=>function($model){
                return $model->wxUsers;
            }
        ];
        return $fields;
    }

    public function getGroupInfo(){
        return $this->hasOne(UserGroup::class, ['id'=>'user_group_id']);
    }

    public function getWxUsers(){
        return $this->hasMany(WxUser::class, ['user_id'=>'id']);
    }

    /**-----------------------实现认证IdentityInterface接口----------------------------------------------*/

    /**
     * @param mixed $token
     * @param null $type
     * @return User|null|IdentityInterface
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
        // 此处是为继承了ParentController 数的接口添加了token过期的限制
        // 除此之外，还需要在Login控制器（它没有继承ParentController，不受此处权限过滤限制）添加过期验证
        // $token = sha1($token);
        $user = User::findOne(['token' => $token]);
        if($user){
            $howMuchTimePast = time() - $user->token_update_at;
            // 1小时过期
            if($howMuchTimePast > 3600){
                return null;
            }else{
                // 如果有持续访问受限接口，此处自动更新token时间，以达到自动续期的目标
                $user->token_update_at = time();
                $user->save();
                return $user;
            }
        }else{
            return null;
        }
    }

    /**
     * @param int|string $id
     * @return User|null|IdentityInterface
     */
    public static function findIdentity($id)
    {
        // TODO: Implement findIdentity() method.
        return User::findOne($id);
    }

    /**
     * @return int|mixed|string
     */
    public function getId()
    {
        // TODO: Implement getId() method.
        return $this->id;
    }

    /**
     * @return string|void
     */
    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    /**
     * @param string $authKey
     * @return bool|void
     */
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }

}
