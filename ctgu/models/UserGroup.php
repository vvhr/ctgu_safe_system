<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_group".
 *
 * @property int $id
 * @property string $group_name
 */
class UserGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_name'], 'string', 'max' => 255],
        ];
    }

    public function extraFields()
    {
        $extraFields = [
            'apiList'=>function($model){
                return $model->apiList;
            }
        ];
        return $extraFields;
    }

    public function getApiList(){
        $this->hasMany(Api::class, ['id'=>'api_id'])->viaTable('user_group_api', ['group_id'=>'id']);
    }
}
