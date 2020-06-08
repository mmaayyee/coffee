<?php

namespace backend\controllers;

use common\helpers\WXApi\EnDeCrypt;
use yii\web\Controller;

class WechatController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * 验证回调模式url
     */
    public function actionIndex()
    {
        EnDeCrypt::verify_url();
    }
}
