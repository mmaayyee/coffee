<?php

namespace backend\controllers;

use backend\models\DistributionTaskEquipSetting;
use backend\models\DistributionTaskEquipSettingSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * DistributionTaskEquipSettingController implements the CRUD actions for DistributionTaskEquipSetting model.
 */
class DistributionTaskEquipSettingController extends Controller
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
     * Lists all DistributionTaskEquipSetting models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new DistributionTaskEquipSettingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DistributionTaskEquipSetting model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id, $flag)
    {
        if (!Yii::$app->user->can('查看日常任务设置')) {
            return $this->redirect(['site/login']);
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
            'flag'  => $flag,
        ]);
    }

    /**
     * Creates a new DistributionTaskEquipSetting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加日常任务设置')) {
            return $this->redirect(['site/login']);
        }
        $flag  = Yii::$app->request->get('flag');
        $model = new DistributionTaskEquipSetting();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "日常任务设置", \backend\models\ManagerLog::CREATE, "添加日常任务设置");
            return $this->redirect(['view', 'id' => $model->id, 'flag' => $flag]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'flag'  => $flag,
            ]);
        }
    }

    /**
     * Updates an existing DistributionTaskEquipSetting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑日常任务设置')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);

        $flag = Yii::$app->request->get('flag');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "日常任务设置", \backend\models\ManagerLog::UPDATE, "编辑日常任务设置");
            return $this->redirect(['view', 'id' => $model->id, 'flag' => $flag]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'flag'  => $flag,
            ]);
        }
    }

    /**
     * Deletes an existing DistributionTaskEquipSetting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除日常任务设置')) {
            return $this->redirect(['site/login']);
        }
        $this->findModel($id)->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "日常任务设置", \backend\models\ManagerLog::DELETE, "删除日常任务设置");
        return $this->redirect(['index']);
    }

    /**
     * Finds the DistributionTaskEquipSetting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DistributionTaskEquipSetting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DistributionTaskEquipSetting::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
