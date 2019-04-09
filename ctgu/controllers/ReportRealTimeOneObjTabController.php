<?php

namespace app\controllers;

use app\models\Device;
use app\models\UserDevice;
use Yii;
use app\controllers\parent\ParentController;
use app\models\ReportRealTimeOneObjTab;
class ReportRealTimeOneObjTabController extends ParentController
{
    /**
     * 实时设备信息
     * @return array
     */
    public function actionIndex(){
        $myId = Yii::$app->user->identity->getId();
        $myGroupId = Yii::$app->user->identity->user_group_id;
        $params = \Yii::$app->request->getQueryParams();
        $query = Device::find();
        $query->where(['<>','project_id','null']);
        // 不查掉线的设备
        $query->andWhere(['<>','state','1']);
        // 不查未启用的设备
        $query->andWhere(['<>','is_del','-1']);
        // user_group_id === 3 代表是个人用户，这时，只搜索与user_id相关联的设备
//        $query->leftJoin('user_device as d','d.device_id = device.id');
//        $query->with('users');
        if($myGroupId === 3) {
            $userDevices = UserDevice::find()->where(['user_id'=>$myId])->asArray()->all();
            $device_ids = array_unique(array_column($userDevices, 'device_id'));
            $query->andWhere(['id'=>$device_ids]);
        }else{
            if(isset($params['user_id'])) {
                $userDevices = UserDevice::find()->where(['user_id'=>$params['user_id']])->asArray()->all();
                $device_ids = array_unique(array_column($userDevices, 'device_id'));
                $query->andWhere(['id'=>$device_ids]);
            }
        }


        if(isset($params['imei']) && trim($params['imei']) !== '')
            $query->andWhere(['like', 'device.imei', trim($params['imei'])]);
        if(isset($params['project_id']) && $params['project_id'] !== '')
            $query->andWhere(['project_id'=>$params['project_id']]);
        if(isset($params['province'])  && $params['province'] !== '')
            $query->andWhere(['province'=>$params['province']]);
        if(isset($params['city'])  && $params['city'] !== '')
            $query->andWhere(['city'=>$params['city']]);
        if(isset($params['district'])  && $params['district'] !== '')
            $query->andWhere(['district'=>$params['district']]);
        if(isset($params['township'])  && $params['township'] !== '')
            $query->andWhere(['township'=>$params['township']]);

        if(isset($params['expand'])) {
            $expandFields = explode(',', $params['expand']);
            if(in_array('usersInfo', $expandFields)){
                $query->with('usersInfo');
            }
            if(in_array('homeUsers', $expandFields)){
                $query->with('homeUsers');
            }
            if(in_array('users', $expandFields)){
                $query->with('users');
            }
        }

        // 分页：
//        return [$query->createCommand()->getRawSql()];

        $totalCount = $query->count();
        $page = 1;
        $offset = 0;
        $limit = 10;
        if(isset($params['page']))
            $page = $params['page'];
        if(isset($params['pageSize']))
            $limit = $params['pageSize'];
        if($page > 1)
            $offset = ($page-1)*$limit;
        $query->offset($offset)->limit($limit);
        $_meta = [
            'currentPage'=>$page,
            'perPage'=>$limit,
            'totalCount'=>$totalCount
        ];
        /*----------------------*/
        $devices = $query->asArray()->all();

        $imeis = array_column($devices,'imei');
        $res = ReportRealTimeOneObjTab::find()->where(['imei'=>$imeis])->orderBy('imei')->all();
        $tempArr = [];
        foreach ($res as $report){
            $tempArr[$report['imei']] = $report->toArray();
        }
        foreach ($devices as $device){
            if(isset($tempArr[$device['imei']])){
                $tempArr[$device['imei']] = array_merge($device, ['report'=>$tempArr[$device['imei']]]);
            }else{
                $tempArr[$device['imei']] = array_merge($device, ['report'=>null]);
            }
        }
        return array_merge(['_items'=>$tempArr,'_meta'=>$_meta]);
    }

    public function actionView(){
        $params = \Yii::$app->request->getQueryParams();
        return ReportRealTimeOneObjTab::findOne(['imei'=>$params['imei']]);
    }
}
