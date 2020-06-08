<?php

namespace backend\controllers;

use backend\models\ManagerLog;
use backend\models\ShopGoods;
use backend\models\ShopGoodsSearch;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;

class ShopGoodsController extends \yii\web\Controller
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
                        'actions' => ['set-post-type', 'get-mail-method'],
                        'allow'   => true,
                    ],
                    [
                        'actions' => ['index', 'create', 'update', 'view', 'delete', 'check', 'async-image', 'create-activity-log'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * 商品列表页
     * @author wxl
     * @date 2017-11-11
     * @return string
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('商品管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new ShopGoodsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 修改商品信息
     * @author wxl
     * @date 2017-11-11
     * @return string
     */
    public function actionUpdate()
    {
        if (!Yii::$app->user->can('编辑商品')) {
            return $this->redirect(['site/login']);
        }
        $goodsId = Yii::$app->request->get('id');
        //获取商品信息
        $shopGoodsModel = Json::decode(ShopGoods::getShopGoodsInfo($goodsId));
        if ($shopGoodsModel['status'] != 1) {
            $shopGoodsModel['status'] = 2;
        }
        //获取商品sku信息
        $skuInfo = ShopGoods::getShopGoodsSkuInfo($goodsId);
        //获取商品sku列表信息
        $skuList        = ShopGoods::getShopGoodsSkuList($goodsId);
        $model          = new ShopGoods();
        $goodsAttribute = Json::decode($shopGoodsModel['goods_attribute']);
        $model->load(['ShopGoods' => $goodsAttribute]);
        $model->sku_attr = $skuInfo;
        $model->sku_list = $skuList;
        $model->load(['ShopGoods' => $shopGoodsModel]);
        $model->content = str_replace("'", '"', $model->content);
        return $this->render('update', ['model' => $model, 'goods_id' => $goodsId]);
    }

    /**
     * 创建商品信息
     * @author wxl
     * @date 2017-11-11
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加商品')) {
            return $this->redirect(['site/login']);
        }
        $model         = new ShopGoods();
        $model->status = ShopGoods::WAIT_CHECK;
        return $this->render('create', ['model' => $model]);
    }

    /**
     * 商品详情
     * @author wxl
     * @date 2017-11-11
     * @return string
     */
    public function actionView()
    {
        if (!Yii::$app->user->can('查看商品')) {
            return $this->redirect(['site/login']);
        }
        $goodsId     = Yii::$app->request->get('id');
        $goodsDetail = ShopGoods::getShopGoodsDetail($goodsId);

        $goodsAttribute = !empty($goodsDetail['goods_attribute']) ? Json::decode($goodsDetail['goods_attribute']) : '';
        $model          = new ShopGoods();
        $model->load(['ShopGoods' => $goodsDetail]);
        $model->load(['ShopGoods' => $goodsAttribute]);
        $data = [];
        foreach ($goodsDetail as $key) {
            if (is_array($key)) {
                array_push($data, $key);
            }
        }
        return $this->render('view', ['model' => $model, 'goodsDetail' => $goodsDetail, 'data' => $data]);
    }

    /**
     * 删除数据
     * @author wxl
     * @date 2017-11-11
     * @return \yii\web\Response
     */
    public function actionDelete()
    {
        if (!Yii::$app->user->can('删除商品')) {
            return $this->redirect(['site/login']);
        }
        $idList = Yii::$app->request->post('goods_id');
        $model  = new ShopGoods();
        $delRes = $model->delete($idList);
        if ($delRes) {
            ManagerLog::saveLog(Yii::$app->user->id, "周边商城管理", ManagerLog::DELETE, "删除商品");
        }
    }

    /**
     * 审核数据
     * @author wxl
     * @date 2017-11-11
     * @return \yii\web\Response
     */
    public function actionCheck()
    {
        if (!Yii::$app->user->can('审核商品')) {
            return $this->redirect(['site/login']);
        }
        $checkData = Yii::$app->request->post();
        $model     = new ShopGoods();
        if ($model->check($checkData)) {
            ManagerLog::saveLog(Yii::$app->user->id, "周边商城管理", ManagerLog::UPDATE, "商品审核");
            echo 1;
        } else {
            echo 0;
        }
    }

    /**
     * 设置包邮方式
     * @author wxl
     * @date 2017-11-11
     * @return string
     */
    public function actionSetPostType()
    {
        if (!Yii::$app->user->can('邮费设置')) {
            return $this->redirect(['site/login']);
        }
        $postType = Yii::$app->request->post('postType');
        $amount   = Yii::$app->request->post('amount');
        return ShopGoods::addMailMethod($postType, $amount);
    }

    /**
     * 获取包邮方式
     * @author wxl
     * @date 2017-11-11
     * @return string
     */
    public function actionGetMailMethod()
    {
        return ShopGoods::getMailMethod();
    }
}
