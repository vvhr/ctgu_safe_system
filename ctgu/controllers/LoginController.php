<?php

namespace app\controllers;

use app\config\Status;
use app\models\User;
use app\models\WxUser;
use wx_tools\WxTools;
use Yii;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;

class LoginController extends Controller
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

    /**
     * @return array
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionGetToken(){
        $params = Yii::$app->request->getBodyParams();
        $userName = trim($params['username']);
        $password = trim($params['password']);
        if(empty($userName) || empty($password)){
            return [
                'code'=>Status::FAIL,
                'data'=>null,
                'msg'=>'帐户或密码不能为空'
            ];
        }

        $model = User::find()->where(['username'=>$userName])->orWhere(['phone'=>$userName])->limit(1)->asArray()->one();
        if($model){
            // 超过登录限制次数
            $timePassOut = time() - (int)$model['try_login_at'];
            if ((int)$model['try_login_count'] >= 5 && $timePassOut<86400) {
                return [
                    'code'=>Status::FAIL,
                    'data'=>null,
                    'msg'=>'您已超过最大登录次数，请24小时后重试'
                ];
            }

            $hash = $model['password'];
            if(Yii::$app->getSecurity()->validatePassword($password, $hash)){
                $user = User::findOne($model['id']);
                // 每次登录时,如果token过期，就产生一个明文token存入数据库。
                // 这样以来，就移除了单点登录的功能
                if(time() - $user->token_update_at > 3600){
                    $token = sha1(time().mt_rand(0,1000));
                    $user->token = $token;
                    $user->token_update_at = time();
                } else {
                    $token = $user->token;
                }
                $user->login_time = date('Y-m-d H:i:s');
                $user->try_login_count = 0;
                if($user->save()){
                    return [
                        'code'=>Status::SUCCESS,
                        'data'=>['token'=>$token],
                        'msg'=>'获取token成功'
                    ];
                }else{
                    return [
                        'code'=>Status::FAIL,
                        'data'=>null,
                        'msg'=>'存储token发生问题'
                    ];
                }

            }else{
                // 密码错误
                $user = User::findOne($model['id']);
                $this->tryPasswordCount($user);
                return [
                    'code'=>Status::FAIL,
                    'data'=>null,
                    'msg'=>'账号密码不正确'
                ];
            }
        }else{
            // 用户名不存在
            return [
                'code'=>Status::FAIL,
                'data'=>null,
                'msg'=>'账号密码不正确'
            ];
        }
    }

    /**
     * @param $user User
     */
    private function tryPasswordCount($user){
        $user->try_login_at = time();
        $user->save();
        $user->updateCounters(['try_login_count'=>1]);
    }

    /**
     * @param $user User
     */
    private function clearTryPasswordCount($user){
        $user->try_login_count = 0;
        $user->save();
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionGetInfo(){
        $params = \Yii::$app->request->getBodyParams();
        $model = User::findOne(['token'=>$params['token']]);
        if($model){
            $howMuchTimePast = time() - $model->token_update_at;
            if($howMuchTimePast > 3600){
                return [
                    'code'=>Status::TOKEN_EXPIRED,
                    'data'=>'token过期',
                    'msg'=>'Token已过期'
                ];
            }else{
                $groupInfo = $model->groupInfo;
                return [
                    'code'=>Status::SUCCESS,
                    'data'=>array_merge($model->toArray(), ['groupInfo'=>$groupInfo->toArray()]),
                    'msg'=>'获取用户信息成功'
                ];
            }
        }else{
            // token无效
            return [
                'code'=>Status::FAIL,
                'data'=>null,
                'msg'=>'获取用户信息失败'
            ];
        }

    }

    /** ---------------------------------------------微信登录功能接口！！！----------------------------------------- */
    /**
     * 利用code进行token获取并登录
     * 如果微信号未绑定user_id，则跳到微信绑定页(需要授权登录：wx.hjdlwl.com)进行手机绑定。
     * @return array
     */
    public function actionGetTokenByWxUserAuthCode(){
        $code = \Yii::$app->request->get('code');
        $wxUserInfo = $this->fetchWxAuthInfoByCode($code);
        // ojZ6f0f4n48Q2YNn4hNMi4UypLk8
        if($wxUserInfo['bCode'] === Status::SUCCESS){
            $openid = $wxUserInfo['bData']['openid'];
            $wxUser = WxUser::findOne(['openid'=>$openid]);
            if($wxUser){
                if($wxUser->user_id !== null){
                    $user = User::findOne($wxUser->user_id);
                    if($user){
                        if(time() - $user->token_update_at > 3600){
                            $token = sha1(time().mt_rand(0,1000));
                            $user->token = $token;
                            $user->token_update_at = time();
                        } else {
                            $token = $user->token;
                        }
                        $user->login_time = date('Y-m-d H:i:s');
                        if($user->save())
                            return [
                                'bCode'=>Status::SUCCESS,
                                'bData'=>$token
                            ];
                        else
                            return [
                                'bCode'=>Status::FAIL,
                                'bData'=>$user->errors
                            ];
                    }
                    else
                        return [
                            'bCode'=>Status::FAIL,
                            'bData'=>'用户ID'.$wxUser->user_id.'在用户表中已被删除，未查到此ID用户信息'
                        ];
                }else{
                    return [
                        'bCode'=>Status::WX_UNBIND_USER,
                        'bData'=>'用户尚未绑定微信'
                    ];
                }
            }
            // 如果微信用户查询为空，由在表中新增一个微信用户
            else{
                $model = new WxUser();
                $model->load($wxUserInfo['bData'], '');
                $model->subscribe_at = time();
                $model->save();
                return [
                    'bCode'=>Status::FAIL,
                    'bData'=>'用户尚未绑定微信'
                ];
            }
        }
        return $wxUserInfo;
    }

    private function fetchWxAuthInfoByCode($code){
        // 获取access_token
        $access_token_obj = WxTools::getAccessTokenByCode($code);
        // 拉取临时access_token，如果报错，报出错误信息并终止程序
        if(isset($access_token_obj['errcode']) && $access_token_obj['errcode']!==0)
            return [
                'bCode' => Status::FAIL,
                'bData' => $access_token_obj['errmsg']
            ];

        // 使用access_token拉取用户信息
        $wxUserInfo = WxTools::getUserInfoByAccessToken($access_token_obj['access_token'],$access_token_obj['openid']);
        if(isset($wxUserInfo['errcode']) && $access_token_obj['errcode']!==0)
            return [
                'bCode' => Status::FAIL,
                'bData' => $wxUserInfo['errmsg']
            ];

        return [
            'bCode' => Status::SUCCESS,
            'bData' => $wxUserInfo
        ];
    }
}
