<?php

namespace backend\controllers;

use backend\models\ShopOrder;
use backend\models\ShopOrderSearch;
use common\models\KdApiSearchApi;
use Yii;

class ShopOrderController extends \yii\web\Controller
{

    /**
     * 订单列表
     * @author wxl
     * @date 2017-11-11
     * @return string
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('查看订单')) {
            return $this->redirect(['site/login']);
        }
        $searchModel                         = new ShopOrderSearch();
        list($dataProvider, $orderGoodsList) = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'    => $searchModel,
            'dataProvider'   => $dataProvider,
            'orderGoodsList' => $orderGoodsList,
        ]);
    }

    /**
     * 订单详情
     * @author wxl
     * @date 2017-11-11
     * @return string
     */
    public function actionView()
    {
        if (!Yii::$app->user->can('查看订单')) {
            return $this->redirect(['site/login']);
        }
        $orderId        = Yii::$app->request->get('id', 1);
        $model          = new ShopOrder();
        $orderInfo      = ShopOrder::getOrderInfoByOrderId($orderId);
        $refundInfo     = ShopOrder::detailRefundInfo($orderId);
        $expressInfo    = isset($orderInfo['expressInfo']) ? $orderInfo['expressInfo'] : [];
        $expressCompany = isset($orderInfo['expressInfo']['express_name']) ? $orderInfo['expressInfo']['express_name'] : "";
        $company        = KdApiSearchApi::getExpressCompany();
        return $this->render('view', [
            'orderInfo'      => $orderInfo,
            'model'          => $model,
            'company'        => $company,
            'expressInfo'    => $expressInfo,
            'expressCompany' => $expressCompany,
            'orderId'        => $orderId,
            'refundInfo'     => $refundInfo,
        ]);
    }

    /**
     * 审核退款
     * @author wxl
     * @date 2017-11-11
     * @return mixed
     */
    public function actionOrderRefund()
    {
        if (!Yii::$app->user->can('订单退款')) {
            return $this->redirect(['site/login']);
        }
        if (Yii::$app->request->isAjax) {
            $orderList    = Yii::$app->request->post('orderList');
            $refundReason = Yii::$app->request->post('refundReason');
            $actionVal    = Yii::$app->request->post('actionVal');
            //审核人ID
            $userId     = Yii::$app->request->post('userId');
            $orderStore = ['orderList' => $orderList, 'refundReason' => $refundReason, 'actionVal' => $actionVal, 'userId' => $userId];
            return ShopOrder::ShopOrderRefund($orderStore);
        }
    }

    /**
     * 订单发货
     * @author wxl
     * @date 2017-11-11
     * @return array
     */
    public function actionUpdateOrderExpress()
    {
        if (Yii::$app->request->isAjax) {
            $orderExpress = Yii::$app->request->post('orderExpress');
            return ShopOrder::updateOrderExpress($orderExpress);
        }
    }

    /**
     * 申请退款
     * @author wxl
     * @date 2017-11-11
     * @return mixed
     */
    public function actionRefundOrder()
    {
        if (Yii::$app->request->isAjax) {
            $orderId      = Yii::$app->request->post('orderId');
            $refundReason = Yii::$app->request->post('refundReason');
            $orderStatus  = Yii::$app->request->post('orderStatus');
            $refundInfo   = ['orderId' => $orderId, 'refundReason' => $refundReason,
                'userId'                   => Yii::$app->user->id,
                'orderStatus'              => $orderStatus,
            ];
            return ShopOrder::applyOrderRefund($refundInfo);
        }
    }

}
