<?php

namespace backend\controllers;

use backend\models\ManagerLog;
use backend\models\ProductOfflineRecord;
use backend\models\ScmStock;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * ProductShelvesController implements the CRUD actions for ScmStock model.
 */
class ProductOfflineController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'shelves-send', 'get-product'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('产品上下架管理')) {
            return $this->redirect(['site/login']);
        }
        $model = new ProductOfflineRecord();
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * 产品上下架 接口处理
     * @author  zmy
     * @version 2017-05-22
     * @return  [type]     [description]
     */
    public function actionShelvesSend()
    {
        if (!Yii::$app->user->can('产品上下架管理')) {
            return $this->redirect(['site/login']);
        }
        $param             = Yii::$app->request->post();
        $model             = new ProductOfflineRecord();
        $retProductOffline = '';
        if ($param) {
            $transaction = Yii::$app->db->beginTransaction();
            // 添加上下架记录
            $retSaveResult = ProductOfflineRecord::createProductOffline($param, yii::$app->user->identity->username);
            // 同步接口
            $retOfflineResult = ProductOfflineRecord::getRetProductOffline($param);

            if (!$retOfflineResult) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '产品上下架同步失败');
                return $this->render('index');
            }
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "产品上下架管理", \backend\models\ManagerLog::CREATE, "添加上下架记录");
            $transaction->commit();
            return $this->redirect(['product-offline-record/index']);
        }
        Yii::$app->getSession()->setFlash('error', '参数有误，产品上下架同步失败');
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * ajax通过buildId 查询出接口，进行组合返回数据
     * @author  zmy
     * @version 2017-05-17
     * @return  [type]     [description]
     */
    public function actionGetProduct()
    {
        $buildId = Yii::$app->request->post("buildId", 0);
        $type    = Yii::$app->request->post("type", 0);
        $input   = ProductOfflineRecord::getProductInput($buildId, $type);
        echo json_encode($input, JSON_UNESCAPED_UNICODE);
    }

}
