<?php

namespace app\controllers;

use app\config\Status;
use app\controllers\parent\ParentController;
use app\customLibrary\ActionTool;
use app\customLibrary\ReturnTool;
use app\models\DeviceException;
use app\models\DeviceExceptionNew;
use app\models\MaintainRecord;
use app\models\UploadForm;
use app\models\UploadWorldExcel;
use app\models\UserDevice;
use Yii;
use yii\data\ActiveDataProvider;

class MaintainRecordController extends ParentController
{
    public function actionIndex(){
        $params = Yii::$app->request->getQueryParams();
        $query = MaintainRecord::find();
        // 添加个人用户组为3筛选条件
        ActionTool::addGroup3UserIdFilter($query);
        // 添加地址筛选条件
        ActionTool::addAddressFilter($query, $params);
        ActionTool::addWithRelation($query, $params,
            [
                'homeUser',
                'device'
            ]);

        if (isset($params['device_exception_id']))
            $query->andWhere(['device_exception_id' => $params['device_exception_id']]);
        if (isset($params['maintain_type']))
            $query->andWhere(['maintain_type' => $params['maintain_type']]);
        if(isset($params['valid'])){
            $query->andWhere(['maintain_record.valid' => $params['valid']]);
        }

        return ActionTool::createActiveDataProvider($query,$params);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate() {
        $data = Yii::$app->request->getBodyParams();
        $model = new MaintainRecord();
        $model->load($data, '');
        $model->inputer = Yii::$app->user->id;
        $model->create_at = date('Y-m-d H:i:s');

        if ($model->save()) {
           return ReturnTool::returnPostMsg(Status::SUCCESS,$model, '添加维护记录成功');
        }

        return ReturnTool::returnPostMsg(Status::FAIL,$model, '添加维护记录失败');
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate() {
        $user_group_id = Yii::$app->user->identity->user_group_id;
        $user_id = Yii::$app->user->id;
        $data = Yii::$app->request->getBodyParams();
        $model = MaintainRecord::findOne(['id' => $data['id']]);
        if ($user_group_id === 3 && $model['inputer'] !== $user_id) {
            return ReturnTool::returnPostMsg(Status::FAIL,$model, '你没有权限修改');
        }
        $model->load($data, '');
        $model->update_at = date('Y-m-d H:i:s');
        if ($model->save()) {
            return ReturnTool::returnPostMsg(Status::SUCCESS,$model, '修改成功');
        }

        return ReturnTool::returnPostMsg(Status::FAIL,$model, '修改失败');
    }

    public function actionView() {
        $params = Yii::$app->request->getQueryParams();
        $result = MaintainRecord::find()->where(['id' => $params['id']])->limit(1)->one();
        return $result;
    }

    /**
     * @return array
     *  故障设备,添加维护记录上传图片的功能
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpload() {
        $data = \Yii::$app->request->getBodyParams();
        if (Yii::$app->request->isPost) {

            if (in_array('world_excel_url', $data)) {
                $model = new UploadWorldExcel();
                $imgStorageDir = Yii::$app->params['wordOrExcelStorageDir']; // 获取文件保存目录
            } else {
                $model = new UploadForm();
                $imgStorageDir = Yii::$app->params['imgStorageDir']; // 获取文件保存目录
            }

            $imgPathName =  $model->upload($imgStorageDir, 'file'); // 保存图片,返回图片保存目录
            if (!$imgPathName) {
                return ReturnTool::returnPostMsg(Status::FAIL,$data, '图片保存失败');
            }
            $maintainRecord = MaintainRecord::find()->where(['id' => (int)$data['id']])->limit(1)->one();
            if ($model->updateUrl($maintainRecord, $data['imageFieldName'], $imgPathName)) { // 保存到数据库
                return ReturnTool::returnPostMsg(Status::SUCCESS,$model, '文件上传成功');
            }
            return ReturnTool::returnPostMsg(Status::SUCCESS,$model, '文件上传成功,保存失败,请联系管理员');
        }

        return ReturnTool::returnPostMsg(Status::FAIL,$data, '文件上传失败');
    }

    /**
     * @throws \yii\base\InvalidConfigException
     *  删除图片及数据库路径
     */
    public function actionDelete() {
        $data = \Yii::$app->request->getBodyParams();
        $maintainRecord = MaintainRecord::find()->where(['id' => $data['id']])->limit(1)->one();
        /** @var MaintainRecord $maintainRecord */
        $oldImg = $maintainRecord[$data['imageFieldName']]; // 获取图片路径删除用
        $maintainRecord[$data['imageFieldName']] = null;

        if ($maintainRecord->save()) {
            if(is_file($oldImg))
                unlink($oldImg); // 删除图片
            return ReturnTool::returnPostMsg(Status::SUCCESS,$maintainRecord, '删除成功');
        }
        return ReturnTool::returnPostMsg(Status::FAIL,$maintainRecord, '删除失败');
    }
}
