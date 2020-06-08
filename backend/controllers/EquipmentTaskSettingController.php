<?php

namespace backend\controllers;

use Yii;
use backend\models\EquipmentTaskSetting;
use backend\models\EquipmentTaskSettingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Manager;

/**
 * EquipmentTaskSettingController implements the CRUD actions for EquipmentTaskSetting model.
 */
class EquipmentTaskSettingController extends Controller
{
    /**
     * Lists all EquipmentTaskSetting models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('公司设备类型日常任务管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel = new EquipmentTaskSettingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EquipmentTaskSetting model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看公司设备类型日常任务')) {
            return $this->redirect(['site/login']);
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new EquipmentTaskSetting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加公司设备类型日常任务')) {
            return $this->redirect(['site/login']);
        }
        $model = new EquipmentTaskSetting();
        if ($param = Yii::$app->request->post()) {
            $model->equipment_type_id = $param['EquipmentTaskSetting']['equipment_type_id'];
            $model->organization_id = isset($param['EquipmentTaskSetting']['organization_id']) ? $param['EquipmentTaskSetting']['organization_id'] : Manager::getManagerBranchID();
            $model->cleaning_cycle = $param['EquipmentTaskSetting']['cleaning_cycle'];
            $model->error_value = $param['EquipmentTaskSetting']['error_value'];
            $model->day_num = $param['EquipmentTaskSetting']['day_num'];
            $refuelCycle = [];
            foreach ($param['EquipmentTaskSetting']['material_type'] as $key => $material_type) {
                $refuelCycle[$key]['material_type'] = $material_type;
                $refuelCycle[$key]['refuel_cycle'] = $param['EquipmentTaskSetting']['refuel_cycle_days'][$key];
            }
            $model->refuel_cycle = json_encode($refuelCycle);

            if ($model->validate() && $model->save()) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "公司设备类型日常任务管理", \backend\models\ManagerLog::CREATE, "添加公司设备类型日常任务");
                return $this->redirect(['view', 'id' => $model->id]);
            }else{
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EquipmentTaskSetting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑公司设备类型日常任务')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);

        if ($param = Yii::$app->request->post()) {
            $model->isNewRecord = false;
            $model->equipment_type_id = $param['EquipmentTaskSetting']['equipment_type_id'];
            $model->organization_id = isset($param['EquipmentTaskSetting']['organization_id']) ? $param['EquipmentTaskSetting']['organization_id'] : Manager::getManagerBranchID();
            $model->cleaning_cycle = $param['EquipmentTaskSetting']['cleaning_cycle'];
                $model->error_value = $param['EquipmentTaskSetting']['error_value'];
            $model->day_num = $param['EquipmentTaskSetting']['day_num'];
            $refuelCycle = [];

            foreach ($param['EquipmentTaskSetting']['material_type'] as $key => $material_type) {
                $refuelCycle[$key]['material_type'] = $material_type;
                $refuelCycle[$key]['refuel_cycle'] = $param['EquipmentTaskSetting']['refuel_cycle_days'][$key];
            }
            $model->refuel_cycle = json_encode($refuelCycle);

            if ($model->validate() && $model->save()) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "公司设备类型日常任务管理", \backend\models\ManagerLog::UPDATE, "编辑公司设备类型日常任务");
                return $this->redirect(['view', 'id' => $model->id]);
            }else{
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EquipmentTaskSetting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除公司设备类型日常任务')) {
            return $this->redirect(['site/login']);
        }
        $this->findModel($id)->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "公司设备类型日常任务管理", \backend\models\ManagerLog::DELETE, "删除公司设备类型日常任务");

        return $this->redirect(['index']);
    }

    /**
     * Finds the EquipmentTaskSetting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EquipmentTaskSetting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipmentTaskSetting::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
