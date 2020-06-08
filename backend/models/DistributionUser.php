<?php

namespace backend\models;

use backend\models\Manager;
use common\helpers\Tools;
use common\models\EquipTask;
use common\models\WxMember;
use Yii;

/**
 * This is the model class for table "distribution_user".
 *
 * @property string $userid
 * @property integer $user_status
 * @property string $leader_id
 * @property integer $is_leader
 *
 * @property WxMember $user
 */
class DistributionUser extends \yii\db\ActiveRecord
{
    public $orgId;
    /** 配送员状态常量 */
    // 上班
    const WORK_ON = 1;
    // 休息
    const WORK_OFF = 2;
    // 请假
    const SLEEP = 3;

    /** 是否是组长常量 */
    // 是组长
    const LEADER_ON = 1;
    // 不是组长
    const LEADER_OFF = 2;

    //是否为组长
    public static $is_leader = [
        ''               => '请选择',
        self::LEADER_ON  => '是',
        self::LEADER_OFF => '否',
    ];

    //配送员状态
    public static $user_status = [
        ''             => '请选择',
        self::WORK_ON  => '上班',
        self::WORK_OFF => '休息',
        self::SLEEP    => '请假',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'distribution_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'user_status', 'is_leader', 'group_id'], 'required'],
            [['user_status', 'is_leader'], 'integer'],
            [['userid', 'leader_id'], 'string', 'max' => 64],
            [['userid'], 'unique'],
            [['userid'], 'exist', 'skipOnError' => true, 'targetClass' => WxMember::className(), 'targetAttribute' => ['userid' => 'userid']],
            [['start_time', 'end_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userid'      => '配送员姓名',
            'user_status' => '配送员状态',
            'leader_id'   => '组长',
            'is_leader'   => '是否为组长',
            'orgId'       => '分公司',
            'start_time'  => '请假开始时间',
            'end_time'    => '请假结束时间',
            'group_id'    => '运维人员分组',

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(WxMember::className(), ['userid' => 'userid']);
    }

    /**
     * 获取配送员组长信息
     * @author  zgw
     * @version 2016-10-22
     * @return  [type]     [description]
     */
    public function getWxUser()
    {
        return $this->hasOne(WxMember::className(), ['userid' => 'leader_id']);
    }

    /**
     * 获取该配送员组长信息
     * @author  zgw
     * @version 2016-10-22
     * @return  [type]     [description]
     */
    public function getLeaderUser()
    {
        return $this->hasOne(self::className(), ['userid' => 'leader_id']);
    }

    /**
     * 获取配送员组长姓名
     * @param  [type] $id    [description]
     * @param  [type] $model [description]
     * @return [type]        [description]
     */
    public static function getUserName($id, $model)
    {
        if (!$id) {
            return '';
        }

        $model = self::findOne($id);
        if (!$model) {
            return '';
        }

        return $model->user ? $model->user->name : '';
    }

    /**
     * 添加配送员
     * @param [type] $data [description]
     */
    public static function addUser($userid, $org_id)
    {
        if (!$userid) {
            return false;
        }

        $model = self::findOne($userid);

        if ($model) {
            return true;
        }
        $newModel              = new DistributionUser();
        $newModel->userid      = $userid;
        $newModel->user_status = 1;
        $newModel->is_leader   = 2;
        $newModel->is_assign   = 1;

        //添加运维人员之前需要查询该运维人员所属公司下运维人员的分组情况
        //获取运维人员列表
        $useridList = WxMember::getMemberIDArr($org_id);

        if (!empty($useridList)) {
            //查询运维人员分组情况
            $groupList = self::getGroup($useridList);
            if (!empty($groupList)) {
                //获取新增人员所属分组
                $group = self::getUserGroup($groupList);
            } else {
                $group = 1;
            }
            $newModel->group_id = $group;
        } else {
            $newModel->group_id = 1;
        }
        $newRet = $newModel->save();
        if (!$newRet) {
            return false;
        }
        //添加排班数据
        $date        = date('Y-m', time());
        $schedule    = DistributionUserSchedule::find()->where(['userid' => $userid, 'date' => $date])->one();
        $scheduleRet = !empty($schedule) ? true : WxMember::insertScheduleData($date, $userid, 1);
        if (!$scheduleRet) {
            return false;
        }
        return true;
    }
    /**
     * 查看运维人员分组情况
     * @param  [type] $userid [description]
     * @return [type]         [description]
     */
    public static function getGroup($userid)
    {
        if ($userid) {
            $query = DistributionUser::find()
                ->andWhere(['in', 'userid', $userid])
                ->orderBy('group_id ASC');
            $groupInfo = $query
                ->select(['group_id'])
                ->asArray()
                ->all();
            return $groupInfo;
        }
    }
    /**
     * 获取新增人员所属分组
     * @param  [type] $groupList [description]
     * @return [type]         [description]
     */
    public static function getUserGroup($groupList)
    {
        foreach ($groupList as $group) {
            $groupArr[$group['group_id']][] = $group;
        }
        //分组数量
        $groupCount = count($groupArr);
        for ($i = 0; $i < $groupCount; $i++) {
            if (empty($groupArr[$i + 1])) {
                return $i + 1;
            }
            $groups = count($groupArr[$i + 1]);
            if ($groups < 7) {
                return $i + 1;
            }
        }
        return $groupCount + 1;
    }
    /**
     * 删除配送员
     * @param  [type] $userid [description]
     * @return [type]         [description]
     */
    public static function delUser($userid)
    {
        if (!$userid) {
            return false;
        }
        $modelRet = self::deleteAll(['userid' => $userid]);
        //删除排班数据
        $scheduleRet = DistributionUserSchedule::deleteAll(['userid' => $userid]);
        //将组长ID修改为空
        $distributionUserRet = self::deleteLeaderId($userid);
        if ($modelRet === false || $scheduleRet === false || $distributionUserRet === false) {
            return false;
        }
        return true;
    }
    /**
     * 将组长ID修改为空
     * @param  string $userid [用户id]
     * @author wangxiwen
     * @version 2018-08-28
     * @return boolean
     */
    public static function deleteLeaderId($userid)
    {
        $userList = self::getUserList($userid);
        if (empty($userList)) {
            return true;
        }
        $distributionUserRet = true;
        foreach ($userList as $user) {
            $user->leader_id = '';
            $userRet         = $user->save(false);
            if (!$userRet) {
                $distributionUserRet = false;
                break;
            }
        }
        return $distributionUserRet;
    }

    /**
     * 获取配送人员列表
     * @author wangxiwen
     * @version 2018-08-28
     * @param string $userid  运维人员id
     * @return object
     */
    public static function getUserList($userid)
    {
        return self::find()
            ->where(['leader_id' => $userid])
            ->all();
    }

    /**
     * 更新运维人员信息
     * @author wangxiwen
     * @version 2018-10-18
     * @param array $groupInfo 编组数据
     * @return
     */
    public static function saveDistributionUser($groupInfo)
    {
        foreach ($groupInfo as $group) {
            $userObj  = self::getDistributionUser($group['userid']);
            $leaderId = $group['is_leader'] == 1 ? $group['userid'] : $group['leader_id'];

            $userObj->leader_id = $leaderId;
            $userObj->is_leader = $group['is_leader'];
            $userObj->group_id  = $group['group_id'];
            $userObjRes         = $userObj->save();
            if (!$userObjRes) {
                return false;
            }
        }
        return true;
    }

    /**
     * 获取运维人员和组长
     * @author wangxiwen
     * @version 2018-10-19
     * @return array
     */
    public static function getUserAssoc()
    {
        //获取运维人员关系数据
        $userAssoc = self::find()
            ->select('userid,leader_id')
            ->asArray()
            ->all();
        return Tools::map($userAssoc, 'userid', 'leader_id', null, null);
    }

    /**
     * 获取符合条件的配送员列表
     * @param  string $field 要查询的字段
     * @param  array  $where 查询条件
     * @return array
     */
    public static function userList($field = '*', $where = [], $joinWith = '')
    {
        if ($joinWith) {
            return self::find()->select($field)->where($where)->joinWith($joinWith)->all();
        } else {
            return self::find()->select($field)->where($where)->all();
        }

    }

    /**
     * 获取组长id和名称
     * @param  string $org_id [description]
     * @return [type]         [description]
     */
    public static function orgLeaderArr($org_id = '')
    {
        $where = ['is_leader' => 1];

        $org_id = $org_id ? $org_id : Manager::getManagerBranchID();

        if ($org_id > 1) {
            $where['wx_member.org_id'] = $org_id;
        }

        $leaderList = self::userList('distribution_user.userid', $where, 'user');

        $userArr = ['' => '请选择'];

        if (!$leaderList) {
            return $userArr;
        }

        foreach ($leaderList as $v) {
            if (!empty($v->user)) {
                $userArr[$v->user->userid] = $v->user->name;
            }
        }
        return $userArr;
    }

    /**
     * 获取工作时长数据
     * @author  zgw
     * @version 2016-08-26
     * @param   string     $author    配送员id
     * @param   string     $startDate 开始日期
     * @param   string     $endDate   结束日期
     * @return  arrray                工作时长
     */
    public static function workTime($author, $startDate, $endDate)
    {

        // 配送任务统计
        $where = ['assign_userid' => $author, 'is_sue' => 2];
        if ($startDate) {
            $where = ['and', ['>=', 'end_delivery_date', $startDate], $where];
        }
        if ($endDate) {
            $where = ['and', ['<=', 'end_delivery_date', $endDate], $where];
        }
        $taskList = DistributionTask::getDistributionTaskList($where);
        $workTime = $distributionTime = $repairTime = $taiCi = 0;
        foreach ($taskList as $taskArr) {
            // 计算总台次
            $taiCi += 1;
            // 一次任务总时长
            $diffTime = $taskArr->end_delivery_time - $taskArr->start_delivery_time;
            if ($diffTime <= 0) {
                continue;
            }
            // 计算多次任务的总时长
            $workTime += $diffTime;
            // 计算配送任务的总时长
            if ($taskArr->task_type == DistributionTask::DELIVERY) {
                $distributionTime += $diffTime;
                if (isset($taskArr->maintenance)) {
                    // 获取本次任务维修时长
                    $repairDiffTime = $taskArr->maintenance->end_repair_time - $taskArr->maintenance->start_repair_time;
                    $repairDiffTime = $repairDiffTime > 0 ? $repairDiffTime : 0;
                    $repairTime += $repairDiffTime;
                    // 获取本次任务配送时长
                    $distributionDiffTime = $diffTime - $repairDiffTime > 0 ? $diffTime - $repairDiffTime : 0;
                    $distributionTime += $distributionDiffTime;
                }
            }
            // 计算维修任务的总时长
            if ($taskArr->task_type == DistributionTask::SERVICE) {
                $repairTime += $diffTime;
            }
        }

        // 设备任务统计
        $equipWhere = ['and', ['assign_userid' => $author], ['>', 'process_result', 1]];
        if ($startDate) {
            $equipWhere = ['and', ['>=', 'end_repair_time', strtotime($startDate)], $equipWhere];
        }
        if ($endDate) {
            $equipWhere = ['and', ['<=', 'end_repair_time', strtotime($endDate . ' 23:59:59')], $equipWhere];
        }
        return self::equipWorkTime($equipWhere, $workTime, $distributionTime, $repairTime, $taiCi);

    }

    /**
     * 维修任务时长
     * @author  zgw
     * @version 2016-11-30
     * @param   [type]     $equipWhere       [description]
     * @param   [type]     $workTime         [description]
     * @param   [type]     $distributionTime [description]
     * @param   [type]     $repairTime       [description]
     * @param   [type]     $taiCi            [description]
     * @return  [type]                       [description]
     */
    public static function equipWorkTime($equipWhere, $workTime, $distributionTime, $repairTime, $taiCi)
    {
        $taskList = EquipTask::getEquipTaskObjList('*', $equipWhere);
        foreach ($taskList as $taskArr) {
            // 计算总台次
            $taiCi += 1;
            // 一次任务总时长
            $diffTime = $taskArr->end_repair_time - $taskArr->start_repair_time;
            if ($diffTime <= 0) {
                continue;
            }
            // 计算多次任务的总时长
            $workTime += $diffTime;
            // 计算维修任务的总时长
            $repairTime += $diffTime;
        }
        // 总工作时长
        $data['workTimeStr'] = self::calculateWorkTime($workTime);
        // 总配送时长
        $data['distributionTimeStr'] = self::calculateWorkTime($distributionTime);
        // 总维修时长
        $data['repairTimeStr'] = self::calculateWorkTime($repairTime);
        // 总台次
        $data['taiCi'] = $taiCi;
        return $data;
    }

    /**
     * 获取领取的物料
     * @author  zgw
     * @version 2016-08-26
     * @param   string     $author    领取人
     * @param   string     $startDate 开始时间
     * @param   string     $endDate   结束时间
     * @return  array                 领取的物料
     */
    public static function receiveMaterial($author, $startDate, $endDate)
    {
        $where = ['and', ['author' => $author], ['or', ['status' => ScmWarehouseOut::OUTTING], ['status' => ScmWarehouseOut::OUTTED]]];
        if ($startDate) {
            $where = ['and', ['>=', 'date', $startDate], $where];
        }
        if ($endDate) {
            $where = ['and', ['<=', 'date', $endDate], $where];
        }
        $warehouseList = ScmWarehouseOut::getWarehouseOutList($where);
        $materialArr   = [];
        foreach ($warehouseList as $warehouse) {
            if (!isset($warehouse->materialType)) {
                continue;
            }
            $materialArr[$warehouse->materialType->material_type_name][$warehouse->material_id] = ['packets' => $warehouse->material_out_num, 'unit' => $warehouse->materialType->unit];
            if ($warehouse->material->weight) {
                $materialArr[$warehouse->materialType->material_type_name][$warehouse->material_id]['content'] = '供应商：' . $warehouse->material->supplier->name . ' 规格：' . $warehouse->material->weight . $warehouse->materialType->spec_unit;
            } else {
                $materialArr[$warehouse->materialType->material_type_name][$warehouse->material_id]['content'] = '供应商：' . $warehouse->material->supplier->name;
            }
        }
        return $materialArr;
    }

    /**
     * 计算工时
     * @author  zgw
     * @version 2016-08-26
     * @param   int     $time 工作的秒数
     * @return  string        组装的时分的字符串
     */
    public static function calculateWorkTime($time)
    {
        $hour   = floor($time / 3600);
        $minute = floor(($time % 3600) / 60);
        $second = ($time % 3600) % 60;
        if ($hour) {
            return $hour . '小时' . $minute . '分' . $second . '秒';
        } else if (!$hour && $minute) {
            return $minute . '分' . $second . '秒';
        } else {
            return $second . '秒';
        }
    }

    /**
     * 获取某个字段的值
     * @author  zgw
     * @version 2016-08-26
     * @param   string|int     $field 要获取的字段
     * @param   array          $where 查询条件
     * @return  string|int            要获取的字段的值
     */
    public static function getField($field, $where)
    {
        $equipDetail = self::find()->select($field)->where($where)->one();
        return $equipDetail ? $equipDetail->$field : '';
    }

    /**
     * 获取运维人员
     * @param  string $userid 运维人员id
     * @return
     */
    protected static function getDistributionUser($userid)
    {
        $userObj = self::findOne($userid);
        return $userObj ?? new self();
    }

}
