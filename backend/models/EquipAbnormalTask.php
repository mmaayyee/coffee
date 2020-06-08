<?php

namespace backend\models;

use backend\models\DistributionUserSchedule;
use common\helpers\Tools;
use common\models\Building;
use common\models\Equipments;
use Yii;

/**
 * This is the model class for table "equip_abnormal_task".
 *
 * @property int $task_id 设备故障任务ID
 * @property string $equip_code 设备编号
 * @property int $build_id 楼宇ID
 * @property int $org_id 分公司ID
 * @property int $create_time 设备故障任务添加时间
 * @property string $abnormal_id 设备故障内容
 * @property int $task_status 任务状态1未操作2下发3转到次日
 */
class EquipAbnormalTask extends \yii\db\ActiveRecord
{
    const Untreated = 1; //未处理
    const LowerHair = 2; //下发
    const NEXTDAY   = 3; //次日
    const COMPLETE  = 4; //已完成

    public static $task_status = [
        1 => '未处理',
        2 => '已下发',
        3 => '转到次日',
        4 => '已完成',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_abnormal_task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_code', 'build_id', 'org_id', 'create_time', 'type'], 'required'],
            [['build_id', 'org_id', 'task_status'], 'integer'],
            [['equip_code'], 'string', 'max' => 50],
            [['abnormal_id'], 'string', 'max' => 1000],
            [['repair'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'task_id'     => '故障任务编号',
            'equip_code'  => '设备编号',
            'build_id'    => '楼宇',
            'org_id'      => '分公司',
            'create_time' => '任务创建时间',
            'abnormal_id' => '异常故障内容',
            'task_status' => '状态',
            'repair'      => '上报故障内容',
            'type'        => '生成故障方式',
        ];
    }
    /**
     * 获取运维人员
     * @author wangxiwen
     * @param $build 楼宇ID
     * @param $date  日期
     * return array
     */
    public static function getDistributionUser($build, $date)
    {

        $buildInfo = self::getBuildInfo($build);

        $userid = self::getScheduleStatus($date, $buildInfo);
        return $userid;
    }
    /**
     * 获取楼宇信息
     * @author wangxiwen
     * @param $build 楼宇ID
     * return array
     */
    public static function getBuildInfo($build)
    {
        $buildInfo = Building::find()
            ->alias('bd')
            ->leftJoin('equipments eq', 'bd.id = eq.build_id')
            ->select('bd.distribution_userid,eq.org_id')
            ->where(['bd.id' => $build])
            ->asArray()
            ->one();
        return $buildInfo;

    }
    /**
     * 通过日期和楼宇ID、分公司ID获取运维人员
     * @param $date 日期
     * $param $buildInfo 楼宇ID和分公司ID
     * return array
     */
    public static function getScheduleStatus($date, $buildInfo)
    {
        $yearMonth = date('Y-m', $date);
        $day       = date('d', $date);
        $userid    = $buildInfo['distribution_userid'];
        $userList  = [];
        $org_id    = $buildInfo['org_id'];
        //如果楼宇负责人不存在则取该楼宇所属分公司下运维人员列表
        if (empty($userid)) {
            $userList = \common\models\WxMember::getMemberIDArr($org_id);
        }
        if (!empty($userid)) {
            $scheduleInfo = self::getDistributionScheduleData($yearMonth, $userid);
            $user         = self::getWorkDistributionUser($day, $scheduleInfo);
            if (!$user) {
                $user = self::getWorkDistributionUser($date, $userList);
                if (!$user) {
                    return '';
                }
            }
        } elseif (!empty($userList)) {
            $scheduleInfo = self::getDistributionScheduleData($yearMonth, $userList);
            $user         = self::getWorkDistributionUser($day, $scheduleInfo);
            if (!$user) {
                return '';
            }
        } else {
            return '';
        }
        return $user;

    }

    /**获取排班数据
     * @author wangxiwen
     * @param $yearMonth 年月
     * @param $userInfo  运维人员
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getDistributionScheduleData($yearMonth, $userInfo)
    {

        $scheduleInfo = DistributionUserSchedule::find()
            ->select('userid,schedule')
            ->andWhere(['date' => $yearMonth])
            ->andWhere(['in', 'userid', $userInfo])
            ->asArray()
            ->all();
        return $scheduleInfo;
    }
    /**
     * 如果楼宇负责人当前日期工作状态为请假则从该分公司随机选择一名状态为上班的运维人员
     * @param $date 日期
     * $param $userid 运维人员
     * return array
     */
    public static function getWorkDistributionUser($day, $scheduleInfo)
    {

        $patten = '/' . $day . '-1/';
        foreach ($scheduleInfo as $schedule) {
            preg_match_all($patten, $schedule['schedule'], $matches);
            if ($matches[0]) {
                $distributionUser[$schedule['userid']] = $matches[0][0];
            } else {
                $distributionUser[$schedule['userid']] = '';
            }
        }
        //当前日期上班的运维人员
        $userInfo = [];
        if (!empty($distributionUser)) {
            foreach ($distributionUser as $userid => $status) {
                if ($status) {
                    $userInfo[] = $userid;
                }
            }
        }
        $userinfoCount = count($userInfo);
        if ($userinfoCount > 0) {
            $userinfoKey = rand(1, $userinfoCount);
            return $userInfo[$userinfoKey - 1];
        } else {
            return '';
        }
    }

    /**
     * 获取故障任务列表
     * @author wangxiwen
     * @datetime 2018-06-04
     * @param arrray $where 查询条件
     * return array
     */
    public static function getEquipAbnormals($orgIdArr)
    {
        $abnormals = self::find()
            ->andFilterWhere(['task_status' => self::NEXTDAY])
            ->andFilterWhere(['org_id' => $orgIdArr])
            ->asArray()
            ->all();
        $abnormalsList = [];
        foreach ($abnormals as $abnormal) {
            $abnormalsList[$abnormal['build_id']] = $abnormal;
        }
        return $abnormalsList;
    }

    /**
     * 获取故障任务
     * @author wangxiwen
     * @version 2018-06-27
     * @param $buildId 楼宇ID
     * @return object
     */
    public static function getEquipAbnormalTask($buildId)
    {
        return self::find()
            ->andWhere(['build_id' => $buildId])
            ->andWhere(['in', 'task_status', [EquipAbnormalTask::LowerHair, EquipAbnormalTask::NEXTDAY]])
            ->one();
    }

    /**
     * 获取故障列表
     * 在组合数据时如果设备状态时正常则需要将故障表中对应楼宇转次日的状态修改成已完成,是否有维修任务字段修改成无
     * @author wangxiwen
     * @version 2018-05-23
     */
    public static function getAbnormals()
    {
        $abnormalArr = self::find()
            ->alias('a')
            ->leftJoin('equipments e', 'a.build_id = e.build_id ')
            ->andWhere(['a.task_status' => EquipAbnormalTask::NEXTDAY])
            ->andWhere(['>', 'e.build_id', 0])
            ->andWhere(['in', 'e.operation_status', [Equipments::COMMERCIAL_OPERATION, Equipments::INTERNAL_USE, Equipments::TEMPORARY_OPERATIONS]])
            ->select('a.task_id,e.equip_code')
            ->asArray()
            ->all();
        return Tools::map($abnormalArr, 'equip_code', 'task_id', null, null);
    }

    /**
     * 获取维修任务标志(日常任务使用)
     * @author wangxiwen
     * @version 2018-10-16
     * @param int $taskId 任务ID
     * @param int $equipStatus 设备状态
     * @return boolean false无维修任务true有维修任务
     */
    public static function getMaintenanceSign($taskId, $equipStatus)
    {
        if (!$taskId) {
            return false;
        }
        if ($equipStatus == 1) {
            //更新故障状态为已完成
            self::saveEquipAbnormalStatus($taskId);
            return false;
        }
        return true;
    }

    /**
     * 更新故障任务状态
     * @param  [type] $taskId [description]
     * @return [type]         [description]
     */
    private static function saveEquipAbnormalStatus($taskId)
    {
        $abnormal              = self::getEquipAbnormal($taskId);
        $abnormal->task_status = self::COMPLETE;
        return $abnormal->save();
    }

    /**
     * 获取故障任务
     * @author wangxiwen
     * @version 2018-10-16
     * @param int $taskId 任务ID
     * @return object
     */
    private static function getEquipAbnormal($taskId)
    {
        return self::findOne($taskId);
    }
}
