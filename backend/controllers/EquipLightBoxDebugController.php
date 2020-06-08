<?php

namespace backend\controllers;

use Yii;
use backend\models\EquipLightBoxDebug;
use backend\models\EquipLightBoxDebugSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
/**
 * EquipLightBoxDebugController implements the CRUD actions for EquipLightBoxDebug model.
 */
class EquipLightBoxDebugController extends Controller
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
     * Lists all EquipLightBoxDebug models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('灯箱调试项管理')){
            return $this->redirect(['site/login']);
        }
        $searchModel = new EquipLightBoxDebugSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new EquipLightBoxDebug model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加灯箱调试项')){
            return $this->redirect(['site/login']);
        }
        $model = new EquipLightBoxDebug();
        // echo Yii::$app->request->get('light_box_id');die;
        $model->light_box_id = Yii::$app->request->get('light_box_id');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "灯箱调试项管理", \backend\models\ManagerLog::CREATE, $model->debug_item);
            return $this->redirect(['index', 'EquipLightBoxDebugSearch[light_box_id]' => $model->light_box_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EquipLightBoxDebug model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑灯箱调试项')){
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "灯箱调试项管理", \backend\models\ManagerLog::UPDATE, $model->debug_item);
            return $this->redirect(['index', 'EquipLightBoxDebugSearch[light_box_id]' => $model->light_box_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EquipLightBoxDebug model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除灯箱调试项')){
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);
        $model->is_del = EquipLightBoxDebug::DEL_YES;
        $model->save();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "灯箱调试项管理", \backend\models\ManagerLog::DELETE, $model->debug_item);
        return $this->redirect(['index', 'EquipLightBoxDebugSearch[light_box_id]' => $model->light_box_id]);
    }

    /**
     * Finds the EquipLightBoxDebug model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return EquipLightBoxDebug the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipLightBoxDebug::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
