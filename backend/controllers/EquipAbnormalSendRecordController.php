<?php

namespace backend\controllers;

use backend\models\EquipAbnormalSendRecord;
use backend\models\EquipAbnormalSendRecordSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EquipAbnoramlSendRecordController implements the CRUD actions for EquipAbnormalSendRecord model.
 */
class EquipAbnormalSendRecordController extends Controller
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all EquipAbnormalSendRecord models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new EquipAbnormalSendRecordSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EquipAbnormalSendRecord model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new EquipAbnormalSendRecord model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EquipAbnormalSendRecord();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "异常报警发送记录管理", \backend\models\ManagerLog::CREATE, "添加异常报警发送记录");
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EquipAbnormalSendRecord model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "异常报警发送记录管理", \backend\models\ManagerLog::UPDATE, "编辑异常报警发送记录");
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EquipAbnormalSendRecord model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "异常报警发送记录管理", \backend\models\ManagerLog::DELETE, "删除异常报警发送记录");

        return $this->redirect(['index']);
    }

    /**
     * Finds the EquipAbnormalSendRecord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EquipAbnormalSendRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipAbnormalSendRecord::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
