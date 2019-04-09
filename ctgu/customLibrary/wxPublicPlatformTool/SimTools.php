<?php
/**
 *
 */

namespace wx_tools;
use phpDocumentor\Reflection\Types\Self_;
use Yii;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


class SimTools
{
    const GATEWAY = 'http://m2mlianzhong.com/api';
    const KEY = 'C9AA6528A58F0E542B0EC33755B11312';

    public static function balance(){
        $apiUrl = self::GATEWAY.'/balance';
        $data = [
            'key'=>self::KEY,
            'sign'=>md5('key='.self::KEY)
        ];
        $res = CurlTools::httpPost($apiUrl,$data);
        return json_decode($res, true);
    }

    /**
     * @param $number // 电话号码 | iccid
     * @return mixed
     */
    public static function query($number){
        $apiUrl = self::GATEWAY.'/query';
        $data = [
            'key'=>self::KEY,
            'number'=>$number,
            'sign'=>md5('number='.$number.'&key='.self::KEY)
        ];
        $res = CurlTools::httpPost($apiUrl,$data);
        return json_decode($res, true);
    }



}