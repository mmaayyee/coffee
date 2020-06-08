<?php

namespace backend\controllers;

use backend\models\EquipAbnormalTask;
use backend\models\EquipAbnormalTaskSearch;
use backend\models\Organization;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EquipAbnormalTaskController implements the CRUD actions for EquipAbnormalTask model.
 */
class EquipAbnormalTaskController extends Controller
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
     * Lists all EquipAbnormalTask models.
     * @return mixed
     */
    public function actionIndex()
    {
        $organization = Organization::getOrganizationList();
        $searchModel  = new EquipAbnormalTaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'organization' => $organization,
        ]);
    }

    /**
     * Displays a single EquipAbnormalTask model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($task_id)
    {

        return $this->render('view', [
            'model' => $this->findModel($task_id),
        ]);
    }

    /**
     * Creates a new EquipAbnormalTask model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EquipAbnormalTask();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "设备故障任务管理", \backend\models\ManagerLog::CREATE, "添加设备故障任务");
            return $this->redirect(['view', 'id' => $model->task_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EquipAbnormalTask model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($task_id)
    {
        $model = $this->findModel($task_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "设备故障任务管理", \backend\models\ManagerLog::UPDATE, "编辑设备故障任务");
            return $this->redirect(['view', 'id' => $model->task_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing EquipAbnormalTask model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($task_id)
    {
        $this->findModel($task_id)->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "设备故障任务管理", \backend\models\ManagerLog::DELETE, "删除设备故障任务");

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing EquipAbnormalTask model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionNext($task_id)
    {
        $model              = $this->findModel($task_id);
        $model->task_status = EquipAbnormalTask::NEXTDAY;
        $model->load(['EquipAbnormalTask' => $model]);
        $model->save();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "设备故障任务管理", \backend\models\ManagerLog::UPDATE, "转次日");
        return $this->redirect(['index']);
    }

    /**
     * Finds the EquipAbnormalTask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EquipAbnormalTask the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipAbnormalTask::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
