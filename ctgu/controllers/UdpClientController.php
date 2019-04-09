<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 2019-3-7
 * Time: 18:13
 */

namespace app\controllers;


use yii\redis\Connection;
use yii\rest\Controller;

class UdpClientController extends Controller
{
    private $protocolHeads = [
            //ccid表示命令类型，第一个ffff0014表示起始符与报文长度，第二个6706表示报文类型与设备类型
            'ccid' => ['ffff0014', '5106']
        ];
    private $ipAndPort = '139.159.254.9:7786';
    public function actionSendCommand(){
        $imei = '01110222810313329cf2f88b';
        return $this->sendCommand('ccid',$imei);
    }

    /*-----------------------------工具类方法---------------------------------*/
    private function createClientAndSendMsg($hexString, $ipAndPort){
        $loop = \React\EventLoop\Factory::create();
        $factory = new \React\Datagram\Factory($loop);
        $factory->createClient($ipAndPort)
                ->then(
                    // 创建客户端成功后的回调方法
                    function (\React\Datagram\Socket $client) use ($loop, $hexString)
                    {
                        $message = hex2bin($hexString);
                        $client->send($message);
                        // 结束客户端
                         $client->end();
                    },
                    // 服务器创建错误时的回调方法
                    function($error) {
                        echo '创建服务器错误: ERROR: ' . $error->getMessage() . PHP_EOL;
                    }
                );
        $loop->run();
    }

    private function sendCommand($protocolHeadsKeyName,$imei){
        /** @var  $redis Connection */
        // 待加密字串
        $strHex = $this->protocolHeads[$protocolHeadsKeyName][1].$imei;
        // 加密整数
        $crcInt = $this->crc16($strHex);
        // 加密16进制字串
        $crcHex = dechex($crcInt);
        // 加密拆分数组
        $crcArr = str_split($crcHex, 2);
        // 加密拆分数组反转
        $crcArr = array_reverse($crcArr);
        // 加密16进制字串，小端模式
        $crcHex = implode($crcArr);
        // dataHex最终命令字串
        $dataHex = $this->protocolHeads[$protocolHeadsKeyName][0].$strHex.$crcHex;
        $dataHex = strtolower($dataHex);
        $this->createClientAndSendMsg($dataHex,$this->ipAndPort);
        return [$dataHex, $this->ipAndPort];
    }

    private function crc16($str)
    {
        // Pack data into binary string
        // 将字符串打包成字节
        $data = pack('H*', $str);
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($data); $i++) {
            $crc ^= ord($data[$i]);
            for ($j = 8; $j != 0; $j--) {
                if (($crc & 0x0001) != 0) {
                    $crc >>= 1;
                    $crc ^= 0xA001;
                } else $crc >>= 1;
            }
        }
        return $crc;
    }
}