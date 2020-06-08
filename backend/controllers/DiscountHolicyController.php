<?php

namespace backend\controllers;

use backend\models\DiscountHolicy;
use backend\models\DiscountHolicySearch;
use backend\models\ManagerLog;
use backend\models\PayTypeApi;
use common\models\Api;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * DiscountHolicyController implements the CRUD actions for DiscountHolicy model.
 */
class DiscountHolicyController extends Controller
{
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

    /**
     * 列表
     * @author tuqiang
     * @version 2017-09-15
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('优惠策略查看')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new DiscountHolicySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $payTypeList  = PayTypeApi::getPayTypeIdNameList()['data'];
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'payTypeList'  => $payTypeList,
        ]);
    }

    /**
     * 添加
     * @author tuqiang
     * @version 2017-09-15
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('优惠策略添加')) {
            return $this->redirect(['site/login']);
        }
        $model = new DiscountHolicy();
        $model->setScenario('create');
        $params = Yii::$app->request->post();
        if ($params) {
            if ($model->load($params) && $model->validate() && Api::discountHolicyCreate($params)) {
                ManagerLog::saveLog(Yii::$app->user->id, "支付方式优惠策略", ManagerLog::CREATE, '添加支付方式优惠策略');
                return $this->redirect(['index']);
            } else {
                $model->isNewRecord = 1;
                unset($model->holicy_type_list['']);
                unset($model->payment_list['']);
                return $this->render('create', ['model' => $model]);
            }
        } else {
            $model->isNewRecord = 1;
            unset($model->holicy_type_list['']);
            unset($model->payment_list['']);
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    /**
     * 修改
     * @author tuqiang
     * @version 2017-09-15
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('优惠策略修改')) {
            return $this->redirect(['site/login']);
        }
        $model = new DiscountHolicy();
        $model->setScenario('update');
        $params = Yii::$app->request->post();
        if (!$params) {
            $model = $model->getDiscountHolicyInfo(array("holicy_id" => $id));
            unset($model->holicy_type_list['']);
            unset($model->payment_list['']);
            if ($model->holicy_type == 2) {
                $model->holicy_price = intval($model->holicy_price);
            }
            return $this->render('update', ['model' => $model]);
        }
        if ($model->load($params) && $model->validate() && Api::discountHolicyUpdate($params)) {
            ManagerLog::saveLog(Yii::$app->user->id, "支付方式优惠策略", ManagerLog::UPDATE, '编辑支付方式优惠策略');
            return $this->redirect(['index']);
        } else {
            if ($model->holicy_type == 2) {
                $model->holicy_price = intval($model->holicy_price);
            }
            unset($model->holicy_type_list['']);
            unset($model->payment_list['']);
            return $this->render('update', ['model' => $model]);
        }
    }

    /**
     * 删除
     * @author tuqiang
     * @version 2017-09-15
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('优惠策略删除')) {
            return $this->redirect(['site/login']);
        }
        ManagerLog::saveLog(Yii::$app->user->id, "支付方式优惠策略", ManagerLog::DELETE, '删除支付方式优惠策略');
        return Api::discountHolicyDelete(array('holicy_id' => $id));
    }
}
