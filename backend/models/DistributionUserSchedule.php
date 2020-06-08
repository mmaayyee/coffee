<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "distribution_user_schedule".
 *
 * @property string $userid 配送员表的用户id
 * @property string $date 年月日期
 * @property string $schedule 配送人员工作时间表1上班2休息
 */
class DistributionUserSchedule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'distribution_user_schedule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'schedule'], 'required'],
            [['userid'], 'string', 'max' => 64],
            [['date'], 'string', 'max' => 7],
            [['schedule'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userid'   => 'Userid',
            'date'     => 'Date',
            'schedule' => 'Schedule',
        ];
    }

    /**
     * 获取人员管理排班数据当前的工作状态
     * @author wangxiwen
     * @version 2018-05-21
     * @return array
     */
    public static function getUserScheduleStatus()
    {
        $yearMonth = date('Y-m');
        $day       = date('d');
        $partten   = '/' . $day . '-\d{1}/';
        //获取人员管理排班数据
        $userSchedule = self::find()
            ->andWhere(['date' => $yearMonth])
            ->select('userid,schedule')
            ->asArray()
            ->all();
        $scheduleList = [];
        foreach ($userSchedule as $schedule) {
            preg_match_all($partten, $schedule['schedule'], $matches);
            $scheduleStatus                    = explode('-', $matches[0][0]);
            $scheduleList[$schedule['userid']] = $scheduleStatus[1];
        }
        return $scheduleList;
    }

    /**
     * 检测指定人员的工作状态是否为上班
     * @author wangxiwen
     * @version 2018-10-21
     * @param string $userid 用户id
     * @return boolean
     */
    public static function verifyUserWorkStatus($userid)
    {
        if (!$userid) {
            return false;
        }
        $schedule    = self::getSchedule($userid);
        $stateStr    = date('d') . '-1';
        $scheduleArr = explode('|', $schedule);
        if (in_array($stateStr, $scheduleArr)) {
            return true;
        }
        return false;
    }

    /**
     * 获取指定运维人员排班数据
     * @author wangxiwen
     * @version 2018-10-21
     * @param string $userid 运维人员
     * @return
     */
    private static function getSchedule($userid)
    {
        return self::find()
            ->andWhere(['date' => date('Y-m')])
            ->andWhere(['userid' => $userid])
            ->select('schedule')
            ->asArray()
            ->scalar();
    }

    /**
     * 获取拥有排班记录的运维人员
     * @author wangxiwen
     * @version 2018-10-17
     * @param array $useridList 运维人员列表
     * @param string $date 日期
     * @return array
     */
    public static function getScheduleUser($useridList, $date)
    {
        $scheduleUser = self::find()
            ->andWhere(['userid' => $useridList])
            ->andWhere(['date' => $date])
            ->select('userid')
            ->column();
        $invalidUser = [];
        foreach ($useridList as $userid) {
            if (!in_array($userid, $scheduleUser)) {
                $invalidUser[] = $userid;
            }
        }
        return $invalidUser;
    }

    /**
     * 验证查询日期最多大于当前日期一个月
     * @param $date    查询日期
     * @return boolean
     */
    public static function verifyMonths($date)
    {
        $currentYear  = date('Y');
        $currentMonth = date('n');
        $selectYear   = date('Y', strtotime($date));
        $selectMonth  = date('n', strtotime($date));
        // 判断年份
        if ($currentYear == $selectYear) {
            //判断月份
            if (($selectMonth - $currentMonth == 1) || ($selectMonth == $currentMonth)) {
                return true;
            }
        }
        //相差一年
        if ($selectYear - $currentYear == 1) {
            if ($selectMonth - $currentMonth == -11) {
                return true;
            }
        }
        return false;
    }

    /**
     * 验证日期格式是否正确
     * @author wangxiwen
     * @version 2018-10-17
     * @param string $date 日期
     * @return boolean
     */
    public static function verifyDate($date)
    {
        $pattern = '/^[2]\d{3}-\d{2}$/';
        return preg_match($pattern, $date);
    }

    /**
     * 批量插入排班数据
     * @param $date
     * @param $userid 运维人员列表
     * @param $days
     * @return
     */
    public static function insertSchedule($date, $userid, $days)
    {
        //获取排班数据
        $schedule = self::getMonthSchedule($days);
        foreach ($userid as $user) {
            $scheduleObj           = new self();
            $scheduleObj->userid   = $user;
            $scheduleObj->date     = $date;
            $scheduleObj->schedule = $schedule;
            $scheduleRes           = $scheduleObj->save();
            if (!$scheduleRes) {
                return false;
            }
        }
        return true;
    }

    /**
     * 获取排班schedule字段
     * @author wangxiwen
     * @version 2018-10-18
     * @param int $days 天数
     * @return
     */
    private static function getMonthSchedule($days)
    {
        $schedule = '';
        for ($i = 0; $i < $days; $i++) {
            if ($i < 9) {
                $schedule .= '0' . ($i + 1) . '-1|';
            } else {
                $schedule .= ($i + 1) . '-1|';
            }
        }
        $schedule = substr($schedule, 0, -1);
        return $schedule;
    }

    /**
     * 更新排班数据
     * @author wangxiwen
     * @version 2018-10-18
     * @param string $date 日期
     * @param array $scheduleArray 排班数据
     * @return
     */
    public static function saveSchedule($date, $scheduleList)
    {
        foreach ($scheduleList as $scheduleArray) {
            $scheduleStr = '';
            foreach ($scheduleArray['schedule'] as $schedule) {
                $scheduleStr .= $schedule . '|';
            }
            $scheduleStr = substr($scheduleStr, 0, -1);

            $scheduleObj           = self::getUserSchedule($date, $scheduleArray['userid']);
            $scheduleObj->userid   = $scheduleArray['userid'];
            $scheduleObj->date     = $date;
            $scheduleObj->schedule = $scheduleStr;
            $scheduleRes           = $scheduleObj->save();
            if (!$scheduleRes) {
                return false;
            }
        }
        return true;
    }

    /**
     * 获取排班数据
     * @author wangxiwen
     * @version 2018-10-18
     * @param string $date 日期
     * @param string $userid 运维人员
     * @return array
     */
    protected static function getUserSchedule($date, $userid)
    {
        $scheduleObj = self::find()->where(['date' => $date, 'userid' => $userid])->one();
        return $scheduleObj ?? new self();
    }

    /**
     * 获取排班数据列表
     * @author wangxiwen
     * @version 2018-10-18
     * @param string $date 日期
     * @param string $userid 运维人员
     * @return
     */
    public static function getUserScheduleList($date, $userid)
    {
        return self::find()
            ->andWhere(['userid' => $userid])
            ->andWhere(['date' => $date])
            ->orderBy('userid ASC')
            ->asArray()
            ->all();
    }

}
