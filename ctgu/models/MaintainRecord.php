<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "maintain_record".
 *
 * @property int $id 主键ID
 * @property int $device_exception_id 关键报警记录ID
 * @property string $maintainer 维护人员
 * @property int $phone 维护电话
 * @property int $maintain_type 维护类型:0现场维护,1社区维护
 * @property string $maintain_message 维护详情
 * @property string $maintain_suggest 维护建议
 * @property string $fault_img 故障图片
 * @property int $inputer 录入员
 * @property string $maintain_time 维护时间
 * @property string $update_at 更新时间
 * @property string $uuid 异常设备
 * @property string $imgUrl0 图片存储路径0
 * @property string $imgUrl1 图片存储路径1
 * @property string $imgUrl2 图片存储路径2
 * @property string world_excel_url world文档或者excel存储路径
 * @property false|string create_at
 */
class MaintainRecord extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'maintain_record';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['device_exception_id', 'phone', 'maintain_type', 'inputer', 'valid'], 'integer'],
            [['maintain_time', 'update_at', 'create_at'], 'safe'],
            [['maintainer', 'maintain_message', 'maintain_suggest', 'uuid', 'imgUrl0', 'imgUrl1', 'imgUrl2', 'world_excel_url'], 'string', 'max' => 255],
            [['uuid'], 'string', 'max' => 30],
        ];
    }

    public function getHomeUser() {
        return $this->hasOne(HomeUser::class, ['uuid'=>'uuid']);
    }

    public function getDevice(){
        return $this->hasOne(Device::class, ['uuid'=>'uuid']);
    }
    public function getUser(){
        return $this->hasOne(User::class, ['id'=>'user_id'])->via('device');
    }
    public function extraFields()
    {
        $fields = [
            'homeUser'=> function($model){
                return $model->homeUser;
            },
            'device'=> function($model){
                return $model->device;
            },
            'user' => function($model){
                return $model->user;
            },
        ];
        return $fields;
    }

}
