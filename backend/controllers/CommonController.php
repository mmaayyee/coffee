<?php

namespace backend\controllers;

use backend\models\ManagerLog;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;

/**
 * 公共类
 */
class CommonController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * 保存操作日志
     * @author zhenggangwei
     * @date   2020-04-18
     * @return integer
     */
    public function actionSaveOperateLog()
    {
        $data = Yii::$app->request->post();
        $res  = ManagerLog::saveLog(Yii::$app->user->id, $data['moduleName'], $data['operateType'], $data['operateContent']);
        return $res ? 1 : 0;
    }

    /**
     * 验证用户操作权限
     * @author zhenggangwei
     * @date   2020-04-18
     * @return array
     */
    public function actionVerifyUserAuth()
    {
        $authName = Yii::$app->request->post();
        $resList  = [];
        foreach ($authName as $key => $auth) {
            $res           = Yii::$app->user->can($auth);
            $resList[$key] = $res ? 1 : 0;
        }
        $resList['fcoffeeUrl'] = Yii::$app->params['fcoffeeUrl'];
        return Json::encode($resList);
    }
}
