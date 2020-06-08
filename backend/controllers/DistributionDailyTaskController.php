<?php

namespace backend\controllers;

use backend\models\DistributionDailyTask;
use backend\models\DistributionTask;
use backend\models\DistributionTaskSetting;
use backend\models\DistributionUserSchedule;
use backend\models\EquipAbnormalTask;
use backend\models\Holiday;
use backend\models\Manager;
use backend\models\Organization;
use backend\models\ScmMaterial;
use common\dailyTask\Tasks;
use common\models\Api;
use common\models\Equipments;
use common\models\WxMember;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;

/**
 * DistributionDailyTaskController implements the CRUD actions for DistributionDailyTask model.
 */
class DistributionDailyTaskController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {

        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    //日常任务数据接口
    public function actionTest()
    {
        $model         = new Tasks();
        $dailyTaskList = $model->distributionTask();
    }
    /**
     * 日常任务页面展示数据
     * @author wangxiwen
     * @version 2018-05-24
     * @return array
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('日常任务管理')) {
            return $this->redirect(['site/login']);
        }
        $orgId = Yii::$app->request->get('org_id');
        if (!$orgId) {
            //获取当前登陆用户所属分公司ID
            $orgId = Manager::getManagerBranchID();
            if ($orgId == 1) {
                //超级管理员登陆人员管理时初始化数据默认为北京分公司
                $orgId = 2;
            }
        }
        $orgIdArr      = Api::getOrgIdArray(['parent_path' => $orgId]);
        $dailyTaskData = DistributionDailyTask::getDailyTaskData($orgIdArr);
        $dailyTaskDate = DistributionDailyTask::getTaskDate($orgIdArr);
        //组合页面显示的日常任务数据
        $showDailyTaskData = DistributionDailyTask::showDailyTaskData($dailyTaskData, $dailyTaskDate, $orgId);
        $model             = Json::encode($showDailyTaskData);

        return $this->render('index', [
            'model'  => $model,
            'org_id' => $orgId,
        ]);
    }

    /**
     * ajax请求日常任务初始化数据
     * @author wangxiwen
     * @version 2018-06-20
     * @return array
     */
    public function actionAjaxDailyTaskData()
    {
        if (Yii::$app->request->isAjax) {
            $orgId         = Yii::$app->request->get('org_id');
            $dailyTaskData = DistributionDailyTask::getDailyTaskData($orgId);
            $dailyTaskDate = DistributionDailyTask::getTaskDate($orgId);
            //组合页面显示的日常任务数据
            $showDailyTaskData = DistributionDailyTask::showDailyTaskData($dailyTaskData, $dailyTaskDate, $orgId);
            return Json::encode($showDailyTaskData);
        } else {
            return Json::encode([]);
        }
    }

    /**
     * 修改清洗、换料周期、配送天数
     * @return [type] [description]
     */
    public function actionSetting()
    {
        $data       = Yii::$app->request->post('data');
        $settingRes = DistributionTaskSetting::addAll($data);
        if ($settingRes === false) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /**
     * 更换运维人员列表
     * @author wangxiwen
     * @version 2018-05-31
     * @return array
     */
    public function actionGetDistributionUserList()
    {
        $orgId     = Yii::$app->request->get('org_id');
        $date      = Yii::$app->request->get('date');
        $userid    = Yii::$app->request->get('userid');
        $yearMonth = date('Y-m', strtotime($date));
        $day       = date('d', strtotime($date));
        //分公司下运维人员列表
        $useridArr = WxMember::getMemberIDArr($orgId);
        //获取运维人员排班数据
        $scheduleList  = DistributionUserSchedule::getUserScheduleList($yearMonth, $useridArr);
        $parten        = '/' . $day . '-\d{1}/';
        $useridInfoArr = [];
        foreach ($scheduleList as $schedule) {
            preg_match_all($parten, $schedule['schedule'], $matches);
            $status = explode('-', $matches[0][0]);
            if ($status[1] == 1 && $schedule['userid'] != $userid) {
                $useridInfoArr[] = $schedule['userid'];
            }
        }
        //通过userid查询运维人员姓名
        $useridInfoList['distributionUserList'] = WxMember::getUserIdNameByUserId($useridInfoArr);
        return Json::encode($useridInfoList);
    }
    /**
     * 修改更换后的运维人员
     * @author wangxiwen
     * @version 2018-05-31
     * @return array
     */
    public function actionSaveDistributionUser()
    {
        $buildId     = Yii::$app->request->get('build_id');
        $date        = Yii::$app->request->get('date');
        $userid      = Yii::$app->request->get('userid');
        $saveUserRes = DistributionDailyTask::saveDailyTaskUser($buildId, $date, $userid);
        if (!$saveUserRes) {
            return 0;
        }
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "日常任务管理", \backend\models\ManagerLog::UPDATE, "修改日常任务运维人员");
        return 1;
    }
    /**
     * 更换运维日常任务日期
     * @author  wangxiwen
     * @version 2018-05-31
     * @return  [type]     [description]
     */
    public function actionChangeDistributionTaskDate()
    {
        $params = Yii::$app->request->get();
        if (empty($params)) {
            return 0;
        }
        $orgId      = $params['org_id'];
        $userId     = $params['userid'];
        $buildId    = $params['build_id'];
        $beforeDate = $params['before_date'];
        $afterDate  = $params['after_date'];
        //更换日期后的楼宇存在任务不允许更换
        $taskCount = DistributionDailyTask::getDailyTaskCount($afterDate, $buildId, $userId);
        if ($taskCount > 0) {
            return 0;
        }
        //日常任务
        $buildTask = DistributionDailyTask::getBuildDailyTaskList($beforeDate, $buildId, $userId, $orgId);
        //判断日期是提前还是推后(1提前2延后)
        $flag = $beforeDate > $afterDate ? 1 : 2;
        //获取节假日日期数据
        $holiday = Holiday::getHolidayArray();

        //获取更换日期后楼宇料仓计算后的物料量并保存
        $result = DistributionDailyTask::getNewBuildDailyTaskList($buildTask, $holiday, $beforeDate, $afterDate, $flag);
        if ($result) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "日常任务管理", \backend\models\ManagerLog::UPDATE, "修改日常任务日期");

            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 下发(批量)运维日常任务
     * @author wangxiwen
     * @version 2018-05-31
     */
    public function actionBatchAssignDailyTask()
    {
        $date  = Yii::$app->request->get('date', '');
        $orgId = Yii::$app->request->get('orgId', '');
        //下发任务仅限当天
        if (!$orgId || !$date || $date != date('Y-m-d')) {
            return 0;
        }
        //获取当前登录用户所属分公司及下属代运维公司
        $orgIdArr = Api::getOrgIdArray(['parent_path' => $orgId, 'is_replace_maintain' => Organization::INSTEAD_YES]);
        //查询运维使用物料信息
        $scmMaterial = ScmMaterial::getScmMaterial();
        //获取该日期的日常配送任务列表
        $dailyTaskList = DistributionDailyTask::getDailyTaskList($date, $orgIdArr, $scmMaterial);
        //获取未完成的运维任务
        $taskList = DistributionTask::getTaskList();
        //获取设备信息
        $equipments = Equipments::getBuildEquipAssoc($orgIdArr);
        //获取转次日的故障信息
        $abnormals = EquipAbnormalTask::getEquipAbnormals($orgIdArr);
        //运维任务数据保存和更新
        $params = [$dailyTaskList, $taskList, $equipments, $abnormals, $date, $scmMaterial, $orgIdArr];
        $ret    = DistributionDailyTask::saveTaskList($params);
        if ($ret) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "日常任务管理", \backend\models\ManagerLog::DELETE, "批量下发日常任务");
            return 1;
        } else {
            return 0;
        }
    }

}
