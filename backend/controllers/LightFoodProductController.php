<?php

namespace backend\controllers;

use backend\models\ManagerLog;
use common\models\LightFoodProduct;
use Yii;
use yii\web\Controller;

/**
 * CoffeeProductController implements the CRUD actions for CoffeeProduct model.
 */
class LightFoodProductController extends Controller
{

    /**
     * Lists all CoffeeProduct models.
     * @return mixed
     */
    public function actionChangeProductStatus()
    {
        if (!Yii::$app->user->can('轻食产品上下架')) {
            return $this->redirect(['site/login']);
        }
        $data = Yii::$app->request->post();
        if ($data) {
            if (!empty($data['productIdList'])) {
                $res = LightFoodProduct::changeProductStutus($data);
                if ($res) {
                    Yii::$app->getSession()->setFlash('success', '操作成功');
                } else {
                    Yii::$app->getSession()->setFlash('success', '操作成功');
                }
                ManagerLog::saveLog(Yii::$app->user->id, "轻食产品上下架", ManagerLog::UPDATE, $data['productStatus'] == 0 ? '上架' : '下架');
            } else {
                Yii::$app->getSession()->setFlash('error', '请选择产品');
            }
        }
        $productList = LightFoodProduct::getStatusProductList();
        return $this->render('change-product-status', ['productList' => $productList]);
    }
}
