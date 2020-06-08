<?php
namespace common\dailyTask;

use backend\models\BuildingHolidayStatus;
use backend\models\BuildingTaskSetting;
use backend\models\DistributionDailyTask;
use backend\models\DistributionTask;
use backend\models\DistributionUser;
use backend\models\DistributionUserSchedule;
use backend\models\EquipAbnormalTask;
use backend\models\EquipMaterialStockAssoc;
use backend\models\EquipmentTaskSetting;
use backend\models\Holiday;
use backend\models\ScmMaterial;
use backend\models\TemporaryEquipSetting;
use common\models\Api;
use common\models\Equipments;
use common\models\WxMember;
use Yii;
use yii\helpers\Json;

/**
 * Created by PhpStorm.
 * User: wangxl
 * Date: 17/7/4
 * Time: 下午3:45
 */
class Tasks
{

    public function distributionTask()
    {
        //获取设备料仓剩余物料和上下限(设备编号=>料仓编号=>剩余物料、料仓上限、料仓下限)
        $stockRamin    = Api::getBase('stock-remain');
        $materialStock = $stockRamin ? Json::decode($stockRamin) : [];
        //节假日平均消耗数据(设备编号=>物料分类=>物料消耗)
        $holidayDate            = Holiday::getHolidayDate();
        $holidayConsume         = Api::postBaseGetAvgConsume('avg-consume', $holidayDate);
        $holidayMaterialConsume = $holidayConsume ? Json::decode($holidayConsume) : [];
        //工作日平均消耗数据(设备编号=>物料分类=>物料消耗)
        $workDate            = Holiday::getWorkDate();
        $workConsume         = Api::postBaseGetAvgConsume('avg-consume', $workDate);
        $workMaterialConsume = $workConsume ? Json::decode($workConsume) : [];
        //获取设备故障数据
        $abnormals    = EquipAbnormalTask::getAbnormals();
        $stockSetting = [
            $materialStock,
            $holidayMaterialConsume,
            $workMaterialConsume,
            $abnormals,
        ];
        //获取设备配置信息
        $equipSetting = self::getEquipSetting($stockSetting);
        //将配置信息存入临时表中(便于添加临时任务时计算添加量、剩余量、最终读数)
        TemporaryEquipSetting::insertEquipSetting($equipSetting);
        //生成日常任务数据
        $ret = self::getDailyTask($equipSetting);
        if ($ret === false) {
            echo '任务执行失败';
        }
        echo '任务执行成功';
    }

    /**
     * 换料周期、清洗周期、配送天数设置
     * @author wangxiwen
     * @version 2018-05-17
     * @param array $materialStock 设备料仓上下限及剩余物料
     * @param array $holidayMaterialConsume 节假日平均消耗
     * @param array $workMaterialConsume 工作日平均消耗
     * @param array $abnormals 设备故障任务
     * @return array
     */
    private static function getEquipSetting($stockSetting)
    {
        list($materialStock, $holidayMaterialConsume, $workMaterialConsume, $abnormals) = $stockSetting;
        //获取设备信息
        $equipments = Equipments::getEquipments();
        //获取根据楼宇设置的配送周期，清洗周期，换料周期数据
        $buildSetting = BuildingTaskSetting::getBuildSetting();
        //获取根据分公司和设备类型设置的配送周期，清洗周期，换料周期数据
        $equipmentSetting = EquipmentTaskSetting::getEquipmentSetting();
        //获取设备运维周期信息*
        $equipCycle = self::getEquipCycle($equipments, $buildSetting, $equipmentSetting);
        //获取人员管理排班数据
        $schedules = DistributionUserSchedule::getUserScheduleStatus();
        //获取运维人员和组长(第一和第二级别)
        $leaders = DistributionUser::getUserAssoc();
        //获取运维主管(第三级别)
        $directors = WxMember::getDistributeDirector();
        //获取设备运维人员信息*
        $equipUser = self::getEquipUser($equipments, $schedules, $leaders, $directors);
        //获取设备料仓信息*
        $equipStockMaterial = EquipMaterialStockAssoc::getEquipStockMaterialDetail();
        //获取物料信息
        $scmMaterial  = ScmMaterial::getScmMaterial();
        $equipSetting = [
            $equipStockMaterial,
            $equipCycle,
            $equipUser,
            $materialStock,
            $holidayMaterialConsume,
            $workMaterialConsume,
            $abnormals,
            $scmMaterial,
        ];
        //获取设备配置信息*
        return self::getEquipStockConfig($equipSetting);
    }

    /**
     * 获取楼宇负责人
     * @author wangxiwen
     * @version 2018-05-21
     * @param array $equipments 设备信息
     * @param array $schedules 排班
     * @param array $leaders   运维人员的运维组长
     * @param array $directors 运维主管
     * @return array
     */
    private static function getEquipUser($equipments, $schedules, $leaders, $directors)
    {
        $equipmentList = [];
        foreach ($equipments as $equipment) {
            $equipCode = $equipment['equip_code'];
            $userId    = $equipment['distribution_userid'];
            $orgId     = $equipment['org_id'];

            $equipmentList[$equipCode]['userid']           = '';
            $equipmentList[$equipCode]['org_id']           = $orgId;
            $equipmentList[$equipCode]['wash_time']        = $equipment['wash_time'];
            $equipmentList[$equipCode]['equipment_status'] = $equipment['equipment_status'];

            //运维人员工作状态1上班2休息3请假,默认为上班
            $status = $schedules[$userId] ?? 1;
            //运维组长
            $leader = !empty($leaders[$userId]) ? $leaders[$userId] : '';
            //运维组长工作状态
            $leaderStatus = $schedules[$leader] ?? 1;
            //运维主管
            $director = !empty($directors[$orgId]) ? $directors[$orgId] : [];

            $equipmentList[$equipCode]['userid'] = '';
            //运维人员&&工作状态为上班则直接分配
            if ($userId && $status == 1) {
                $equipmentList[$equipCode]['userid'] = $userId;
                continue;
            }
            //运维人员&&工作状态为休息&&运维组长&&工作状态为上班则分配给其组长
            if ($userId && $status != 1 && $leader && $leaderStatus == 1) {
                $equipmentList[$equipCode]['userid'] = $leader;
                continue;
            }
            //(有运维人员&&有运维组长&&工作状态为休息||没有运维人员)&&有运维主管则直接分配给运维主管
            if ((($userId != '' && $status != 1 && $leader != '' && $leaderStatus != 1) || ($userId == '')) && !empty($director)) {
                //运维主管可能存在多人，随机分配一人
                $directorCount                       = count($director);
                $random                              = mt_rand(0, $directorCount - 1);
                $equipmentList[$equipCode]['userid'] = $director[$random];
                continue;
            }
        }
        return $equipmentList;
    }

    /**
     * 获取设备料仓配置信息
     * @author wangxiwen
     * @version 2018-10-15
     * @param array $equipStockMaterial 设备料仓信息
     * @param array $equipCycle 设备运维周期信息
     * @param array $equipUser 设备运维人员信息
     * @param array $materialStock 料仓上下限及剩余物料
     * @param array $holidayMaterialConsume 节假日平均消耗
     * @param array $workMaterialConsume 工作日平均消耗
     * @return
     */
    private static function getEquipStockConfig($equipSetting)
    {
        list($equipStockMaterial, $equipCycle, $equipUser, $materialStock, $holidayMaterialConsume, $workMaterialConsume, $abnormals, $scmMaterial) = $equipSetting;

        $equipStockList = [];
        foreach ($equipStockMaterial as $equipCode => $stockMaterial) {

            $equipStockList[$equipCode]['userid']           = $equipUser[$equipCode]['userid'] ?? '';
            $equipStockList[$equipCode]['org_id']           = $equipUser[$equipCode]['org_id'] ?? 0;
            $equipStockList[$equipCode]['wash_time']        = $equipUser[$equipCode]['wash_time'] ?? 0;
            $equipStatus                                    = $equipUser[$equipCode]['equipment_status'] ?? 0;
            $equipStockList[$equipCode]['equipment_status'] = $equipStatus;
            $equipStockList[$equipCode]['day_num']          = $equipCycle[$equipCode]['day_num'] ?? 0;
            $equipStockList[$equipCode]['cleaning_cycle']   = $equipCycle[$equipCode]['cleaning_cycle'] ?? 0;
            $refuelCycle                                    = $equipCycle[$equipCode]['refuel_cycle'] ?? [];
            $taskId                                         = $abnormals[$equipCode] ?? 0;
            $equipStockList[$equipCode]['is_repair']        = EquipAbnormalTask::getMaintenanceSign($taskId, $equipStatus) ? 1 : 0;
            foreach ($stockMaterial as $stockCode => $material) {
                $materialTypeId                         = $material['material_type'];
                $equipStockList[$equipCode]['build_id'] = $material['build_id'];

                $equipStockList[$equipCode]['refuel_cycle'][$stockCode] = [
                    'material_type'       => $materialTypeId,
                    'weight'              => $scmMaterial[$materialTypeId]['weight'] ?? 0,
                    'stock_id'            => $material['stock_id'] ?? 0,
                    'refuel_time'         => $material['refuel_time'] ?? 0,
                    'refuel_cycle'        => $refuelCycle[$materialTypeId] ?? 0,
                    'surplus_material'    => $materialStock[$equipCode][$stockCode]['surplus_material'] ?? 0,
                    'stock_volume_bound'  => $materialStock[$equipCode][$stockCode]['stock_volume_bound'] ?? 0,
                    'bottom_value'        => $materialStock[$equipCode][$stockCode]['bottom_value'] ?? 0,
                    'holiday_consumption' => $holidayMaterialConsume[$equipCode][$materialTypeId] ?? 0,
                    'work_consumption'    => $workMaterialConsume[$equipCode][$materialTypeId] ?? 0,
                ];
            }
        }
        return $equipStockList;
    }

    /**
     * 获取设备周期配置信息
     * @author wangxiwen
     * @version 2018-10-13
     * @param array $equipments 设备信息
     * @param array $buildSetting 楼宇周期
     * @param array $equipmentSetting 设备周期
     * @return
     */
    private static function getEquipCycle($equipments, $buildSetting, $equipmentSetting)
    {
        $taskSettingList = [];
        foreach ($equipments as $equipment) {
            $orgId       = $equipment['org_id'];
            $equipTypeId = $equipment['equip_type_id'];
            $buildId     = $equipment['build_id'];
            $equipCode   = $equipment['equip_code'];
            //按楼宇获得周期
            if (!empty($buildSetting[$buildId])) {
                $taskSettingList[$equipCode] = $buildSetting[$buildId];
                continue;
            }
            //按分公司和设备类型获得周期
            if (!empty($equipmentSetting[$equipTypeId])) {
                if (!empty($equipmentSetting[$equipTypeId][$orgId])) {
                    $taskSettingList[$equipCode] = $equipmentSetting[$equipTypeId][$orgId];
                    continue;
                }
                if (!empty($equipmentSetting[$equipTypeId][0])) {
                    $taskSettingList[$equipCode] = $equipmentSetting[$equipTypeId][0];
                    continue;
                }
            } else {
                if (!empty($equipmentSetting[0][$orgId])) {
                    $taskSettingList[$equipCode] = $equipmentSetting[0][$orgId];
                    continue;
                }
                if (!empty($equipmentSetting[0][0])) {
                    $taskSettingList[$equipCode] = $equipmentSetting[0][0];
                    continue;
                }
            }
        }
        return $taskSettingList;
    }

    /**
     * 生成日常任务数据
     * @author wangxiwen
     * @version 2018-05-28
     * @param $equipSetting 设备基础信息
     * @return array
     */
    private static function getDailyTask($equipSetting)
    {
        //获取当前时间后30天的节假日
        $futureHoliday = Holiday::getFutureHoliday();
        //获取当前日常任务数据
        $firstData       = self::createDailyTask($equipSetting, $futureHoliday, 1);
        $dailyTaskList[] = $firstData['dailyTaskList'];
        $equipSetting    = $firstData['equipSetting'];
        //获取第二天日常任务数据
        $secondData      = self::createDailyTask($equipSetting, $futureHoliday, 2);
        $dailyTaskList[] = $secondData['dailyTaskList'];
        $equipSetting    = $secondData['equipSetting'];
        //获取第三天日常任务数据
        $thirdData       = self::createDailyTask($equipSetting, $futureHoliday, 3);
        $dailyTaskList[] = $thirdData['dailyTaskList'];
        //获取不运维楼宇
        $buildInoperate = BuildingHolidayStatus::getHolidayInoperateBuild();
        //获取三天中的节假日日期
        $dateList = Holiday::getFutureInoperate();
        //保存数据
        return self::saveDailyTask($dailyTaskList, $buildInoperate, $dateList);
    }

    /**
     * 获取三天的运维日常任务数据
     * @author wangxiwen
     * @version 2018-05-29
     * @param array $equipSetting 组合数据
     * @param array $futureHoliday 未来30天节假日日期
     * @param int $days 第几天
     * @return array
     */
    private static function createDailyTask($equipSetting, $futureHoliday, $days)
    {
        $dailyTaskList = [];
        foreach ($equipSetting as $equipCode => &$setting) {
            //验证设备是否需要清洗
            $needClean = self::verifyClean($setting['wash_time'], $setting['cleaning_cycle'], $days);
            //验证料仓是否需要换料
            $stockCodes = self::verifyRefuel($setting['refuel_cycle'], $days);
            //日常任务-加料|换料
            $dailyTask = self::addMaterial($stockCodes, $setting, $futureHoliday, $days, $needClean);
            $setting   = $dailyTask['equipSetting'];
            //获取清洗任务数据及更新设备清洗时间
            if ($needClean || !empty($dailyTask['materialList'])) {
                //更改清洗周期为最新
                $setting['wash_time']               = time() + ($days - 1) * 60 * 60 * 24;
                $taskType                           = DistributionTask::CLEAN;
                $dailyTask['materialList']['clean'] = self::getCleanOrRepair($setting, $taskType, $days);
            }
            //获取维修任务数据
            if ($setting['is_repair'] == 1) {
                //日常任务中如果存在维修任务则必须在第一天生成维修任务
                $setting['is_repair']                = 0;
                $taskType                            = DistributionTask::SERVICE;
                $dailyTask['materialList']['repair'] = self::getCleanOrRepair($setting, $taskType, $days);
            }
            if (empty($dailyTask['materialList'])) {
                continue;
            }
            $dailyTaskList[$equipCode] = $dailyTask['materialList'];
            unset($setting);
        }
        return [
            'dailyTaskList' => $dailyTaskList,
            'equipSetting'  => $equipSetting,
        ];
    }

    /**
     * 获取清洗|维修任务的数据(日常任务使用)
     * @author wangxiwen
     * @version 2018-10-16
     * @param array $equipSetting 设备配置信息
     * @param int   $taskType 设备类型
     * @param int   $days 天数
     * @return array
     */
    private static function getCleanOrRepair($equipSetting, $taskType, $days)
    {
        return [
            'build_id'                 => $equipSetting['build_id'],
            'material_id'              => 0,
            'org_id'                   => $equipSetting['org_id'],
            'consume_material'         => 0,
            'packet_num'               => 0,
            'distribution_userid'      => $equipSetting['userid'],
            'weight'                   => 0,
            'date'                     => date('Y-m-d', time() + ($days - 1) * 60 * 60 * 24),
            'task_type'                => $taskType,
            'holiday_consume_material' => 0,
            'stock_code'               => 0,
            'is_recharge'              => 0,
            'reading'                  => 0,
        ];
    }

    /**
     * 保存日常任务
     * @author wangxiwen
     * @version 2018-05-29
     * @param array $dailyTaskList 日常任务
     * @param array $buildInoperate 节假日不运维楼宇
     * @param array $dateList 节假日列表
     * @return array
     */
    private static function saveDailyTask($dailyTaskList, $buildInoperate, $dateList)
    {
        foreach ($dailyTaskList as $dailyTaskArray) {
            foreach ($dailyTaskArray as $equipCode => $dailyTask) {
                if (empty($dailyTask)) {
                    continue;
                }
                foreach ($dailyTask as $task) {
                    if (in_array($task['build_id'], $buildInoperate) && in_array($task['date'], $dateList)) {
                        continue;
                    }
                    $dailyTaskObj = new DistributionDailyTask();

                    $taskList[] = [
                        $task['build_id'],
                        $task['material_id'],
                        $task['org_id'],
                        $task['consume_material'],
                        $task['packet_num'],
                        $task['distribution_userid'],
                        $task['weight'],
                        $task['date'],
                        $task['task_type'],
                        $task['holiday_consume_material'],
                        $task['stock_code'],
                        $task['is_recharge'],
                        $task['reading'],
                    ];
                }
            }
        }
        //插入数据之前需要清空数据库
        DistributionDailyTask::deleteAll();

        return Yii::$app->db->createCommand()->batchInsert('distribution_daily_task', [
            'build_id',
            'material_id',
            'org_id',
            'consume_material',
            'packet_num',
            'distribution_userid',
            'weight',
            'date',
            'task_type',
            'holiday_consume_material',
            'stock_code',
            'is_recharge',
            'reading',
        ], $taskList)->execute();

    }

    /**
     * 获取配送周期内物料总消耗
     * @author wangxiwen
     * @version 2018-10-16
     * @param array $workConsume 工作日平均消耗
     * @param array $holidayConsume 节假日平均消耗
     * @param array $distributeCycle 配送周期
     * @param array $futureHoliday 节假日日期
     * @param int $days 天数(1第一天2第二天3第三天---三天日常任务)
     * @return int
     */
    public static function getConsumeTotal($workConsume, $holidayConsume, $distributeCycle, $futureHoliday, $days)
    {
        $consumeTotal = 0;
        for ($i = 0; $i < $distributeCycle; $i++) {
            $date = date('Y-m-d', (time() + ($i + $days - 1) * 60 * 60 * 24));
            if (in_array($date, $futureHoliday)) {
                $consumeTotal += $holidayConsume;
            } else {
                $consumeTotal += $workConsume;
            }
        }
        return $consumeTotal;
    }

    /**
     * 验证是否满足换料任务
     * @author wangxiwen
     * @version 2018-5-28
     * @param array $refuelCycle 料仓信息
     * @param int $days 天数
     * @return true|false
     */
    private static function verifyRefuel($refuelCycle, $days)
    {
        $stockCodes = [];
        foreach ($refuelCycle as $stockCode => $cycle) {
            $refuelTime  = $cycle['refuel_time'];
            $refuelCycle = $cycle['refuel_cycle'];
            if (!$refuelCycle) {
                continue;
            }
            //当前日期-上次换料日期 - 换料周期 > 0
            $times = (time() + ($days - 1) * 60 * 60 * 24) - $refuelTime - $refuelCycle * 60 * 60 * 24;
            if ($times > 0) {
                $stockCodes[] = $stockCode;
            }
        }
        return $stockCodes;
    }

    /**
     * 验证是否满足清洗任务
     * @author  wangxiwen
     * @version 2018-5-28
     * @param   int $washTime   设备上次清洗时间
     * @param   int $cleanCycle 设备清洗周期
     * @param   int $days       天数
     * @return  boolean
     */
    private static function verifyClean($washTime, $cleanCycle, $days)
    {
        //当前时间 - 上次清洗时间 > 换料周期
        return time() + ($days - 1) * 60 * 60 * 24 - $washTime - $cleanCycle * 60 * 60 * 24 >= 0 ? true : false;
    }

    /**
     * 获取物料添加的数据
     * @author wangxiwen
     * @version 2018-05-28
     * @param array $stockCodes 需要换料的料仓
     * @param array $equipSetting 设备料仓基本信息数组
     * @param array $futureHoliday 节假日日期数组
     * @param integer $days 天数
     * @param boolean $needClean 是否需要清洗标志true是false否
     * @return
     */
    private static function addMaterial($stockCodes, $equipSetting, $futureHoliday, $days, $needClean)
    {
        $materialList = [];
        foreach ($equipSetting['refuel_cycle'] as $stockCode => &$stockSetting) {
            $distributeCycle = $equipSetting['day_num'];
            $workConsume     = $stockSetting['work_consumption'];
            $holidayConsume  = $stockSetting['holiday_consumption'];
            $surplusMaterial = $stockSetting['surplus_material'];
            $upperLimit      = $stockSetting['stock_volume_bound'];
            $lowerLimit      = $stockSetting['bottom_value'];
            //未来两天的物料消耗
            $twoDaysConsume = self::getConsumeTotal($workConsume, $holidayConsume, 2, $futureHoliday, $days);
            //配送周期时间内的物料消耗
            $consumeTotal = self::getConsumeTotal($workConsume, $holidayConsume, $distributeCycle, $futureHoliday, $days);
            //注:只要设备料仓存在加料操作则默认换料时间更新为当前时间且料仓剩余物料更新为加料后的数值
            //换料
            $feedConsume = 0;
            $refuelRes   = false;
            if (in_array($stockCode, $stockCodes)) {
                //如果料仓需要换料则料仓剩余物料自动清零，只需要计算配送周期内的物料消耗即可
                $feedConsume = $consumeTotal + $lowerLimit > $upperLimit ? $upperLimit : $consumeTotal + $lowerLimit;
                //添加物料如果大于0小于100则默认按100g计算
                if ($feedConsume > 0 && $feedConsume < 100) {
                    $feedConsume = 100;
                }
                //加料后料仓读数(蓝牙秤初始化数据使用)
                $reading    = $feedConsume;
                $taskType   = DistributionTask::REFUEL;
                $isRecharge = 1;
                $refuelRes  = true;
            }
            //判断料仓剩余物料是否满足2天或者配送周期内的物料消耗
            $isSatisfyConsume      = $surplusMaterial - $twoDaysConsume - $lowerLimit > 0 ? true : false;
            $isSatisfyCycleConsume = $surplusMaterial - $consumeTotal - $lowerLimit > 0 ? true : false;
            //加料
            if (!$refuelRes && ((!$needClean && !$isSatisfyConsume) || (!$isSatisfyCycleConsume && $needClean))) {
                //料仓最大添加量
                $stockMaxAdd = $upperLimit - $surplusMaterial;
                //料仓实际应添加量
                $feedConsume = $consumeTotal - $surplusMaterial + $lowerLimit;
                $feedConsume = $feedConsume > $stockMaxAdd ? $stockMaxAdd : $feedConsume;
                //添加物料如果大于0小于100则默认按100g计算
                if ($feedConsume > 0 && $feedConsume < 100) {
                    $feedConsume = 100;
                }
                $reading    = $feedConsume + $surplusMaterial;
                $taskType   = DistributionTask::DELIVERY;
                $isRecharge = 0;

            }
            if ($feedConsume <= 0) {
                continue;
            }
            $stockSetting['refuel_time'] = time() + ($days - 1) * 60 * 60 * 24;
            //计算料仓加料完成后的剩余物料
            $stockSetting['surplus_material'] = self::getSurplusMaterial($feedConsume, $workConsume, $holidayConsume, $reading, $futureHoliday, $days);

            $materialList[$stockCode] = [
                'build_id'                 => $equipSetting['build_id'],
                'material_id'              => $stockSetting['material_type'],
                'org_id'                   => $equipSetting['org_id'],
                'consume_material'         => $workConsume,
                'packet_num'               => $feedConsume,
                'distribution_userid'      => $equipSetting['userid'],
                'weight'                   => $stockSetting['weight'],
                'date'                     => date('Y-m-d', time() + ($days - 1) * 60 * 60 * 24),
                'task_type'                => $taskType,
                'holiday_consume_material' => $holidayConsume,
                'stock_code'               => $stockCode,
                'is_recharge'              => $isRecharge,
                'reading'                  => $reading,
            ];
            unset($stockSetting);
        }
        return [
            'materialList' => $materialList,
            'equipSetting' => $equipSetting,
        ];
    }

    /**
     * 获取加完物料后的料仓剩余物料
     * @author  wangxinwen
     * @version 2018-05-30
     * @param   float $feedConsume 添加物料量
     * @param   float $workConsume 工作日物料消耗
     * @param   float $holidayConsume 节假日物料消耗
     * @param   float $surplusMaterial 剩余物料
     * @param   array $futureHoliday 节假日日期
     * @param   array $days 天数
     * @return  float
     */
    private static function getSurplusMaterial($feedConsume, $workConsume, $holidayConsume, $surplusMaterial, $futureHoliday, $days)
    {
        $consumption = self::getCurrentConsume($workConsume, $holidayConsume, $futureHoliday, $days);
        return $feedConsume + $surplusMaterial - $consumption;
    }

    /**
     * 获取日期当天的平均消耗
     * @author wangxiwen
     * @version 2018-05-16
     * @param array $workConsume 工作日物料消耗
     * @param array $holidayConsume 节假日物料消耗
     * @param array $futureHoliday 节假日日期
     * @param int $days 天数
     * @return int
     */
    private static function getCurrentConsume($workConsume, $holidayConsume, $futureHoliday, $days)
    {
        $date = date('Y-m-d', (time() + 60 * 60 * 24 * ($days - 1)));
        if (in_array($date, $futureHoliday)) {
            return $holidayConsume;
        } else {
            return $workConsume;
        }
    }

}
