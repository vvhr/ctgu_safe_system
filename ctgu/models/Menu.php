<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "menu".
 *
 * @property int $id 主键id
 * @property string $menu_name 菜单名称
 * @property string $route_name 点击跳转路径
 * @property int $parent_id 父级菜单id
 * @property string $create_time 创建日期
 * @property int $has_children
 * @property int $level
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'menu';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id', 'has_children', 'level'], 'integer'],
            [['create_time'], 'safe'],
            [['menu_name'], 'string', 'max' => 45],
            [['route_name'], 'string', 'max' => 300],
        ];
    }
}
