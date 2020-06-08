<?php

namespace app\controllers;

use app\models\GrindEquipAssoc;
use app\models\GrindEquipAssocSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * GrindEquipAssocController implements the CRUD actions for GrindEquipAssoc model.
 */
class GrindEquipAssocController extends Controller
{
    public function behaviors()
    {
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
     * Lists all GrindEquipAssoc models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel  = new GrindEquipAssocSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GrindEquipAssoc model.
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
     * Creates a new GrindEquipAssoc model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GrindEquipAssoc();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "预磨豆楼宇设备关联管理", \backend\models\ManagerLog::CREATE, "添加预磨豆楼宇设备关联信息");
            return $this->redirect(['view', 'id' => $model->grind_equip_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing GrindEquipAssoc model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "预磨豆楼宇设备关联管理", \backend\models\ManagerLog::UPDATE, "编辑预磨豆楼宇设备关联信息");
            return $this->redirect(['view', 'id' => $model->grind_equip_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing GrindEquipAssoc model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "预磨豆楼宇设备关联管理", \backend\models\ManagerLog::DELETE, "删除预磨豆楼宇设备关联信息");

        return $this->redirect(['index']);
    }

    /**
     * Finds the GrindEquipAssoc model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GrindEquipAssoc the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GrindEquipAssoc::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
