<?php

namespace backend\controllers;

use backend\models\AppVersionManagement;
use backend\models\ManagerLog;
use backend\models\ScmEquipType;
use backend\models\ScmEquipTypeSearch;
use common\models\AgentsApi;
use common\models\Api;
use common\models\EquipmentTypeParameterApi;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ScmEquipTypeController implements the CRUD actions for ScmEquipType model.
 */
class ScmEquipTypeController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'view', 'delete', 'config', 'synchronous'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all ScmEquipType models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('设备类型管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new ScmEquipTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ScmEquipType model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看设备类型')) {
            return $this->redirect(['site/login']);
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ScmEquipType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加设备类型')) {
            return $this->redirect(['site/login']);
        }
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();

        $model    = new ScmEquipType();
        $postData = Yii::$app->request->post();
        if ($model->load($postData) && $model->validate()) {

            $model->empty_box_weight   = $this->filterEmptyWeight($model->empty_box_weight, $model->matstock);
            $model->readable_attribute = Json::encode($model->readable_attribute);
            $model->stock_num          = count($model->matstock);
            $model->create_time        = time();
            $modelRet                  = $model->save();
            if (!$modelRet) {
                Yii::$app->getSession()->setFlash('error', '对不起，数据添加失败，请检查。');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            //新添数据到app版本号管理中
            $retApp = AppVersionManagement::saveAppVersionManagement($model->id);
            if (!$retApp) {
                Yii::$app->getSession()->setFlash('error', 'app版本表同步失败');
                $transaction->rollBack();
            }
            // 添加操作日志
            ManagerLog::saveLog(Yii::$app->user->id, "设备类型管理", ManagerLog::CREATE, $model->model);
            // 添加设备类型和料仓关联表
            ScmEquipType::scmMatStock($postData, $model, $transaction, "create");
            // 添加设备类型和物料关联表（杯子、杯盖）
            ScmEquipType::scmMaterial($postData, $model, $transaction, "create");

            // 同步设备类型到智能平台
            $equipTypeSync = ScmEquipType::sendEquipTypeSync($model);
            if (!$equipTypeSync) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '设备类型同步到智能平台失败');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            // 同步设备类型和料仓关系到智能平台
            if ($postData['ScmEquipType']['matstock']) {
                $postData['ScmEquipType']['id']            = $model->id;
                $postData['ScmEquipType']['equip_type_id'] = $model->id;
                $equipTypeStockSync                        = Api::matstockSync($postData['ScmEquipType']);
                if (!$equipTypeStockSync) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', '设备类型料仓同步失败');
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
            }
            // 同步设备类型到代理商平台
            $agentsData = ['equip_type_id' => $model->id, 'equip_type_name' => $model->model];

            $agentsSyncRes = AgentsApi::equipTypeSync($agentsData);

            if (!$agentsSyncRes || $agentsSyncRes['error_code'] == 1) {
                $transaction->rollBack();
                $msg = isset($agentsSyncRes['msg']) ? '设备类型同步到代理商系统失败:' . $agentsSyncRes['msg'] : '设备类型同步到代理商系统失败';
                Yii::$app->getSession()->setFlash('error', $msg);
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            //事务通过
            $transaction->commit();
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ScmEquipType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑设备类型')) {
            return $this->redirect(['site/login']);
        }
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();

        $model           = $this->findModel($id);
        $postData        = Yii::$app->request->post();
        $matstockIdArr   = ScmEquipType::getMatstockIdArr($id);
        $model->matstock = $matstockIdArr;
        if ($model->load($postData) && $model->validate()) {
            $model->empty_box_weight   = $this->filterEmptyWeight($model->empty_box_weight, $model->matstock);
            $model->readable_attribute = Json::encode($model->readable_attribute);
            $model->stock_num          = count($model->matstock);
            $model->create_time        = time();
            $modelRet                  = $model->save();
            if (!$modelRet) {
                Yii::$app->getSession()->setFlash('error', '对不起，数据修改失败，请检查。');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
            // 日志写入
            ManagerLog::saveLog(Yii::$app->user->id, "设备类型管理", ManagerLog::UPDATE, $model->model);
            // 设备类型物料 料仓关联表
            ScmEquipType::scmMatStock($postData, $model, $transaction, "update");
            // 设备类型 物料关联表添加
            ScmEquipType::scmMaterial($postData, $model, $transaction, "update");

            // 同步设备类型到智能平台
            $equipTypeSync = ScmEquipType::sendEquipTypeSync($model);
            if (!$equipTypeSync) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '设备类型同步失败');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }

            // 同步设备类型和料仓关系
            if ($postData['ScmEquipType']['matstock']) {
                $postData['ScmEquipType']['id'] = $model->id;
                $equipTypeStockSync             = Api::matstockSync($postData['ScmEquipType']);
                if (!$equipTypeStockSync) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', '设备类型料仓同步失败');
                    return $this->render('update', [
                        'model' => $model,
                    ]);
                }
            }

            // 同步设备类型到代理商平台
            $agentsData    = ['equip_type_id' => $model->id, 'equip_type_name' => $model->model];
            $agentsSyncRes = AgentsApi::equipTypeSync($agentsData);
            if (!$agentsSyncRes || $agentsSyncRes['error_code'] == 1) {
                $transaction->rollBack();
                $msg = isset($agentsSyncRes['msg']) ? '设备类型同步到代理商系统失败:' . $agentsSyncRes['msg'] : '设备类型同步到代理商系统失败';
                Yii::$app->getSession()->setFlash('error', $msg);
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
            //事务通过
            $transaction->commit();
            return $this->redirect(['index']);
        } else {
            $model->readable_attribute = Json::decode($model->readable_attribute, 1);
            $model->empty_box_weight   = Json::decode($model->empty_box_weight);
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    /**
     * 配置设备类型参数值
     * @param  [int]    org_id   地区id
     * @return json
     */
    public function actionConfig()
    {
        if (!Yii::$app->user->can('配置设备分类参数')) {
            return $this->redirect(['site/login']);
        }
        $equipmentTypeId = Yii::$app->request->get('id');
        if (Yii::$app->request->isPost) {
            //接收获取列表条件 org_id
            $param['org_id']            = Yii::$app->request->post('org_id');
            $param['equipments_id']     = 0;
            $param['equipment_type_id'] = $equipmentTypeId;
            $paramValList               = EquipmentTypeParameterApi::getEquipTypeParamValList($param);
            return Json::encode($paramValList['typeParamList']);
        }
        //默认查找全国数据
        $where['org_id']            = 0;
        $where['equipments_id']     = 0;
        $where['equipment_type_id'] = $equipmentTypeId;
        $paramValList               = EquipmentTypeParameterApi::getEquipTypeParamValList($where);
        //获取地区列表
        $orgList = EquipmentTypeParameterApi::getGetOrgList([]);
        return $this->render('config', [
            'orgList'         => $orgList,
            'paramValList'    => $paramValList['typeParamList'],
            'equipmentTypeId' => $equipmentTypeId,
        ]);
    }
    /**
     * 同步设备参数数据
     * @method post
     * @param  [int]    paramter_id           设备类别参数id
     * @param  [int]    equipment_type_id     设备类别id
     * @param  [string] parameter_value       参数设定值
     * @param  [string] org_id                地区id
     */
    public function actionSynchronous()
    {
        if (!Yii::$app->user->can('配置设备分类参数同步')) {
            return $this->redirect(['site/login']);
        }
        $data                = Yii::$app->request->post();
        $sendData['data']    = $data;
        $sendData['user_id'] = (string) Yii::$app->user->id;
        //新增值
        $result = EquipmentTypeParameterApi::updateEquipTypeParamVal($sendData);
        // 新增erp 日志
        if ($result['status'] == 'success') {
            ManagerLog::saveLog(Yii::$app->user->id, "设备类型管理", ManagerLog::UPDATE, $result['logStr']);
        }
        unset($result['logStr']);
        return Json::encode($result);
    }
    /**
     * Deletes an existing ScmEquipType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除设备类型')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);
        $model->delete();
        ManagerLog::saveLog(Yii::$app->user->id, "设备类型管理", ManagerLog::DELETE, $model->model);

        return $this->redirect(['index']);
    }

    /**
     * Finds the ScmEquipType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ScmEquipType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ScmEquipType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 过滤空料盒数值
     * @authro wangxiwen
     * @datetime 2018-06-19
     * return string|json
     */
    protected function filterEmptyWeight($emptyWeightList, $matstockIdArr = [])
    {
        $weightList = [];
        foreach ($emptyWeightList as $stockId => $weight) {
            if ($matstockIdArr && !in_array($stockId, $matstockIdArr)) {
                continue;
            }
            if (is_numeric($weight) && $weight >= 0) {
                $weightList[$stockId] = $weight;
            } else {
                $weightList[$stockId] = 0;
            }
        }
        return Json::encode($weightList);
    }
}
