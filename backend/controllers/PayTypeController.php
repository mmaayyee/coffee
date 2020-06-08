<?php
namespace backend\controllers;

use backend\models\ManagerLog;
use backend\models\PayType;
use backend\models\PayTypeApi;
use backend\models\PayTypeSearch;
use Yii;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * PayTypeController implements the CRUD actions for PayType model.
 */
class PayTypeController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * Lists all PayType models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('查看支付方式')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new PayTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing PayType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * "pay_type":支付方式id,
    "pay_type_icon_url":支付方式图标图片地址,
    "qr_bg_url":二维码背景图图片地址,
    "is_support_strategy":是否支持优惠策略，1为支持，0为不支持,
    "is_show":是否默认打开，1为打开，0为不打开,
    "default_strategy":默认优惠策略id,
    "serial_id":序号

     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑支付方式')) {
            return $this->redirect(['site/login']);
        }
        $payTypeInfo                    = PayTypeApi::updatePayType($id)['data'];
        $payType                        = [];
        $payType['id']                  = $payTypeInfo['pay_type_id'];
        $payType['pay_type_name']       = $payTypeInfo['pay_type_name'];
        $payType['pay_type_icon_url']   = Yii::$app->params['fcoffeeUrl'] . $payTypeInfo['logo_pic'];
        $payType['qr_bg_url']           = Yii::$app->params['fcoffeeUrl'] . $payTypeInfo['bg_pic'];
        $payType['is_support_strategy'] = $payTypeInfo['is_support_discount'];
        $payType['is_show']             = $payTypeInfo['is_open'];
        $payType['default_strategy']    = $payTypeInfo['discount_holicy_id'] ? $payTypeInfo['discount_holicy_id'] : '';
        $payType['serial_id']           = $payTypeInfo['weight'];
        $payType['is_use_build']        = $payTypeInfo['is_use_build'];
        $payTypeHolicy                  = PayTypeApi::getPayTypeHolicy();
        return $this->render('update', [
            'payTypeHolicy' => Json::encode($payTypeHolicy),
            'payType'       => Json::encode($payType),
        ]);

    }
    /**
     * [actionUpdate description]
     * @author zhenggangwei
     * @date   2018-12-12
     * @param  [type]     $id [description]
     * @return [type]         [description]
     */
    public function actionUpdateSave($id)
    {
        if (!Yii::$app->user->can('编辑支付方式')) {
            return $this->redirect(['site/login']);
        }
        $params     = file_get_contents("php://input");
        $sendParams = [];
        if ($params) {
            $params                            = Json::decode($params);
            $sendParams['logo_pic']            = $params['pay_type_icon'];
            $sendParams['bg_pic']              = $params['qr_bg'];
            $sendParams['is_support_discount'] = $params['is_support_strategy'];
            $sendParams['is_open']             = $params['is_show'];
            $sendParams['discount_holicy_id']  = !$params['default_strategy'] ? 0 : $params['default_strategy'];
            $sendParams['weight']              = $params['serial_id'];
        }
        $payTypeInfo = PayTypeApi::updatePayType($id, $sendParams);
        if ($payTypeInfo['error_code'] == 0) {
            ManagerLog::saveLog(Yii::$app->user->id, "支付方式", ManagerLog::UPDATE, '编辑支付方式');
        }
        return Json::encode($payTypeInfo);

    }
}
