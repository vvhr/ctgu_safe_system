<?php

namespace app\models;


use yii\mongodb\Query;

class MongoDBUtils
{
    public static function findDeviceReportNews($uuid, $startTime,$endTime){
        $query = new Query();
        $query->select(['uuid','v','c','lc','t','h1','h3','h5','h7','h9','a1','a5','a3','a7','a9','p','np','rate','eType','eDetailType','eComment','eHexL','eHexH','aSignHex','reportTime'])
            ->from('DeviceReportNewCollection_'.$uuid)
             ->where(['between','reportTime',$startTime,$endTime])
            ->limit(50000);
        $rows = $query->all();
        return $rows;
    }
}
