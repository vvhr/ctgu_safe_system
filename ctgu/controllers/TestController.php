<?php

namespace app\controllers;
use app\models\Device;
use app\models\MongoDBUtils;
use wx_tools\WxTools;
use Yii;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;

class TestController extends Controller
{
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(
            [
                'corsFilter'=>[
                    'class' => Cors::class,
                    'cors'=>[
                        // restrict access to
                        'Origin' => ALLOW_ORIGIN,
                        'Access-Control-Request-Headers' => ['*'],
                    ]
                ]
            ],
            parent::behaviors()
        );
        return $behaviors;
    }

    public function actionWxAuthHref(){
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize'
            .'?appid='.WxTools::APP_ID
            .'&redirect_uri='.urldecode('http://safe-mobile.hjdlwl.com/#/setting/wxUser')
            .'&response_type=code&scope=snsapi_userinfo'
            .'&state=myState#wechat_redirect';
        var_dump($url);
        return $url;
    }

    /*public function actionSendTemplateMessage(){
        $wxTools = new WxTools();
        $res = $wxTools->sendTemplate01Message('ojZ6f0f4n48Q2YNn4hNMi4UypLk8', time(),'广东深圳','报警');
        if($res['errcode'] !== 0)
            return ReturnTool::returnPostMsg(Status::FAIL, $res, '发送失败');
        else
            return ReturnTool::returnPostMsg(Status::SUCCESS, $res, '发送成功');
    }*/

    public function actionTestRedis(){
        $redis = \Yii::$app->redis;
        $redis->set('name','tony');
        return $redis->get('name');
    }
//    public function actionMongo(){
//        return MongoDBUtils::findDeviceReportNews('200025000357345633313720_1','2019-03-28 00:00:00',"2019-03-29 12:00:00");
//    }

//    /**
//     * 启用或禁用设备，如果禁用，java端将不再存储分析该设备数据
//     * @throws \yii\base\InvalidConfigException
//     */
//    public function actionSwitchDevice(){
//        /** @var  $redis yii\redis\Connection*/
//        $redis = Yii::$app->redis;
//        $params = \Yii::$app->request->getBodyParams();
//        $uuid = $params['uuid'];
//        $enable = $params['enable'];
//        $redis->set($uuid.'_enable',(int)$enable);
//        $device = Device::findOne($uuid);
//        if($device){
//            $device->enable = $redis->get($uuid.'_enable');
//            $device->save();
//        }
//    }
}
