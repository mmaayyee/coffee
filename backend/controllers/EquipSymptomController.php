<?php

namespace backend\controllers;

use backend\models\EquipSymptom;
use backend\models\EquipSymptomSearch;
use backend\models\ManagerLog;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EquipSymptomController implements the CRUD actions for EquipSymptom model.
 */
class EquipSymptomController extends Controller {

    /**
     * Lists all EquipSymptom models.
     * @return mixed
     */
    public function actionIndex() {
        if (!Yii::$app->user->can('查看故障现象')) {
            return $this->redirect(['site/login']);
        }

        $searchModel  = new EquipSymptomSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new EquipSymptom model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        if (!Yii::$app->user->can('添加故障现象')) {
            return $this->redirect(['site/login']);
        }

        $transaction = Yii::$app->db->beginTransaction();
        $model       = new EquipSymptom();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $logRes = ManagerLog::saveLog(Yii::$app->user->id, "故障现象管理", ManagerLog::CREATE, $model->symptom);
            if ($logRes) {
                $transaction->commit();
            } else {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '删除失败');
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EquipSymptom model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        if (!Yii::$app->user->can('编辑故障现象')) {
            return $this->redirect(['site/login']);
        }

        $transaction = Yii::$app->db->beginTransaction();
        $model       = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $logRes = ManagerLog::saveLog(Yii::$app->user->id, "故障现象管理", ManagerLog::UPDATE, $model->symptom);
            if ($logRes) {
                $transaction->commit();
            } else {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '删除失败');
            }
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EquipSymptom model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        if (!Yii::$app->user->can('删除故障现象')) {
            return $this->redirect(['site/login']);
        }
        $transaction = Yii::$app->db->beginTransaction();
        $model       = $this->findModel($id);
        // $delRes = $model->delete();
        $model->is_del = 2;
        $delRes        = $model->save();
        $logRes        = ManagerLog::saveLog(Yii::$app->user->id, "故障现象管理", ManagerLog::DELETE, $model->symptom);
        if ($delRes !== false && $logRes) {
            $transaction->commit();
        } else {
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('error', '删除失败');
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the EquipSymptom model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EquipSymptom the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = EquipSymptom::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
