<?php

namespace backend\controllers;

use backend\models\CoffeeProductSetup;
use backend\models\CoffeeProductSetupSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CoffeeProductSetupController implements the CRUD actions for CoffeeProductSetup model.
 */
class CoffeeProductSetupController extends Controller
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
     * Lists all CoffeeProductSetup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new CoffeeProductSetupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CoffeeProductSetup model.
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
     * Creates a new CoffeeProductSetup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CoffeeProductSetup();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "单品配方管理", \backend\models\ManagerLog::CREATE, "添加单品配方");
            return $this->redirect(['view', 'id' => $model->setup_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CoffeeProductSetup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "单品配方管理", \backend\models\ManagerLog::UPDATE, "编辑单品配方");
            return $this->redirect(['view', 'id' => $model->setup_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CoffeeProductSetup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "单品配方管理", \backend\models\ManagerLog::DELETE, "删除单品配方");

        return $this->redirect(['index']);
    }

    /**
     * Finds the CoffeeProductSetup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CoffeeProductSetup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CoffeeProductSetup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
