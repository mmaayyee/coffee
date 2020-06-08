<?php

namespace backend\controllers;

use backend\models\ManagerLog;
use backend\models\ScmMaterialType;
use backend\models\ScmMaterialTypeSearch;
use common\models\AgentsApi;
use common\models\Api;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ScmMaterialTypeController implements the CRUD actions for ScmMaterialType model.
 */
class ScmMaterialTypeController extends Controller
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
     * Lists all ScmMaterialType models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('查看物料分类')) {
            return $this->redirect(['site/login']);
        }

        $searchModel  = new ScmMaterialTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new ScmMaterialType model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加物料分类')) {
            return $this->redirect(['site/login']);
        }
        $transaction = Yii::$app->db->beginTransaction();
        $model       = new ScmMaterialType();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            //添加操作日志
            $logRes = ManagerLog::saveLog(Yii::$app->user->id, "物料分类管理", ManagerLog::CREATE, $model->material_type_name);
            if (!$logRes) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '操作日志添加失败');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            // 同步物料分类
            $syncData[$model->id] = $model->material_type_name;
            $syncRes              = Api::materialTypeSync($syncData);
            if (!$syncRes) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '物料分类同步智能平台失败');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            // 同步物料分类到代理商平台
            $data          = Yii::$app->request->post('ScmMaterialType');
            $data['id']    = $model->id;
            $agentsSyncRes = AgentsApi::materialTypeSync($data);
            if (!$agentsSyncRes || $agentsSyncRes['error_code'] == 1) {
                $transaction->rollBack();
                $msg = isset($agentsSyncRes['msg']) ? '物料分类同步代理商系统失败:' . $agentsSyncRes['msg'] : '物料分类同步代理商系统失败';
                Yii::$app->getSession()->setFlash('error', $msg);
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            $transaction->commit();
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ScmMaterialType model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑物料分类')) {
            return $this->redirect(['site/login']);
        }
        $transaction = Yii::$app->db->beginTransaction();
        $model       = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            //添加操作日志
            $logRes = ManagerLog::saveLog(Yii::$app->user->id, "物料分类管理", ManagerLog::UPDATE, $model->material_type_name);
            if (!$logRes) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '操作日志添加失败');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }

            // 同步物料分类
            $syncData[$model->id] = $model->material_type_name;
            $syncRes              = Api::materialTypeSync($syncData);
            if (!$syncRes) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '物料分类同步失败');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }

            // 同步物料分类到代理商平台
            $data          = Yii::$app->request->post('ScmMaterialType');
            $data['id']    = $model->id;
            $agentsSyncRes = AgentsApi::materialTypeSync($data);
            if (!$agentsSyncRes || $agentsSyncRes['error_code'] == 1) {
                $transaction->rollBack();
                $msg = isset($agentsSyncRes['msg']) ? '物料分类同步代理商系统失败:' . $agentsSyncRes['msg'] : '物料分类同步代理商系统失败';
                Yii::$app->getSession()->setFlash('error', $msg);
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            $transaction->commit();
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ScmMaterialType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除物料分类')) {
            return $this->redirect(['site/login']);
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ScmMaterialType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ScmMaterialType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ScmMaterialType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
