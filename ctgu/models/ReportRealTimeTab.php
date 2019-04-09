<?php

namespace app\models;

use app\config\Status;
use Yii;
use yii\mongodb\ActiveRecord;
use yii\mongodb\Connection;

class ReportRealTimeTab extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return 'reportRealTimeTab';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return ['_id', 'imei',

            'voltage1',
            'electricity',
            'leakageCurrent',
            'temperatureA',

            'voltage2',
            'electricity2',
            'leakageCurrent2',
            'temperatureB',

            'voltage3',
            'electricity3',
            'leakageCurrent3',
            'temperatureC',

            'voltage4',
            'electricity4',
            'leakageCurrent4',
            'temperatureN',

            'reportTime'];
    }

    /**
     * LC LeakageCurrent
     * @param $date
     * @return array|\MongoDB\Driver\Cursor
     * @throws \yii\mongodb\Exception
     * 被以下方法依赖
     * @see \app\models\MinMaxLc::insertMinMaxLcByDate()
     */
    public static function getMaxMinLCOfDeviceByDate($date){
        /** @var  $mongoDb Connection*/
        $mongoDb = Yii::$app->mongodb;
        $collection = $mongoDb->getCollection('reportRealTimeTab');
        $res = $collection->aggregate([
            // 管道操作符
            [
                '$match' => [
                    'reportTime' => ['$gte' => $date.' 00:00:00', '$lte' => $date.' 23:59:59']
                ],
            ],
            [
                '$group' => [
                    '_id' => '$imei',
                    'max_lc1' => ['$max'=>'$leakageCurrent'],
                    'min_lc1' => ['$min'=>'$leakageCurrent'],
                    'max_lc2' => ['$max'=>'$leakageCurrent2'],
                    'min_lc2' => ['$min'=>'$leakageCurrent2'],
                    'max_lc3' => ['$max'=>'$leakageCurrent3'],
                    'min_lc3' => ['$min'=>'$leakageCurrent3'],
                    'max_lc4' => ['$max'=>'$leakageCurrent4'],
                    'min_lc4' => ['$min'=>'$leakageCurrent4'],
                    'max_tp1' => ['$max'=>'$temperatureA'],
                    'min_tp1' => ['$min'=>'$temperatureA'],
                    'max_tp2' => ['$max'=>'$temperatureB'],
                    'min_tp2' => ['$min'=>'$temperatureB'],
                    'max_tp3' => ['$max'=>'$temperatureC'],
                    'min_tp3' => ['$min'=>'$temperatureC'],
                    'max_tp4' => ['$max'=>'$temperatureN'],
                    'min_tp4' => ['$min'=>'$temperatureN'],
                ],
            ]
        ]);
        return $res;
    }
}
