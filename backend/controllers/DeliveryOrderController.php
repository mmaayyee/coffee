<?php

namespace backend\controllers;

use backend\models\DeliveryOrder;
use backend\models\DeliveryOrderSearch;
use common\models\DeliveryApi;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * DeliveryOrderController implements the CRUD actions for DeliveryOrder model.
 */
class DeliveryOrderController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [

                ],
            ],
        ];
    }

    public function actionCount()
    {
        if (!Yii::$app->user->can('外卖日报')) {
            return $this->redirect(['site/login']);
        }
        return $this->render('count', [
            'orderCount' => DeliveryApi::getOrderCount(),
        ]);
    }

    /**
     * Lists all DeliveryOrder models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('外卖订单')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new DeliveryOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //获取取消原因列表 排除超时和用户取消
        $param['idArray'] = [
            DeliveryOrder::ORDER_FAIL_TIME_OUT,
            DeliveryOrder::ORDER_FAIL_USER_CANCLE,
        ];
        $reasonList = DeliveryApi::getFailReasonList($param);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'reasonList'   => $reasonList,
        ]);
    }

    /**
     * Displays a single DeliveryOrder model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看外卖订单详情')) {
            return $this->redirect(['site/login']);
        }
        //获取外卖订单详情
        $deliveryOrder = DeliveryApi::getDeliveryOrder(['delivery_order_id' => $id]);
        if ($deliveryOrder['status'] == 'error') {
            return $this->redirect(['index']);
        }
        //获取取消原因列表 排除超时和用户取消
        $param['idArray'] = [
            DeliveryOrder::ORDER_FAIL_TIME_OUT,
            DeliveryOrder::ORDER_FAIL_USER_CANCLE,
        ];
        $reasonList = DeliveryApi::getFailReasonList($param);
        return $this->render('view', [
            'deliveryOrder'   => $deliveryOrder['data']['deliveryOrder'],
            'orderInfo'       => $deliveryOrder['data']['orderInfo'],
            'deliveryPerson'  => $deliveryOrder['data']['deliveryPerson'],
            'logList'         => $deliveryOrder['data']['logList'],
            'useCoupon'       => $deliveryOrder['data']['useCoupon'],
            'useCostCoupon'   => $deliveryOrder['data']['useCostCoupon'],
            'userAddress'     => $deliveryOrder['data']['userAddress'],
            'failReason'      => $deliveryOrder['data']['failReason'],
            'buildingName'    => $deliveryOrder['data']['buildingName'],
            'productNameList' => $deliveryOrder['data']['productNameList'],
            'reasonList'      => $reasonList,
        ]);
    }

    /**
     * Deletes an existing DeliveryOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param ["delivery_order_id":1,"fail_reason_id":3]
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionCancel()
    {
        if (!Yii::$app->user->can('取消外卖订单')) {
            return $this->redirect(['site/login']);
        }
        $postData                = Yii::$app->request->post();
        $postData['custom_name'] = Yii::$app->user->identity->role;
        //执行取消
        $saveResult = DeliveryApi::cancelDeliveryOrderByCustom($postData);
        return json_encode($saveResult);
    }

    /**
     * actionSwitch  转移外卖订单
     * @author  jiangfeng
     * @version 2018/11/20
     * @return false|string|\yii\web\Response
     */
    public function actionSwitch()
    {
        if (!Yii::$app->user->can('转移外卖订单')) {
            return $this->redirect(['site/login']);
        }
        $postData = Yii::$app->request->post();
        //执行取消
        $saveResult = DeliveryApi::switchDeliveryOrder($postData);
        return Json::encode($saveResult);
    }

    /**
     * 获取区域配送员
     * @author  jiangfeng
     * @version 2018/11/20
     * @return string|\yii\web\Response
     */
    public function actionGetPersonByRegion()
    {
        if (!Yii::$app->user->can('转移外卖订单')) {
            return $this->redirect(['site/login']);
        }
        $param = Yii::$app->request->post();
        return Json::encode(DeliveryApi::getPersonByRegion($param));
    }
}
