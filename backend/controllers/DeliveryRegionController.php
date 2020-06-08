<?php

namespace backend\controllers;

use backend\models\DeliveryRegion;
use backend\models\DeliveryRegionSearch;
use common\models\DeliveryApi;
use common\models\WxMember;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class DeliveryRegionController extends Controller
{
    /**
     * Lists all DeliveryPerson models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new DeliveryRegionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new DeliveryPerson model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('新增配送区域')) {
            return $this->redirect(['site/login']);
        }
        $model = new DeliveryRegion();
        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post();
            if ($model->load($postData)) {
                $saveResult = DeliveryApi::updateDeliveryPerson($postData['DeliveryPerson']);
                if ($saveResult['status'] == 'success') {
                    \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "配送区域管理", \backend\models\ManagerLog::CREATE, "添加配送区域");
                    return $this->redirect('index');
                }
                Yii::$app->getSession()->setFlash('error', $saveResult['msg']);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DeliveryPerson model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑配送区域')) {
            return $this->redirect(['site/login']);
        }
        $model = new DeliveryRegion();
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 修改区域状态
     * @author jiangfeng
     * @version 2018/11/20
     * @return string
     */
    public function actionDeliveryRegionChange()
    {
        if (!Yii::$app->user->can('编辑配送区域')) {
            return $this->redirect(['site/login']);
        }
        $param = Yii::$app->request->get();
        return Json::encode(DeliveryApi::deliveryRegionChange($param));
    }
}
