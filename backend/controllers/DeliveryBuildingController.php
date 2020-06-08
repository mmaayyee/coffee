<?php

namespace backend\controllers;

use backend\models\DeliveryBuilding;
use backend\models\DeliveryBuildingSearch;
use common\models\DeliveryApi;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * DeliveryBuildingController implements the CRUD actions for DeliveryBuilding model.
 */
class DeliveryBuildingController extends Controller
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
                    'delete' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * Lists all DeliveryBuilding models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('配送点位管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new DeliveryBuildingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new DeliveryBuilding model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('新增配送点位')) {
            return $this->redirect(['site/login']);
        }
        $model = new DeliveryBuilding();
        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post();
            if ($model->load($postData)) {
                $saveResult = DeliveryApi::updateDeliveryBuilding($postData['DeliveryBuilding']);
                if ($saveResult['status'] == 'success') {
                    return $this->redirect('index');
                }
                Yii::$app->getSession()->setFlash('error', $saveResult['msg']);
            }
        }
        //加载可选楼宇列表
        $buildingList = DeliveryApi::getBuildingList();
        //加载可选人员列表
        $personList = DeliveryApi::getPersonList();
        return $this->render('create', [
            'model'        => $model,
            'buildingList' => $buildingList,
            'personList'   => $personList,
        ]);
    }

    /**
     * Updates an existing DeliveryBuilding model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑配送点位')) {
            return $this->redirect(['site/login']);
        }
        $deliveryBuilding = DeliveryApi::getDeliveryBuilding(['building_id' => $id]);
        if ($deliveryBuilding['status'] == 'error') {
            return $this->redirect('index');
        }
        $model = new DeliveryBuilding();
        $model->load(['DeliveryBuilding' => $deliveryBuilding['data']]);
        //处理时间
        $timeArray            = explode('~', $model->business_time);
        $model->business_time = $timeArray[0];
        $model->end_time      = $timeArray[1];
        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post();
            if ($model->load($postData)) {
                $saveResult = DeliveryApi::updateDeliveryBuilding($postData['DeliveryBuilding']);
                if ($saveResult['status'] == 'success') {
                    return $this->redirect('index');
                }
                Yii::$app->getSession()->setFlash('error', $saveResult['msg']);
            }
        }
        //加载可选楼宇列表
        $buildingList = DeliveryApi::getBuildingList(['building_id' => $id]);
        //加载可选人员列表
        $personList = DeliveryApi::getPersonList();
        return $this->render('update', [
            'model'        => $model,
            'buildingList' => $buildingList,
            'personList'   => $personList,
        ]);
    }

    /**
     * Deletes an existing DeliveryBuilding model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除配送点位')) {
            return $this->redirect(['site/login']);
        }
        $saveResult = DeliveryApi::delDeliveryBuilding(['building_id' => $id]);
        return json_encode($saveResult);
    }
    /**
     * Changes an existing DeliveryBuilding model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionChange($id)
    {
        if (!Yii::$app->user->can('点位营业状态编辑')) {
            return $this->redirect(['site/login']);
        }
        $saveResult = DeliveryApi::changeDeliveryBuildingStatus(['building_id' => $id]);
        return json_encode($saveResult);

    }
}
