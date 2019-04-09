<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 2018/6/4
 * Time: 11:20
 */

namespace app\controllers\parent;

use app\components\ActionApiFilter;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;

class ParentController extends Controller
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        // 配置用户自定义输出序列化行为
        'collectionEnvelope' => '_items',
    ];

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
        $behaviors['authenticator'] = ['class' => HttpBearerAuth::class];
        $behaviors['apiFilter'] = ['class' => ActionApiFilter::class];
        return $behaviors;
    }
}