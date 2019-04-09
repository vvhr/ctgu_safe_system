<?php

namespace app\models;

use yii\mongodb\ActiveRecord;

/**
 * @property int project_id
 * @property string imei
 */
class ProjectDevice extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return 'projectDevice';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return ['_id', 'project_id', 'imei'];
    }
}
