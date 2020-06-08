<?php

namespace backend\controllers;

use backend\models\ScmTotalInventory;
use backend\models\ScmTotalInventorySearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ScmTotalInventoryController implements the CRUD actions for ScmTotalInventory model.
 */
class ScmTotalInventoryController extends Controller {
    public function behaviors() {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all ScmTotalInventory models.
     * @return mixed
     */
    public function actionIndex() {
        if (!Yii::$app->user->can('库存信息管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new ScmTotalInventorySearch();
        $data = $searchModel->search(Yii::$app->request->queryParams);
        // $sql            = 'select sum(total_number) total_number, material_id from scm_total_inventory GROUP by material_id where ';
        // $totalinventory = ScmTotalInventory::findBySql($sql)->asArray()->all();
        //$totalinventory = $searchModel->totalSearch();
        return $this->render('index', [
            'searchModel'    => $searchModel,
            'dataProvider'   => $data['dataProvider'],
            'pages'          => $data['pages']
            //'totalinventory' => $totalinventory,
        ]);
    }

    /**
     * Displays a single ScmTotalInventory model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ScmTotalInventory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new ScmTotalInventory();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "库存信息管理", \backend\models\ManagerLog::CREATE, "添加库存信息");

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ScmTotalInventory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "库存信息管理", \backend\models\ManagerLog::UPDATE, "编辑库存信息");
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ScmTotalInventory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "库存信息管理", \backend\models\ManagerLog::DELETE, "删除库存信息");
        return $this->redirect(['index']);
    }

    /**
     * Finds the ScmTotalInventory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ScmTotalInventory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = ScmTotalInventory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
