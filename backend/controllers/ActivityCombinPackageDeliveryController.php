<?php

namespace backend\controllers;

use Yii;
use backend\models\ActivityCombinPackageDelivery;
use backend\models\ActivityCombinPackageDeliverySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\WxMember;
use common\models\ActivityApi;

/**
 * ActivityCombinPackageDeliveryController implements the CRUD actions for ActivityCombinPackageDelivery model.
 */
class ActivityCombinPackageDeliveryController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ActivityCombinPackageDelivery models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('自组合用户发货管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel = new ActivityCombinPackageDeliverySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ActivityCombinPackageDelivery model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ActivityCombinPackageDelivery model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ActivityCombinPackageDelivery();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->delivery_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ActivityCombinPackageDelivery model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->delivery_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ActivityCombinPackageDelivery model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * 修改发货信息
     * @author  zmy
     * @version 2018-04-04
     * @return  [type]     [description]
     */
    public function actionDeliverGoods()
    {
        if (!Yii::$app->user->can('自组合用户发货')) {
            return $this->redirect(['site/login']);
        }
        // 运维人员 id=>name 数组
        $distributionIdToNameList = WxMember::getDistributionIdToNameList();
        $param = Yii::$app->request->post();
        $activityId = $param['activity_id'];
        
        unset($param['_csrf']);
        unset($param['activity_id']);
        $distributionUserId = isset($param['distribution_user_id']) ? $param['distribution_user_id'] : "";
        $param['distribution_user_name'] = isset($distributionIdToNameList[$distributionUserId]) ? $distributionIdToNameList[$distributionUserId] : '';

        $ret = ActivityApi::updateDeliverGoods($param);
        if($ret){
            return $this->redirect(['activity-combin-package-delivery/index', 'ActivityCombinPackageDeliverySearch[activity_id]'=>$activityId]);
        }
        Yii::$app->getSession()->setFlash('error', '发货信息修改失败');
        return $this->redirect(['activity-combin-package-delivery/index', 'ActivityCombinPackageDeliverySearch[activity_id]'=>$activityId]);
    }

    /**
     * Finds the ActivityCombinPackageDelivery model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ActivityCombinPackageDelivery the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ActivityCombinPackageDelivery::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
