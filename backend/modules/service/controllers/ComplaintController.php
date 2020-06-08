<?php

namespace backend\modules\service\controllers;

// use backend\models\ComplaintSearch;
use backend\models\OrderInfo;
use backend\models\UserConsume;
use backend\modules\service\helpers\Api;
use backend\modules\service\models\Complaint;
use common\helpers\multiRequest\MutiRequestManager;
use common\helpers\Tools;
use common\models\Equipments;
use yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * 客诉管理
 * @author wlw
 * @date   2018-09-17
 *
 */
class ComplaintController extends Controller
{
    /**
     * 添加咨询类型
     */
    public function actionMobileSearch()
    {
        $this->view->title = '电话搜索';
        if (Yii::$app->request->isAjax) {
            $mobile      = Yii::$app->request->get('mobile');
            $userInfoReq = Api::mobileSearch($mobile);
            $manager     = new MutiRequestManager();
            $manager->addRequest($userInfoReq);
            $manager->run();
            $userInfo = [];
            if ($userInfoReq->isSuccess()) {
                $req      = json_decode($userInfoReq->getContents(), true);
                $userInfo = $req['error_code'] == 0 ? $req['data'] : [];
            }
            $userInfo['nickname'] = empty($userInfo['nickname']) ? '' : str_replace('"', '', $userInfo['nickname']);
            return $this->asJson([
                'userInfo' => $userInfo,
            ]);
        }

        return $this->render('mobile-search');
    }

    /**
     * 获取用户最近的消费，订单，客诉记录
     */
    public function actionLatestInfo()
    {
        $userId = Yii::$app->request->get('user_id');

        $orderListReq     = Api::getUserOrderList($userId);
        $consumListReq    = Api::getUserConsumList($userId);
        $complaintListReq = Api::getUserComplaintList($userId);

        $manager = new MutiRequestManager();
        $manager->addRequest($orderListReq)
            ->addRequest($consumListReq)
            ->addRequest($complaintListReq)
            ->run();

        $orderList     = $orderListReq->parseJsonData();
        $consumList    = $consumListReq->parseJsonData();
        $complaintList = $complaintListReq->parseJsonData();
        return $this->asJson([
            'orderList'     => array_values($orderList['data']['result']),
            'consumList'    => $consumList['data']['result'],
            'complaintList' => $complaintList['data']['result'],
        ]);
    }
    /**
     *  用户订单查看全部
     * @Author:   GaoYongLi
     * @DateTime: 2018-10-12
     * @param:    [param]
     * @return
     * @return    [type]     [description]
     */
    public function actionUserOrderList()
    {
        $searchParams  = Yii::$app->request->get('UserConsume');
        $consumeUserId = Yii::$app->request->get('user_id');
        $consumeUserID = $searchParams['user_id'];
        $orderCode     = $searchParams['order_id'];
        $startTime     = $searchParams['createdFrom'] == 0 ? '' : strtotime($searchParams['createdFrom']);
        $endTime       = $searchParams['createdTo'] == 0 ? '' : strtotime($searchParams['createdTo']);
        $userId        = !empty($consumeUserId) ? $consumeUserId : $consumeUserID;
        $pager         = new Pagination(['totalCount' => 1000, 'pageSize' => '20']);
        $request       = Api::getUserOrderList(
            $userId,
            ['start_time' => $startTime, 'end_time' => $endTime, 'order_code' => $orderCode],
            $pager->limit,
            $pager->offset
        );
        $request->run();
        $resp = $request->parseJsonData();

        if ($resp === false) {
            die('网络错误:' . $request->getHttpCode());
        }
        $orderList = $resp['data']['result'];
        $models    = new UserConsume();
        $pager     = new \yii\data\Pagination(['totalCount' => $resp['data']['total'], 'defaultPageSize' => '20']);
        return $this->render('user-order-list',
            [
                'pager'       => $pager,
                'orderList'   => $orderList,
                'userId'      => $userId,
                'model'       => $models,
                'createdFrom' => $searchParams['createdFrom'],
                'createdTo'   => $searchParams['createdTo'],
                'orderID'     => $searchParams['order_id'],
            ]);
    }

    /**
     * 用户消费列表
     * @return string
     */
    public function actionUserConsumeList()
    {
        $searchParams  = Yii::$app->request->get('UserConsume');
        $consumeUserId = Yii::$app->request->get('user_id');
        $consumeUserID = $searchParams['user_id'];
        $orderCode     = $searchParams['order_id'];
        $startTime     = $searchParams['createdFrom'] == 0 ? '' : strtotime($searchParams['createdFrom']);
        $endTime       = $searchParams['createdTo'] == 0 ? '' : strtotime($searchParams['createdTo']);
        $pager         = new Pagination(['totalCount' => 1000, 'pageSize' => '20']);
        $userId        = !empty($consumeUserId) ? $consumeUserId : $consumeUserID;
        $request       = Api::getUserConsumList(
            $userId,
            ['start_time' => $startTime, 'end_time' => $endTime, 'order_code' => $orderCode],
            $pager->limit,
            $pager->offset
        );
        $request->run();
        $resp = $request->parseJsonData();
        if ($resp === false) {
            die('网络错误:' . $request->getHttpCode());
        }
        $consumeList = $resp['data']['result'];
        $models      = new UserConsume();
        $pager       = new \yii\data\Pagination(['totalCount' => $resp['data']['total'], 'defaultPageSize' => '20']);
        return $this->render('user-consume-list', [
            'pager'       => $pager,
            'consumeList' => $consumeList,
            'userId'      => $userId,
            'buildID'     => '',
            'model'       => $models,
            'createdFrom' => $searchParams['createdFrom'],
            'createdTo'   => $searchParams['createdTo'],
            'orderID'     => $searchParams['order_id'],
        ]);
    }
    /**
     * 用户客诉列表
     * @return string
     */
    public function actionUserComplaintList()
    {
        $searchParams = Yii::$app->request->get('UserConsume');
        $userId       = Yii::$app->request->get('user_id');
        $userID       = $searchParams['user_id'];
        $pager        = new Pagination(['totalCount' => 1000, 'pageSize' => '20']);
        $userID       = !empty($userId) ? $userId : $userID;
        $request      = Api::getPositionComplaintList(
            '',
            $pager->limit,
            $pager->offset,
            $userID
        );
        $request->run();
        $resp = $request->parseJsonData();
        if ($resp === false) {
            die('网络错误:' . $request->getHttpCode());
        }
        $complaintList = $resp['data']['result'];
        $pager         = new \yii\data\Pagination(['totalCount' => $resp['data']['total'], 'defaultPageSize' => '20']);
        return $this->render('user-complaint-list', [
            'pager'         => $pager,
            'complaintList' => $complaintList,
        ]);
    }

    /**
     * 根据点位名称和编码搜索点位信息
     * @return \yii\web\Response
     */
    public function actionPositionSearch()
    {
        $this->view->title = '点位搜索';
        if (Yii::$app->request->isAjax) {
            $name         = Yii::$app->request->get('Complaint')['building_id'];
            $number       = Yii::$app->request->get('number');
            $positionInfo = Api::positionSearch($name, $number);
            $positionInfo->run();
            $info = $positionInfo->parseJsonData();
            if ($info !== false && $info['error_code'] == 0) {
                return $this->asJson($info['data']);
            } else {
                return $this->asJson([]);
            }
        }
        $model = new Complaint();
        $build = Api::getBuildingList();
        $build->run();
        $buildings    = $build->parseJsonData()['data'];
        $buildingList = array_combine(array_column($buildings, 'id'), array_column($buildings, 'name'));
        return $this->render('position-search', ['buildingList' => $buildingList, 'model' => $model]);
    }

    public function actionPositionLastestInfo()
    {
        $buildId          = Yii::$app->request->get('build_id');
        $ConsumRequest    = Api::getPositionConsumList($buildId);
        $complaintRequest = Api::getPositionComplaintList($buildId);
        $manager          = new MutiRequestManager();
        $manager->addRequest($ConsumRequest)->addRequest($complaintRequest)->run();
        $consumList    = $ConsumRequest->parseJsonData();
        $complaintList = $complaintRequest->parseJsonData();
        if ($consumList !== false && $consumList['error_code'] == 0) {
            $consumList = $consumList['data']['result'];
        } else {
            $consumList = [];
        }
        if ($complaintList !== false) {
            $complaintList = $complaintList['data']['result'];
        } else {
            $complaintList = [];
        }
        return $this->asJson(['consumList' => $consumList, 'complaintList' => $complaintList]);
    }
    /**
     * 楼宇消费列表
     * @return string
     */
    public function actionBuildConsumeList()
    {
        $searchParams = Yii::$app->request->get('UserConsume');
        $buildId      = Yii::$app->request->get('build_id');
        $buildID      = $searchParams['build_id'];
        $orderCode    = $searchParams['order_id'];
        $startTime    = $searchParams['createdFrom'] == 0 ? '' : strtotime($searchParams['createdFrom']);
        $endTime      = $searchParams['createdTo'] == 0 ? '' : strtotime($searchParams['createdTo']);
        $pager        = new Pagination(['totalCount' => 1000, 'pageSize' => '20']);
        $buildID      = !empty($buildId) ? $buildId : $buildID;
        $request      = Api::getPositionConsumList(
            $buildID,
            ['start_time' => $startTime, 'end_time' => $endTime, 'order_code' => $orderCode],
            $pager->limit,
            $pager->offset
        );
        $request->run();
        $resp = $request->parseJsonData();
        if ($resp === false) {
            die('网络错误:' . $request->getHttpCode());
        }
        $consumeList = $resp['data']['result'];
        $models      = new UserConsume();
        $pager       = new \yii\data\Pagination(['totalCount' => $resp['data']['total'], 'defaultPageSize' => '20']);
        return $this->render('user-consume-list', [
            'pager'       => $pager,
            'consumeList' => $consumeList,
            'userId'      => '',
            'model'       => $models,
            'buildID'     => $buildID,
            'createdFrom' => $searchParams['createdFrom'],
            'createdTo'   => $searchParams['createdTo'],
            'orderID'     => $searchParams['order_id'],
        ]);
    }

    /**
     * 用户客诉列表
     * @return string
     */
    public function actionBuildComplaintList()
    {
        $searchParams = Yii::$app->request->get('UserConsume');
        $buildId      = Yii::$app->request->get('build_id');
        $buildID      = $searchParams['build_id'];
        $pager        = new Pagination(['totalCount' => 1000, 'pageSize' => '20']);
        $buildID      = !empty($buildId) ? $buildId : $buildID;
        $request      = Api::getPositionComplaintList(
            $buildID,
            $pager->limit,
            $pager->offset
        );
        $request->run();
        $resp = $request->parseJsonData();
        if ($resp === false) {
            die('网络错误:' . $request->getHttpCode());
        }
        $complaintList = $resp['data']['result'];
        $pager         = new \yii\data\Pagination(['totalCount' => $resp['data']['total'], 'defaultPageSize' => '20']);
        return $this->render('user-complaint-list', [
            'pager'         => $pager,
            'complaintList' => $complaintList,
        ]);
    }
    public function actionAddComplaint()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/site/login']);
        }
        $this->view->title = '客诉添加';
        $model             = new Complaint();
        $userConsume       = Yii::$app->request->get();

        $manager = new MutiRequestManager();

        $orgReq = Api::getOrgName();
        $manager->addRequest($orgReq);
        $advisoryReq = Api::getAdvisoryAndQuestionTypes();
        $manager->addRequest($advisoryReq);

        $solutionReq = Api::getSolutionList();
        $manager->addRequest($solutionReq);

        $questionReq = Api::getQuestionTypes(1);
        $manager->addRequest($questionReq);

        $payWayReq = Api::getAllPayWay();
        $manager->addRequest($payWayReq);

        $buildingReq = Api::getBuildingList();
        $manager->addRequest($buildingReq);

        $buildingEquipReq = Api::GetBuildingEquipments();
        $manager->addRequest($buildingEquipReq);
        if (!empty($userConsume['user_consume_id'])) {
            $complaintReq = Api::getCustomerComplaint($userConsume['user_consume_id']);
            $manager->addRequest($complaintReq);
        }
        if (empty($userConsume['user_consume_id']) && !empty($userConsume['order_code'])) {
            $complaintReq = Api::getCustomerComplaintByOrderCode($userConsume['order_code']);
            $manager->addRequest($complaintReq);
        }
        if (!empty($userConsume['complain_id'])) {
            $complaintReq = Api::getCustomerComplaintByID($userConsume['complain_id']);
            $manager->addRequest($complaintReq);
        }
        $manager->run();
        // 如果已经创建了
        if (isset($complaintReq)) {
            $conplaintInfo = $complaintReq->parseJsonData()['data'];
            if (!empty($conplaintInfo['complaintInfo'])) {
                $model = Complaint::loadingData($conplaintInfo['complaintInfo']);
            }
        }
        $orgAndNameList = $orgReq->parseJsonData();
        $orgAndNameList = $orgAndNameList['data'];
        $orgList        = array_combine(array_keys($orgAndNameList), array_column($orgAndNameList, 'org_name'));
        $cityList       = array_combine(array_keys($orgAndNameList), array_column($orgAndNameList, 'org_city'));
        $advisoryType   = $advisoryReq->parseJsonData();
        $advisoryList   = [];
        $questionList   = [];
        foreach ($advisoryType as $key => $advisory) {
            $advisoryList[$advisory['advisory_type_id']] = $advisory['advisory_type_name'];
            foreach ($advisory['customerServiceQuestion'] as $k => $question) {
                $questionList[$advisory['advisory_type_id']][$question['question_type_id']] = $question['question_type_name'];
            }
        }
        $solutionList = $solutionReq->parseJsonData()['data'];
        $payWay       = $payWayReq->parseJsonData();
        $payWayList   = $payWay['data'];

        $buildings = $buildingReq->parseJsonData()['data'];

        $buildingList    = array_combine(array_column($buildings, 'id'), array_column($buildings, 'name'));
        $orgBuildingList = ArrayHelper::map($buildings, 'id', 'name', 'org_id');
        $equipTypeList   = array_combine(array_column($buildings, 'id'), array_column($buildings, 'equipment_name'));
        $equipLogList    = array_combine(array_column($buildings, 'id'), array_column($buildings, 'last_log'));
        $optionData      = [
            'cityList'      => $cityList,
            'equipTypeList' => $equipTypeList,
            'equipLogList'  => $equipLogList,
            'questionList'  => $questionList,
        ];

        $userConsumeId = 0;
        if (!empty($userConsume) && empty($model->complaint_id)) {
            $model->org_id          = empty($userConsume['org_id']) ? 0 : $userConsume['org_id'];
            $model->building_id     = empty($userConsume['build_id']) ? 0 : $userConsume['build_id'];
            $model->order_code      = $userConsume['order_code'] . ',';
            $model->user_id         = $userConsume['user_id'];
            $model->nikename        = $userConsume['nickname'];
            $model->buy_time        = date('Y-m-d H:i:s', $userConsume['pay_at']);
            $model->pay_type        = $userConsume['pay_type'];
            $model->register_mobile = $userConsume['register_mobile'];
            $model->user_consume_id = empty($userConsume['user_consume_id']) ? 0 : $userConsume['user_consume_id'];
            $userConsumeId          = $model->user_consume_id;
        }
        $model->manager_id   = Yii::$app->user->identity->id;
        $model->manager_name = Yii::$app->user->identity->username;

        return $this->render('add-complaint', [
            'model'           => $model,
            'orgList'         => $orgList,
            'advisoryList'    => $advisoryList,
            'payWayList'      => $payWayList,
            'optionData'      => $optionData,
            'buildingList'    => $buildingList,
            'userConsumeId'   => $userConsumeId,
            'solutionList'    => $solutionList,
            'orgBuildingList' => $orgBuildingList,
        ]);
    }

    public function actionIndex()
    {
//        if (!Yii::$app->user->can('客诉列表')) {
        //            return $this->redirect(['/site/login']);
        //        }
        $searchModel           = new \backend\modules\service\models\ComplaintSearch();
        $customerComplaintList = $searchModel->search(Yii::$app->request->queryParams);
        $manager               = new MutiRequestManager();

        $orgReq = Api::getOrgName();
        $manager->addRequest($orgReq);
        $advisoryReq = Api::getAdvisoryTypes();
        $manager->addRequest($advisoryReq);
        $solutionReq = Api::getSolutionList();
        $manager->addRequest($solutionReq);
        $buildingReq = Api::getBuildingList();
        $manager->addRequest($buildingReq);
        $questionReq = Api::getQuestionTypes();
        $orgNameReq  = Api::getOrgName();
        $manager->addRequest($questionReq)
            ->addRequest($solutionReq)
            ->addRequest($advisoryReq)
            ->addRequest($orgNameReq);
        $manager->run();

        $buildings       = $buildingReq->parseJsonData()['data'];
        $buildingList    = array_combine(array_column($buildings, 'id'), array_column($buildings, 'name'));
        $orgBuildingList = ArrayHelper::map($buildings, 'id', 'name', 'org_id');
        $solution        = $solutionReq->parseJsonData()['data'];
        $question        = $questionReq->parseJsonData();
        $questionList    = array_combine(array_column($question, 'question_type_id'), array_column($question, 'question_type_name'));
        $advisory        = $advisoryReq->parseJsonData();
        $advisoryList    = array_combine(array_column($advisory, 'advisory_type_id'), array_column($advisory, 'advisory_type_name'));
        $org             = $orgNameReq->parseJsonData()['data'];
        $orgList         = array_combine(array_column($org, 'org_id'), array_column($org, 'org_name'));
        $processStatus   = Complaint::getProcessStatusList();
        return $this->render('index', [
            'searchModel'     => $searchModel,
            'dataProvider'    => $customerComplaintList,
            'buildingList'    => $buildingList,
            'solution'        => $solution,
            'questionList'    => $questionList,
            'advisoryList'    => $advisoryList,
            'orgList'         => $orgList,
            'processStatus'   => $processStatus,
            'orgBuildingList' => $orgBuildingList,
        ]);
    }
    public function actionExport()
    {
        $customerComplaintList = Complaint::exportComplaint(Yii::$app->request->queryParams);
        if (!empty($customerComplaintList['customerComplaint'])) {
            $order       = new OrderInfo();
            $objPHPExcel = new \PHPExcel();
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20); //所有单元格（列）默认宽度
            $objPHPExcel->setActiveSheetIndex(0)
                ->setTitle('客服系统-客诉记录')
                ->setCellValue('A1', '客诉编号')
                ->setCellValue('B1', '创建时间')
                ->setCellValue('C1', '工号')
                ->setCellValue('D1', '机构名称')
                ->setCellValue('E1', '所在城市')
                ->setCellValue('F1', '咨询类型')
                ->setCellValue('G1', '问题类型')
                ->setCellValue('H1', '问题描述')
                ->setCellValue('I1', '点位名称')
                ->setCellValue('J1', '后台显示问题')
                ->setCellValue('K1', '设备分类')
                ->setCellValue('L1', '客户名称')
                ->setCellValue('M1', '来电号码')
                ->setCellValue('N1', '注册号码')
                ->setCellValue('O1', '微信昵称')
                ->setCellValue('P1', '支付方式')
                ->setCellValue('Q1', '购买品种')
                ->setCellValue('R1', '购买时间')
                ->setCellValue('S1', '协议解决方案')
                ->setCellValue('T1', '已退咖啡品种')
                ->setCellValue('U1', '需退款金（元）')
                ->setCellValue('V1', '订单编号')
                ->setCellValue('W1', '最迟退款日期')
                ->setCellValue('X1', '实际退款日期')
                ->setCellValue('Y1', '进度')
                ->setCellValue('Z1', '处理时间')
                ->setCellValue('AA1', '客户区分')
                ->setCellValue('AB1', '退款是否消费')
            ;
            $startNum = 2;
            foreach ($customerComplaintList['customerComplaint'] as $customer) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setTitle('客服系统-客诉记录')
                    ->setCellValue('A' . $startNum, $customer['complaint_code'] . "\t")
                    ->setCellValue('B' . $startNum, $customer['add_time'])
                    ->setCellValue('C' . $startNum, $customer['manager_name'])
                    ->setCellValue('D' . $startNum, $customer['org_id'])
                    ->setCellValue('E' . $startNum, $customer['org_city'])
                    ->setCellValue('F' . $startNum, $customer['advisory_type_id'])
                    ->setCellValue('G' . $startNum, $customer['question_type_id'])
                    ->setCellValue('H' . $startNum, $customer['question_describe'])
                    ->setCellValue('I' . $startNum, $customer['building_name'])
                    ->setCellValue('J' . $startNum, $customer['equipment_last_log'])
                    ->setCellValue('K' . $startNum, $customer['equipment_type'])
                    ->setCellValue('L' . $startNum, $customer['customer_name'])
                    ->setCellValue('M' . $startNum, $customer['callin_mobile'])
                    ->setCellValue('N' . $startNum, $customer['register_mobile'])
                    ->setCellValue('O' . $startNum, $customer['nikename'])
                    ->setCellValue('P' . $startNum, $customer['pay_type'] == -1 ? '未下单' : $order->getPaytype($customer['pay_type']))
                    ->setCellValue('Q' . $startNum, $customer['buy_type'])
                    ->setCellValue('R' . $startNum, $customer['buy_time'])
                    ->setCellValue('S' . $startNum, $customer['solution_id'])
                    ->setCellValue('T' . $startNum, $customer['retired_coffee_type'])
                    ->setCellValue('U' . $startNum, $customer['order_refund_price'])
                    ->setCellValue('V' . $startNum, '\'' . $customer['order_code'])
                    ->setCellValue('W' . $startNum, $customer['latest_refund_time'])
                    ->setCellValue('X' . $startNum, $customer['real_refund_time'])
                    ->setCellValue('Y' . $startNum, $customer['process_status'])
                    ->setCellValue('Z' . $startNum, $customer['complete_time'] <= 0 ? '' : Tools::time2string($customer['complete_time'] - strtotime($customer['add_time'])))
                    ->setCellValue('AA' . $startNum, Complaint::$customerTypeList[$customer['customer_type']] ?? '')
                    ->setCellValue('AB' . $startNum, $customer['is_consumption'])
                ;
                $startNum++;
            }
            $objWriter      = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $outputFileName = "咖啡零点吧-客诉数据信息列表" . date("Y-m-d") . ".xlsx";
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header('Content-Disposition:inline;filename="' . $outputFileName . '"');
            header("Content-Transfer-Encoding: binary");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //2007
            header('Cache-Control: max-age=0'); //禁止缓存
            $objWriter->save('php://output');
            exit;
        }
    }
    public function actionView($id)
    {
        $complaintID = Yii::$app->request->get('id');
        if ($complaintID) {
            $complaint = Api::getComplaintInfo((int) $complaintID);
            $manager   = new MutiRequestManager();
            $manager->addRequest($complaint);
            $manager->run();
            $complaintInfo = $complaint->parseJsonData()['data'];
            return $this->render('view', [
                'model' => (object) $complaintInfo['complaintInfo'],
            ]);
        }
    }

    /**
     * 用户咖啡
     * gaoyongli
     * @return string
     */
    public function actionUserCoffee()
    {
        $this->view->title = '用户咖啡';
        if (Yii::$app->request->isGet) {
            $userID     = Yii::$app->request->get('user_id');
            $userCoffee = Api::getUserCoffee($userID);
            $userCoffee->run();
            $userCoffeeList = $userCoffee->parseJsonData();
            $coffee         = $userCoffeeList['data'];
            return $this->render('user-coffee', ['userCoffee' => $coffee]);
        }
    }

    /**
     *  用户优惠劵
     * gaoyongli
     * @return string
     */
    public function actionUserCoupon()
    {
        $this->view->title = '用户优惠劵';
        if (Yii::$app->request->isGet) {
            $userID     = Yii::$app->request->get('user_id');
            $userCoupon = Api::getUserCoupon($userID);
            $userCoupon->run();
            $userCouponList = $userCoupon->parseJsonData();
            $coupon         = $userCouponList['data'];
            return $this->render('user-coupon', ['userCouponList' => $coupon]);
        }
    }
    public function actionBuildCoffee()
    {
        $this->view->title = '饮品菜单';
        if (Yii::$app->request->isGet) {
            $equipmentCode = Yii::$app->request->get('equipment_code');
            $productList   = Api::getProductList($equipmentCode);
            $productList->run();
            $buildProductList = $productList->parseJsonData();
            $product          = $buildProductList['data'];
            return $this->render('build-product-list', ['buildProductList' => $product]);
        }
    }
    /**
     * 数据不同步跳转问题.
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-01
     * @param:    [param]
     * @return
     * @return    [type]     [description]
     */
    public function actionEquipmentsInfo()
    {
        $equipmentCode = Yii::$app->request->get('code');
        $equipment     = Equipments::equip($equipmentCode);
        $eqID          = isset($equipment->id) ? $equipment->id : 0;
        return $this->redirect(array('/equipments/view?id=' . $eqID));
    }

    /**
     * 根据公司ID获取楼宇名称列表
     * @author zhenggangwei
     * @date   2020-01-04
     * @param  integer     $cid 公司ID
     * @return array
     */
    public function actionBuilding($cid)
    {
        $buildNameList = [];
        if ($cid) {
            $buildName = Api::getBuildNameByCid($cid);
            $buildName->run();
            $buildNameList = $buildName->parseJsonData();
        }
        return $this->render('building', ['buildNameList' => $buildNameList]);
    }
}
