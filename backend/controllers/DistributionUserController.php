<?php

namespace backend\controllers;

use backend\models\DistributionFiller;
use backend\models\DistributionTask;
use backend\models\DistributionUser;
use backend\models\DistributionUserSchedule;
use backend\models\DistributionUserSearch;
use backend\models\Manager;
use backend\models\ScmStock;
use backend\models\ScmWarehouseOut;
use common\models\Building;
use common\models\WxMember;
use Yii;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * DistributionUserController implements the CRUD actions for DistributionUser model.
 */
class DistributionUserController extends Controller
{
    public $enableCsrfValidation = false;

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

    /**
     * 跨域提交完成后进行header信息跳转。
     * @author  zmy
     * @version 2018-03-01
     * @return  [type]     [description]
     */
    public function returnHeader()
    {
        header('content-type:application:json;charset=utf8');
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST,GET');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        if (strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS') {
            exit;
        }
    }

    /**
     * Lists all DistributionUser models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('运维人员列表')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new DistributionUserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DistributionUser model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看运维人员')) {
            return $this->redirect(['site/login']);
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * 人员管理
     * @author wangxiwen
     * @version 2018-4-17
     * @return
     */
    public function actionManagement()
    {
        if (!Yii::$app->user->can('人员管理')) {
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
        //获取人员管理数据
        $model = WxMember::getManagement($orgId);

        return $this->render('management', [
            'model'  => $model,
            'org_id' => $orgId,
        ]);
    }

    /**
     * AJAX通过日期获取数据
     * @author wangxiwen
     * @version 2018-10-17
     * @return
     */
    public function actionGetSchedule()
    {
        $date         = Yii::$app->request->get('date');
        $orgId        = Yii::$app->request->get('org_id');
        $userSchedule = [];
        $dateList     = [];
        $isChange     = 1;
        $result       = DistributionUserSchedule::verifyDate($date);
        if ($result) {
            $dateArray = explode('-', $date);
            $year      = $dateArray[0];
            $month     = $dateArray[1];
            $days      = WxMember::getDays($year, $month);
            $dateList  = ['year' => $year, 'month' => $month, 'days' => $days];
            if (!$orgId) {
                //获取当前登陆用户所属分公司ID
                $orgId = Manager::getManagerBranchID();
                if ($orgId == 1) {
                    //超级管理员登陆人员管理时初始化数据默认为北京分公司
                    $orgId = 2;
                }
            }
            //获取运维人员列表
            $userid = WxMember::getMemberIDArr($orgId);
            if ($userid) {
                //没有排班记录的运维人员
                $invalidUserid = DistributionUserSchedule::getScheduleUser($userid, $date);
                //查询日期最多大于当前日期一个月
                $isInsert = DistributionUserSchedule::verifyMonths($date);
                if ($invalidUserid && $isInsert) {
                    DistributionUserSchedule::insertSchedule($date, $invalidUserid, $days);
                }
                //获取最新排班数据
                $userSchedule = WxMember::getSchedule($userid, $date);
            }
        }
        return Json::encode([
            'scheduleInfo' => $userSchedule,
            'date'         => $dateList,
            'isChange'     => $isChange,
        ]);
    }

    /**
     * 更新编组数据
     * @author wangxiwen
     * @version 2018-10-18
     * @return
     */
    public function actionUpdateGroup()
    {
        $this->returnHeader();
        $param  = file_get_contents('php://input');
        $params = $param ? Json::decode($param) : [];
        //更新编组数据
        DistributionUser::saveDistributionUser($params['groupInfo']);
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "运维人员管理", \backend\models\ManagerLog::UPDATE, "更新运维人员编组");
        $orgId = $params['org_id'];
        if (!$orgId) {
            //获取当前登陆用户所属分公司ID
            $orgId = Manager::getManagerBranchID();
            if ($orgId == 1) {
                //超级管理员登陆人员管理时初始化数据默认为北京分公司
                $orgId = 2;
            }
        }
        //获取人员管理数据
        return WxMember::getManagement($orgId);
    }

    /**
     * 更新排班数据
     * @author wangxiwen
     * @version 2018-10-18
     * @return [type] [description]
     */
    public function actionUpdateSchedule()
    {
        $this->returnHeader();
        $param  = file_get_contents('php://input');
        $params = $param ? Json::decode($param) : [];
        //获取月份天数
        $dateArr   = explode('-', $params['date']);
        $year      = $dateArr[0];
        $month     = $dateArr[1];
        $days      = WxMember::getDays($year, $month);
        $dateArray = ['year' => $year, 'month' => $month, 'days' => $days];
        //更新排班数据
        DistributionUserSchedule::saveSchedule($params['date'], $params['scheduleInfo']);
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "运维人员管理", \backend\models\ManagerLog::UPDATE, "更新运维人员排班");
        //获取登陆用户所属公司ID
        $orgId = $params['org_id'];
        if (!$orgId) {
            //获取当前登陆用户所属分公司ID
            $orgId = Manager::getManagerBranchID();
            if ($orgId == 1) {
                //超级管理员登陆人员管理时初始化数据默认为北京分公司
                $orgId = 2;
            }
        }
        //获取运维人员列表
        $userid = WxMember::getMemberIDArr($orgId);
        //获取最新的排班管理数据
        $scheduleInfo = WxMember::getSchedule($userid, $params['date']);
        return Json::encode([
            'date'         => $dateArray,
            'scheduleInfo' => $scheduleInfo,
        ]);
    }

    /**
     * 配送员负责的楼宇
     * @return [type] [description]
     */
    public function actionUserBuild()
    {
        if (!Yii::$app->user->can('配送分工')) {
            return $this->redirect(['site/login']);
        }

        $type         = Yii::$app->request->get('type', 1);
        $managerOrgId = Manager::getManagerBranchID();
        if ($managerOrgId == 1) {
            $managerOrgId = 2;
        }

        $orgID = Yii::$app->request->get('org_id');
        if (!$orgID) {
            $orgID = $managerOrgId;
        }
        //获取配送员
        $userArr = WxMember::getDistributionUsers($orgID);
        unset($userArr['']);
        //获取楼宇
        $buildList = Building::getRunBuildList($orgID);
        //获取楼宇数组
        $buildIdNameOption = '<option value="">请选择</option>';
        foreach ($buildList as $v) {
            $buildIdNameOption .= '<option lng="' . $v['longitude'] . '" lat="' . $v['latitude'] . '" value="' . $v['id'] . '">' . $v["name"] . '</option>';
        }
        return $this->render('user_build', [
            'build_list'        => $buildList,
            'org_id'            => $orgID,
            'buildIdNameOption' => $buildIdNameOption,
            'userArr'           => $userArr,
            'type'              => $type,
        ]);
    }

    /**
     * 人员分配
     * @return [type] [description]
     */
    public function actionSaveUserBuild()
    {
        $upres              = true;
        $buildingList       = Yii::$app->request->get('build_id_arr');
        $distributionUserID = Yii::$app->request->get('distribution_userid');
        $type               = Yii::$app->request->get('type');
        if (!$distributionUserID) {
            return false;
        }
        $buildingList = $type == 1 ? [] : $buildingList;
        $result       = Building::setBuildingForUser($distributionUserID, $buildingList);
        if ($result) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "运维人员管理", \backend\models\ManagerLog::UPDATE, "分配运维人员");
        }
        return $result;
    }

    /**
     * 获取配送员的楼宇详细信息
     * 新增修改获取楼宇信息
     * @Author:   GaoYongLi
     * @DateTime: 2018-10-25
     * @param:    [param]
     * @return
     * @return    [type]     [description]
     */
    public function actionBuildDetail()
    {
        $id          = Yii::$app->request->get('id');
        $buildDetail = Building::findOne($id);
        // 配送员姓名
        $dis_user_name = $buildDetail->wxUser ? $buildDetail->wxUser->name : '';
        // 配送员手机号
        $mobile = $buildDetail->wxUser ? $buildDetail->wxUser->mobile : '';
        //获取组长姓名
        $leader_name = $status = ''; //默认组长姓名、配送员状态
        if ($buildDetail->distributionUser) {
            if ($buildDetail->distributionUser->is_leader == 1) {
                //自己是组长
                $leader_name = $dis_user_name;
            } else if ($buildDetail->distributionUser->leader_id) {
                //组长id存在
                $leader_name = $buildDetail->distributionUser->wxUser->name;
            }
            //配送员状态
            $status = DistributionUser::$user_status[$buildDetail->distributionUser->user_status];
        }
        //配送员负责的楼宇名称
        $build_name_arr = Building::getBuildNameArr($buildDetail->distribution_userid);
        //组装前端显示的数据
        $html = '<p>楼宇名称：' . $buildDetail->name . '</p><p>所属配送员：' . $dis_user_name . '</p><p>联系电话：' . $mobile . '</p><p>组长：' . $leader_name . '</p><p>状态：' . $status . '</p><p>配送员的楼宇<br/></p>';
        foreach ($build_name_arr as $key => $value) {
            $html .= '<p>' . $value . '</p>';
        }
        return $html;
    }

    /**
     * Finds the DistributionUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return DistributionUser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DistributionUser::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 个人数据统计
     * @author  zgw
     * @version 2016-08-25
     * @return  [type]     [description]
     */
    public function actionUserDataSync()
    {
        $startDate = Yii::$app->request->get('startDate');
        $endDate   = Yii::$app->request->get('endDate');
        $author    = Yii::$app->request->get('author');
        // 获取工作时长
        $data = DistributionUser::workTime($author, $startDate, $endDate);

        // 获取领取的物料
        $data['material'] = DistributionUser::receiveMaterial($author, $startDate, $endDate);

        return $this->render('user_data_sync', ['data' => $data, 'startDate' => $startDate, 'endDate' => $endDate, 'author' => $author]);
    }

    /**
     * 任务记录
     * @author  zgw
     * @version 2016-08-25
     * @return  [type]     [description]
     */
    public function actionTaskRecord()
    {
        $startDate = Yii::$app->request->get('startDate');
        $endDate   = Yii::$app->request->get('endDate');
        $author    = Yii::$app->request->get('author');

        $where = ['assign_userid' => $author, 'is_sue' => 2];
        if ($startDate && $endDate) {
            $where = ['and', ['between', 'end_delivery_date', $startDate, $endDate], $where];
        } else if ($startDate && !$endDate) {
            $where = ['and', ['>=', 'end_delivery_date', $startDate], $where];
        } else if (!$startDate && $endDate) {
            $where = ['and', ['<=', 'end_delivery_date', $endDate], $where];
        }
        $query      = DistributionTask::find()->where($where);
        $pages      = new Pagination(['totalCount' => $query->count(), 'defaultPageSize' => 20]);
        $taskRecord = $query->orderBy('id DESC')->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('task_record', ['taskRecord' => $taskRecord, 'startDate' => $startDate, 'endDate' => $endDate, 'author' => $author, 'pages' => $pages]);
    }

    /**
     * 配送记录
     * @author  zgw
     * @version 2016-08-25
     * @return  [type]     [description]
     */
    public function actionDistributionRecord()
    {
        $startDate = Yii::$app->request->get('startDate');
        $endDate   = Yii::$app->request->get('endDate');
        $author    = Yii::$app->request->get('author');
        $where     = ['add_material_author' => $author];

        if ($startDate && $endDate) {
            $where = ['and', ['between', 'create_date', $startDate, $endDate], $where];
        } else if ($startDate && !$endDate) {
            $where = ['and', ['>=', 'create_date', $startDate], $where];
        } else if (!$startDate && $endDate) {
            $where = ['and', ['<=', 'create_date', $endDate], $where];
        }

        $query      = DistributionFiller::find()->where($where);
        $pages      = new Pagination(['totalCount' => $query->count(), 'defaultPageSize' => 20]);
        $recordList = $query->orderBy('Id DESC')->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('distribution_record', ['recordList' => $recordList, 'startDate' => $startDate, 'endDate' => $endDate, 'author' => $author, 'pages' => $pages]);
    }

    /**
     * 领料记录
     * @author  zgw
     * @version 2016-08-25
     * @return  [type]     [description]
     */
    public function actionReceiveMaterialRecord()
    {
        $startDate = Yii::$app->request->get('startDate');
        $endDate   = Yii::$app->request->get('endDate');
        $author    = Yii::$app->request->get('author');

        $where = ['author' => $author, 'status' => 3];
        if ($startDate && $endDate) {
            $where = ['and', ['between', 'date', $startDate, $endDate], $where];
        } else if ($startDate && !$endDate) {
            $where = ['and', ['>=', 'date', $startDate], $where];
        } else if (!$startDate && $endDate) {
            $where = ['and', ['<=', 'date', $endDate], $where];
        }

        $query      = ScmWarehouseOut::find()->where($where);
        $pages      = new Pagination(['totalCount' => $query->count(), 'defaultPageSize' => 20]);
        $recordList = $query->orderBy('id DESC')->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('receive_material_record', ['recordList' => $recordList, 'startDate' => $startDate, 'endDate' => $endDate, 'author' => $author, 'pages' => $pages]);
    }

    /**
     * 还料记录
     * @author  zgw
     * @version 2016-08-25
     * @return  [type]     [description]
     */
    public function actionReturnMaterialRecord()
    {
        $startDate = Yii::$app->request->get('startDate');
        $endDate   = Yii::$app->request->get('endDate');
        $author    = Yii::$app->request->get('author');

        $where = ['distribution_clerk_id' => $author, 'reason' => 2];
        if ($startDate && $endDate) {
            $where = ['and', ['between', 'ctime', strtotime($startDate), strtotime($endDate . ' 23:59:59')], $where];
        } else if ($startDate && !$endDate) {
            $where = ['and', ['>=', 'ctime', strtotime($startDate)], $where];
        } else if (!$startDate && $endDate) {
            $where = ['and', ['<=', 'ctime', strtotime($endDate . ' 23:59:59')], $where];
        }
        $query      = ScmStock::find()->where($where);
        $pages      = new Pagination(['totalCount' => $query->count(), 'defaultPageSize' => 20]);
        $recordList = $query->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('return_material_record', ['recordList' => $recordList, 'startDate' => $startDate, 'endDate' => $endDate, 'author' => $author, 'pages' => $pages]);
    }

}
