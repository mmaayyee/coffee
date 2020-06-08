<?php

namespace backend\controllers;

use backend\models\ManagerLog;
use backend\models\ScmMaterial;
use backend\models\ScmMaterialSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ScmMaterialController implements the CRUD actions for ScmMaterial model.
 */
class ScmMaterialController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view', 'index', 'create', 'update', 'delete', 'batch-delete', 'ajax-material-list'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all ScmMaterial models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('物料信息管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new ScmMaterialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ScmMaterial model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看物料')) {
            return $this->redirect(['site/login']);
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ScmMaterial model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加物料')) {
            return $this->redirect(['site/login']);
        }
        $data  = Yii::$app->request->post('ScmMaterial');
        $model = new ScmMaterial();
        if ($data) {
            $data['weight'] = $data['weight'] ?? 0;
            if ($model->load(['ScmMaterial' => $data]) && $model->validate()) {
                $model->create_time = time();
                // 开启事务
                $transaction = Yii::$app->db->beginTransaction();
                if ($model->save()) {
                    // 同步物料信息到代理商
                    $data['material_id'] = $model->id;
                    $syncRes             = $model::syncMaterial($data);
                    if ($syncRes['error_code'] === 0) {
                        ManagerLog::saveLog(Yii::$app->user->id, "物料", ManagerLog::CREATE, $model->name);
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                }
                $transaction->rollBack();
            }
            return $this->render('create', [
                'model' => $model,
            ]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ScmMaterial model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑物料')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);
        $data  = Yii::$app->request->post('ScmMaterial');
        if ($data) {
            $data['weight'] = $data['weight'] ?? 0;
            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();
            if ($model->load(['ScmMaterial' => $data]) && $model->save()) {
                // 同步物料信息到代理商
                $data['material_id'] = $model->id;
                $syncRes             = $model::syncMaterial($data);
                if ($syncRes['error_code'] === 0) {
                    ManagerLog::saveLog(Yii::$app->user->id, "物料", ManagerLog::UPDATE, $model->name);
                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    Yii::$app->getSession()->setFlash('error', '接口同步失败：' . $syncRes['msg']);
                }
            }
            $transaction->rollBack();
            return $this->render('update', [
                'model' => $model,
            ]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ScmMaterial model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除物料')) {
            return $this->redirect(['site/login']);
        }

        $model = $this->findModel($id);

        if ($model->delete()) {
            ManagerLog::saveLog(Yii::$app->user->id, "物料", ManagerLog::DELETE, $model->name);
            return $this->redirect(['index']);
        }

    }

    public function actionBatchDelete()
    {

        if (!Yii::$app->user->can('删除物料')) {
            return $this->redirect(['site/login']);
        }

        if (Yii::$app->request->isAjax) {
            $keys = Yii::$app->request->post("keys");

            $model = new ScmMaterial();
            if ($model->deleteAll(['id' => $keys])) {
                return json_encode(true);
            } else {
                return json_encode(false);
            }
        }

    }

    /**
     * Finds the ScmMaterial model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ScmMaterial the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ScmMaterial::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAjaxMaterialList($stockId, $equipTypeId = '')
    {
        // 获取料仓放入的物料列表
        echo ScmMaterial::getMaterialFromMaterialStock($stockId, $equipTypeId);
    }
}
