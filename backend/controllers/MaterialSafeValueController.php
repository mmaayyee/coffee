<?php

namespace backend\controllers;

use backend\models\MaterialSafeValue;
use backend\models\MaterialSafeValueSearch;
use backend\models\ProductMaterialStockAssoc;
use backend\models\ScmMaterialStock;
use backend\models\ScmMaterialType;
use common\helpers\Tools;
use common\models\Building;
use common\models\Equipments;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * MaterialSafeValueController implements the CRUD actions for MaterialSafeValue model.
 */
class MaterialSafeValueController extends Controller
{

    /**
     * Lists all MaterialSafeValue models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('料仓预警值管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new MaterialSafeValueSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MaterialSafeValue model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($equipmentId)
    {
        if (!Yii::$app->user->can('查看料仓预警值')) {
            return $this->redirect(['site/login']);
        }
        if (($model = MaterialSafeValue::findOne(['equipment_id' => $equipmentId])) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new MaterialSafeValue model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加料仓预警值')) {
            return $this->redirect(['site/login']);
        }

        $model = new MaterialSafeValue();
        if ($params = Yii::$app->request->post()) {
            if (empty($params['equipment_id'])) {
                $model->addError('build_id', '楼宇不能为空');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            if (!isset($params['MaterialSafeValue']['safe_value'])) {
                $model->addError('safe_value', '预警值不能为空');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            $isValidate = true;
            foreach ($params['MaterialSafeValue']['safe_value'] as $stock => $value) {
                //验证设备
                $model->material_stock_id = $stock;
                $model->safe_value        = $value;
                $model->equipment_id      = $params['equipment_id'];
                $model->bottom_value      = isset($params['MaterialSafeValue']['bottom_value'][$stock]) ? $params['MaterialSafeValue']['bottom_value'][$stock] : 0;
                if (!$model->validate()) {
                    $buildMessage = $model->getErrors('equipment_id');
                    isset($buildMessage[0]) ? $model->addError('build_id', $buildMessage[0]) : '';

                    return $this->render('create', [
                        'model' => $model,
                    ]);
                    $isValidate = false;
                    break;
                };
            }
            if ($isValidate) {
                //批量插入预警值
                foreach ($params['MaterialSafeValue']['safe_value'] as $stock => $value) {
                    $bottomValue = empty($params['MaterialSafeValue']['bottom_value'][$stock]) ? 0 : $params['MaterialSafeValue']['bottom_value'][$stock];
                    $rows[]      = ['equipment_id' => $params['equipment_id'], 'material_stock_id' => $stock, 'safe_value' => $value, 'bottom_value' => $bottomValue];
                }
                Yii::$app->db->createCommand()->batchInsert(MaterialSafeValue::tableName(), ['equipment_id', 'material_stock_id', 'safe_value', 'bottom_value'], $rows)->execute();
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "料仓预警值管理", \backend\models\ManagerLog::CREATE, "添加料仓预警值");
                return $this->redirect(['view', 'equipmentId' => $params['equipment_id']]);
            }

        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing MaterialSafeValue model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($equipmentId)
    {
        if (!Yii::$app->user->can('编辑料仓预警值')) {
            return $this->redirect(['site/login']);
        }
        if (($model = MaterialSafeValue::findOne(['equipment_id' => $equipmentId])) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $model->build_id = Equipments::findOne($equipmentId)->build->id;

        if ($params = Yii::$app->request->post()) {
            $transaction = Yii::$app->db->beginTransaction();
            //删除该设备的预警值
            $delRow = MaterialSafeValue::deleteAll(['equipment_id' => $equipmentId]);
            if ($delRow < 1) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '删除预警值失败');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }

            //批量插入预警值
            foreach ($params['MaterialSafeValue']['safe_value'] as $stock => $value) {
                $bottomValue = empty($params['MaterialSafeValue']['bottom_value'][$stock]) ? 0 : $params['MaterialSafeValue']['bottom_value'][$stock];
                $rows[]      = ['equipment_id' => $params['equipment_id'], 'material_stock_id' => $stock, 'safe_value' => $value, 'bottom_value' => $bottomValue];
            }
            $result = Yii::$app->db->createCommand()->batchInsert(MaterialSafeValue::tableName(), ['equipment_id', 'material_stock_id', 'safe_value', 'bottom_value'], $rows)->execute();
            if (!$result) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '插入预警值失败');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "料仓预警值管理", \backend\models\ManagerLog::UPDATE, "编辑料仓预警值");
            $transaction->commit();
            return $this->redirect(['view', 'equipmentId' => $equipmentId]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing MaterialSafeValue model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($equipmentId)
    {
        if (!Yii::$app->user->can('删除料仓预警值')) {
            return $this->redirect(['site/login']);
        }
        $rows = MaterialSafeValue::deleteAll(['equipment_id' => $equipmentId]);
        if ($rows < 1) {
            Yii::$app->getSession()->setFlash('error', '删除预警值失败');
        }
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "料仓预警值管理", \backend\models\ManagerLog::DELETE, "删除料仓预警值");
        return $this->redirect(['index']);
    }

    /**
     * Finds the MaterialSafeValue model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MaterialSafeValue the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MaterialSafeValue::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 获取楼宇绑定的设备
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionAjaxGetEquipment()
    {
        if (Yii::$app->request->isAjax) {
            $buildId              = Yii::$app->request->get('build_id');
            $buildModel           = Building::findOne($buildId);
            $data['equipment_id'] = $buildModel->equip->id;
            //获取产品组ID
            $productGroupId = $buildModel->equip->pro_group_id;
            $stocks         = ProductMaterialStockAssoc::getProductGroupMaterialList('material_stock_id', ['pro_group_id' => $productGroupId]);

            $stockMaterial = ProductMaterialStockAssoc::getStockIdOfMaterialType($productGroupId);
            //获取料仓对应的物料分类
            $materialTypeList = ScmMaterialType::getMaterialTypeStock();

            //料仓信息
            $materialArr = ScmMaterialStock::getMaterialStockIdNameArr();
            //获取存在的料仓预警值
            $safeValue = MaterialSafeValue::find()->select('material_stock_id,safe_value,bottom_value')->where(['equipment_id' => $buildModel->equip->id])->asArray()->all();
            foreach ($stocks as $stock) {
                $data['material_stock'][$stock->material_stock_id] = $materialArr[$stock->material_stock_id];
                $data['unit'][$stock->material_stock_id]           = ScmMaterialStock::getField('type', ['id' => $stock->material_stock_id]) === 0 ? '克' : '个';
                $material                                          = isset($materialTypeList[$stockMaterial[$stock->material_stock_id]]) ? $materialTypeList[$stockMaterial[$stock->material_stock_id]] : $materialArr[$stock->material_stock_id];
                $data['material_type'][$stock->material_stock_id]  = $material;
            }
            if ($safeValue) {
                $data['safe_value']   = Tools::map($safeValue, 'material_stock_id', 'safe_value', null, 0);
                $data['bottom_value'] = Tools::map($safeValue, 'material_stock_id', 'bottom_value', null, 0);
            }
            return json_encode($data);
        } else {
            throw new NotFoundHttpException('不是ajax请求');
        }
    }
}
