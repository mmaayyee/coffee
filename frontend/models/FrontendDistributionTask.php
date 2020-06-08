<?php

namespace frontend\models;

use backend\models\ChangeProduct;
use backend\models\DistributionFiller;
use backend\models\DistributionFillerGram;
use backend\models\DistributionMaintenance;
use backend\models\DistributionTask;
use backend\models\DistributionUser;
use backend\models\DistributionWater;
use backend\models\EquipAbnormalTask;
use backend\models\EquipMalfunction;
use backend\models\EquipMaterialStockAssoc;
use backend\models\ProductMaterialStockAssoc;
use backend\models\ScmMaterial;
use backend\models\ScmMaterialStock;
use backend\models\ScmUserSurplusMaterial;
use backend\models\ScmUserSurplusMaterialGram;
use common\helpers\WXApi\MediaImg;
use common\models\Api;
use common\models\Building;
use common\models\Equipments;
use common\models\EquipTask;
use common\models\EquipTaskFitting;
use common\models\WxMember;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "distribution_task".
 */
class FrontendDistributionTask extends \yii\db\ActiveRecord
{
    /**
     * 获取企业微信上传的图片
     * @author wangxiwen
     * @datetime 2018-06-27
     * @param array $madiaId 素材ID
     * @id int $id 运维任务Id
     * return true|false
     */
    public static function getUploadImg($mediaId)
    {
        $mediaImg = new MediaImg();
        return $mediaImg->getMediaImg($mediaId);
    }

    /**
     * 企业微信任务完成更新设备料仓剩余物料
     * @author wangxiwen
     * @version 2018-06-26
     * @param object taskModel 任务详情
     * @return true|false
     */
    private static function saveSurplusMaterial($taskModel)
    {
        $materialArr  = Json::decode($taskModel->bluetooth_upload);
        $materialList = [];
        foreach ($materialArr as $stockCode => $materials) {
            foreach ($materials as $materialTypeId => $material) {
                $materialList['surplusMaterial'][$stockCode] = $material['overAmount'];
            }
        }
        $materialList['equipment_code'] = Equipments::getEquipCode($taskModel->equip_id);
        return Api::postBaseSurplusMaterial('save-surplus-material', $materialList);
    }

    /**
     * 企业微信任务完成更新运维人员手中剩余物料
     * @author wangxiwen
     * @version 2018-06-26
     * @param object $taskModel 任务信息
     * @param array  $scmMaterial 物料信息
     * @return boolean
     */
    private static function saveUserSurplusMaterial($taskModel, $scmMaterial)
    {
        $materialArr = Json::decode($taskModel->bluetooth_upload);
        foreach ($materialArr as $materials) {
            foreach ($materials as $materialTypeId => $material) {
                $addAmount         = intval($material['addAmount']);
                $scmMaterialDetail = $scmMaterial[$materialTypeId] ?? [];
                if (empty($scmMaterialDetail)) {
                    continue;
                }
                $materialId = $scmMaterialDetail['material_id'];
                $supplierId = $scmMaterial[$materialTypeId]['supplier_id'];
                $weight     = $scmMaterial[$materialTypeId]['weight'];
                $type       = $scmMaterial[$materialTypeId]['type'];

                //获取散料信息
                $surplusMaterialGram = ScmUserSurplusMaterialGram::getScmUserSurplusMaterialGram($taskModel->assign_userid, $materialTypeId);
                //获取整料信息
                $surplusMaterial = ScmUserSurplusMaterial::getScmUserSurplusMaterial($taskModel->assign_userid, $materialId);
                //更新运维人员手中剩余物料 如果散料表中剩余物料大于添加量则或者散料表中剩余物料小于添加量且添加量不足一包，则只减少散料，值为负数或者散料表中剩余物料小于添加量且添加量大于一包，则减少整包后再减少散料，此时整料和散料表均可能为负数
                $gram = $surplusMaterialGram->gram - $addAmount;
                if (($surplusMaterialGram->gram < $addAmount && $addAmount > $weight) || $type == 2) {
                    $weight                        = $weight > 0 && $type == 1 ? $weight : 1;
                    $packets                       = floor($addAmount / $weight);
                    $surplusMaterial->material_id  = $materialId;
                    $surplusMaterial->material_num = $surplusMaterial->material_num - $packets;
                    $surplusMaterial->author       = $taskModel->assign_userid;
                    $surplusMaterial->date         = date('Y-m-d');

                    $ret = $surplusMaterial->save();
                    if (!$ret) {
                        return false;
                    }
                    $gram = $surplusMaterialGram->gram - ($addAmount - $weight * $packets);
                }
                if (!$gram) {
                    continue;
                }
                $surplusMaterialGram->material_type_id = $materialTypeId;
                $surplusMaterialGram->supplier_id      = $supplierId;
                $surplusMaterialGram->gram             = $gram;
                $surplusMaterialGram->author           = $taskModel->assign_userid;
                $surplusMaterialGram->date             = date('Y-m-d');
                $ret                                   = $surplusMaterialGram->save();
                if (!$ret) {
                    return false;
                }
            }

        }
        return true;
    }

    /**
     * 获取料仓ID
     * @author wangxiwen
     * @version 2018-07-09
     * @param int equipId 设备ID
     * return array
     */
    private static function getStockIds($equipId)
    {
        $materials = EquipMaterialStockAssoc::find()
            ->where(['equip_id' => $equipId])
            ->select('material_type,material_stock_id')
            ->asArray()
            ->all();
        $stockIds = [];
        foreach ($materials as $material) {
            $stockIds[$material['material_type']] = $material['material_stock_id'];
        }
        return $stockIds;
    }
    /**
     * 获取料仓编号
     * @author wangxiwen
     * @version 2018-07-09
     * @param int equipId 设备ID
     * @return array
     */
    private static function getStockCodes()
    {
        $materials = ScmMaterialStock::find()
            ->select('id,stock_code')
            ->asArray()
            ->all();
        $stockCodes = [];
        foreach ($materials as $material) {
            $stockCodes[$material['id']] = $material['stock_code'];
        }
        return $stockCodes;
    }

    /**
     * 获取分公司ID
     * @author wangxiwen
     * @datetime 2018-06-26
     * @param string $userid 用户ID
     * @return int
     */
    public static function getOrgId($userid)
    {
        return WxMember::find()->where(['userid' => $userid])->select('org_id')->scalar();
    }

    /**
     * 获取分配投放验收负责设备人员
     * @param  $buildId, $type
     */
    public static function getEquipNameArr($buildId = '', $type = '1')
    {
        // 获取楼宇所在分公司
        $orgId = Building::getField('org_id', ['id' => $buildId]);
        // 获取成员列表
        $userList    = WxMember::distributionIdNameArr($orgId, $type);
        $userListArr = ['' => '请选择'];

        foreach ($userList as $userObj) {
            if (isset($userObj->distributionUser->user_status)) {
                if ($userObj->distributionUser->user_status == DistributionUser::WORK_ON) {
                    $userListArr[$userObj->userid] = $userObj->name;
                }
            } else {
                $userListArr[$userObj->userid] = $userObj->name;
            }
        }
        return $userListArr;
    }

    /**
     * 添加物料添加表
     * @author wangxiwen
     * @version 2018-06-26
     * @param array $stockList 料仓id和code对应关系数组
     * @param object $taskModel 任务信息
     * @param array  $stockMaterial 设备料仓信息
     * @return boolean
     */
    private static function createDistributionFiller($stockList, $taskModel, $scmMaterial)
    {
        //保存蓝牙秤上传数据到运维物料添加表和运维物料散料添加表
        $materialArr = Json::decode($taskModel->bluetooth_upload);
        foreach ($materialArr as $stockCode => $materials) {
            foreach ($materials as $materialTypeId => $material) {
                $addAmount         = intval($material['addAmount']);
                $stockId           = $stockList[$stockCode] ?? 0;
                $scmMaterialDetail = $scmMaterial[$materialTypeId] ?? [];
                if (empty($scmMaterialDetail) || !$stockId) {
                    continue;
                }
                $supplierId = $scmMaterialDetail['supplier_id'];
                $materialId = $scmMaterialDetail['material_id'];
                $type       = $scmMaterialDetail['type'];
                $weight     = $scmMaterialDetail['weight'] > 0 && $type == 1 ? $scmMaterialDetail['weight'] : 1;
                $gram       = $addAmount;
                if ($addAmount > $weight || $type == 2) {
                    $packets                                  = floor($addAmount / $weight);
                    $distributionFiller                       = new DistributionFiller();
                    $distributionFiller->equip_id             = $taskModel->equip_id;
                    $distributionFiller->build_id             = $taskModel->build_id;
                    $distributionFiller->material_type        = $materialTypeId;
                    $distributionFiller->material_id          = $materialId;
                    $distributionFiller->number               = (int) $packets;
                    $distributionFiller->stock_id             = $stockId;
                    $distributionFiller->distribution_task_id = $taskModel->id;
                    $distributionFiller->create_date          = date('Y-m-d');
                    $distributionFiller->add_material_author  = $taskModel->assign_userid;
                    $ret                                      = $distributionFiller->save();
                    if (!$ret) {
                        return false;
                    }
                    $gram = $addAmount - $packets * $weight;
                }
                if (!$gram) {
                    continue;
                }
                $distributionFillerGram                       = new DistributionFillerGram();
                $distributionFillerGram->equip_id             = $taskModel->equip_id;
                $distributionFillerGram->build_id             = $taskModel->build_id;
                $distributionFillerGram->distribution_task_id = $taskModel->id;
                $distributionFillerGram->supplier_id          = $supplierId;
                $distributionFillerGram->gram                 = (int) $gram;
                $distributionFillerGram->material_type_id     = $materialTypeId;
                $ret                                          = $distributionFillerGram->save();
                if (!$ret) {
                    return false;
                }

            }
        }
        return true;
    }

    /**
     * 配送物料插入添料数据更新配送员手中物料
     * @date 2017-09-11
     * @author wxl
     * @param array $stockInfo
     * @param array $taskModel
     * @param int $id
     * @param int $userId
     */
    private static function detailAddChangeMaterial($stockInfo = [], $taskModel = [], $id = 0, $userId = 0, $stockId, $stockMaterialWeight = 0)
    {
        foreach ($stockInfo['material_id'] as $typeKey => $materialId) {
            //查询供应商
            $material   = ScmMaterial::getMaterialDetail('supplier_id', ['id' => $materialId]);
            $supplierId = isset($material['supplier_id']) ? $material['supplier_id'] : 0;

            //包数
            if (isset($stockInfo['packets'][$typeKey]) && intval($stockInfo['packets'][$typeKey]) !== 0) {
                //物料添料数据
                DistributionFiller::addDistributionFillerRecord([
                    'equip_id'             => $taskModel["equip_id"],
                    'build_id'             => $taskModel['build_id'],
                    'material_type'        => $stockInfo['material_type'],
                    'stock_id'             => $stockInfo['stock_id'],
                    'distribution_task_id' => $id,
                    'add_material_author'  => $userId,
                    'material_id'          => $materialId,
                    'number'               => $stockInfo['packets'][$typeKey],
                ]);

                //修改配送员手中的物料
                ScmUserSurplusMaterial::editSurplusMaterial($userId, $materialId, intval($stockInfo['packets'][$typeKey]), 2);

            }
            //克数
            if (isset($stockInfo['material_out_gram'][$typeKey]) && intval($stockInfo['material_out_gram'][$typeKey]) !== 0) {
                //物料添料数据(散料)
                DistributionFillerGram::addDistributionFillerGramRecord([
                    'equip_id'             => $taskModel["equip_id"],
                    'build_id'             => $taskModel['build_id'],
                    'distribution_task_id' => $id,
                    'material_type_id'     => $stockInfo['material_type'],
                    'supplier_id'          => $supplierId,
                    'gram'                 => $stockInfo['material_out_gram'][$typeKey],
                ]);

                //修改配送员手中的散料
                ScmUserSurplusMaterialGram::editSurplusMaterialGram($materialId, $userId, $supplierId, $stockInfo['material_type'], intval($stockInfo['material_out_gram'][$typeKey]), 'del');

            }

            //如果是换料增加配送员手中物料
            //判断是否是更换产品组的任务
            $isChangeProductGroup = ChangeProduct::IsSameLastProductIdPresentProductId($taskModel['equip_id']);
            if (!$isChangeProductGroup) {
                $lastProductId         = ChangeProduct::getField('last_product_id', ['equip_id' => $taskModel['equip_id']]);
                $stockIdOfMaterialType = ProductMaterialStockAssoc::getStockIdOfMaterialType($lastProductId);
                //更改设备上次产品组和本次产品组一致
                $changeSameProductGroup = ChangeProduct::modifyLastProductId($taskModel['equip_id']);
                if (!$changeSameProductGroup) {
                    Yii::$app->getSession()->setFlash('error', '更改上次产品组本次产品组一致失败');
                    return false;
                }
            }
            $materialTypeId = isset($stockIdOfMaterialType[$stockId]) ? $stockIdOfMaterialType[$stockId] : $stockInfo['material_type'];

            if ($stockInfo['is_change'] == 2 && $stockMaterialWeight !== 0 && $materialTypeId) {
                //判断是否是更换产品组的任务
                ScmUserSurplusMaterialGram::editSurplusMaterialGram($materialId, $userId, $supplierId, $materialTypeId, $stockMaterialWeight, 'add');
            }

        }
    }

    /**
     *  创建水单
     *  @param $params, $distributionTaskArr, $transaction
     **/
    public static function createDistributionWater($params, $distributionTaskArr, $distributionID = 0)
    {
        $distributionTaskID = $distributionID > 0 ? $distributionID : (isset($distributionTaskArr->id) ? $distributionTaskArr->id : 0);

        if (isset($params['distributionWater']["surplusWater"]) && $params['distributionWater']["supplierId"] && isset($params['distributionWater']["needWater"]) && $params['distributionWater']["needWater"]) {
            //添加入水单表中
            $waterModel                       = new DistributionWater();
            $waterModel->build_id             = $distributionTaskArr['build_id'];
            $waterModel->surplus_water        = $params['distributionWater']['surplusWater'];
            $waterModel->supplier_id          = $params['distributionWater']['supplierId'];
            $waterModel->need_water           = $params['distributionWater']['needWater'] ? $params['distributionWater']['needWater'] : '0';
            $waterModel->distribution_task_id = $distributionTaskID;
            $waterModel->create_time          = time();
            $waterRet                         = $waterModel->save();
            if (!$waterRet) {

                Yii::$app->getSession()->setFlash('error', '水单添加失败');
                return false;
            }
        }
        return true;
    }

    /**
     *  添加到维修表中
     *  @param $params, $repairTaskSign, $transaction
     **/
    public static function createDistributionAttendance($params, $taskModel)
    {
        $beginRepairTime   = $params['maintenance']['start_repair_time'];
        $endRepairTime     = $params['maintenance']['end_repair_time'];
        $description       = $params['maintenance']['malfunction_description'];
        $processMethod     = $params['maintenance']['process_method'];
        $processResult     = $params['maintenance']['process_result'];
        $malfunctionReason = $params['malfunction_reason'];
        if ($beginRepairTime && $endRepairTime && $malfunctionReason) {
            $maintenanceModel = new DistributionMaintenance();
            //获取故障信息
            $equipAbnormalTask                         = EquipAbnormalTask::getEquipAbnormalTask($taskModel->build_id);
            $maintenanceModel->malfunction_reason      = implode(',', $malfunctionReason);
            $maintenanceModel->distribution_task_id    = $taskModel->id;
            $maintenanceModel->start_repair_time       = strtotime($beginRepairTime . ':59');
            $maintenanceModel->end_repair_time         = strtotime($endRepairTime . ':59');
            $maintenanceModel->malfunction_description = $description;
            $maintenanceModel->process_method          = $processMethod;
            $maintenanceModel->process_result          = $processResult;
            $maintenanceRes                            = $maintenanceModel->save();
            //修改运维表中维修任务结果状态
            $abnormalRes = true;
            if ($processResult == 2) {
                $taskModel->result = 2;
                $taskRes           = $taskModel->save();
                //添加到设备任务表中
                $equipTaskRes = EquipTask::insetEquipTask($params, $taskModel);
                if (!$equipTaskRes) {
                    return false;
                }
            } elseif ($processResult == 3) {
                //修改运维任务维修状态,修改故障任务表中该楼宇的任务状态为已完成
                $taskModel->result = 1;
                $taskRes           = $taskModel->save();
            }
            //如果故障任务中存在已下发或者转次日的任务,更改状态为已完成
            if (!empty($equipAbnormalTask)) {
                $equipAbnormalTask->task_status = EquipAbnormalTask::COMPLETE;
                $abnormalRes                    = $equipAbnormalTask->save();
            }
            if (!$maintenanceRes || !$taskRes || !$abnormalRes) {
                return false;
            }
        }
        return true;
    }

    /**
     *  添加配件
     *  @param $params, $transaction
     **/
    public static function createDistributionFitting($params, $id)
    {
        $taskFitting = true;
        if (isset($params['fitting'])) {
            $fittingArr = [];
            foreach ($params['fitting'] as $key => $value) {
                $taskFittingModel                 = new EquipTaskFitting();
                $taskFittingModel->task_id        = $id;
                $taskFittingModel->task_type      = 1;
                $taskFittingModel->fitting_name   = $value['fitting_name'];
                $taskFittingModel->fitting_model  = $value['fitting_model'];
                $taskFittingModel->factory_number = $value['fitting_number'];
                $taskFittingModel->num            = $value["fitting_num"];
                $taskFittingModel->remark         = $value['remark'];
                $retTaskFitting                   = $taskFittingModel->save();
                if ($retTaskFitting === false) {
                    $taskFitting = false;
                    Yii::$app->getSession()->setFlash("error", "配件添加失败！");
                }
            }
        }
        return $taskFitting;
    }

    /**
     *  修改配送任务表
     *  @param $distributeTaskSign, $repairTaskSign, $taskModel, $transaction
     **/
    private static function createDistributionTask($params, $taskModel)
    {
        if (!empty($params['electric']) && is_numeric($params['electric']) && $params['electric'] > 0) {
            //电表读数
            $taskModel->meter_read = round($params['electric'], 2);
        }
        if ($params['add_water'] > 0) {
            $taskModel->add_water = $params['add_water'];
        }
        if ($params['surplus_water'] > 0) {
            $taskModel->surplus_water = $params['surplus_water'];
        }
        $taskModel->is_sue = 2;
        // 任务完成时间
        $taskModel->end_delivery_date = date('Y-m-d H:i:s', time());
        // 任务完成地址
        $taskModel->end_latitude  = isset($params['end_latitude']) ? $params['end_latitude'] : '';
        $taskModel->end_longitude = isset($params['end_longitude']) ? $params['end_longitude'] : '';
        $taskModel->end_address   = isset($params['end_address']) ? $params['end_address'] : '';
        $taskRes                  = $taskModel->save();
        if (!$taskRes) {
            return false;
        }
        return true;
    }

    /**
     *  获取月统计台数
     *
     **/
    public static function getMonthCount($userId)
    {
        $query = DistributionTask::find()->where(['is_sue' => 2, 'assign_userid' => $userId]);
        //计算出每月多少天数
        $thisMonth = date("Y-m-d", mktime(0, 0, 0, date('m'), 1, date("Y")));
        $nextMonth = date("Y-m-d", mktime(0, 0, 0, date('m') + 1, 1, date("Y")));

        //日期查询
        $query->andFilterWhere(['>=', 'distribution_task.end_delivery_date', $thisMonth]);
        $query->andFilterWhere(['<=', 'distribution_task.end_delivery_date', $nextMonth]);

        $taskMonthCount = $query->count();

        return $taskMonthCount;
    }

    /**
     *  根据ID查询故障原因的列表
     *  @param malfunction_reason
     *  @return string
     **/
    public static function getMalfunctionReasonStr($malfunction_reason)
    {

        $malfunctionIdArr   = explode(",", $malfunction_reason);
        $malfunctionNameArr = [];
        foreach ($malfunctionIdArr as $key => $value) {
            $malfunctionNameArr[] = EquipMalfunction::getMalfunctionDetail("*", ['id' => $value])['content'];
        }
        return implode("<br/>", $malfunctionNameArr);
    }

    /**
     * 设备料仓物料信息排序
     * @author wangxiwen
     * @version 2018-10-08
     * @param  [array] $materialArray [物料信息]
     * @return [array]                [物料信息]
     */
    public static function materialStockSort($materialArray)
    {
        $materialList = [];
        foreach ($materialArray as $material) {
            $stockCode                = $material['material_stock_code'];
            $materialList[$stockCode] = $material;
        }
        sort($materialList);
        return $materialList;
    }

    /**
     * 企业微信任务完成保存数据时验证提交数据
     * @author wangxiwen
     * @version 2018-10-10
     * @param array $params 提交数据
     * @return boolean
     */
    public static function verifyParams($params)
    {
        if ($params['add_cups'] == '' || $params['surplus_cups'] == '' || $params['add_cover'] == '' || $params['surplus_cover'] == '' || $params['add_water'] == '' || $params['surplus_water'] == '') {
            return false;
        }
        return true;
    }
    /**
     * 企业微信任务完成保存数据时验证任务类别
     * @author wangxiwen
     * @version 2018-10-10
     * @param object $taskModel 任务详情
     * @return boolean true 纯物料添加任务 false 正常任务
     */
    public static function verifyTaskType($taskModel)
    {
        return empty($taskModel->delivery_task) && $taskModel->task_type == 1 ? true : false;
    }
    /**
     * 企业微信任务完成保存数据时检测清洗任务是否完成
     * @author wangxiwen
     * @version 2018-10-10
     * @param array $params 提交数据
     * @param object $taskModel 任务详情
     * @return boolean
     */
    public static function verifyClean($params, $taskModel)
    {
        //获取设备编号
        $equipCode = Equipments::getEquipCode($taskModel->equip_id);
        //检测清洗任务是否完成
        $washTime = Api::getBase('equip-wash-time', '&equipCode=' . $equipCode);
        if (empty($washTime) || $washTime < $taskModel->start_delivery_time) {
            return false;
        }
        return true;
    }
    /**
     * 企业微信任务完成保存数据时(存在维修任务)检测维修时间
     * @author wangxiwen
     * @version 2018-10-10
     * @param array $params 提交数据
     * @param object $taskModel 任务详情
     * @return boolean
     */
    public static function verifyRepair($params, $taskModel)
    {
        $taskType = explode(',', $taskModel->task_type);
        if (in_array(DistributionTask::SERVICE, $taskType)) {
            if (empty($params['maintenance']['start_repair_time']) || empty($params['maintenance']['end_repair_time'])) {
                return false;
            }
        }
        return true;
    }
    /**
     * 企业微信任务完成保存数据时如果新增杯子杯盖则添加到bluetooth_uploads字段中
     * @author wangxiwen
     * @version 2018-10-10
     * @param array $params 提交数据
     * @param object $taskModel 任务详情
     * @param array $stockMaterialType 产品组料仓物料分类
     * @return json
     */
    public static function addBluetoothUpload($params, $taskModel, $stockMaterialType)
    {
        $bluetoothUpload = Json::decode($taskModel->bluetooth_upload);
        //杯子
        $cupMaterialTypeId = $stockMaterialType['cups'] ?? 0;
        //杯盖
        $coverMaterialTypeId = $stockMaterialType['cover'] ?? 0;
        if ($cupMaterialTypeId && ($params['add_cups'] > 0 || $params['surplus_cups'] > 0)) {
            $bluetoothUpload['cups'][$cupMaterialTypeId] = ['addAmount' => $params['add_cups'], 'overAmount' => $params['surplus_cups'], 'changeAmount' => 0];
        }
        if ($coverMaterialTypeId && ($params['add_cover'] > 0 || $params['surplus_cover'] > 0)) {
            $bluetoothUpload['cover'][$coverMaterialTypeId] = ['addAmount' => $params['add_cover'], 'overAmount' => $params['surplus_cover'], 'changeAmount' => 0];
        }
        //转成字符串防止Json出现越界
        foreach ($bluetoothUpload as $stockCode => $uploadArray) {
            foreach ($uploadArray as $materialTypeId => $upload) {
                $bluetoothUpload[$stockCode][$materialTypeId] = [
                    'addAmount'    => (string) $upload['addAmount'],
                    'overAmount'   => (string) $upload['overAmount'],
                    'changeAmount' => (string) $upload['changeAmount'],
                ];
            }
        }
        return Json::encode($bluetoothUpload);
    }
    /**
     * 企业微信任务完成保存数据时更新数据库
     * @author wangxiwen
     * @version 2018-10-10
     * @param bool $taskTypeRes 任务类别-纯物料添加任务true正常任务false
     * @param array $params 提交的数据
     * @param object $taskModel 任务详情
     * @param array $scmMaterial 物料信息
     * @return json
     */
    public static function saveDistributionTask($taskTypeRes = true, $params, $taskModel, $scmMaterial)
    {
        //获取料仓编号和料仓ID对应关系数组
        $stockList = ScmMaterialStock::getMaterialStockCodeToId();
        //更新运维人员手中剩余物料
        $userSurplusMaterialSaveRes = self::saveUserSurplusMaterial($taskModel, $scmMaterial);
        if (!$userSurplusMaterialSaveRes) {
            return '更新运维人员手中剩余物料失败';
        }
        //添加物料
        $distributeSaveRes = self::createDistributionFiller($stockList, $taskModel, $scmMaterial);
        if (!$distributeSaveRes) {
            return '添加物料失败';
        }
        //修改电表,修改任务状态为已完成
        $distributionTaskRes = self::createDistributionTask($params, $taskModel);
        if (!$distributionTaskRes) {
            return '修改任务状态失败';
        }
        //更新设备料仓换料时间
        $saveRefuelTimeRes = EquipMaterialStockAssoc::saveRefuelTime($taskModel->equip_id);
        if (!$saveRefuelTimeRes) {
            return '更新设备料仓换料时间失败';
        }
        //非纯物料添加任务会执行以下操作
        if (!$taskTypeRes) {
            //添加维修内容
            $reparieRes = self::createDistributionAttendance($params, $taskModel);
            if (!$reparieRes) {
                return '添加维修内容失败';
            }
            //更新设备清洗时间
            $equipSaveRes = Equipments::updateWaterTime($taskModel->equip_id);
            if (!$equipSaveRes) {
                return '更新设备清洗时间失败';
            }
        }
        //更改料仓剩余物料(智能系统)
        $surplusMaterialSaveRes = self::saveSurplusMaterial($taskModel);
        if (!$surplusMaterialSaveRes) {
            return '更新料仓剩余物料失败';
        }
        return true;
    }

}
