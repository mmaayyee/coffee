<?php
namespace frontend\controllers;

use common\helpers\WXApi\Menu;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public function actionIndex()
    {
        echo "访问页面不存在";
    }

    public function actionCreateMenu()
    {
        $menuModel = new Menu();
        $res       = $menuModel->add();
        var_dump($res);
        $res = $menuModel->addEquip();
        var_dump($res);
        $res = $menuModel->addSupplier();
        var_dump($res);
        $res = $menuModel->addWater();
        var_dump($res);
        $res = $menuModel->addSelfHelper();
        var_dump($res);
        $res = $menuModel->addAgent();
        var_dump($res);
        $res = $menuModel->addDelivery();
        var_dump($res);
        $res = $menuModel->addPoint();
        var_dump($res);
    }

    public function actionError($message = '页面不存在')
    {
        return $this->render('error', ['message' => $message]);
    }

}
