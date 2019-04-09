<?php

namespace app\controllers;

use app\config\Status;
use app\models\DeviceException;
use app\controllers\parent\ParentController;
use app\models\UserDevice;
use MongoDB\Driver\WriteResult;
use Yii;
use yii\data\ActiveDataProvider;

class DeviceExceptionController extends ParentController
{
    public function actionIndex(){
        $params = \Yii::$app->request->getQueryParams();
        $query = DeviceException::find()->joinWith(['device'])->where(['device.is_del'=>0]);

        if(Yii::$app->user->identity->user_group_id === 3) {
            $myId = Yii::$app->user->identity->getId();
            $userDevices = UserDevice::find()->where(['user_id'=>$myId])->asArray()->all();
            $device_imeis = array_unique(array_column($userDevices, 'imei'));
            $query->andWhere(['device_exception.imei'=>$device_imeis]);
        }

        if(isset($params['expand'])) {
            $expandFields = explode(',', $params['expand']);
            if(in_array('users', $expandFields)){
                $query->joinWith('users');
            }
            $query->groupBy('device_exception.id');
        }
        if(isset($params['expand'])) {
            $expandFields = explode(',', $params['expand']);
            if(in_array('maintainRecord', $expandFields)){
                $query->With(['maintainRecord'=> function($model) {
                    $model->andWhere(['maintain_record.valid' => 1]);
                }]);
            }
        }
        
        if(!empty($params['username']))
            $query->andWhere(['like', 'user.username', trim($params['username'])]);
        if(!empty($params['imei']))
            $query->andWhere(['device_exception.imei'=>trim($params['imei'])]);
        if(isset($params['treatment_result'])  && $params['treatment_result'] !== '')
            $query->andWhere(['treatment_result'=>$params['treatment_result']]);
        if(isset($params['province'])  && $params['province'] !== '')
            $query->andWhere(['province'=>$params['province']]);
        if(isset($params['city'])  && $params['city'] !== '')
            $query->andWhere(['city'=>$params['city']]);
        if(isset($params['district'])  && $params['district'] !== '')
            $query->andWhere(['district'=>$params['district']]);
        if(isset($params['township'])  && $params['township'] !== '')
            $query->andWhere(['township'=>$params['township']]);
        if(isset($params['type']) && trim($params['type']) !== '')
            $query->andWhere(['device_exception.type'=>$params['type']]);

//        return $query->createCommand()->getRawSql();
        // 设备默认每页多少条
        $pageSize = 10;
        if(isset($params['pageSize'])){
            $pageSize = (int)$params['pageSize'];
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
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);
    }
    /*按地区与时间范围统计总数*/
    public function actionTotal(){
        $params = \Yii::$app->request->getQueryParams();
        $query = DeviceException::find()->select('COUNT(*) as total')->leftJoin('device','device_exception.imei = device.imei');
        $query->andWhere(['device_exception.type'=>1]);
        if(isset($params['date_from']) && isset($params['date_to']) && $params['date_from'] && $params['date_to']){
            $query->andWhere(['>=', 'device_exception.create_time', $params['date_from']]);
            $query->andWhere(['<=', 'device_exception.create_time', $params['date_to']]);
        }
        if(isset($params['province'])  && $params['province'] !== '')
            $query->andWhere(['province'=>$params['province']]);
        if(isset($params['city'])  && $params['city'] !== '')
            $query->andWhere(['city'=>$params['city']]);
        if(isset($params['district'])  && $params['district'] !== '')
            $query->andWhere(['district'=>$params['district']]);
        if(isset($params['township'])  && $params['township'] !== '')
            $query->andWhere(['township'=>$params['township']]);
        return (int)($query->scalar());
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     * 按区域分组得出每个区域的报警总数
     */
    public function actionTotalGroupByDistrict(){
        $params = \Yii::$app->request->getQueryParams();
        $districtLevel = $params['district_level'];

        $query = DeviceException::find()->select([$params['district_level'],'COUNT(*) as total'])->leftJoin('device','device_exception.imei = device.imei');
        $query->andWhere(['device_exception.type'=>1]);
        if(isset($params['date_from']) && isset($params['date_to']) && $params['date_from'] && $params['date_to']){
            $query->andWhere(['>=', 'device_exception.create_time', $params['date_from']]);
            $query->andWhere(['<', 'device_exception.create_time', $params['date_to']]);
        }
        if(isset($params['province'])  && $params['province'] !== '')
            $query->andWhere(['province'=>$params['province']]);
        if(isset($params['city'])  && $params['city'] !== '')
            $query->andWhere(['city'=>$params['city']]);
        if(isset($params['district'])  && $params['district'] !== '')
            $query->andWhere(['district'=>$params['district']]);
        if(isset($params['township'])  && $params['township'] !== '')
            $query->andWhere(['township'=>$params['township']]);
        return $query->asArray()->groupBy($districtLevel)->all();
    }

    public function actionTotalGroupByMonthOfYear(){
        $params = \Yii::$app->request->getQueryParams();
        $query = DeviceException::find()->select(['MONTH(device_exception.create_time) as month', 'COUNT(*) as total'])->leftJoin('device','device_exception.imei = device.imei');
        $query->andWhere(['device_exception.type'=>1]);
        $query->andWhere(['>=', 'device_exception.create_time', $params['year'].'-01-01']);
        $query->andWhere(['<', 'device_exception.create_time', ($params['year']+1).'-01-01']);
        if(isset($params['province'])  && $params['province'] !== '')
            $query->andWhere(['province'=>$params['province']]);
        if(isset($params['city'])  && $params['city'] !== '')
            $query->andWhere(['city'=>$params['city']]);
        if(isset($params['district'])  && $params['district'] !== '')
            $query->andWhere(['district'=>$params['district']]);
        if(isset($params['township'])  && $params['township'] !== '')
            $query->andWhere(['township'=>$params['township']]);
//        return $query->asArray()->groupBy('MONTH(device_exception.create_time)')->createCommand()->getRawSql();
        $res = $query->asArray()->groupBy('MONTH(device_exception.create_time)')->all();
        $arr = [];
        foreach ($res as $item){
            $arr[$item['month']] = $item['total'];
        }
        for($i=1; $i<=12;$i++){
            if(!isset($arr[$i])){
                $arr[$i] = 0;
            }
        }
        return $arr;
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionClearAlarm(){
        $params = \Yii::$app->request->getBodyParams();
        $imei = $params['imei'];

        // 把device_exception表的中处理状态全改为2
        $sql = 'UPDATE device_exception SET treatment_result = 2 WHERE imei = :imei AND treatment_result = 1';
        $res = Yii::$app->db->createCommand($sql, [':imei'=>$imei])->execute();

        // 把device表中的state改为0
        $sql = 'UPDATE device SET state = 0 WHERE imei = :imei';
        Yii::$app->db->createCommand($sql, [':imei'=>$imei])->execute();

        // 删除mongodb相关记录
        /** @var  $mongoDb \yii\mongodb\Connection*/
        $mongoDb = Yii::$app->mongodb;
        /** @var  $writeResult WriteResult*/
        $writeResult = $mongoDb->createCommand()->delete('exceptionReportTab', ['imei'=>$imei]);

        return [
            'bCode' => Status::SUCCESS,
            'bData' => 'Mysql中更新了'.$res.'条记录为已处理，并将设备状态更新为正常',
            'bMongoData' => 'MongoDb从exceptionReportTab中删除了'.$writeResult->getDeletedCount().'条记录'
        ];
    }


}
