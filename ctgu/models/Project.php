<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "project".
 *
 * @property int $id
 * @property string $project_name 项目名称
 * @property string $person_liable 责任人
 * @property string $contact 联系方式
 * @property int $project_type 项目类型
 * @property string $address 详细地址
 * @property int $region_id 区域id
 * @property int $term 项目期限
 * @property string $created_at 创建日期
 * @property int $install_num 安装数量
 * @property bool $is_del 是否删除：0/false未删除，1/true已删除
 * @property string $maintenance 维护人员
 * @property string $contact_m 维护人员联系方式
 * @property string $install_time 安装时间
 * @property int $operator_user_id
 */
class Project extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_type', 'region_id', 'term', 'install_num', 'operator_user_id'], 'integer'],
            [['project_name'], 'unique'],
            [['created_at', 'installed_at'], 'safe'],
            [['is_del'], 'boolean'],
            [['project_name'], 'string', 'max' => 50],
            [['person_liable', 'maintenance'], 'string', 'max' => 20],
            [['contact', 'contact_m'], 'string', 'max' => 15],
            [['address'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_name' => 'Project Name',
            'person_liable' => 'Person Liable',
            'contact' => 'Contact',
            'project_type' => 'Project Type',
            'address' => 'Address',
            'region_id' => 'Region ID',
            'term' => 'Term',
            'created_at' => 'Created At',
            'install_num' => 'Install Num',
            'is_del' => 'Is Del',
            'maintenance' => 'Maintenance',
            'contact_m' => 'Contact M',
            'install_time' => 'Install Time',
            'operator_user_id' => 'Operator User ID',
        ];
    }
}
