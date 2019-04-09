<?php

namespace app\controllers;

use app\controllers\parent\ParentController;
use app\customLibrary\ReturnTool;
use app\models\DeviceExceptionNew;
use app\models\SmsLog;
use app\models\WxUser;
use wx_tools\WxTools;
use app\config\Status;

class WxUserController extends ParentController
{
    // 由code接取微信用户资料并更新数据库
    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAddOrUpdateByCode(){
        $params = \Yii::$app->request->getBodyParams();
        $res = null;
        if(isset($params['code'])){
            //第一步：由code得到用户微信资料
            /** 1-由code换特定access_token*/
            $access_token_obj = WxTools::getAccessTokenByCode($params['code']);
            if(isset($access_token_obj['errcode']) && $access_token_obj['errcode']!==0){
                return ReturnTool::returnPostMsg(Status::FAIL, $access_token_obj['errmsg'], '由code获取access_token失败');
            }
            /** 2-使用access_token拉取用户信息*/
            $wxUserInfo = WxTools::getUserInfoByAccessToken($access_token_obj['access_token'],$access_token_obj['openid']);
            if(isset($wxUserInfo['errcode'])){
                return ReturnTool::returnPostMsg(Status::FAIL, $wxUserInfo['errmsg'], '由code获取access_token失败');
            }
            /** 3-查询open_id是否存在于微信用户数据库中 */
            $wxUser = WxUser::findOne(['openid'=>$wxUserInfo['openid']]);
            /** 4-如果微信用户已存在，就更新微信用户资料，同时将微信号绑定到当前登录用户 */
            if($wxUser){
                $wxUser->load($wxUserInfo,'');
                $wxUser->user_id = \Yii::$app->user->id;
                if($wxUser->save()){
                    return ReturnTool::returnPostMsg(Status::SUCCESS, $wxUser, '更新微信资料成功');
                }else{
                    return ReturnTool::returnPostMsg(Status::FAIL, $wxUser->errors, '更新微信资料失败');
                }
            /** 5-如果微信用户不存在，就插入微信用户 */
            }else{
                $wxUser = new WxUser();
                $wxUser->load($wxUserInfo,'');
                $wxUser->subscribe_at = time();
                $wxUser->user_id = \Yii::$app->user->id;
                if($wxUser->save()){
                    return ReturnTool::returnPostMsg(Status::SUCCESS, $wxUser, '增加微信用户成功');

                }else{
                    return ReturnTool::returnPostMsg(Status::FAIL, $wxUser->errors, '增加微信用户失败');
                }
            }
        }else{
            return ReturnTool::returnPostMsg(Status::FAIL, null, 'code查询参数缺失');
        }
    }

    /**
     * 发送异常报警推送信息
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSendAlarmMessage(){
        $params = \Yii::$app->request->getBodyParams();
        $device_exception_new_id = $params['device_exception_new_id'];
        $alarmInfo = DeviceExceptionNew::findOne($device_exception_new_id);
        if($alarmInfo){
            $device = $alarmInfo->device;
            // 检测是否关联微信用户
            $wxUsers = $device->wxUsers;
            if($wxUsers){
                $resData = [];
                $wxTools = new WxTools();
                /** @var WxUser $wxUser */
                foreach ($wxUsers as $wxUser){
                    if($wxUser->enable_receive_msg === 1){
                        $homeUser = $device->homeUser;
                        $addressForMsg = $device->district.'/'.$device->township.'/'.$device->street.'/'.$device->address.'/ 住户信息：'.$homeUser->unit.$homeUser->house_number.$homeUser->contact.$homeUser->phone;
                        $alarmContentForMsg = '设备号:'.$alarmInfo->uuid.' 漏电流'.$alarmInfo->leakage_current.'mA 温度'.($alarmInfo->temperature/10).'C 电流'.($alarmInfo->electricity/1000).'A 电压'.($alarmInfo->voltage/100).'V';
                        $timeForMsg = $alarmInfo->create_time;
                        $res = $wxTools->sendTemplate01Message($wxUser->openid, $timeForMsg,$addressForMsg, $alarmContentForMsg);
                        // 把消息存起来
                        $smsLog = new SmsLog();
                        $smsLog->receiver = $wxUser->nickname;
                        $smsLog->message = '报警地址：'.$addressForMsg.'报警参数'.$alarmContentForMsg.'报警时间'.$timeForMsg;
                        $smsLog->send_time = date('Y-m-d H:i:s');
                        $smsLog->type=3;
                        $smsLog->device_exception_new_id = $alarmInfo->id;
                        $smsLog->imei = $alarmInfo->uuid;
                        $smsLog->homeId = $homeUser->id;
                        $smsLog->sender_user_id = \Yii::$app->user->id;
                        $smsLog->wx_user_id = $wxUser->id;
                        if($res['errcode'] === 0)
                            $smsLog->send_state = 1;
                        else
                            $smsLog->send_state = 2;
                        $smsLog->save();
                        array_push($resData, [$res, $alarmInfo, $smsLog->errors]);
                    }
                }
                return ReturnTool::returnPostMsg(Status::SUCCESS, $resData, '发送完毕，详细请看console.log控制台信息');
            }else{
                return ReturnTool::returnPostMsg(Status::FAIL, null, '该设备关联的用户未绑定任何微信账户');
            }
        }else{
            return ReturnTool::returnPostMsg(Status::FAIL, null, $device_exception_new_id.'对应的异常报警记录不存在');
        }
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionEnableOrNotReceiveMsg(){
        $params = \Yii::$app->request->getBodyParams();
        $id = (int)$params['id'];
        $wxUser = WxUser::findOne($id);
        if($wxUser){
            $wxUser->enable_receive_msg === 1?$wxUser->enable_receive_msg=0:$wxUser->enable_receive_msg =1;
            if($wxUser->save()){
                return ReturnTool::returnPostMsg(Status::SUCCESS, $wxUser, '更改消息接收状态成功');
            }else{
                return ReturnTool::returnPostMsg(Status::FAIL, $wxUser->errors, '更改消息接收状态失败');
            }
        }else{
            return ReturnTool::returnPostMsg(Status::FAIL, null, $id.'对应的微信用户不存在');
        }
    }

}
