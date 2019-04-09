<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 2018-11-6
 * Time: 17:28
 */

namespace app\components;

use app\config\Status;
use app\models\Api;
use app\models\UserGroupApi;
use Yii;
use yii\base\ActionFilter;

class ActionApiFilter extends ActionFilter
{
    private $error = '';
    public function beforeAction($action)
    {
        $user_group_id =\Yii::$app->user->identity->user_group_id;
        $api = $action->getUniqueId();
        if($user_group_id!==1){
            if($this->filterActionPermission($api))
                return parent::beforeAction($action);
            else{
                Yii::$app->response->statusCode = 200;
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                $this->error.='用户无此接口权限:'.$api;
                Yii::$app->response->data = [
                    'bCode' => Status::FAIL,
                    'bData' => $this->error,
                    'method' => Yii::$app->request->getMethod(),
                    '_items' => [],
                    '_meta' => [
                        'totalCount'=>0,
                        'pageCount'=>0,
                        'currentPage'=>1,
                        'perPage'=>10,
                    ]
                ];
                return false;
            }
        }
        return parent::beforeAction($action);
    }

    /**
     * @param $api
     * @return bool
     */
    private function filterActionPermission($api){
        $user_group_id =\Yii::$app->user->identity->user_group_id;
        $res = '';
        $apiModel = Api::findOne(['api'=>$api]);
        if(!$apiModel){
            $res.= 'api'.$api.'尚未入库，请用导入功能入库';
            $bool = false;
        }else{
            $user_group_api = UserGroupApi::findOne(['api_id'=>$apiModel->id, 'group_id'=>$user_group_id]);
            if($user_group_api){
                $bool = true;
            }else{
                $bool = false;
            }
        }

        $this->error = $res;
        // 响应处理
        return $bool;
    }
}