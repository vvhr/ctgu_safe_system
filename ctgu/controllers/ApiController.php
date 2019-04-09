<?php

namespace app\controllers;

use app\config\Status;
use app\models\Api;
use app\models\UserGroupApi;
use Yii;
use app\controllers\parent\ParentController;
use yii\data\ActiveDataProvider;
use yii\db\StaleObjectException;
use yii\helpers\Inflector;

class ApiController extends ParentController
{
    public function actionIndex(){
        $params = \Yii::$app->request->getQueryParams();
        $query = Api::find();

        $pageSize = 1000;
        if(isset($params['pageSize'])){
            $pageSize = (int)$params['pageSize'];
        }
        return new ActiveDataProvider([
            // 使用with方法实现贪加载
            'query' => $query,
            // 分页信息
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            // 排序信息
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ]
            ],
        ]);
    }
    public function actionBulkAddApi(){
        $apiArr = $this->getAllApiFromDir();
        $res = [
            'delete_success'=>[],
            'delete_fail'=>[],
            'create_success'=>[],
            'create_fail'=>[]
        ];
        // 数据库中存在，而实际接口不存在时，从数据库中删除他
        $models = Api::find()->where(['NOT IN', 'api', $apiArr])->all();
        foreach ($models as $model){
            // 删除用户组权限分配表中的含有权限ID的记录
            $userGroupApis = UserGroupApi::find()->where(['api_id'=>$model->id])->all();
            foreach ($userGroupApis as $userGroupApi){
                try {
                    $userGroupApi->delete();
                    array_push($res['delete_success'], '用户权限表中绑定记录的ID'.$userGroupApi->id);
                } catch (StaleObjectException $e) {
                    array_push($res['delete_fail'], '用户权限表中绑定记录的ID'.$userGroupApi->id);
                } catch (\Throwable $e) {
                }
            }
            // 删除表中多余的接口
            try {
                $model->delete();
                array_push($res['delete_success'], $model->api);
            } catch (StaleObjectException $e) {
                array_push($res['delete_fail'], $model->api);
            } catch (\Throwable $e) {
            }
        }
        // 新增接口
        foreach ($apiArr as $api){
            if(Api::findOne(['api'=>$api])===null){
                $model = new Api();
                $model->api = $api;
                $model->update_at = date('Y-m-d H:i:s');
                if($model->save()){
                    array_push($res['create_success'], $api);
                }else{
                    array_push($res['create_fail'], $model->errors);
                }
            }
        }
        return [
            'bCode' => Status::SUCCESS,
            'bData' => $res
        ];
    }
    private function getAllApiFromDir(){
        $baseDir = Yii::$app->basePath;
        $controllerDir = $baseDir.'/controllers/';
        $dirHandle = opendir($controllerDir);
        $apiArr = [];
        if($dirHandle){
            // 遍历控制器目录下的所有控制器类文件
            while(($file=readdir($dirHandle))!==false) {
                $fileRealPath = realpath($controllerDir.$file);
//                var_dump($fileRealPath);
                if($file==='.' || $file==='..' || is_dir($fileRealPath)){
                    continue;
                }
//                array_push($controllers, $file);
                $controllerIDStr = (substr($file, 0 , -14));
                $controllerIDStr = Inflector::camel2id($controllerIDStr);

                // 读取单个控制器文件
                $fileHandle = fopen($controllerDir. $file, "r");
                if ($fileHandle) {
                    // 遍历每一行
                    while (($line = fgets($fileHandle)) !== false) {
                        // 如果不是继承自parentController。则无需加入权限分配
                        if (preg_match('/^class.*extends(.*)$/', $line, $display)){
                            if(trim($display[1]) !== 'ParentController'){
                                break;
                            }
                        }
                        // 正则匹配每一行内容，符合规则/public function action(.*?)\(/，并抓取匹配到的内容
                        if (preg_match('/public function action(.*?)\(/', $line, $display)):
                            // 获取内容必须大于2，防止actions方法被抓取
                            if (strlen($display[1]) > 2):
                                $actionIdStr = Inflector::camel2id($display[1]);
                                array_push($apiArr, $controllerIDStr.'/'.$actionIdStr);
                            endif;
                        endif;
                    }
                }
            }
        }
        return $apiArr;
    }
}
