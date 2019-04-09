<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 2018-12-25
 * Time: 10:41
 */

// 顶级命名空间可直接使用 app。这个顶级命名空间在框架中已经放入了映射数组
namespace app\customLibrary;

use app\models\User;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

final class ActionTool
{
    /**
     * 用于添加地址参数过滤条件。必须左联Device表，否则不能使用此方法
     * @param ActiveQuery $query
     * @param $params
     */
    public static function addAddressFilter(ActiveQuery $query, array $params){
        if(isset($params['province']))
            $query->andFilterWhere(['province'=>$params['province']]);
        if(isset($params['city']))
            $query->andFilterWhere(['city'=>$params['city']]);
        if(isset($params['district']))
            $query->andFilterWhere(['district'=>$params['district']]);
        if(isset($params['township']))
            $query->andFilterWhere(['township'=>$params['township']]);

    }

    /**
     * 个人用户组查询权限过滤,需要device表
     * @param ActiveQuery $query
     */
    public static function addGroup3UserIdFilter(ActiveQuery $query){
        /** @var  $userModel User*/
        $userModel = \Yii::$app->user->identity;
        // 个人用户组查询权限过滤
        if($userModel->user_group_id === 3) {
            $myId = $userModel->getId();
            $query->andWhere(['device.user_id'=>$myId]);
        }
    }

    /**
     * 根据expand参数给query添加with关系
     * @param ActiveQuery $query
     * @param array $params
     * @param array $joinRelations
     */
    public static function addWithRelation(ActiveQuery $query,  array $params, array $joinRelations=['relationName'=>['tableName'=>null,'fieldName'=>null,'operator'=>'=']] ){
        if(isset($params['expand'])){
            $splicedExpandParam = explode(',', $params['expand']);
            foreach ($splicedExpandParam as $relation){
                // 最后一个参数指定了必须使用joinWith进行关联的关系。同时指定了joinWith关联时使用的查询条件
                if(key_exists($relation, $joinRelations)){
                    $conditionMeta = $joinRelations[$relation];
                    $query->joinWith($relation);
                    if(isset($params[$conditionMeta['fieldName']])){
                        $operator = $conditionMeta['operator'];
                        $fieldName = $conditionMeta['tableName'].'.'.$conditionMeta['fieldName'];
                        $queryValue = $params[$conditionMeta['fieldName']];
                        $query->andFilterWhere([$operator, $fieldName, $queryValue]);
                    }
                }
                // 如果不在指定范围内，则直接使用with的进行关联
                else
                    $query->with($relation);
            }
        }
    }


    /**
     * @param ActiveQuery $query
     * @param array $params
     * @param string $orderField
     * @return ActiveDataProvider
     */
    public static function createActiveDataProvider(ActiveQuery $query, array $params, $orderField = 'id'){
        // 设备默认每页多少条
        $pageSize = 10;
        if(isset($params['pageSize'])){
            $pageSize = (int)$params['pageSize'];
        }
        // 默认排序
        $orderBy = [$orderField=>SORT_ASC];
        if(isset($params['order_by']) && isset($params['order_method'])){
            $orderBy = [$params['order_by'] => (int)$params['order_method']];
        }
        return new ActiveDataProvider([
            // 使用with方法实现贪加载
            'query' => $query,
            // 分页信息
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            // 排序信息
            'sort' => [
                'defaultOrder' => $orderBy
            ],
        ]);
    }

    /**
     * 创造数据提供者
     * @param ActiveQuery $query
     * @param array $params
     * @return ActiveDataProvider
     */
    public static function createActiveDataProviderMongo(\yii\mongodb\ActiveQuery $query, array $params){
        // 设备默认每页多少条
        $pageSize = 10;
        if(isset($params['pageSize'])){
            $pageSize = (int)$params['pageSize'];
        }
        // 默认排序
        $orderBy = ['_id'=>SORT_ASC];
        if(isset($params['order_by']) && isset($params['order_method'])){
            $orderBy = [$params['order_by'] => (int)$params['order_method']];
        }
        return new ActiveDataProvider([
            // 使用with方法实现贪加载
            'query' => $query,
            // 分页信息
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            // 排序信息
            'sort' => [
                'defaultOrder' => $orderBy
            ],
        ]);
    }
}