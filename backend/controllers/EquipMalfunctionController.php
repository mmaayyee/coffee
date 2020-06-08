<?php

namespace backend\controllers;

use Yii;
use backend\models\EquipMalfunction;
use backend\models\EquipMalfunctionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;


/**
 * EquipMalfunctionController implements the CRUD actions for EquipMalfunction model.
 */
class EquipMalfunctionController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view', 'index', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all EquipMalfunction models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('故障原因管理')){
            return $this->redirect(['site/login']);
        }

        $searchModel = new EquipMalfunctionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new EquipMalfunction model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加故障原因')){
            return $this->redirect(['site/login']);
        }

        $model = new EquipMalfunction();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "故障原因", \backend\models\ManagerLog::CREATE, $model->content);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EquipMalfunction model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑故障原因')){
            return $this->redirect(['site/login']);
        }

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "故障原因", \backend\models\ManagerLog::UPDATE, $model->content);
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EquipMalfunction model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除故障原因')){
            return $this->redirect(['site/login']);
        }

        $model = $this->findModel($id);
        // $model->delete()
        $model->is_del = 2;
        if($model->save()){
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "故障原因", \backend\models\ManagerLog::DELETE, $model->content);
            return $this->redirect(['index']);
        }

        
    }

    /**
     * Finds the EquipMalfunction model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return EquipMalfunction the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipMalfunction::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
