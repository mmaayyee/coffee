<?php

namespace backend\controllers;

use backend\models\MaterielLog;
use backend\models\MaterielLogSearch;
use common\models\Api;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * MaterielLogController implements the CRUD actions for MaterielLog model.
 */
class MaterielLogController extends Controller
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

    /**
     * Lists all MaterielLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('物料消耗记录')) {
            return $this->redirect(['site/login']);
        }
        $productIDNameList = ['' => '请选择'] + Api::getProductIDName();
        $searchModel       = new MaterielLogSearch();
        $dataProvider      = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'productIDNameList' => $productIDNameList,
            'searchModel'       => $searchModel,
            'dataProvider'      => $dataProvider,
        ]);
    }

    /**
     * Displays a single MaterielLog model.
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
     * Creates a new MaterielLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MaterielLog();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "物料消耗操作记录管理", \backend\models\ManagerLog::CREATE, "添加物料消耗操作记录");
            return $this->redirect(['view', 'id' => $model->materiel_log_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing MaterielLog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "物料消耗操作记录管理", \backend\models\ManagerLog::UPDATE, "编辑物料消耗操作记录");
            return $this->redirect(['view', 'id' => $model->materiel_log_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing MaterielLog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "物料消耗操作记录管理", \backend\models\ManagerLog::DELETE, "删除物料消耗操作记录");
        return $this->redirect(['index']);
    }

    /**
     * Finds the MaterielLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MaterielLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MaterielLog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
