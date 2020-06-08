<?php

namespace backend\controllers;

use backend\models\EquipTypeProductConfig;
use backend\models\EquipTypeProductConfigSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EquipTypeProductConfigController implements the CRUD actions for EquipTypeProductConfig model.
 */
class EquipTypeProductConfigController extends Controller
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
     * Lists all EquipTypeProductConfig models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new EquipTypeProductConfigSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EquipTypeProductConfig model.
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
     * Creates a new EquipTypeProductConfig model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EquipTypeProductConfig();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "设备类型单品配置管理", \backend\models\ManagerLog::CREATE, "添加设备类型单品配置");
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EquipTypeProductConfig model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "设备类型单品配置管理", \backend\models\ManagerLog::UPDATE, "编辑设备类型单品配置");
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EquipTypeProductConfig model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "设备类型单品配置管理", \backend\models\ManagerLog::DELETE, "删除设备类型单品配置");

        return $this->redirect(['index']);
    }

    /**
     * Finds the EquipTypeProductConfig model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EquipTypeProductConfig the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipTypeProductConfig::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
