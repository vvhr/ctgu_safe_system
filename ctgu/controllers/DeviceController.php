<?php

namespace app\controllers;

use app\config\Status;
use app\customLibrary\ActionTool;
use app\customLibrary\ReturnTool;
use app\models\Device;
use app\models\DeviceExceptionNew;
use Yii;
use app\controllers\parent\ParentController;
use yii\data\ActiveDataProvider;

class DeviceController extends ParentController
{
    /**
     * @return ActiveDataProvider
     */
    public function actionIndex(){
        $params = \Yii::$app->request->getQueryParams();
        $query = Device::find()->joinWith('report');
        // 添加个人用户组为3筛选条件
        ActionTool::addGroup3UserIdFilter($query);
        // 添加地址筛选条件
        ActionTool::addAddressFilter($query, $params);
        // 添加with关系与joinWith关联查询贪加载
        ActionTool::addWithRelation($query, $params,
            [
                'user'=>['tableName'=>'user', 'fieldName'=>'username','operator'=>'like'],
                'project'=>['tableName'=>'project', 'fieldName'=>'project_name','operator'=>'like']
            ]);

        if(isset($params['state']))
            $query->andFilterWhere(['device.state'=>$params['state']]);
        if(isset($params['uuid']))
            $query->andWhere(['like', 'device.uuid', trim($params['uuid'])]);
        if(isset($params['address']))
            $query->andWhere(['like', 'device.address', trim($params['address'])]);
        if(isset($params['enable']))
            $query->andFilterWhere(['device.enable'=>$params['enable']]);
        if(isset($params['user_id']))
            $query->andFilterWhere(['user_id'=>$params['user_id']]);
        if(isset($params['project_id']))
            $query->andFilterWhere(['device.project_id'=>$params['project_id']]);
        if(isset($params['unbind'])){
            switch ($params['unbind']){
                case 0:
                    $query->andWhere('ISNULL(device.user_id)');
                    break;
                case 1:
                    $query->andWhere('device.user_id IS NOT NULL');
                    break;
                default:
                    break;
            }

        }
        // 指定查询没有绑定到这个指定用户ID的所有设备
        if(isset($params['unbindThisUserId']) && $params['unbindThisUserId']){
            $query->andWhere(['<>', 'user_id', $params['unbindThisUserId']]);
            $query->orWhere('ISNULL(device.user_id)');
        }
        // 指定查询没有绑定到这个指定项目ID的所有设备
        if(isset($params['unbindThisProjectId']) && $params['unbindThisProjectId']){
            $query->andWhere(['<>', 'device.project_id', $params['unbindThisProjectId']]);
            $query->orWhere('ISNULL(device.project_id)');
        }
        //return $query->createCommand()->getRawSql();
        return ActionTool::createActiveDataProvider($query,$params);
    }

    /**
     * @return Device|null
     */
    public function actionView(){
        $params = Yii::$app->request->getQueryParams();
        $id = $params['id'];
        $model = Device::findone($id);
        return $model;
    }

    /**
     * @return array
     */
    public function actionBulkAddDevice(){
        $resData = Device::bulkAddDevice();
        return ReturnTool::returnPostMsg(Status::SUCCESS,$resData,'导入完成');
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionBulkBindUser(){
        $params = \Yii::$app->request->getBodyParams();
        $res = Device::bulkBindUser((int)$params['user_id'], $params['device_ids']);
        return ReturnTool::returnPostMsg(Status::SUCCESS, $res, '绑定了'.count($res).'个设备');
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionBulkBindProject(){
        $params = \Yii::$app->request->getBodyParams();
        $res = Device::bulkBindProject((int)$params['project_id'], $params['device_ids']);
        return ReturnTool::returnPostMsg(Status::SUCCESS, $res, '绑定了'.count($res).'个设备');
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionUpdate(){
        $successMsg = '更新设备成功';
        $failMsg = '更新设备失败';
        $params = \Yii::$app->request->getBodyParams();
        $device = null;
        if(isset($params['id'])){
            $device = Device::findOne($params['id']);
        }elseif (isset($params['uuid'])){
            $device = Device::findOne(['uuid'=>$params['uuid']]);
        }

        if($device){
            $device->load($params,'');
            // 解绑用户
            if(isset($params['unbind']) && $params['unbind']===1) {
                $device->user_id = null;
                $successMsg = '解绑设备用户成功';
                $failMsg = '解绑设备用户失败';
            }
            // 解绑项目
            if(isset($params['unbindProject']) && $params['unbindProject']===1) {
                $device->project_id = null;
                $successMsg = '解绑设备项目成功';
                $failMsg = '解绑设备项目失败';
            }

            if($device->save()){
                if (isset($params['clear_fault']) && $params['clear_fault'] == 'clear_fault') {
                    DeviceExceptionNew::deviceExceptionNewUpdate($params['uuid']);
                }
                $res = ReturnTool::returnPostMsg(Status::SUCCESS,$device, $successMsg);
            }else{
                $res = ReturnTool::returnPostMsg(Status::FAIL,$device->errors, $failMsg);
            }
            return $res;
        }
        return ReturnTool::returnPostMsg(Status::FAIL,null, '设备ID不存在');
    }

    /**
     * 获取设备总数
     * @return int
     */
    public function actionTotal(){
        $params = \Yii::$app->request->getQueryParams();
        // 必须是已启用的设备
        $query = Device::find()->andWhere(['enable'=>1]);
        ActionTool::addGroup3UserIdFilter($query);
        ActionTool::addAddressFilter($query, $params);

        if(isset($params['state']))
            $query->andFilterWhere(['device.state'=>$params['state']]);

        return (int)($query->count());
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     * 按区域分组得出每个区域的设备总数
     */
    public function actionTotalGroupByDistrict(){
        $params = \Yii::$app->request->getQueryParams();
        $districtLevel = $params['district_level'];

        $query = Device::find()->select([$params['district_level'],'COUNT(*) as total']);
        $query->andWhere(['device.enable'=>1]);

        ActionTool::addAddressFilter($query, $params);
        ActionTool::addGroup3UserIdFilter($query);
        return $query->asArray()->groupBy($districtLevel)->all();
    }

}
