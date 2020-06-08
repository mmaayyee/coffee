<?php

namespace backend\controllers;

use backend\models\BuildingTaskSetting;
use backend\models\ChangeProduct;
use backend\models\EquipDelivery;
use backend\models\EquipRfidCardAssoc;
use backend\models\EquipSurplusMaterial;
use backend\models\EquipSyncSearch;
use backend\models\FormulaAdjustmentLogSearch;
use backend\models\Manager;
use backend\models\ManagerLog;
use backend\models\MaterialSafeValue;
use backend\models\Organization;
use backend\models\ScmEquipType;
use backend\models\ScmMaterialStock;
use backend\models\ScmWarehouse;
use common\models\AgentsApi;
use common\models\Api;
use common\models\Building;
use common\models\EquipBuildingAssoc;
use common\models\EquipLightBoxRepair;
use common\models\Equipments;
use common\models\EquipmentsSearch;
use common\models\EquipmentTypeParameterApi;
use common\models\EquipTask;
use PHPExcel;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EquipmentsController implements the CRUD actions for Equipments model.
 */
class EquipmentsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'view', 'delete', 'equip-lock-status', 'equip-weight', 'un-equip-weight', 'equip-unlock-status', 'map', 'ajax-map-all', 'equip-warehouse', 'building-bind', 'pro-group-list', 'scrapped', 'un-bind', 'equip-sync', 'update-equip-run-status', 'change-light-box', 'bind', 'get-warehouse', 'excel-export', 'config', 'synchronous', 'formula-adjustment', 'formula-adjustment-log', 'open'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Equipments models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('设备信息管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new EquipmentsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $orgList      = Organization::getOrgIdTypeList();
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'orgList'      => $orgList,
            'param'        => Yii::$app->request->queryParams,
        ]);
    }

    /**
     * Displays a single Equipments model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看设备信息')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);

        //获取日常任务设置
        $model = BuildingTaskSetting::getEquipmentTaskSetting($model);

        $supplierArr[''] = '请选择';
        //获取当前用户的Id 及信息
        $managerId = Yii::$app->user->id;
        $branch    = Manager::find()->where(['id' => $managerId])->one()->branch;
        //料仓剩余物料 array()
        $equipSurpluseMaterialList = Equipments::equipmentProductGroupStock($model->equip_code);

        // 将数组转化为对象
        $dataProvider = EquipSurplusMaterial::equipSurplusMaterialSerch($equipSurpluseMaterialList, $model->equip_code);
        // $searchModel  = new EquipSurplusMaterialSearch();
        // $dataProvider = $searchModel->searchByEquipCode(Yii::$app->request->queryParams, $model->equip_code);
        return $this->render('view', [
            'model'               => $model,
            'lightBoxRepairModel' => new EquipLightBoxRepair(),
            'scmWarehouse'        => new ScmWarehouse(),
            'org_id'              => $branch,
            'dataProvider'        => $dataProvider,
        ]);
    }

    /**
     * Creates a new Equipments model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加设备信息')) {
            return $this->redirect(['site/login']);
        }
        //规则 ：ID 0 + 批次 00 + 数目 000
        //获取当前用户的Id 及信息
        $managerId = Yii::$app->user->id;
        $branch    = Manager::find()->where(['id' => $managerId])->one()->branch;
        $model     = new Equipments();
        $postData  = Yii::$app->request->post();
        // 添加场景
        $model->scenario = 'create';
        //自动累加配 批次数量
        $equipModel = Equipments::find()->orderBy('batch DESC')->one();
        if ($equipModel) {
            $batch = $equipModel->batch + 1;
        } else {
            $batch = 0;
        }

        if ($postData) {
            $param = $postData['Equipments'];
            // 验证设备添加数量是否大于0
            if (!$param['number'] || $param['number'] < 1) {
                Yii::$app->getSession()->setFlash('error', '添加数量不能小于1');
                return $this->render('create', [
                    'model'  => $model,
                    'branch' => $branch,
                ]);
            }
            $retEquipCode = $model->getEeqipCode($param['equip_type_id'], $batch);
            $transaction  = Yii::$app->db->beginTransaction();
            $ret          = true;
            $data         = [];
            for ($i = 0; $i < $param['number']; $i++) {
                $_model                = clone $model;
                $_model->create_time   = $_model->wash_time   = $_model->refuel_time   = time();
                $_model->equip_type_id = $param['equip_type_id'];
                $_model->warehouse_id  = $param['warehouse_id'];
                $_model->org_id        = (isset($param['org_id']) && $param['org_id']) ? $param['org_id'] : $branch;

                if (strlen($i) == 1) {
                    $equipCode = $retEquipCode . "0000" . $i;
                } else if (strlen($i) == 2) {
                    $equipCode = $retEquipCode . "000" . $i;
                } else if (strlen($i) == 3) {
                    $equipCode = $retEquipCode . "00" . $i;
                } else if (strlen($i) == 4) {
                    $equipCode = $retEquipCode . "0" . $i;
                }
                $_model->number     = 1;
                $_model->equip_code = $equipCode;
                $_model->batch      = $batch;
                if (!$_model->save()) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', '接口同步失败');
                    return $this->render('create', [
                        'model'  => $model,
                        'branch' => $branch,
                    ]);
                }

                // 添加设备配方
                // $formulaRet = Api::getFormulaAdjustment('formula-adjustment-api/formula-adjustment-create.html?equipment_code=' . $equipCode);
                // if (!$formulaRet) {
                //     $transaction->rollBack();
                //     Yii::$app->getSession()->setFlash('error', '设备配方添加失败');
                //     return $this->render('create', [
                //         'model'  => $model,
                //         'branch' => $branch,
                //     ]);
                // }
                // $formulaData[$i] = $formulaRet;
                $data[$i] = [
                    'equip_type_id'        => $_model->equip_type_id,
                    'organization_id'      => $_model->org_id,
                    'equipment_code'       => $_model->equip_code,
                    'pro_group_id'         => 0,
                    'equip_operation_time' => "0",
                    'equipment_status'     => 1,
                    'operation_status'     => 5,
                    'is_lock'              => 2,
                    'factory_code'         => '',
                ];
                $equipmentCodeArray[] = $_model->equip_code;
            }
            $logRes = ManagerLog::saveLog(Yii::$app->user->id, "设备信息管理", ManagerLog::CREATE, "添加批次:" . $_model->batch);
            if ($logRes) {
                // 同步设备数据
                $equipSyncRes = Api::equipmentSync($data);
                if (!$equipSyncRes) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', '接口同步失败');
                    return $this->render('create', [
                        'model'  => $model,
                        'branch' => $branch,
                    ]);
                }
                foreach ($equipmentCodeArray as $equipCode) {
                    //添加设备配方
                    $formulaRet = Api::getFormulaAdjustment('formula-adjustment-api/formula-adjustment-create.html?equipment_code=' . $equipCode);
                    if (!$formulaRet) {
                        $transaction->rollBack();
                        Yii::$app->getSession()->setFlash('error', '设备配方添加失败');
                        return $this->render('create', [
                            'model'  => $model,
                            'branch' => $branch,
                        ]);
                    }
                }
                $transaction->commit();
                return $this->redirect(['index']);
            } else {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '添加失败');
                return $this->render('create', [
                    'model'  => $model,
                    'branch' => $branch,
                ]);
            }
        } else {
            return $this->render('create', [
                'model'  => $model,
                'branch' => $branch,
            ]);
        }
    }

    /**
     * Updates an existing Equipments model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑设备信息')) {
            return $this->redirect(['site/login']);
        }
        $model             = $this->findModel($id);
        $oldProductGroupId = $model->pro_group_id;
        $transaction       = Yii::$app->db->beginTransaction();

        $params = Yii::$app->request->post();
        if (isset($params['Equipments']['card_number']) && !empty($params['Equipments']['card_number'])) {
            $equipId = Equipments::getField('id', ['and', 'id !=' . $id, 'card_number="' . $params['Equipments']['card_number'] . '"']);
            if ($equipId) {
                Yii::$app->getSession()->setFlash('error', '流量卡号已存在,请更换流量卡号');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
        if ($model->load($params) && $model->save()) {
            // 添加操作日志
            $logRes = ManagerLog::saveLog(Yii::$app->user->id, "设备信息管理", ManagerLog::UPDATE, "修改设备信息");
            if (!$logRes) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '操作日志添加失败');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
            // 同步到智能平台
            $equipSyncRes = Equipments::syncEquip($model);
            if (!$equipSyncRes) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '接口同步失败');
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
            // 同步产品组到代理商平台
            $orgModel = Organization::findModel($model->org_id);
            if ($orgModel) {
                if ($orgModel->organization_type == Organization::TYPE_AGENTS) {
                    $agentsData   = ['equipment_code' => $model->equip_code, 'group_id' => $model->pro_group_id, 'equipment_id' => $model->equip_type_id];
                    $equipSyncRes = AgentsApi::updateEquipment($agentsData);
                }
            }

            if (intval($model->pro_group_id) !== $oldProductGroupId && $oldProductGroupId !== 0 && intval($model->pro_group_id) !== 0) {
                //保存产品组修改记录
                $changeProductArray = ['equip_id' => $id, 'build_id' => $model->build_id, 'last_product_id' => $oldProductGroupId, 'present_product_id' => intval($model->pro_group_id), 'created_user' => Yii::$app->user->id];
                $changeResult       = ChangeProduct::addProductGroupChangeRecord($changeProductArray);
                if (!$changeResult) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', '更改产品组记录失败');
                    return $this->render('update', [
                        'model' => $model,
                    ]);
                }
            }

            $transaction->commit();
            return $this->redirect(['index', 'id' => $model->id]);
        } else {
            //若已绑定（即楼宇不为空，is_unbinding>0 解绑过），则禁止修改
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    /**
     * 配置设备类型参数值
     * @param  [int]    equipmentsCode   设备编号
     * @return json
     */
    public function actionConfig($equipmentsCode)
    {
        if (!Yii::$app->user->can('设备参数配置')) {
            return $this->redirect(['site/login']);
        }
        $param['equipments_code'] = $equipmentsCode;
        $dataList                 = EquipmentTypeParameterApi::getEquipTypeParamValList($param);
        return $this->render('config', [
            'paramValList'   => $dataList['typeParamList'],
            'equipmentsCode' => $equipmentsCode,
        ]);
    }
    /**
     * 同步设备参数数据
     * @method post
     * @param  [string] equipmentsCode        设备编号
     * @param  [string]  dataList[
     *         [int]    paramter_id           设备类别参数id
     *         [int]    equipment_type_id     设备类别id
     *         [string] parameter_value       参数设定值
     *         [string] org_id                地区id
     *                   ]
     */
    public function actionSynchronous()
    {
        $data                     = Yii::$app->request->post();
        $data['user_id']          = Yii::$app->user->identity->userid;
        $param['equipments_code'] = $data['equipments_code'];
        $historyDataList          = EquipmentTypeParameterApi::getEquipTypeParamValList($param);
        //新增值
        $result = EquipmentTypeParameterApi::updateEquipParamVal($data);
        if ($result['status'] == 'success') {
            $newDataList = array_column($data['dataList'], null, 'parameter_id');
            foreach ($historyDataList['typeParamList'] as $historyDate) {
                if (isset($newDataList[$historyDate['id']])) {
                    $historyValue = $historyDate['parameter_value'] == '' ? '空' : $historyDate['parameter_value'];
                    if ($historyDate['parameter_value'] != $newDataList[$historyDate['id']]['parameter_value']) {
                        $strLog = '设备编号为'
                            . $data['equipments_code']
                            . ' 配置参数' . $historyDate['parameter_name'] . '的值由 '
                            . $historyValue
                            . ' 修改为 ' . $newDataList[$historyDate['id']]['parameter_value'];
                        ManagerLog::saveLog(Yii::$app->user->id,
                            '设备信息管理',
                            ManagerLog::UPDATE,
                            $strLog
                        );
                    }
                }
            }
        }
        return json_encode($result);
    }
    /**
     *  解除绑定
     *  (注：此处把原来的get传参变为post，
     *  原有的$id = $param["ScmWarehouse"]['equip_id']
     *  @param $id
     *  @return index
     **/
    public function actionUnBind()
    {
        $param = Yii::$app->request->post();
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        //获取楼宇id 删除支付渠道策略楼宇
        $equipdiscountBuildingAssocDeleteModel = Equipments::find()->where(['id' => $param["ScmWarehouse"]['equip_id']])->one();
        //处理解绑操作
        $res        = Equipments::getUnBind($param["ScmWarehouse"]['equip_id'], $param["ScmWarehouse"]['name']);
        $equipModel = Equipments::find()->where(['id' => $param["ScmWarehouse"]['equip_id']])->one();

        if ($res) {
            $buildModel = Building::findOne(['id' => $equipdiscountBuildingAssocDeleteModel->build_id]);
            Api::discountBuildingAssocDeleteByEquip(array('build_number' => $buildModel->build_number));
            $logRes = ManagerLog::saveLog(Yii::$app->user->id, "设备信息管理", ManagerLog::UPDATE, "操作：设备解绑操作成功，操作人：" . Yii::$app->user->identity->username);
            if (!$logRes) {
                Yii::$app->getSession()->setFlash("error", "设备解绑操作添加人失败");
                return $this->redirect($_SERVER['HTTP_REFERER']);
            }

            // 进行门禁卡管理表中删除已解绑的设备
            if ($equipModel && $equipModel->equip_code) {
                //查询门禁卡中是否有这个设备，如果有，则删除，否则，不删除。
                $rfidAssocObj = EquipRfidCardAssoc::find()->where(['equip_code' => $equipModel->equip_code])->all();
                if ($rfidAssocObj) {
                    $retDeleteRfid = EquipRfidCardAssoc::unBindDeleteRfidAssoc($equipModel->equip_code);
                    if (!$retDeleteRfid) {
                        Yii::$app->getSession()->setFlash("error", "删除门禁卡中设备失败");
                        return $this->redirect($_SERVER['HTTP_REFERER']);
                    }
                }
            }
            // 删除该设备的灯带方案
            Api::getDelLightProgramAssocByBuildNumber($buildModel->build_number);
            //删除该设备的预警值设置
            MaterialSafeValue::clearEquipmentSaveValue($param["ScmWarehouse"]['equip_id']);
            $transaction->commit();
        } else {
            $transaction->rollBack();
        }
        return $this->redirect(['view', 'id' => $param["ScmWarehouse"]['equip_id']]);
    }

    /**
     *  修改设备状态
     *
     **/
    public function actionUpdateEquipRunStatus()
    {
        $transaction                  = Yii::$app->db->beginTransaction();
        $equipId                      = Yii::$app->request->post('equipId');
        $operationStatus              = Yii::$app->request->post('operationStatus');
        $equipModel                   = Equipments::findOne($equipId);
        $equipModel->operation_status = $operationStatus;

        if ($operationStatus != Equipments::NO_OPERATION) {
            $equipModel->equip_operation_time = $equipModel->equip_operation_time > 0 ? $equipModel->equip_operation_time : time();
        }
        if ($equipModel->save()) {
            $logRes = ManagerLog::saveLog(Yii::$app->user->id, "设备信息管理", ManagerLog::UPDATE, Yii::$app->user->identity->realname . "将" . $equipModel->build->name . "的设备运营状态改为" . $equipModel::operationStatusByConditionsArray(2)[$equipModel->operation_status]);
            if (!$logRes) {
                echo "false";
                die;
            }
            // 同步设备到只能平台
            $equipSyncRes = Equipments::syncEquip($equipModel);
            if (!$equipSyncRes) {
                $transaction->rollBack();
                echo "false";
                die;
            }
            // 同步运营状态到代理商平台
            $orgModel = Organization::findModel($equipModel->org_id);
            if ($orgModel) {
                if ($orgModel->organization_type == Organization::TYPE_AGENTS) {
                    $agentsData   = ['equipment_code' => $equipModel->equip_code, 'operate_status' => $equipModel->operation_status, 'operated_at' => $equipModel->equip_operation_time];
                    $equipSyncRes = AgentsApi::updateEquipment($agentsData);
                }
            }
            $transaction->commit();
            echo "true";
        } else {
            $transaction->rollBack();
            echo "false";
        }
    }

    /**
     * Finds the Equipments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Equipments the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Equipments::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     *   锁定设备状态
     **/
    public function actionEquipLockStatus()
    {
        $equipId = Yii::$app->request->post('equipId');
        $isLock  = Yii::$app->request->post('isLock');
        $model   = Equipments::findOne($equipId);

        $model->is_lock = $isLock;
        // 如果本公司操作则将锁定类型改为本公司，此种状态代理商无法解锁
        $model->lock_type = Equipments::COMPANYLOCKED;

        $transaction = Yii::$app->db->beginTransaction();
        if ($model->save(false)) {
            // 同步锁定操作 coffee后台
            $lock     = $isLock - 1;
            $syncData = ['equip_code' => $model->equip_code, 'lock' => $lock];
            $syncRes  = Api::equipmentLock($syncData);
            if (!$syncRes) {
                $transaction->rollBack();
                echo "同步智能平台失败";
                die;
            }
            $orgModel = Organization::findModel($model->org_id);
            if ($orgModel) {
                if ($orgModel->organization_type == Organization::TYPE_AGENTS && ($isLock == 1 || $isLock == 2)) {
                    //同步锁定(解锁)操作 代理商后台
                    $agentsSyncData = '&equip_code=' . $model->equip_code . '&is_lock=' . $isLock;
                    $agentsSyncRes  = AgentsApi::agentsEquipisLock($agentsSyncData);
                    if (!$agentsSyncRes || $agentsSyncRes['error_code'] == 1) {
                        $transaction->rollBack();
                        echo isset($agentsSyncRes['msg']) ? $agentsSyncRes['msg'] : "同步代理商失败";
                        die;
                    }
                }
            }
            $transaction->commit();
            $lockName = $model::$lock[$model->is_lock];
            ManagerLog::saveLog(Yii::$app->user->id, "设备信息管理", ManagerLog::UPDATE, Yii::$app->user->identity->realname . "将" . $model->build->name . "的设备锁定状态改为" . $lockName);
            echo "true";
        }
    }

    /**
     *  地图模式 (腾讯)
     *  @return map
     */
    public function actionMap()
    {
        // 获取当前管理员所在分公司id
        $managerOrgId = Manager::getManagerBranchID();
        // 按城市名称搜索楼宇
        $city = Yii::$app->request->get('city', '北京市');
        // 已投放的楼宇
        $buildWhere = ['build_status' => Building::SERVED];
        // 如果有build_id 有值则是设备详情中传值。
        $buildId = Yii::$app->request->get('build_id');
        // 根据楼宇id获取城市名称（从设备列表页连接进入的）
        if ($buildId) {
            $buildOneInfo = Building::find()->where(['id' => $buildId])->one();
            if (in_array($buildOneInfo['province'], Building::$cities)) {
                $city = $buildOneInfo['province'];
            } else {
                $city = $buildOneInfo['city'];
            }
        }
        // 如果分公司id存在则只显示该分公司下的城市列表
        if ($managerOrgId > 1) {
            // 获取该分公司下的城市
            $city = Organization::getField('org_city', ['org_id' => $managerOrgId]);
            // 楼宇搜索添加分公司搜索条件
            $buildWhere['org_id'] = $managerOrgId;

        }
        if (in_array($city, Building::$cities)) {
            $buildWhere['province'] = $city;
        } else {
            $buildWhere['city'] = $city;
        }
        $buildListObj = Building::getBuildObj('id, longitude, latitude, province, city, area, address, name', $buildWhere);
        //查询已投放的城市 数组
        $buildCity = Equipments::getPutCityArr($managerOrgId);
        //组件关联查询设备状态
        $buildEquipAssocArr = Equipments::getBuildEquipAssocArr($buildListObj);

        //获取楼宇数组
        $buildIdNameOption = '<option value="">请选择</option>';
        foreach ($buildEquipAssocArr as $v) {
            $buildIdNameOption .= '<option lng="' . $v['longitude'] . '" lat="' . $v['latitude'] . '" value="' . $v['id'] . '">' . $v["name"] . '</option>';
        }
        return $this->render('equip-map', [
            'buildIdNameOption' => $buildIdNameOption,
            'build_list'        => $buildEquipAssocArr,
            'buildCity'         => $buildCity,
            'city'              => $city,
            'build_id'          => $buildId,
        ]);
    }

    public function actionEquipWarehouse()
    {
        if (Yii::$app->request->isAjax) {
            $orgId = Yii::$app->request->post("org_id");
            // 分库查询条件
            $wareHouseWhere['use'] = ScmWarehouse::EQUIP_USE;
            if ($orgId != 1) {
                $wareHouseWhere['organization_id'] = $orgId;
            }
            $warehouseArr = ScmWarehouse::getWarehouseNameArray($wareHouseWhere);
            if (!$warehouseArr) {
                return '<option value="">请先添加分库</option>';
            }
            $returnHtml = '';
            foreach ($warehouseArr as $warehouseId => $warehouseName) {
                $returnHtml .= "<option value='" . $warehouseId . "'>" . $warehouseName . "</option>";
            }
            return $returnHtml;
        }
    }

    /**
     *  设备绑定操作
     *  @param $id
     **/
    public function actionBuildingBind($id)
    {
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        if (Yii::$app->request->get()) {
            $param                        = Yii::$app->request->get()['Equipments'];
            $equipModel                   = Equipments::findOne($id);
            $equipModel->operation_status = !empty($param['operation_status']) ? $param['operation_status'] : '0';
            $equipModel->equipment_status = !empty($param['equipment_status']) ? $param['equipment_status'] : "0";
            $equipModel->pro_group_id     = !empty($param['pro_group_id']) ? $param['pro_group_id'] : '0';
            $retEquip                     = $equipModel->save();
            if (!$retEquip) {
                echo "修改设备失败";
                $transaction->rollBack();exit();
            }

            $assocModel = EquipBuildingAssoc::find()->where(['build_id' => $param['building']])->asArray()->one();

            //插入设备楼宇关联表中
            $equipBuildingAssocModel           = new EquipBuildingAssoc();
            $equipBuildingAssocModel->equip_id = $id;
            $equipBuildingAssocModel->build_id = $param['building'];
            $retAssoc                          = $equipBuildingAssocModel->save();
            if (!$retAssoc) {
                echo "设备，楼宇关联表虚拟关联失败";
                $transaction->rollBack();exit();
            }
        }

        // 修改楼宇表的状态
        $buildModel               = Building::findOne($param['building']);
        $buildModel->build_status = Building::LAUNCHTASK;
        $retBuild                 = $buildModel->save();
        if (!$retBuild) {
            echo "楼宇表状态修改失败。";
            $transaction->rollBack();exit();
        }
        // 添加到设备任务库中(添加一条设备投放验收任务)
        $equipDeliveModel = EquipDelivery::find()->where(['build_id' => $param['building']])->one();
        $buildType        = Building::getBuildingDetail("build_type", ["id" => $equipDeliveModel->build_id])['build_type'];
        $buildTypeName    = Building::getBuildTypeArray()[$buildType];
        $content          = '楼宇类别：' . $buildTypeName . ' ' . '发起时间：' . date("Y-m-d", $equipDeliveModel->create_time) . ' ' . '销售负责人：' . $equipDeliveModel->sales_person . ' ' . '设备数量：' . $equipDeliveModel->delivery_number . '台';
        //插入操作
        $equipTaskModel              = new EquipTask();
        $equipTaskModel->build_id    = $equipDeliveModel->build_id;
        $equipTaskModel->task_type   = EquipTask::TRAFFICKING_TASK;
        $equipTaskModel->relevant_id = $equipDeliveModel->Id;
        $equipTaskModel->content     = $content;
        $equipTaskModel->create_user = Yii::$app->user->identity->realname;
        $equipTaskModel->create_time = time();
        $retTask                     = $equipTaskModel->save();
        if (!$retTask) {
            echo "添加投放验收任务错误";
            $transaction->rollBack();exit();
        }

        //事务通过
        $transaction->commit();
        //跳转到详情页
        return $this->redirect(['equipments/view', 'id' => $id]);

    }

    /**
     * 报废操作
     * @return [type] [description]
     */
    public function actionScrapped()
    {
        $id    = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if (!$model->build_id) {
            $model->equipment_status = Equipments::MALFUNCTION;
            $model->operation_status = Equipments::SCRAPPED;
            if (!$model->save()) {
                Yii::$app->getSession()->setFlash('error', '操作失败');
            }

            //添加操作日志
            $logRes = ManagerLog::saveLog(Yii::$app->user->id, "设备信息管理", ManagerLog::UPDATE, "操作：设备报废操作成功，操作人：" . Yii::$app->user->identity->username);
            if (!$logRes) {
                Yii::$app->getSession()->setFlash('error', '操作日志添加失败');
                return $this->redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            Yii::$app->getSession()->setFlash('error', '只有解绑后的设备才能执行报废操作');
        }
        return $this->redirect(['equipments/view', 'id' => $id]);
    }

    /**
     * 权重操作
     * @return ture/ false
     */
    public function actionEquipWeight()
    {
        $equipWeightId = $_POST['equipWeightId'];
        $model         = Equipments::findOne($equipWeightId);
        $model->weight = time();
        if ($model->save(false)) {
            echo "true";
        } else {
            echo 'false';
        }
    }

    /**
     * 取消权重的操作
     * @return ture/ false
     */
    public function actionUnEquipWeight()
    {
        $equipWeightId = $_POST['equipWeightId'];
        $model         = Equipments::findOne($equipWeightId);
        $model->weight = 0;
        if ($model->save(false)) {
            echo "true";
        } else {
            echo 'false';
        }
    }

    public function actionEquipSync()
    {
        if (!Yii::$app->user->can('设备数据统计')) {
            return $this->redirect(['site/login']);
        }
        $syncArr      = $oneSyncArr      = $allSyncArr      = $searchModel      = $allCompanyObj      = [];
        $searchModel  = new EquipSyncSearch();
        $data         = Yii::$app->request->queryParams;
        $managerOrgId = \backend\models\Manager::getManagerBranchID();
        if ($managerOrgId > 1) {
            $data['org_id'] = $managerOrgId;
        }
        $oneCompanyObj = $searchModel->search($data);
        //组装数组
        if ($oneCompanyObj) {
            foreach ($oneCompanyObj as $k => $v) {
                $orgModel = Organization::findModel($v->org_id);
                if (!$orgModel || !$v->equipTypeModel) {
                    continue;
                }
                $oneSyncArr[$orgModel->org_name]['data'][$v->operationStatusArray()[$v->operation_status]]['data'][$v->equipTypeModel->model] = $v->syncnum;

            }

        }
        if (!isset($data['org_id']) || empty($data['org_id'])) {
            $data['all']   = 1;
            $allCompanyObj = $searchModel->search($data);
        }
        if ($allCompanyObj) {
            foreach ($allCompanyObj as $k => $v) {
                $allSyncArr[$v->operationStatusArray()[$v->operation_status]]['data'][$v->equipTypeModel->model] = $v->syncnum;
            }
        }
        if ($oneSyncArr && (!isset($data['org_id']) || empty($data['org_id']))) {
            $oneSyncArr['全国']['data'] = $allSyncArr;
            $oneSyncArr                   = array_reverse($oneSyncArr);
        }
        $syncArr = $this->processData($oneSyncArr);

        return $this->render('equip_sync', [
            'list'        => $syncArr,
            'searchModel' => $searchModel,
        ]);
    }

    public function processData($syncArr)
    {
        if (!$syncArr) {
            return [];
        }
        foreach ($syncArr as $k => &$v) {
            //分公司
            $i   = $oneCompanyNum   = 0; //初始化函数和设备累计数量
            $arr = []; //初始化每种运营状态的累计统计数组
            foreach ($v['data'] as $key => &$value) {
                //运营状态
                $oneCompanyNum += array_sum($value['data']); //分公司所有运营状态的累计统计总数
                $value['num'] = array_sum($value['data']); //分公司下一种运营状态的累计统计
                foreach ($value['data'] as $ke => $val) {
                    //设备类型
                    $arr[$ke][] = $val;
                    $i++;
                }
            }
            unset($value);
            $syncArr[$k]['data']['累计统计'] = [
                'num' => $oneCompanyNum,
            ];
            foreach ($arr as $m => $n) {
                $syncArr[$k]['data']['累计统计']['data'][$m] = array_sum($n);
                $i++;
            }

            $syncArr[$k]['data'] = array_reverse($syncArr[$k]['data']);
            $syncArr[$k]['rows'] = $i;

        }
        unset($v);
        return $syncArr;
    }

    /**
     * 选择灯箱
     * @author  zgw
     * @version 2016-10-09
     * @return  [type]     [description]
     */
    public function actionChangeLightBox()
    {
        $equipId     = Yii::$app->request->post('equipId');
        $lightBoxId  = Yii::$app->request->post('lightBoxId');
        $transaction = Yii::$app->db->beginTransaction();
        // 设备添加灯箱id
        $equipModel               = Equipments::findOne($equipId);
        $equipModel->light_box_id = $lightBoxId;
        // 生成灯箱验收任务
        $equipTaskModel              = new EquipTask();
        $equipTaskModel->build_id    = $equipModel->build_id;
        $equipTaskModel->equip_id    = $equipModel->id;
        $equipTaskModel->task_type   = 3;
        $equipTaskModel->content     = '新增灯箱验收任务';
        $equipTaskModel->create_time = time();

        if ($equipModel->save() === false || $equipTaskModel->save() === false) {
            $transaction->rollBack();
            echo "false";
        } else {
            $transaction->commit();
            echo "true";
        }
    }

    /**
     * 楼宇和设备手动绑定功能
     * @author  zgw
     * @version 2016-12-22
     * @return  [type]     [description]
     */
    public function actionBind()
    {
        $param = Yii::$app->request->post('Equipments');
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        // 获取设备信息
        $equipInfo = Equipments::findOne($param['id']);
        // 获取楼宇信息
        $buildInfo = Building::findOne($param['build_id']);
        if (!$buildInfo || !$equipInfo) {
            Yii::$app->getSession()->setFlash("error", "绑定失败");
            return $this->redirect(['view', 'id' => $param['id']]);
        }
        //修改设备信息
        $equipRes = Equipments::changeEquip($equipInfo, $param['build_id'], $param['pro_group_id'], $param['operation_status']);
        // 修改楼宇状态
        $buildRes = Building::changeBuild($buildInfo);

        if ($equipRes && $buildRes) {
            $logRes = ManagerLog::saveLog(Yii::$app->user->id, "设备信息管理", ManagerLog::UPDATE, "操作：设备绑定操作成功，操作人：" . Yii::$app->user->identity->username);
            if (!$logRes) {
                Yii::$app->getSession()->setFlash("error", "设备绑定操作添加人失败");
                $transaction->rollBack();
                return $this->redirect(['view', 'id' => $param['id']]);
            }
            // 同步绑定操作到智能平台
            $syncResData = ['equip_code' => $equipInfo->equip_code, 'build_number' => $buildInfo->build_number, 'bind' => '1', 'group_id' => $param['pro_group_id'], 'online' => $param['operation_status']];
            $syncRes     = Api::equipmentBind($syncResData);
            if (!$syncRes) {
                Yii::$app->getSession()->setFlash("error", "同步绑定操作失败");
                $transaction->rollBack();
                return $this->redirect(['view', 'id' => $param['id']]);
            }
            $transaction->commit();
        } else {
            $transaction->rollBack();
            return $this->redirect(['view', 'id' => $param['id']]);
        }
        return $this->redirect(['view', 'id' => $param['id']]);
    }

    /**
     * 获取分公司仓库
     * @author  zgw
     * @version 2017-02-28
     * @param   [type]     $orgId [description]
     * @return  [type]            [description]
     */
    public function actionGetWarehouse($orgId)
    {
        echo json_encode(ScmWarehouse::getOrgWarehouse($orgId), JSON_UNESCAPED_UNICODE);
    }

    /**
     * 导出设备信息Excel
     * @author wxl
     * @return \yii\web\Response
     * @throws \PHPExcel_Exception
     */
    public function actionExcelExport()
    {
        if (!Yii::$app->user->can('导出设备信息Excel')) {
            return $this->redirect(['site/login']);
        }
        $searchModel   = new EquipmentsSearch();
        $param         = isset(Yii::$app->request->queryParams['param']) ? Yii::$app->request->queryParams['param'] : '';
        $dataProvider  = $searchModel->search($param, false);
        $equipmentList = $dataProvider->query->select('equipments.id,equip_code,building.name,equip_type_id,equipments.org_id,equipment_status,operation_status,is_lock,pro_group_id,factory_code,card_number,build_type,first_free_strategy,strategy_change_date,first_backup_strategy,last_log,last_update')
            ->asArray()->all();
        $header = ['ID', '设备编号', '楼宇名称', '设备类型', '分公司', '设备状态', '运营状态', '是否锁定', '产品组名', '出厂编号', '流量卡号', '楼宇类型', '首杯免费策略', '首杯策略变更日期  ', '首杯备份策略', '最新日志', '更新时间', '机构类型'];
        // 获取全部设备类型数据
        $equipTypeList = \backend\models\ScmEquipType::getEquipTypeIdNameArr();
        // 获取产品组列表
        $groupList = $searchModel->proGroupList();
        // 获取楼宇类型
        $buildTypeList = \backend\models\BuildType::getBuildType();
        // 获取优惠券套餐ID和name列表
        $couponGroupList = \common\models\Building::getFirstStagegyNameArray();
        unset($groupList['']);
        unset($buildTypeList['']);
        $orgList = Organization::getOrgIdTypeList();
        foreach ($equipmentList as &$equipment) {
            $orgId                              = $equipment['org_id'];
            $equipment['equip_type_id']         = $equipTypeList[$equipment['equip_type_id']] ?? '';
            $equipment['org_id']                = $searchModel->orgArr[$orgId] ?? '';
            $equipment['equipment_status']      = $searchModel::$equipStatusArray[$equipment['equipment_status']] ?? '';
            $equipment['operation_status']      = $searchModel::$operationStatusArray[$equipment['operation_status']] ?? '';
            $equipment['is_lock']               = $searchModel::$changeLock[$equipment['is_lock']] ?? '';
            $equipment['pro_group_id']          = $groupList[$equipment['pro_group_id']] ?? '';
            $equipment['build_type']            = $buildTypeList[$equipment['build_type']] ?? '';
            $equipment['first_free_strategy']   = $couponGroupList[$equipment['first_free_strategy']] ?? '';
            $equipment['first_backup_strategy'] = $couponGroupList[$equipment['first_backup_strategy']] ?? '';
            $equipment['last_update']           = empty($equipment['last_update']) ? '' : date('Y-m-d H:i:s', $equipment['last_update']);
            $equipment['organization_type']     = $orgList[$orgId] ?? '';
        }
        \common\helpers\Tools::exportData('设备导出', $header, $equipmentList);
    }

    /**
     * 配方调整
     */
    public function actionFormulaAdjustment()
    {
        if (!Yii::$app->user->can('配方调整')) {
            return $this->redirect(['site/login']);
        }
        $params = Yii::$app->request->post();

        if (!empty($params)) {
            //获取当前操作用户姓名
            $username           = Manager::getManagerRealname();
            $params['username'] = $username;
            //更新配方调整数据
            $result = Api::saveFormulaAdjustment('formula-adjustment-api/save-formula-adjustment.html', $params);
            $ret    = Json::decode($result);
            //更新成功
            if ($ret['code'] == 0) {
                //防止新窗口打开，刷新页面重新提交表单数据
                return $this->redirect(['formula-adjustment?equip_code=' . $params['equipment_code']]);
            } else {
                Yii::$app->getSession()->setFlash('error', $ret['msg']);
            }
        }
        $equipCode   = Yii::$app->request->get('equip_code');
        $equipTypeId = Yii::$app->request->get('equipTypeId');
        //获取配方调整数据
        $formulas                = Api::getFormulaAdjustment('formula-adjustment-api/formula-adjustment-view.html?equipment_code=' . $equipCode);
        $formulaData             = $formulas ? Json::decode($formulas) : [];
        $matstockIdArr           = ScmEquipType::getMatstockIdArr($equipTypeId);
        $materialStockIdCodeList = ScmMaterialStock::getMaterialStockCodeToId();
        $formulaList             = [];
        foreach ($formulaData as $formul) {
            $stockId = $materialStockIdCodeList[$formul['stock_code']] ?? 0;
            if (in_array($stockId, $matstockIdArr)) {
                $formulaList[] = $formul;
            }
        }
        return $this->render('formula-adjustment', [
            'formulaList' => $formulaList,
            'equipCode'   => $equipCode,
            'equipTypeId' => $equipTypeId,
        ]);
    }

    /**
     * 配方调整修改日志
     */
    public function actionFormulaAdjustmentLog()
    {
        if (!Yii::$app->user->can('配方调整')) {
            return $this->redirect(['site/login']);
        }
        $params = Yii::$app->request->queryParams;
        if (empty($params['equip_code'])) {
            Yii::$app->getSession()->setFlash('error', '缺少参数设备编号');
        }
        $searchModel  = new FormulaAdjustmentLogSearch();
        $dataProvider = $searchModel->search($params);
        return $this->render('formula-adjustment-log', [
            'model'        => $searchModel,
            'dataProvider' => $dataProvider,
            'equipCode'    => $params['equip_code'],
            'equipTypeId'  => $params['equipTypeId'],
        ]);
    }

    /**
     * 远程开门(从智能系统迁移过来的)
     * @author wangxiwen
     * @version 2018-11-01
     */
    public function actionOpen($id)
    {
        if (!Yii::$app->user->can('远程开门')) {
            return $this->redirect(['site/login']);
        }
        $model      = $this->findModel($id);
        $socketList = Api::getSocketServer($model->equip_code);
        return $this->render('open', [
            'socket'       => empty($socketList['socket']) ? "" : $socketList['socket'],
            'socketServer' => empty($socketList['socketServer']) ? '' : $socketList['socketServer'],
            'model'        => $model,
        ]);
    }
}
