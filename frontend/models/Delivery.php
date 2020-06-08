<?php
namespace frontend\models;

use backend\models\BuildingSiteStatistics;
use backend\models\EquipAcceptance;
use backend\models\EquipDelivery;
use common\models\Api;
use common\models\Building;
use common\models\EquipDeliveryRecord;
use common\models\Equipments;
use common\models\EquipTask;
use common\models\SendNotice;
use common\models\WxMember;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Delivery
 */
class Delivery extends Model
{

    public static function deliveryAcceptance($params, $deliveryId, $userid)
    {
        // 验证出厂编号是否合法
        $verifyFactoryCodeResult = EquipDelivery::verifyFactoryCode($deliveryId, $params['factory_code']);
        if ($verifyFactoryCodeResult['result'] == false) {
            Yii::$app->getSession()->setFlash('error', $verifyFactoryCodeResult['msg']);
            return false;
        }
        $equipInfo     = $verifyFactoryCodeResult['equipInfo'];
        $deliveryModel = $verifyFactoryCodeResult['deliveryModel'];
        // 添加灯箱id
        $equipInfo->light_box_id = $deliveryModel->is_lightbox;
        // 添加sim卡号
        $equipInfo->card_number = $params['sim_number'] ? $params['sim_number'] : '';

        // 获取验收任务详情
        $taskInfo = EquipTask::taskDetailObj(['task_type' => EquipTask::TRAFFICKING_TASK, 'process_result' => EquipTask::UNTREATED, 'relevant_id' => $deliveryId]);
        if (!$taskInfo) {
            Yii::$app->getSession()->setFlash('error', '验收任务信息获取失败');
            return false;
        }

        // 验收成功
        if ($params['delivery_result'] || $params['delivery_result'] === '0') {
            // 设备状态
            $equipInfo->equipment_status = Equipments::NORMAL;
            if ($params['delivery_result'] == EquipDelivery::NO_OPERATION) {
                // 设备锁定状态(如果投放成功未运营则设备锁定)
                $equipInfo->is_lock = Equipments::LOCKED;
                // 投放单状态值
                $deliveryModel->delivery_status = EquipDelivery::UN_TRAFFICK_SUCCESS;
            } else {
                // 设备锁定状态(如果投放成功已运营则设备解锁)
                $equipInfo->is_lock = Equipments::UNLOCK;
                // 投放单状态值
                $deliveryModel->delivery_status = EquipDelivery::TRAFFICK_SUCCESS;
            }
            // 设备运营状态
            $equipInfo->operation_status = $params['delivery_result'];
            // 设备开始运营时间
            $equipInfo->equip_operation_time = time();
            // 任务处理结果
            $taskInfo->process_result = EquipTask::RESULT_SUCCESS;
            $statusMessage            = '成功';
        } else {
            // 验收失败
            // 设备状态
            $equipInfo->equipment_status = Equipments::MALFUNCTION;
            // 设备锁定状态
            $equipInfo->is_lock = Equipments::LOCKED;
            // 设备运营状态
            $equipInfo->operation_status = Equipments::NO_OPERATION;
            // 投放单状态值
            $deliveryModel->delivery_status = EquipDelivery::DELIVERY_FAILURE;
            // 任务处理结果
            $taskInfo->process_result = EquipTask::RESULT_FAILURE;
            // 添加维修任务
            // 定义故障现象常量
            $content       = isset($params['content']) ? $params['content'] : '';
            $createTaskRes = EquipTask::createTask($deliveryModel->build_id, $equipInfo->id, $content, $params['fail_remark'], $deliveryId);
            $statusMessage = '失败';
        }

        //投放验收结果发送给公司主管和经理以及投放人
        $user = ArrayHelper::getColumn(WxMember::getRoleByOrg($equipInfo->org_id), 'userid');
        //投放人
        $sale      = WxMember::getUserIdArr(['name' => $deliveryModel->sales_person]);
        $userList  = !empty($sale[0]) ? implode('|', $user) . '|' . $sale[0] : implode('|', $user);
        $url       = 'equip-delivery/delivery-info?delivery_id=' . $deliveryId . '&recive_time=' . $taskInfo->recive_time . '&end_repair_time=' . time();
        $buildName = Building::getField('name', ['id' => $deliveryModel->build_id]);
        $taskRet   = SendNotice::sendWxNotice($userList, $url, $buildName . '投放' . $statusMessage . '，请注意查看。', Yii::$app->params['equip_agentid']);
        if (!$taskRet) {
            Yii::$app->getSession()->setFlash("error", "信息发送失败");
        }
        $saveBuildRes = true;
        //如果投放状态为成功已运营则同步楼宇开始运营时间
        if ($deliveryModel->delivery_status == EquipDelivery::TRAFFICK_SUCCESS) {
            $saveBuildRes = BuildingSiteStatistics::saveBuildDeliveryTime($deliveryModel->build->build_number);
        }
        // 修改设备
        $equipRes = Equipments::changeEquip($equipInfo, $deliveryModel->build_id, $params['pro_group_id']);
        //修改设备浓度值
        $concentrationRes = Equipments::changeConcentration($equipInfo, $params['concentration']);
        // 修改楼宇
        $buildInfo = Building::findOne($deliveryModel->build_id);
        $buildRes  = Building::changeBuild($buildInfo);
        // 修改投放单
        $deliveryRes = EquipDelivery::changeDelivery($params, $deliveryModel);
        // 修改验收任务
        $taskRes = EquipTask::changeTask($taskInfo, $equipInfo->id, $params);
        // 添加验收结果
        $acceptanceRes = EquipAcceptance::acceptanceResult($params, $deliveryModel, $userid);
        // 添加投放记录
        $deliveryRecord = EquipDeliveryRecord::deliveryRecord($deliveryModel->build_id, $equipInfo->id, $deliveryId, $deliveryModel->delivery_status);
        // 验证数据操作是否成功
        if ($equipRes && $buildRes && $concentrationRes && $deliveryRes && $taskRes && $acceptanceRes && $deliveryRecord && $saveBuildRes) {
            // 同步楼宇和设备数据
            return self::syncBind($equipInfo, $buildInfo);
        }
        return false;
    }

    /**
     * 同步数据
     * @author  zgw
     * @version 2016-09-08
     * @param   [type]     $equipModel [description]
     * @param   [type]     $buildModel [description]
     * @return  [type]                 [description]
     */
    public static function syncBind($equipModel, $buildModel)
    {
        // 同步设备到只能平台
        $equipSyncRes = Equipments::syncEquip($equipModel);
        if (!$equipSyncRes) {
            Yii::$app->getSession()->setFlash('error', "同步设备信息操作失败");
            return false;
        }
        // 同步绑定操作
        $syncResData = ['equip_code' => $equipModel->equip_code, 'build_number' => $buildModel->build_number, 'bind' => '1'];
        $syncRes     = Api::equipmentBind($syncResData);
        if (!$syncRes) {
            Yii::$app->getSession()->setFlash("error", "同步绑定操作失败");
            return false;
        }
        return true;
    }
}
