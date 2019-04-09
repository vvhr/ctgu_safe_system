<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_group_api".
 *
 * @property int $id
 * @property int $group_id
 * @property int $api_id
 */
class UserGroupApi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_group_api';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id', 'api_id'], 'integer'],
        ];
    }

    public function extraFields()
    {
        $extraFields = [
            'groupInfo'=>function($model){
                return $model->groupInfo;
            },
            'apiInfo'=>function($model){
                return $model->apiInfo;
            }
        ];
        return $extraFields;
    }

    public function getGroupInfo(){
        return $this->hasOne(UserGroup::class, ['id'=>'group_id']);
    }

    public function getApiInfo(){
        return $this->hasOne(Api::class, ['id'=>'api_id']);
    }
}
