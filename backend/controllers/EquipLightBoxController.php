<?php

namespace backend\controllers;

use backend\models\EquipLightBox;
use backend\models\EquipLightBoxSearch;
use backend\models\ManagerLog;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EquipLightBoxController implements the CRUD actions for EquipLightBox model.
 */
class EquipLightBoxController extends Controller
{
    /**
     * Lists all EquipLightBox models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('查看灯箱')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new EquipLightBoxSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new EquipLightBox model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加灯箱')) {
            return $this->redirect(['site/login']);
        }
        $model = new EquipLightBox();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            ManagerLog::saveLog(Yii::$app->user->id, "灯箱管理", ManagerLog::CREATE, $model->light_box_name);
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }

    }

    /**
     * Updates an existing EquipLightBox model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑灯箱')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            ManagerLog::saveLog(Yii::$app->user->id, "灯箱管理", ManagerLog::UPDATE, $model->light_box_name);
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EquipLightBox model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除灯箱')) {
            return $this->redirect(['site/login']);
        }
        $model         = $this->findModel($id);
        $model->is_del = EquipLightBox::DEL_YES;
        if (!$model->save()) {
            echo "<Pre/>";
            print_r($model);die;
            Yii::$app->getSession()->setFlash('error', '删除失败');
        }
        ManagerLog::saveLog(Yii::$app->user->id, "灯箱管理", ManagerLog::DELETE, $model->light_box_name);
        return $this->redirect(['index']);
    }

    /**
     * Finds the EquipLightBox model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EquipLightBox the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipLightBox::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
