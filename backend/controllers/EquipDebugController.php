<?php

namespace backend\controllers;

use Yii;
use backend\models\EquipDebug;
use backend\models\EquipDebugSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
/**
 * EquipDebugController implements the CRUD actions for EquipDebug model.
 */
class EquipDebugController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'view', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],            
        ];
    }

    /**
     * Lists all EquipDebug models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('设备调试项管理')){
            return $this->redirect(['site/login']);
        }
        $searchModel = new EquipDebugSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new EquipDebug model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加设备调试项')){
            return $this->redirect(['site/login']);
        }
        $model = new EquipDebug();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "设备调试项管理", \backend\models\ManagerLog::CREATE, $model->debug_item);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EquipDebug model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑设备调试项')){
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "设备调试项管理", \backend\models\ManagerLog::UPDATE, $model->debug_item);
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EquipDebug model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除设备调试项')){
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);
        $model->is_del = EquipDebug::DEL_YES;
        $model->save();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "设备调试项管理", \backend\models\ManagerLog::DELETE, $model->debug_item);
        return $this->redirect(['index']);
    }

    /**
     * Finds the EquipDebug model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return EquipDebug the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipDebug::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
