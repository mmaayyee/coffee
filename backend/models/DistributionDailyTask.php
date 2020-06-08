<?php

namespace backend\models;

use backend\models\OutStatistics;
use backend\models\ScmMaterial;
use backend\models\ScmUserSurplusMaterial;
use backend\models\ScmUserSurplusMaterialGram;
use common\helpers\Tools;
use common\models\Building;
use common\models\Equipments;
use common\models\SendNotice;
use common\models\WxMember;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "distribution_daily_task".
 *
 * @property integer $id
 * @property integer $equip_id
 * @property integer $material_type
 * @property integer $packet_num
 * @property string $date
 *
 * @property Equipments $equip
 * @property ScmMaterial $material
 */
class DistributionDailyTask extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'distribution_daily_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['build_id', 'material_id'], 'required'],
            [['build_id', 'material_id', 'org_id'], 'integer'],
            [['consume_material', 'packet_num'], 'number'],
            [['date', 'weight'], 'safe'],
            [['distribution_userid'], 'string', 'max' => 64],
            [['remark'], 'string', 'max' => 300],
            [['build_id'], 'exist', 'skipOnError' => true, 'targetClass' => Building::className(), 'targetAttribute' => ['build_id' => 'id']],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'build_id'            => 'Build ID',
            'material_id'         => 'Material ID',
            'org_id'              => 'Org ID',
            'consume_material'    => 'Consume Material',
            'packet_num'          => 'Packet Num',
            'distribution_userid' => 'Distribution Userid',
            'date'                => 'Date',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuild()
    {
        return $this->hasOne(Building::className(), ['id' => 'build_id']);
    }

    public function getEquip()
    {
        return $this->hasOne(Equipments::className(), ['build_id' => 'build_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial()
    {
        return $this->hasOne(ScmMaterial::className(), ['id' => 'material_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(WxMember::className(), ['userid' => 'distribution_userid']);
    }

    /**
     * 获取日常任务数据
     * @author wangxiwen
     * @version 2018-05-24
     * return array
     */
    public static function getDailyTaskData($orgId)
    {
        return self::find()
            ->andWhere(['org_id' => $orgId])
            ->orderBy('date ASC,distribution_userid ASC')
            ->asArray()
            ->all();
    }

    /*
     * 获取日常任务日期
     * @author wangxiwen
     * @version 2018-05-24
     * @param int $orgId 分公司ID
     * return array
     */
    public static function getTaskDate($orgId)
    {
        $dateArr = self::find()
            ->andWhere(['org_id' => $orgId])
            ->orderBy('date ASC')
            ->distinct()
            ->select('date')
            ->asArray()
            ->all();
        $dateList = [];
        foreach ($dateArr as $date) {
            $dateList[] = $date['date'];
        }
        return $dateList;
    }
    /**
     * 组合页面展示的日常任务数据
     * @author wangxiwen
     * @version 2019-05-24
     * @param $dailyTaskData 日常任务数据
     * return array
     */
    public static function showDailyTaskData($dailyTaskData, $dailyTaskDate, $orgId)
    {
        //获取日常任务运维楼宇数量和名称
        $buildNumber = self::getBuildNumber($dailyTaskData);
        //获取物料分类信息
        $materialArr = ScmMaterial::getScmMaterial();
        //获取日常任务运维物料分类信息
        $materialList = self::getMaterialTypeList($dailyTaskData, $materialArr);
        //获取任务楼宇组装楼宇任务状态
        $taskTypeList = self::getTaskTypeList($dailyTaskData, $buildNumber);
        //获取运维人员姓名
        $userNameList = self::getUserName();
        $taskList     = [
            'dailyTaskList' => [],
            'date'          => [],
            'orgId'         => 0,
        ];
        foreach ($dailyTaskData as $taskData) {
            $userid = $taskData['distribution_userid'];
            $date   = $taskData['date'];

            $taskList['dailyTaskList'][$userid]['userName']            = $userNameList[$userid] ?? '未设置';
            $taskList['dailyTaskList'][$userid][$date]['buildNumber']  = $buildNumber[$userid][$date]['buildNumber'];
            $taskList['dailyTaskList'][$userid][$date]['materialList'] = $materialList[$userid][$date]['materialList'] ?? [];
            $taskList['dailyTaskList'][$userid][$date]['taskList']     = $taskTypeList[$userid][$date];
            $taskList['date']                                          = $dailyTaskDate;
            $taskList['orgId']                                         = $orgId;
        }
        return $taskList;
    }
    /**
     * 获取日常任务运维楼宇数量
     * @author wangxiwen
     * @version 2018-05-24
     * @param $dailyTaskData 日常任务数据
     * return array
     */
    private static function getBuildNumber($dailyTaskData)
    {
        //通过楼宇ID获取楼宇名称
        $buildName = self::getBuildName();
        $taskList  = [];
        foreach ($dailyTaskData as $taskData) {
            $taskList[$taskData['distribution_userid']][$taskData['date']][$taskData['build_id']] = $buildName[$taskData['build_id']];
        }
        //楼宇数量
        foreach ($taskList as $distribution_userid => $taskArr) {
            foreach ($taskArr as $date => $task) {
                $taskList[$distribution_userid][$date]['buildNumber'] = count($task);
            }
        }
        return $taskList;
    }
    /**
     * 获取任务楼宇组装楼宇任务状态
     * @author wangxiwen
     * @version 2018-05-24
     * @param $dailyTaskData 日常任务数据
     * return array
     */
    private static function getTaskTypeList($dailyTaskData, $buildNumber)
    {
        $taskList = [];
        foreach ($dailyTaskData as $taskData) {
            $task['buildId']   = $taskData['build_id'];
            $task['buildName'] = $buildNumber[$taskData['distribution_userid']][$taskData['date']][$taskData['build_id']];
            $task['type']      = $taskData['task_type'];
            if (empty($taskList[$taskData['distribution_userid']][$taskData['date']][$task['buildId']])) {
                $taskList[$taskData['distribution_userid']][$taskData['date']][$task['buildId']] = $task;
            } else if (strpos($taskList[$taskData['distribution_userid']][$taskData['date']][$task['buildId']]['type'], $task['type']) === false) {
                $taskList[$taskData['distribution_userid']][$taskData['date']][$task['buildId']]['type'] .= ',' . $task['type'];
            }
        }
        foreach ($taskList as $distribution_userid => $taskArr) {
            foreach ($taskArr as $date => $taskInfo) {
                foreach ($taskInfo as $buildId => $task) {
                    $taskInfo[$buildId]['typeName'] = self::getTypeName($task['type']);
                }
                $taskList[$distribution_userid][$date] = array_values($taskInfo);
            }
        }
        return $taskList;
    }

    /**
     * 获取任务类型名称
     * @author wangxiwen
     * @version 2018-05-25
     * @param $type 任务类型
     * return string
     */
    private static function getTypeName($type)
    {
        $typeList = explode(',', $type);
        sort($typeList);
        $taskType = '';
        foreach ($typeList as $type) {
            if ($type == DistributionTask::DELIVERY) {
                $taskType .= '配送,';
            } elseif ($type == DistributionTask::SERVICE) {
                $taskType .= '维修,';
            } elseif ($type == DistributionTask::URGENT) {
                $taskType .= '紧急,';
            } elseif ($type == DistributionTask::CLEAN) {
                $taskType .= '清洗,';
            } elseif ($type == DistributionTask::REFUEL) {
                $taskType .= '换料,';
            }
        }
        $taskType = substr($taskType, 0, -1);
        return $taskType;
    }

    /**
     * 获取运维人员姓名
     * @author wangxiwen
     * @version 2018-05-25
     * return array
     */
    public static function getUserName()
    {
        $userArr = WxMember::find()
            ->andWhere(['is_del' => 1])
            ->select('userid,name')
            ->asArray()
            ->all();
        $userList = [];
        foreach ($userArr as $user) {
            $userList[$user['userid']] = $user['name'];
        }
        $undefined = '未设置';
        array_push($userList, $undefined);
        return $userList;
    }
    /**
     * 获取使用的分类物料
     * @author wangxiwen
     * @version 2018-05-24
     * @param $dailyTask 日常任务数据
     * @param $materialArr 物料信息
     * @return array
     */
    private static function getMaterialTypeList($dailyTask, $materialArr)
    {
        //数据分组
        $materialList = [];
        $material     = [];
        foreach ($dailyTask as $task) {
            $userid = $task['distribution_userid'];
            $date   = $task['date'];
            if ($task['packet_num'] != 0 && $task['weight'] != 0 && $task['material_id'] != 0) {
                $material['number']     = ceil($task['packet_num'] / $task['weight']);
                $material['name']       = $materialArr[$task['material_id']]['material_type_name'] ?? '未知';
                $material['materialId'] = $task['material_id'];
                if (empty($materialList[$userid][$date]['materialList'][$material['materialId']])) {
                    $materialList[$userid][$date]['materialList'][$material['materialId']] = $material;
                } else {
                    $materialList[$userid][$date]['materialList'][$material['materialId']]['number'] += $material['number'];
                }
            }
        }
        $materialLists = [];
        foreach ($materialList as $userid => $materialArrs) {
            foreach ($materialArrs as $date => $materialInfo) {
                $materialLists[$userid][$date]['materialList'] = array_values($materialInfo['materialList']);
            }
        }
        return $materialLists;

    }

    /**
     * 通过楼宇ID获取楼宇名称
     * @author wangxiwen
     * @version 2018-05-24
     * return array
     */
    public static function getBuildName()
    {
        $buildArr = Building::find()
            ->select('id,name')
            ->asArray()
            ->all();
        $buildList = [];
        foreach ($buildArr as $build) {
            $buildList[$build['id']] = $build['name'];
        }
        return $buildList;
    }
    /**
     * 获取楼宇日常任务数据
     * @author wangxiwen
     * @version 2018-05-31
     * @param string $date 更换前日期
     * @param int $buildId 楼宇ID
     * @param string $userId 运维人员id
     * @param int $orgId 分公司ID
     * @param
     */
    public static function getBuildDailyTaskList($date, $buildId, $userId, $orgId)
    {
        return self::find()
            ->andWhere(['date' => $date])
            ->andWhere(['build_id' => $buildId])
            ->andWhere(['distribution_userid' => $userId])
            ->andWhere(['org_id' => $orgId])
            ->all();
    }

    /**
     * 获取更换日期后楼宇料仓计算后的物料量
     * @author wangxiwen
     * @version 2018-06-01
     * @param array $buildTask 楼宇日常任务
     * @param array $holiday 节假日日期
     * @param array $beforeDate 更改前日期
     * @param array $afterDate 更改后日期
     * @param int $flag 1提前2延后
     * @return array
     */
    public static function getNewBuildDailyTaskList($buildTask, $holiday, $beforeDate, $afterDate, $flag)
    {
        foreach ($buildTask as $task) {
            //日期提前
            if ($flag == 1) {
                if (in_array($afterDate, $holiday)) {
                    $packetNum = $task->packet_num - $task->holiday_consume_material;
                } else {
                    $packetNum = $task->packet_num - $task->consume_material;
                }
            } else {
                //日期延后
                if (in_array($beforeDate, $holiday)) {
                    $packetNum = $task->packet_num + $task->holiday_consume_material;
                } else {
                    $packetNum = $task->packet_num + $task->consume_material;
                }
            }
            $task->date       = $afterDate;
            $task->packet_num = $packetNum;
            $saveTaskRes      = $task->save();
            if (!$saveTaskRes) {
                return false;
            }
        }
        return true;
    }

    /**
     * 获取符合条件的日常任务数据
     * @author  wangxiwen
     * @version 2018-06-04
     * @param   array      $date 日期
     * @param   array      $scmMaterial 运维使用的物料
     * @return  array
     */
    public static function getDailyTaskList($date, $orgIdArr, $scmMaterial)
    {
        $dailyTaskArray = self::find()
            ->andFilterWhere(['date' => $date])
            ->andFilterWhere(['org_id' => $orgIdArr])
            ->select('build_id,material_id material_type_id,org_id,packet_num,distribution_userid,weight,date,task_type,stock_code,reading')
            ->asArray()
            ->all();
        $dailyTaskList = [];
        foreach ($dailyTaskArray as $task) {
            $buildId        = $task['build_id'];
            $userId         = $task['distribution_userid'];
            $orgId          = $task['org_id'];
            $materialTypeId = $task['material_type_id'];
            $materialId     = $scmMaterial[$materialTypeId]['material_id'] ?? 0;
            $packetNum      = $task['packet_num'];
            $weight         = $task['weight'];
            $packets        = $weight > 0 ? floor($packetNum / $weight) : 0;
            $gram           = $weight > 0 ? $packetNum % $weight : 0;
            $materialWeight = $task['packet_num'];
            $taskType       = $task['task_type'];
            $stockCode      = $task['stock_code'];
            $reading        = $task['reading'];
            if (empty($dailyTaskList[$buildId])) {
                $dailyTaskList[$buildId] = [
                    'date'                => $task['date'],
                    'distribution_userid' => $userId,
                    'build_id'            => $buildId,
                    'org_id'              => $orgId,
                    'task_type'           => $taskType,
                ];
            } else {
                $dailyTaskList[$buildId]['task_type'] = self::getTaskType($dailyTaskList[$buildId]['task_type'], $taskType);
            }
            $dailyTaskList[$buildId]['material_info'][$stockCode] = [
                'material_type_id' => $materialTypeId,
                'material_id'      => $materialId,
                'packetNum'        => $packetNum,
                'weight'           => $weight,
                'packets'          => $packets,
                'gram'             => $gram,
            ];
            $dailyTaskList[$buildId]['reading'][$stockCode] = $reading;
        }
        return $dailyTaskList;
    }

    /**
     * 获取用于生成出库单的日常任务数据
     * @author wangxiwen
     * @version 2018-06-06
     * @param $where 任务下发时接收的参数
     * @return array
     */
    private static function getDailyTaskMaterialList($date)
    {
        $dailyTaskList = self::find()
            ->andWhere(['date' => $date])
            ->andWhere(['>', 'material_id', 0])
            ->andWhere(['!=', 'distribution_userid', '0'])
            ->select('distribution_userid, date, material_id as material_type_id,org_id,sum(packet_num) packet_num,weight')
            ->asArray()
            ->groupBy('distribution_userid,material_type_id')
            ->all();
        return $dailyTaskList;
    }

    /**
     * 日常任务下发，如果楼宇存在运维任务判断最终运维人员
     * @author wangxiwen
     * @version 2019-06-04
     * @param string $user 运维任务中的运维人员
     * @param string $dailyUser 日常任务中的运维人员
     * @param int $deliveryTime 任务打卡时间
     * @return string
     */
    public static function getAssignUserId($user, $dailyUser, $deliveryTime = 0)
    {
        if (!$deliveryTime && $dailyUser) {
            return $dailyUser;
        }
        return $user;
    }

    /**
     * 获取保存的任务类型
     * @author wangixnwen
     * @version 2018-06-04
     * @param $taskType 运维任务类型
     * @param $dailyTaskType 日常任务类型
     * @return string
     */
    private static function getTaskType($taskType, $dailyTaskType)
    {
        if (!$taskType) {
            return $dailyTaskType;
        }
        $taskType      = explode(',', $taskType);
        $dailyTaskType = explode(',', $dailyTaskType);
        foreach ($dailyTaskType as $dailyType) {
            if (!in_array($dailyType, $taskType)) {
                array_push($taskType, $dailyType);
            }
        }
        sort($taskType);
        $taskType = implode(',', $taskType);
        return $taskType;

    }

    /**
     * 保存日常任务下发的数据并生成出库单和预估单
     * @author wangxiwen
     * @version 2018-06-06
     * @param array $dailyTaskList 日常任务列表
     * @param array $taskList 运维任务列表
     * @param array $equipments 设备信息
     * @param array $abnormals 故障信息
     * @return bool
     */
    public static function saveTaskList($params)
    {
        list($dailyTaskList, $taskList, $equipments, $abnormals, $date, $scmMaterial, $orgIdArr) = $params;

        //获取仓库信息(分公司ID=>仓库ID)
        $scmWarehouse = self::getScmWarehouseInfo($orgIdArr);
        //获取备用料包
        $sparePackets = self::getSparePacketsList();
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        //更新运维任务
        $saveTaskRes = self::saveDistributionTask($taskList, $dailyTaskList, $equipments, $abnormals);
        //获取出库单
        $outList = self::getWarehouseOut($date, $scmMaterial, $scmWarehouse, $sparePackets);
        //保存出库单
        $saveOutRes = ScmWarehouseOut::saveWarehouseOut($outList);
        //获取出库单统计
        $outStaticList = self::getStaticOutOrEstimate($outList);
        //保存出库单统计
        $saveStaticOutRes = OutStatistics::saveWarehouseOutStatic($outStaticList);
        //获取预估单
        $estimateList = self::getWarehouseEstimate($date, $scmMaterial, $scmWarehouse, $sparePackets);
        //保存预估单
        $saveEstimateRes = ScmWarehouseEstimate::saveWarehouseEstimate($estimateList);
        //获取预估单统计
        $estimateStaticList = self::getStaticOutOrEstimate($estimateList);
        //保存预估单统计
        $saveStaticEstimateRes = EstimateStatistics::saveWarehouseEstimateStatic($estimateStaticList);
        if (!$saveTaskRes || !$saveOutRes || !$saveStaticOutRes || !$saveEstimateRes || !$saveStaticEstimateRes) {
            $transaction->rollBack();
            return false;
        }
        //删除运维日常任务表中数据
        self::deleteAll(['date' => $date, 'org_id' => $orgIdArr]);
        $transaction->commit();
        return true;
    }
    /**
     * 更新运维任务
     * @author wangxiwen
     * @version 2018-10-12
     * @param object $taskList 已存在的运维任务
     * @param array $dailyTaskList 运维日常任务
     * @param array $equipments 设备信息
     * @param array $abnormals 故障信息
     * @return
     */
    private static function saveDistributionTask($taskList, $dailyTaskList, $equipments, $abnormals)
    {
        $userList   = [];
        $insertData = [];
        foreach ($dailyTaskList as $buildId => $dailyTask) {
            //销毁清洗和维修任务中配送和添加后读数字段
            unset($dailyTask['material_info'][0]);
            unset($dailyTask['reading'][0]);
            $equipId       = $equipments[$buildId] ?? 0;
            $authorId      = Yii::$app->user->id;
            $dailyDelivery = DistributionTask::filterDelivery($dailyTask['material_info']);
            //物料添加后的读数（蓝牙秤使用）
            $readingString = !empty($dailyTask['reading']) ? Json::encode($dailyTask['reading']) : '';
            $dailyAbnormal = !empty($abnormals[$buildId]['abnormal_id']) ? Json::decode($abnormals[$buildId]['abnormal_id']) : [];
            $dailyRepair   = !empty($abnormals[$buildId]['repair']) ? Json::decode($abnormals[$buildId]['repair']) : [];
            if (!empty($taskList[$buildId])) {
                $taskObj                   = $taskList[$buildId];
                $userId                    = self::getAssignUserId($taskObj->assign_userid, $dailyTask['distribution_userid'], $taskObj->start_delivery_time);
                $taskType                  = self::getTaskType($taskObj->task_type, $dailyTask['task_type']);
                $delivery                  = $taskObj->delivery_task != '' ? Json::decode($taskObj->delivery_task) : [];
                $deliveryTaskStr           = DistributionTask::getDeliveryTask($delivery, $dailyDelivery);
                $abnormal                  = $taskObj->abnormal != '' ? Json::decode($taskObj->abnormal) : [];
                $abnormalStr               = DistributionTask::getAbnormal($abnormal, $dailyAbnormal);
                $malfunction               = $taskObj->malfunction_task != '' ? explode(',', $taskObj->malfunction_task) : [];
                $malfunctionTask           = DistributionTask::getMalfunctionTask($malfunction, $dailyRepair);
                $taskObj->build_id         = $buildId;
                $taskObj->equip_id         = $equipId;
                $taskObj->author_id        = $authorId;
                $taskObj->create_time      = time();
                $taskObj->assign_userid    = $userId ?? '';
                $taskObj->task_type        = DistributionTask::saveTaskType($taskType);
                $taskObj->malfunction_task = $malfunctionTask;
                $taskObj->delivery_task    = $deliveryTaskStr;
                $taskObj->abnormal         = $abnormalStr;
                $taskObj->reading          = $readingString;
                $taskRes                   = $taskObj->save();
                if (!$taskRes) {
                    return false;
                }
                $userList[] = $userId;
            } else {
                $userId          = $dailyTask['distribution_userid'];
                $taskType        = $dailyTask['task_type'];
                $deliveryTaskStr = DistributionTask::getDeliveryTask([], $dailyDelivery);
                $abnormalStr     = DistributionTask::getAbnormal([], $dailyAbnormal);
                $malfunctionTask = DistributionTask::getMalfunctionTask([], $dailyRepair);
                $insertData[]    = [$buildId, $equipId, $authorId, $userId, $taskType, time(), $malfunctionTask, $deliveryTaskStr, $abnormalStr, $readingString];
                $userList[]      = $userId;
            }
        }
        //批量添加新增运维数据
        if (!empty($insertData)) {
            $insertKey = ['build_id', 'equip_id', 'author_id', 'assign_userid', 'task_type', 'create_time', 'malfunction_task', 'delivery_task', 'abnormal', 'reading'];
            Yii::$app->db->createCommand()->batchInsert(DistributionTask::tableName(), $insertKey, $insertData)->execute();
        }
        //发送微信消息
        if (!empty($userList)) {
            $userList = array_unique($userList, SORT_STRING);
            foreach ($userList as $userId) {
                if (!$userId) {
                    continue;
                }
                SendNotice::sendWxNotice($userId, 'distribution-task/index', '您有新的运维任务，请注意查收。', Yii::$app->params['distribution_agentid']);
            }
        }
        return true;
    }

    /**
     * 获取出库单数据
     * @author wangixnwen
     * @version 2018-10-12
     * @param $date 日期
     * @param $scmMaterial 物料关系数组
     * @param $scmWarehouse 仓库信息
     * @param $sparePackets 备用料包
     * @return array
     */
    private static function getWarehouseOut($date, $scmMaterial, $scmWarehouse, $sparePackets)
    {
        //查询日常任务
        $dailyTaskData = self::getDailyTaskMaterialList($date);
        //获取运维人员手中剩余整包物料
        $surplusMaterial = ScmUserSurplusMaterial::getUserSurplusMaterial();
        //获取运维人员手中剩余散装物料
        $surplusMaterialGram = ScmUserSurplusMaterialGram::getUserSurplusMaterialGram();
        $outData             = [];
        foreach ($dailyTaskData as $taskData) {
            //运维人员ID
            $userId = $taskData['distribution_userid'];
            //物料分类ID
            $materialTypeId = $taskData['material_type_id'];
            //物料ID
            $materialId = $scmMaterial[$materialTypeId]['material_id'] ?? 0;
            //仓库ID
            $warehouseId = $scmWarehouse[$taskData['org_id']] ?? 0;
            //备用料包
            $sparePacket = $sparePackets[$materialId] ?? 0;
            //物料规格
            $weight = $taskData['weight'] > 0 ? $taskData['weight'] : 1;
            //运维人员手中剩余整包物料
            $materialPackets = $surplusMaterial[$userId][$materialId] ?? 0;
            //运维人员手中剩余散装物料
            $materialGram = $surplusMaterialGram[$userId][$materialTypeId] ?? 0;
            //出库单物料出库数量
            $materialOutNum = ceil(($taskData['packet_num'] - $materialPackets * $weight - $materialGram) / $weight) + $sparePacket;
            if ($materialOutNum <= 0) {
                continue;
            }
            $outData[$userId][$materialId]['date']             = $date;
            $outData[$userId][$materialId]['confirm_date']     = '';
            $outData[$userId][$materialId]['author']           = $userId;
            $outData[$userId][$materialId]['warehouse_id']     = $warehouseId;
            $outData[$userId][$materialId]['material_id']      = $materialId;
            $outData[$userId][$materialId]['material_out_num'] = $materialOutNum;
            $outData[$userId][$materialId]['status']           = 1;
            $outData[$userId][$materialId]['material_type_id'] = $materialTypeId;
            $outData[$userId][$materialId]['weight']           = $weight;
            $outData[$userId][$materialId]['org_id']           = $taskData['org_id'];
        }
        return $outData;
    }

    /**
     * 获取预估单数据
     * @author wangixnwen
     * @version 2018-06-06
     * @param $date 日期
     * @param $scmMaterial 物料关系数组
     * @param $scmWarehouse 仓库信息
     * @param $sparePackets 备用料包
     * @return array
     */
    private static function getWarehouseEstimate($date, $scmMaterial, $scmWarehouse, $sparePacket)
    {
        $date = date('Y-m-d', strtotime($date) + 60 * 60 * 24);
        //查询日常任务
        $dailyTaskData = self::getDailyTaskMaterialList($date);
        $estimateData  = [];
        foreach ($dailyTaskData as $taskData) {
            //运维人员ID
            $userId = $taskData['distribution_userid'];
            //物料分类ID
            $materialTypeId = $taskData['material_type_id'];
            //物料ID
            $materialId = $scmMaterial[$materialTypeId]['material_id'] ?? 0;
            //仓库ID
            $warehouseId = !empty($scmWarehouse[$taskData['org_id']]) ? $scmWarehouse[$taskData['org_id']] : 0;
            //备用料包
            $sparePacket = !empty($sparePackets[$materialId]) ? $sparePackets[$materialId] : 0;
            //物料规格
            $weight = $taskData['weight'] > 0 ? $taskData['weight'] : 1;
            //出库单物料出库数量
            $materialOutNum = ceil($taskData['packet_num'] / $weight) + $sparePacket;
            if ($materialOutNum <= 0) {
                continue;
            }
            $estimateData[$userId][$materialId]['date']             = $date;
            $estimateData[$userId][$materialId]['confirm_date']     = '';
            $estimateData[$userId][$materialId]['author']           = $userId;
            $estimateData[$userId][$materialId]['warehouse_id']     = $warehouseId;
            $estimateData[$userId][$materialId]['material_id']      = $materialId;
            $estimateData[$userId][$materialId]['material_out_num'] = $materialOutNum;
            $estimateData[$userId][$materialId]['status']           = 1;
            $estimateData[$userId][$materialId]['material_type_id'] = $materialTypeId;
            $estimateData[$userId][$materialId]['weight']           = $weight;
            $estimateData[$userId][$materialId]['org_id']           = $taskData['org_id'];
        }
        return $estimateData;
    }

    /**
     * 获取出库单或预估单统计数据
     * @author wangxiwen
     * @version 2018-06-13
     * @param array $outStatisticsData统计数据
     * @return array
     */
    private static function getStaticOutOrEstimate($staticArray)
    {
        $materialList = [];
        $weightList   = [];
        $staticsList  = [];
        foreach ($staticArray as $materialArray) {
            foreach ($materialArray as $material) {
                $materialTypeId = $material['material_type_id'];
                $orgId          = $material['org_id'];
                $packets        = $material['material_out_num'];
                $weight         = $material['weight'];
                $date           = $material['date'];
                if ($packets == 0) {
                    continue;
                }
                //物料规格
                $weightList[$orgId][$materialTypeId] = $weight;
                //物料加料量
                if (!empty($materialList[$orgId][$materialTypeId])) {
                    $materialList[$orgId][$materialTypeId] += $packets;
                } else {
                    $materialList[$orgId][$materialTypeId] = $packets;
                }
            }
            $staticsList[$orgId]['org_id'] = $orgId;
            $staticsList[$orgId]['status'] = 1;
            $staticsList[$orgId]['date']   = $date;
            $staticsList[$orgId]['type']   = 1;
            //将物料类型ID 加料量 规格组合到一起
            $staticsList[$orgId]['material_info'] = self::getMaterialInfo($materialList[$orgId], $weightList[$orgId]);
        }
        return $staticsList;
    }
    /**
     * 获取出库单或预估单统计保存数据
     * @author wangxiwen
     * @version 2018-06-13
     * @param array $outStatisticsData出库单统计数据
     * @return array
     */
    private static function saveOutStatisticsData($outStatisticsData)
    {
        foreach ($outStatisticsData as $orgId => $statisticsData) {
            $saveStatisticsData[] = [
                $statisticsData['org_id'],
                $statisticsData['material_info'],
                $statisticsData['status'],
                $statisticsData['date'],
                $statisticsData['type'],
            ];
        }
        return $saveStatisticsData;
    }

    /**
     * 出库单统计将物料类型ID 加料量 规格组合到一起
     * @author wangxiwen
     * @version 2018-06-13
     * @param $material 物料类型和添加量
     * @param $weights 物料规格
     * @return string|json
     */
    private static function getMaterialInfo($material, $weights)
    {
        $materialInfo = [];
        foreach ($material as $materialId => $packets) {
            $weight                    = $weights[$materialId];
            $materialInfo[$materialId] = $weight . '|' . $packets;
        }
        return Json::encode($materialInfo);
    }
    /**
     * 获取备用料包数据
     * @author wangxiwen
     * @version 2018-06-07
     * return array
     */
    private static function getSparePacketsList()
    {
        $sparePackets    = DistributionSparePackets::find()->asArray()->all();
        $sparePacketList = [];
        foreach ($sparePackets as $sparePacket) {
            $sparePacketList[$sparePacket['material_id']] = $sparePacket['packets'];
        }
        return $sparePacketList;
    }

    /**
     * 获取仓库信息
     * @author wangxiwen
     * @version 2018-06-07
     * @return array
     */
    private static function getScmWarehouseInfo($orgIdArr)
    {
        $scmWarehouse = ScmWarehouse::find()
            ->andFilterWhere(['use' => 0])
            ->andFilterWhere(['organization_id' => $orgIdArr])
            ->select('id,organization_id')
            ->asArray()
            ->all();
        return Tools::map($scmWarehouse, 'organization_id', 'id', null, null);
    }

    /**
     * 修改日常任务人员
     * @author wangxiwen
     * @version 2018-10-23
     * @param int $buildId 楼宇ID
     * @param string $date 日期
     * @param string $userid 更换人员
     * @return boolean
     */
    public static function saveDailyTaskUser($buildId, $date, $userid)
    {
        return self::updateAll(['distribution_userid' => $userid], ['build_id' => $buildId, 'date' => $date]);
    }
    /**
     * 获取运维日常任务
     * @author wangxiwen
     * @version 2018-10-23
     * @param string $date 更换后的日期
     * @param int $buildId 楼宇ID
     * @param string $userId 用户ID
     * @return int
     */
    public static function getDailyTaskCount($date, $buildId, $userId)
    {
        return self::find()
            ->andWhere(['date' => $date])
            ->andWhere(['build_id' => $buildId])
            ->andWhere(['distribution_userid' => $userId])
            ->count();
    }

    /**
     * 获取预估单中任务涉及楼宇
     * @author wangxiwen
     * @version 2018-06-21
     * @param int $orgId 分公司ID
     * @param string $date 日期 格式yyyy-mm-dd
     * @return array
     */
    public static function getBuildArray($orgId, $date)
    {
        $where      = [];
        $date       = date('Y-m-d', strtotime($date) + 60 * 60 * 24);
        $where      = $orgId > 1 ? ['dt.org_id' => $orgId] : [];
        $buildArray = self::find()
            ->alias('dt')
            ->distinct()
            ->leftJoin('building b', 'dt.build_id = b.id')
            ->andWhere(['date' => $date])
            ->andWhere($where)
            ->select('dt.distribution_userid,b.name')
            ->asArray()
            ->all();
        $buildList = [];
        foreach ($buildArray as $build) {
            $userId = $build['distribution_userid'];
            if (!$userId) {
                continue;
            }
            $buildList[$userId][] = $build['name'];
        }
        return $buildList;
    }

}
