<?php

namespace app\models;

use yii\mongodb\ActiveRecord;

class ReportRealTimeTab2 extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return 'reportRealTimeTab2';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return ['_id', 'imei',
            'voltage',
            'electricity',
            'leakageCurrent',
            'temperature',
            'channel',
            'uuid',
            'markMalfunctionsL',
            'markMalfunctionsH',
            'warningSigns',
            'reportTime'];
    }
}
