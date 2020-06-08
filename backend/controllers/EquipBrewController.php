<?php

namespace backend\controllers;

use backend\models\EquipBrew;
use backend\models\EquipBrewSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EquipBrewController implements the CRUD actions for EquipBrew model.
 */
class EquipBrewController extends Controller
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
     * Lists all EquipBrew models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new EquipBrewSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'      => $searchModel,
            'dataProvider'     => $dataProvider,
            'productIdNameArr' => EquipBrew::getProductNameList(),
        ]);
    }

    /**
     * Displays a single EquipBrew model.
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
     * Creates a new EquipBrew model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EquipBrew();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "设备冲泡器管理", \backend\models\ManagerLog::CREATE, "添加设备冲泡器时间");
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EquipBrew model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "设备冲泡器管理", \backend\models\ManagerLog::UPDATE, "编辑设备冲泡器时间");
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EquipBrew model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "设备冲泡器管理", \backend\models\ManagerLog::DELETE, "删除设备冲泡器时间");
        return $this->redirect(['index']);
    }

    /**
     * Finds the EquipBrew model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EquipBrew the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipBrew::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
