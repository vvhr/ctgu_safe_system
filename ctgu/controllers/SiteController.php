<?php

namespace app\controllers;

use yii\rest\Controller;

class SiteController extends Controller
{
    public function actionError(){
        return ['你所访问的资源不存在'];
    }
}
