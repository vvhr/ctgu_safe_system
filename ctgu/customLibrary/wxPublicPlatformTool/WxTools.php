<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 2018/7/9
 * Time: 12:50
 */

namespace wx_tools;
use Yii;

class WxTools
{
    // 在微信平台所配置的token, 用于验证是否来自于微信端的请求时使用
    const TOKEN = 'hjdlwl2018';
    // APP_ID 与 APP_SECRET主要用来请求access_token，而access_token主要用来调用微信接口
    const APP_ID = 'wx0c97d8929155e74a';
    const APP_SECRET = '2376d8f7fc657d69f243738933ac4a17';
    // access_token保存目录：
    private $accessTokenDir;
    // 模版消息的模板ID
    const NOTIFY_TEMPLATE_ID_01 = 'hT7VVNpY95pMOAMWaCMfJ7t7Kxu6Q9TnrpIlOu8j8Ug';
    /**  该ID所对应的模板为：
     * {{first.DATA}}
    报警时间：{{keyword1.DATA}}
    报警位置：{{keyword2.DATA}}
    报警内容：{{keyword3.DATA}}
    {{remark.DATA}}
     * */
    public function __construct()
    {
        $this->accessTokenDir = Yii::$app->getBasePath().'/runtime/wx_access_token';
    }

    /**--------------------处理微信方请求时要用到的工具----------------------------------------*/
    /**
     * 验证请求是否来自于微信服务器 ($params 表示服信服务器的请求查询参数)
     * @param $params
     * @return bool
     */
    public function checkSignature($params)
    {
        if(!isset($params['timestamp']) || !isset($params['nonce']) || !isset($params['signature']))
            return false;
        // 元素组成数组
        $tmpArr = array(self::TOKEN, $params["timestamp"], $params["nonce"]);
        // 字典排序：SORT_STRING表示按字典序排序
        sort($tmpArr, SORT_STRING);
        // 数组拼接成字符串
        $tmpStr = implode( $tmpArr );
        // 字符串加密变成signature
        $signature = sha1( $tmpStr );

        if( $signature === $params["signature"] ){
            return true;
        }else{
            return false;
        }
    }

    /**--------------------调用微信各个接口工具--------------------------------------------------*/
    // 发送模版消息：
    /**  该ID所对应的模板为：
     * {{first.DATA}}
    报警时间：{{keyword1.DATA}}
    报警位置：{{keyword2.DATA}}
    报警内容：{{keyword3.DATA}}
    {{remark.DATA}}
     * */
    /**
     * 请求发送模版消息接口
     * @param $toUserName
     * @param $time
     * @param $address
     * @param $alarmContent
     * @return mixed  返回微信端发来的JSON（成功或失败）
     */
    public function sendTemplate01Message($toUserName,$time,$address,$alarmContent) {
        $res = $this->getAccessToken();
        if($res['errcode'] !== 0) {
            return $res;
        }
        $wxApiUrl = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$res['access_token'];
        $data = [
            'touser'=>$toUserName,
            'template_id'=>self::NOTIFY_TEMPLATE_ID_01,
            'url'=>'',
            'topcolor'=>'#000000',
            'data'=>[
                'first'=>[
                    'value'=>'华建电气安全监控报警:',
                    'color'=>'#000000'
                ],
                'keyword1'=>[
                    'value'=>$time,
                    'color'=>'#888888'
                ],
                'keyword2'=>[
                    'value'=>$address,
                    'color'=>'#888888'
                ],
                'keyword3'=>[
                    'value'=>$alarmContent,
                    'color'=>'#888888'
                ],
                'remark'=>[
                    'value'=>'请尽快安排技术人员在2个小时内进行处理。',
                    'color'=>'#888888'
                ],
            ],
        ];
        return json_decode(CurlTools::httpPost($wxApiUrl,json_encode($data)), true);
    }

    /**
     * 由用户授权的code拉取用户access_token
     * @param $code
     * @return mixed
     */
    public static function getAccessTokenByCode($code){
        $apiUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token'
            .'?appid='.self::APP_ID
            .'&secret='.self::APP_SECRET
            .'&code='.$code.'&grant_type=authorization_code';
        return json_decode(CurlTools::httpGet($apiUrl), true);
    }

    public static function getUserInfoByAccessToken($accessToken,$openId){
        $apiUrl = 'https://api.weixin.qq.com/sns/userinfo'
            .'?access_token='.$accessToken
            .'&openid='.$openId
            .'&lang=zh_CN';
        return json_decode(CurlTools::httpGet($apiUrl), true);
    }

    /**
     * 请求获取所有微信用户openid接口
     * @return array|bool|mixed
     */
    public function getWxUsers() {
        $res = $this->getAccessToken();
        if($res['errcode'] !== 0) {
            return $res;
        }
        $wxApiUrl = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$res['access_token'];
        return json_decode(CurlTools::httpGet($wxApiUrl), true);
    }

    /**--------------------调用微信接口时要用到的工具----------------------------------------*/
    /**
     * 获取accessToken值
     * @return array|bool|mixed 错误代码与消息
     */
    public function getAccessToken() {
        $access_token_file = $this->accessTokenDir."/access_token.php";
        //自动建立存放accessToken的目录
        if(!is_dir($this->accessTokenDir))
            @mkdir($this->accessTokenDir, 0777);
        //获取accessToken
        $access_token_data = array();
        if(is_file($access_token_file)) //如果access_token.php文件存在，则读取accessToken数据
        {
            $content = trim(substr(file_get_contents($access_token_file), 15));
            $access_token_data = json_decode($content,true);
        }
        //如果读取到有效accessToken数据，则直接返回accessToken
        if($access_token_data && $access_token_data['expire_time'] > time()) {
            // var_dump('从文件获取');
            return [
                'errcode'=>0,
                'msg'=>'获取文件中保存的access_token成功',
                'access_token' =>$access_token_data['access_token']
            ];
        } else { //没有读取到有效数据则重写则accessToken文件
            return $this->getAndWriteFileAccessToken();
        }
    }
    /**
     * 请求微信平台获取accessToken接口并返回accessToken,并写入文件
     * @return bool|mixed
     */
    private function getAndWriteFileAccessToken()
    {
        // 如果是企业号用以下URL获取access_token
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.self::APP_ID.'&secret='.self::APP_SECRET;
        $res = json_decode(CurlTools::httpGet($url),false);
        if(isset($res->errcode) && $res->errcode !== 0) {
            return [
                'errcode'=>$res->errcode,
                'msg'=>'调用微信的【获取access_token接口】时错误',
                'errmsg' =>$res->errmsg
            ];
        } else {
            $access_token = $res->access_token;
            $data['expire_time'] = time() + 7000;
            $data['access_token'] = $access_token;
            file_put_contents($this->accessTokenDir."/access_token.php", "<?php exit();?> ".json_encode($data));
            // var_dump('微信端接口');
            return [
                'errcode'=>0,
                'msg'=>'调用微信的【获取access_token接口】成功',
                'access_token' =>$access_token
            ];
        }
    }

    /**-----------------------------------静态格式转换等工具类--------------------------------------------------*/
    /**
     * 生成明文xml文本消息 字符串可直接echo，作为微信端请求的响应数据（该方法返回明文消息字符串，需要作为encryptMsg()方法的参数之一再加密成待发送消息）
     * @param $toUser
     * @param $fromUser
     * @param $content
     * @return string
     */
    public static function xmlMessage($fromUser, $toUser, $content)
    {
        $xmlFormat = '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[%s]]></Content></xml>';
        return sprintf($xmlFormat, $toUser, $fromUser, time(), $content);
    }
}