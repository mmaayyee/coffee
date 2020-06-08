<?php

namespace backend\controllers;

use Yii;
use backend\models\ScmSupplier;
use backend\models\ScmSupplierSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ScmSupplierController implements the CRUD actions for ScmSupplier model.
 */
class ScmSupplierController extends Controller
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
     * Lists all ScmSupplier models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('供应商管理')){
            return $this->redirect(['site/login']);
        }

        $searchModel = new ScmSupplierSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ScmSupplier model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看供应商')){
            return $this->redirect(['site/login']);
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ScmSupplier model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('创建供应商')){
            return $this->redirect(['site/login']);
        }

        $model = new ScmSupplier();
        $param = Yii::$app->request->post();
        $model->create_time = time();
        if ($model->load($param) && $model->validate()) {
            if ($model->type == ScmSupplier::WATER) {
                $orgId  = '-'.implode('-', $param['ScmSupplier']['org_id']).'-';
                $model->org_id = $orgId;
            }
            if ($model->save()) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "供应商", \backend\models\ManagerLog::CREATE, $model->username);
                return $this->redirect(['view', 'id' => $model->id]);
            }else{
                echo "添加失败！";exit();
            }
            
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ScmSupplier model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑供应商')){
            return $this->redirect(['site/login']);
        }

        $model = $this->findModel($id);
        $param = Yii::$app->request->post();
        if ($model->load($param) && $model->validate()) {
            if ($model->type == ScmSupplier::WATER) {
                $orgId  = '-'.implode('-', $param['ScmSupplier']['org_id']).'-';
                $model->org_id = $orgId;
            } else {
                $model->org_id = 0;
            }
            if ($model->save()) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "供应商", \backend\models\ManagerLog::UPDATE, $model->username);
                return $this->redirect(['view', 'id' => $model->id]);
            }
            
        } else {
            if ($model->org_id) {
                $model->org_id  =   explode('-', trim($model->org_id, '-'));   
            }
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ScmSupplier model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除供应商')){
            return $this->redirect(['site/login']);
        }

        $model = $this->findModel($id);

        if($model->delete()){
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "供应商", \backend\models\ManagerLog::DELETE, $model->username);
            return $this->redirect(['index']);
        }

        
    }

    /**
     * Finds the ScmSupplier model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ScmSupplier the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ScmSupplier::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
