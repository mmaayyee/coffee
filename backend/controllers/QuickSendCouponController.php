<?php

namespace backend\controllers;

use backend\models\QuickSendCoupon;
use backend\models\QuickSendCouponSearch;
use common\helpers\Tools;
use common\models\Api;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * QuickSendCouponController implements the CRUD actions for QuickSendCoupon model.
 */
class QuickSendCouponController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all QuickSendCoupon models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('快速发券列表')) {
            return $this->redirect(['site/login']);
        }
        $params['page'] = isset($params['page']) ? $params['page'] : 0;
        $searchModel    = new QuickSendCouponSearch();
        $dataProvider   = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single QuickSendCoupon model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {

        $model = QuickSendCoupon::getDetails($id);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new QuickSendCoupon model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('快速发券添加')) {
            return $this->redirect(['site/login']);
        }
        QuickSendCoupon::getCouponPackage();
        $model      = new QuickSendCoupon();
        $couponList = QuickSendCoupon::getQuickSendCouponList([
            'is_product'  => QuickSendCoupon::COMMON_COUPON,
            'coupon_type' => QuickSendCoupon::CASH_COUPON,
        ]);
        foreach ($couponList as $cid => &$cname) {
            if ($cid) {
                $cname = $cid . "_" . $cname;
            }
        }
        unset($cname);
        $couponGroupList = QuickSendCoupon::getCouponPackage();
        foreach ($couponGroupList as $gid => &$gname) {
            if ($gid) {
                $gname = $gid . "_" . $gname;
            }

        }
        unset($gname);
        $model->setScenario('create');
        $params        = Yii::$app->request->post();
        $consumeParams = Yii::$app->request->get();
        if ($consumeParams) {
            $model->consume_id = $consumeParams['consume_id'] ?? '';
            $model->order_code = $consumeParams['order_code'] ?? '';
            $model->phone      = $consumeParams['phone'] ?? '';
        }
        if ($params) {
            if ($model->load($params) && $model->validate() && Api::quickSendCouponCreate($params)) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "快速发券管理", \backend\models\ManagerLog::CREATE, "添加快速发券");
                return $this->redirect(['index']);
            } else {
                if ($model->send_phone != '') {
                    foreach (explode(',', $model->send_phone) as $key => $value) {
                        $model->send_phone_list[$value] = $value;
                        unset($model->send_phone_list[$key]);
                    }
                }
                $model->isNewRecord = 0;
                return $this->render('create', ['model' => $model, 'couponList' => $couponList, 'couponGroupList' => $couponGroupList]);
            }
        } else {
            $model->isNewRecord = 1;
            return $this->render('create', [
                'model'           => $model,
                'couponList'      => $couponList,
                'couponGroupList' => $couponGroupList,
            ]);
        }
    }

    public function actionExport()
    {
        if (!Yii::$app->user->can('快速发券列表')) {
            return $this->redirect(['site/login']);
        }
        $params              = Yii::$app->request->queryParams;
        $quickSendCouponList = Api::quickSendCouponExport($params);
        $header              = ['用户账号', '类型', '发券时间', '优惠券名称', '发券数量', '消费记录ID', '订单编号', '来电号码', '备注'];
        return Tools::exportData('快速发券', $header, $quickSendCouponList);

    }
    /**
     * Finds the QuickSendCoupon model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return QuickSendCoupon the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = QuickSendCoupon::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
     * 根据条件获取的单品优惠券列表
     * @author  tuqiang
     * @version 2017-09-23
     * @return  array('coupon_id' => coupon_name);
     */
    public function actionGetQuickSendCouponList()
    {
        $params['coupon_type'] = Yii::$app->request->get('coupon_type');
        $params['is_product']  = Yii::$app->request->get('is_product');
        $couponList            = Api::getQuickSendCouponList($params);
        foreach ($couponList as &$coupon) {
            $coupon['coupon_name'] = $coupon['coupon_id'] . "_" . $coupon['coupon_name'];
        }
        unset($coupon);
        echo Json::encode($couponList);
    }
    /**
     * 判断当前添加的手机号是否存在，并且是否在黑名单中
     * @author  tuqiang
     * @version 2017-09-23
     * @param   $sendPhone  手机号
     * @return  array('code' => 0/1/2,'msg' => 'lalalal');
     */
    public function actionVerifyQuickSendCouponPhone()
    {
        $params['send_phone'] = Yii::$app->request->get('sendPhone');
        echo json_encode(Api::verifyQuickSendCouponPhone($params));die;
    }
}
