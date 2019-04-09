<?php
/**
 * 注意：本类库依赖 guzzle HTTP请求工具： composer require guzzlehttp/guzzle:~6.0
 * Created by PhpStorm.
 * User: tony
 * Date: 2018/7/9
 * Time: 12:50
 */

namespace wx_tools;
use Yii;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


class SmsTools
{
    const BASE_URL = 'https://117.78.29.66:10443';
    const APP_KEY ='Q74S7MDhfrjK6RX27E38GVs08mez';
    const APP_SECRET ='YQ6qg8GtaC74ML3SnhV1rrY0NIJj';
    // TEMPLATE_ID_OF_REGISTER_CODE模板内容 : 尊敬的${NUM_11}的用户，您的验证码为${NUM_6}，请在10分钟之内完成验证。 所属签名 : 华建云
    const TEMPLATE_ID_OF_REGISTER_CODE = '14406cb9a2a541de9fef01ea8f131f8f';
    // 验证码类通道号：在签名管理中查看，注意：模板号代表的模板必须属于通道号所代码的短信息推送类型。如：上面是验证码推送模版，是属于下面的验证码类通道
    const CHANEL_OF_VERIFY_CODE = 'csms18062704';

    /**
     * @param $tel
     * @param $verifyCode
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function sendVerifyCode($tel,$verifyCode){
        $apiUrl = self::BASE_URL.'/sms/batchSendSms/v1';
        // 短信模板变量中将替换的内容
        $TEMPLATE_PARAS = '["'.$tel.'","'.$verifyCode.'"]';
        // 接收信息的手机号码
        // 状态报告接收地址，为空或者不填表示不接收状态报告
        $client = new Client();
        $res = '';
        try {
            $response = $client->request('POST', $apiUrl, [
                'form_params' => [
                    'from' => self::CHANEL_OF_VERIFY_CODE,
                    'to' => $tel,
                    'templateId' => self::TEMPLATE_ID_OF_REGISTER_CODE,
                    'templateParas' => $TEMPLATE_PARAS,
                    'statusCallback' => ''
                ],
                'headers' => [
                    'Authorization' => 'WSSE realm="SDP",profile="UsernameToken",type="Appkey"',
                    'X-WSSE' => self::buildWsseHeader(self::APP_KEY, self::APP_SECRET)
                ],
                'verify' => false
            ]);

            $res =  Psr7\str($response);
        } catch (RequestException $e) {
            $res = $e;
            $res = Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                $res =  Psr7\str($e->getResponse());
            }
        }
        if(strpos($res,'"status":"000000"')){
            return [
                'errcode'=>0,
                'msg'=>$res
            ];
        }
        return [
            'errcode'=>404,
            'errmsg'=>$res
        ];
    }
    public static function buildWsseHeader($appKey, $appSecret){
        $now = date('Y-m-d\TH:i:s\Z');
        $nonce = uniqid();
        $base64 = base64_encode(hash('sha256', ($nonce . $now . $appSecret)));
        return sprintf("UsernameToken Username=\"%s\",PasswordDigest=\"%s\",Nonce=\"%s\",Created=\"%s\"",
            $appKey, $base64, $nonce, $now);
    }
}