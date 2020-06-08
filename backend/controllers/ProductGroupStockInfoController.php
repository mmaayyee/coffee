<?php

namespace backend\controllers;

use backend\models\ManagerLog;
use backend\models\ProductGroupStockInfo;
use backend\models\ScmEquipTypeMatstockAssoc;
use backend\models\ScmMaterialStock;
use common\models\Api;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ProductGroupStockInfoController implements the CRUD actions for ProductGroupStockInfo model.
 */
class ProductGroupStockInfoController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'view', 'delete', 'get-equip-type-stock-info'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all ProductGroupStockInfo models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('产品组料仓信息管理')) {
            return $this->redirect(['site/login']);
        }
        $param    = Yii::$app->request->queryParams;
        $model    = new ProductGroupStockInfo();
        $page     = Yii::$app->request->get('page', 1);
        $pageSize = 20;
        $data     = [];
        if (isset($param['ProductGroupStockInfo']) && $param['ProductGroupStockInfo']) {
            $data["product_group_stock_name"] = $model->product_group_stock_name = $param['ProductGroupStockInfo']["product_group_stock_name"];
            $model->equip_type_id             = $data["equip_type_id"]             = $param['ProductGroupStockInfo']['equip_type_id'];
        }
        $proGroupStockList = json_decode(Api::getProGroupStockInfo($data, $pageSize, $page), true);

        $pages = new Pagination(['totalCount' => $proGroupStockList['totalCount'], 'pageSize' => $pageSize]);
        return $this->render('index', [
            'model'             => $model,
            'page'              => $page,
            'pageSize'          => $pageSize,
            'pages'             => $pages,
            'proGroupStockList' => $proGroupStockList['proGroupStockArr'],
        ]);

    }

    /**
     * Displays a single ProductGroupStockInfo model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看产品组料仓')) {
            return $this->redirect(['site/login']);
        }
        // 展示产品组  料仓信息
        $stockInfo = Json::decode(Api::getProGroupStockInfoByID($id));
        // 根据ID 查询出相应数据
        $model                           = new ProductGroupStockInfo();
        $model->product_group_stock_name = $stockInfo['product_group_stock_name'];
        $model->equip_type_id            = $stockInfo['equip_type_name'];
        $stockCodeToStockName            = ScmMaterialStock::getMaterialStockCodeName();
        return $this->render('view', [
            'model'                => $model,
            'stockList'            => $stockInfo['stockList'],
            'stockCodeToStockName' => $stockCodeToStockName,
        ]);
    }

    /**
     * Creates a new ProductGroupStockInfo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加产品组料仓')) {
            return $this->redirect(['site/login']);
        }
        $param           = Yii::$app->request->post();
        $model           = new ProductGroupStockInfo();
        $model->scenario = 'create';
        if ($param) {
            // 调用 产品组料仓信息添加接口
            $saveResult = Api::saveProGroupStockInfo("save-pro-group-stock-info", $param['ProductGroupStockInfo']);
            if (!empty($saveResult)) {
                ManagerLog::saveLog(Yii::$app->user->id, "产品料仓管理", ManagerLog::CREATE, $param['ProductGroupStockInfo']['product_group_stock_name']);
                return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->setFlash('error', '同步产品组料仓失败，请重试');
            }
        }
        return $this->render('_form', [
            'model'     => $model,
            'stockList' => '',
        ]);
    }

    /**
     * 通过设备类型ID，组合设备类型料仓物料类型json
     * @author  zmy
     * @version 2017-08-29
     * @return  [string]     [json]
     */
    public function actionGetEquipTypeStockInfo()
    {
        $equipTypeID = Yii::$app->request->get('equipTypeID');
        echo Json::encode(ScmEquipTypeMatstockAssoc::getEquipTypeStockList($equipTypeID, ''));
    }

    /**
     * Updates an existing ProductGroupStockInfo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑产品组料仓')) {
            return $this->redirect(['site/login']);
        }
        $param           = Yii::$app->request->post();
        $model           = new ProductGroupStockInfo();
        $model->scenario = 'update';
        // 根据ID 查询出相应数据
        $stockInfo                       = Json::decode(Api::getProGroupStockInfoByID($id));
        $model->product_group_stock_name = $stockInfo['product_group_stock_name'];
        $model->equip_type_id            = $stockInfo['equip_type_id'];
        $model->id                       = $stockInfo['id'];
        if ($param) {
            //更新产品组料仓信息结果
            $saveResult = Api::saveProGroupStockInfo("save-pro-group-stock-info", $param['ProductGroupStockInfo']);
            if (!empty($saveResult)) {
                if (!empty($saveResult['groupId'])) {
                    //同步erp后台产品组料仓信息
                    $erpRet = ProductGroupStockInfo::saveProductGroupStockInfo($param['ProductGroupStockInfo'], $saveResult['groupId']);
                    if (!$erpRet) {
                        Yii::$app->getSession()->setFlash('error', '同步产品组料仓失败，请重试');
                    }
                }
                ManagerLog::saveLog(Yii::$app->user->id, "产品料仓管理", ManagerLog::UPDATE, $param['ProductGroupStockInfo']['product_group_stock_name']);
                return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->setFlash('error', '同步产品组料仓失败，请重试');
            }
        }
        $model->isNewRecord = false;
        return $this->render('_form', [
            'model'     => $model,
            'stockList' => Json::encode($stockInfo),
        ]);
    }

    /**
     * Deletes an existing ProductGroupStockInfo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
        if (!Yii::$app->user->can('删除产品组料仓')) {
            return $this->redirect(['site/login']);
        }
        $id                    = Yii::$app->request->post('id');
        $productGroupStockName = Yii::$app->request->post('productGroupStockName');
        $result                = Api::delProGroupStockInfoByID($id);
        if ($result) {
            ManagerLog::saveLog(Yii::$app->user->id, "产品料仓管理", ManagerLog::DELETE, $productGroupStockName);
        }
        echo $result ? 1 : 0;
    }

    /**
     * Finds the ProductGroupStockInfo model based on its primary key value. If
     * the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductGroupStockInfo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductGroupStockInfo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
