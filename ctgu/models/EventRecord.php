<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/4/3
 * Time: 11:42
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "event_record".
 *
 * @property int $id
 * @property string $uuid
 * @property int $homePortraitId
 * @property string $appName
 * @property int $onOff
 * @property int $v
 * @property int $c
 * @property string $reportTime
 */

class EventRecord extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'event_record';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['homePortraitId', 'v', 'c', 'onOff'], 'integer'],
            [['reportTime'], 'safe'],
            [['uuid', 'appName'], 'string'],
        ];
    }

    public function getDevice(){
        return $this->hasOne(Device::class, ['uuid'=>'uuid']);
    }

    public function getUser(){
        return $this->hasOne(User::class, ['id'=>'user_id'])->via('device');
    }

    public function extraFields()
    {
        $extraFields = [
            'device'=>function($model){
                return $model->device;
            },
            'user'=> function($model){
                return $model->user;
            }
        ];
        return $extraFields;
    }
}