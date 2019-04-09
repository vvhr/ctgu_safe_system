<?php

namespace app\models;

use yii\mongodb\ActiveRecord;

class ExceptionReportTab extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return 'exceptionReportTab';
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

            'type','alarmType',

            'reportTime'];
    }
}
