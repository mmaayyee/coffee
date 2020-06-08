<?php

namespace frontend\controllers;

use backend\models\DistributionDailyTask;
use backend\models\DistributionFiller;
use backend\models\DistributionFillerGram;
use backend\models\DistributionMaintenance;
use backend\models\DistributionTask;
use backend\models\DistributionUser;
use backend\models\Organization;
use backend\models\OutStatistics;
use backend\models\ScmMaterial;
use backend\models\ScmMaterialType;
use backend\models\ScmWarehouseOut;
use common\helpers\Tools;
use common\models\Api;
use common\models\Building;
use common\models\Equipments;
use common\models\EquipTask;
use common\models\EquipTaskFitting;
use common\models\JSSDK;
use common\models\SendNotice;
use common\models\Sysconfig;
use common\models\WxMember;
use frontend\models\DistributionTaskImgurl;
use frontend\models\FrontendDistributionTask;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;

/**
 * DistributionTaskController implements the CRUD actions for DistributionTask model.
 */
class DistributionTaskController extends BaseController
{

    /**
     * 企业微信保存素材图片
     * @author wangxiwen
     * @version 2018-06-27
     * @param int $id 任务ID
     * @param string $media_id素材ID
     * @return bool|string
     */
    public function actionUpload()
    {
        $id      = Yii::$app->request->get('id');
        $mediaId = Yii::$app->request->get('media_id');
        $imgName = time() . $id . '.jpg';
        $date    = date('Y-m-d');
        $imgUrl  = '/web/uploads/';
        if (!file_exists(Yii::$app->basePath . $imgUrl . $date)) {
            mkdir(Yii::$app->basePath . $imgUrl . $date, 0777, true);
        }
        $uploadImg = FrontendDistributionTask::getUploadImg($mediaId);
        file_put_contents(Yii::$app->basePath . $imgUrl . $date . '/' . $imgName, $uploadImg);
        $imgFilePath = 'uploads/' . $date . '/' . $imgName;
        //插入数据库
        DistributionTaskImgurl::saveTaskImgurl($id, $imgFilePath);
        return Yii::$app->params['frontend'] . $imgFilePath;
    }
    /**
     * 任务完成页面的检测
     * @author wangxiwen
     * @version 2018-06-19
     * @return array
     */
    public function actionTesting()
    {
        $id = Yii::$app->request->get('id');
        //获取运维任务信息
        $taskList = DistributionTask::getDistributionTask($id);
        //获取设备编号
        $equipCode = Equipments::getEquipCode($taskList->equip_id);
        //检测清洗任务是否完成
        $washSign = ['done' => 0, 'date' => ''];
        $washTime = Api::getBase('equip-wash-time', '&equipCode=' . $equipCode);
        if (!empty($washTime) && $washTime > $taskList->start_delivery_time) {
            $washSign['done'] = 1;
            $washSign['date'] = date('Y-m-d H:i:s', $washTime);
        }
        //检测加料|换料任务是否完成
        $materialSign = ['done' => 0, 'date' => '', 'material' => []];
        $scmMaterial  = scmMaterial::getScmMaterial();
        $materialList = [];
        if (!empty($taskList->bluetooth_upload)) {
            $materialArr = Json::decode($taskList->bluetooth_upload);
            foreach ($materialArr as $stockCode => $materials) {
                foreach ($materials as $materialTypeId => $material) {
                    $scmMaterialDetail = $scmMaterial[$materialTypeId] ?? [];
                    if (empty($scmMaterialDetail)) {
                        continue;
                    }
                    $type                          = $scmMaterialDetail['type'];
                    $unit                          = $scmMaterialDetail['unit'];
                    $weightUnit                    = $scmMaterialDetail['weight_unit'];
                    $materialTypeName              = $scmMaterialDetail['material_type_name'];
                    $showUnit                      = $type == 1 ? $weightUnit : $unit;
                    $materialList[$materialTypeId] = [
                        'material_type_name' => $materialTypeName,
                        'gram'               => round($material['addAmount'], 2) . $showUnit,
                    ];
                }
            }
            $materialSign['done']     = 1;
            $materialSign['date']     = date('Y-m-d H:i:s', $taskList->end_delivery_time);
            $materialSign['material'] = $materialList;
        }
        $testingInfo = [
            'clean'        => $washSign,
            'distribution' => $materialSign,
        ];
        return Json::encode($testingInfo);
    }

    /**
     * 显示任务的列表 条件：没有完成的任务.
     * @author wangxiwen
     * @return mixed
     */
    public function actionIndex()
    {
        $jsSdk       = new JSSDK();
        $signPackage = $jsSdk->getJsSdk();
        //待办任务列表
        $taskToBeDone = DistributionTask::getTaskToBeDone($this->userinfo['userid']);
        //待办任务数量
        $taskToBeDoneCount = count($taskToBeDone);
        //待办任务中的紧急任务置顶
        $taskToBeDoneList = DistributionTask::setUrgentTaskTop($taskToBeDone, $taskToBeDoneCount);
        // 已完成任务列表
        $taskHistorical = DistributionTask::getTaskHistorical($this->userinfo['userid']);
        // 已完成任务数量
        $taskHistoricalCount = count($taskHistorical);
        // 未接受任务数量
        $taskUnreceived = DistributionTask::getTaskUnreceived($this->userinfo['userid']);
        //未接收任务数量
        $taskUnreceivedCount = count($taskUnreceived);
        //获取楼宇名称
        $buildName = DistributionDailyTask::getBuildName();

        return $this->render('index', [
            'signPackage'         => $signPackage,
            'taskToBeDone'        => $taskToBeDoneList,
            'taskToBeDoneCount'   => $taskToBeDoneCount,
            'taskHistorical'      => $taskHistorical,
            'taskHistoricalCount' => $taskHistoricalCount,
            'buildName'           => $buildName,
            'userid'              => $this->userinfo['userid'],
            'taskUnreceivedCount' => $taskUnreceivedCount,
        ]);
    }

    /**
     * 接收任务
     * @author wangxiwen
     * @version 2018-06-22
     * @return
     */
    public function actionRecive()
    {
        $userId = Yii::$app->request->get('userid');
        //更新任务任务接收时间
        DistributionTask::saveTaskReciveTime($userId);
        return $this->redirect(['index']);
    }

    /**
     * 任务打卡页
     * @author wangxiwen
     * @version 2018-10-10
     * @param int $id 任务ID
     **/
    public function actionEmergencyIndex($id)
    {
        $distributeTaskArr = DistributionTask::getDistributionTask($id);
        if (!$distributeTaskArr || trim($distributeTaskArr->assign_userid) != trim($this->userinfo['userid'])) {
            return $this->render('/site/error', ['message' => '该数据不存在或者您没有此操作权限']);
        }
        // 获取该用户所在分公司
        $orgId = WxMember::getOrgId($this->userinfo['userid']);
        return $this->render('emergency-task-index', [
            'distributeTaskArr' => $distributeTaskArr,
            'orgId'             => $orgId,
        ]);
    }

    /**
     * 完成打卡更新操作
     * @author wangxiwen
     * @version 2018-10-10
     * @param int $id 任务ID
     **/
    public function actionEmergencyClock()
    {
        // 获取任务id
        $id = Yii::$app->request->get('id');
        //任务开始位置信息
        $params['startLatitude']  = Yii::$app->request->get('start_latitude');
        $params['startLongitude'] = Yii::$app->request->get('start_longitude');
        $params['startAddress']   = Yii::$app->request->get('start_address');
        // 获取任务详情
        $model = DistributionTask::getDistributionTask($id);
        if (!$model || trim($model->assign_userid) != trim($this->userinfo['userid'])) {
            return $this->render('/site/error', ['message' => '该数据不存在或者您没有此操作权限']);
        }
        //判断除当前任务外是否存在已打卡未完成的任务
        $notDelivery = DistributionTask::getTaskSignedIn($id, $model->assign_userid);
        if (!empty($notDelivery)) {
            Yii::$app->getSession()->setFlash("error", "存在未完成任务,打卡失败");
            return $this->redirect(['index']);
        }
        //任务类型
        $taskType = explode(',', $model->task_type);
        //更新任务打卡信息
        $saveRes = DistributionTask::saveTaskSignedIn($model, $params);
        if (!$saveRes) {
            return $this->render('/site/error', ['message' => '打卡失败']);
        }
        //判断任务类型决定跳转页面
        if (in_array(DistributionTask::URGENT, $taskType) && count($taskType) == 1) {
            return $this->redirect(['emergency-index?id=' . $id]);
        } else {
            return $this->redirect(['distribution-index?id=' . $id]);
        }

    }

    /**
     *  紧急任务完成更新操作
     *  @author wangxiwen
     *  @version 2018-10-10
     **/
    public function actionUrgentComplete()
    {
        // 获取任务id
        $id = Yii::$app->request->get('id');
        //任务完成位置信息
        $params['endLatitude']  = Yii::$app->request->get('end_latitude');
        $params['endLongitude'] = Yii::$app->request->get('end_longitude');
        $params['endAddress']   = Yii::$app->request->get('end_address');
        // 获取任务详情
        $model = DistributionTask::getDistributionTask($id);

        if (!$model || trim($model->assign_userid) != trim($this->userinfo['userid'])) {
            return $this->render('/site/error', ['message' => '该数据不存在或者您没有此操作权限']);
        }
        $saveRes = DistributionTask::saveTaskUrgent($model, $params);
        if (!$saveRes) {
            return $this->render('/site/error', ['message' => '任务完成失败']);
        }
        return $this->redirect(['index']);
    }

    /**
     *  打卡完成后进入任务完成页面
     *  @author wangxiwen
     *  @version 2018-10-10
     *  @param int $id 任务ID
     **/
    public function actionDistributionIndex($id)
    {
        $model = DistributionTask::getDistributionTask($id);
        if (!$model || trim($model->assign_userid) != trim($this->userinfo['userid'])) {
            return $this->render('/site/error', ['message' => '该数据不存在或者您没有此操作权限']);
        }
        $model->task_type = explode(',', $model->task_type);
        // 获取该用户所在分公司
        $orgId = WxMember::getOrgId($this->userinfo['userid']);
        // 判断任务中是否有维修任务
        $isRepair = 0;
        if (in_array(DistributionTask::SERVICE, $model->task_type)) {
            $isRepair = 1;
        }
        //查询是否存在任务图片
        $imgList = DistributionTaskImgurl::getTaskImgUrlList($id);
        return $this->render('daily-task-detail', [
            'isRepair'  => $isRepair,
            'taskId'    => $id,
            'orgId'     => $orgId,
            'startTime' => $model->start_delivery_time,
            'taskType'  => $model->task_type,
            'abnormals' => $model->abnormal,
            'imgList'   => $imgList,
        ]);
    }

    /**
     *  维修|任务接收任务和开始打卡。
     *  @return 进入详情页
     **/
    public function actionDailyClock()
    {
        $taskId         = Yii::$app->request->get('id');
        $startLatitude  = Yii::$app->request->get('start_latitude');
        $startLongitude = Yii::$app->request->get('start_longitude');
        $startAddress   = Yii::$app->request->get('start_address');
        $model          = DistributionTask::findOne($taskId);
        if (!$model || trim($model->assign_userid) != trim($this->userinfo['userid'])) {
            return $this->render('/site/error', ['message' => '该数据不存在或者您没有此操作权限']);
        }
        if (!$model->recive_time) {
            $model->recive_time = time();
        } else if (!$model->start_delivery_time) {
            $model->start_delivery_time = time();
            $model->start_address       = $startAddress;
            $model->start_latitude      = $startLatitude;
            $model->start_longitude     = $startLongitude;
        }
        if ($model->save() === false) {
            Yii::$app->getSessioin()->setFlash('error', '对不起，打卡失败。');
        }
        return $this->redirect(['distribution-index?id=' . $taskId]);
    }

    /**
     *  任务完成页面（配送、维修）保存数据
     *  @author wangxiwen
     *  @version 2018-10-10
     **/
    public function actionTaskExecution()
    {
        //获取提交的数据
        $params = Yii::$app->request->post();
        //验证提交数据(杯子杯盖水)
        $submitRes = FrontendDistributionTask::verifyParams($params);
        if (!$submitRes) {
            Yii::$app->getSession()->setFlash("error", "*为必填选项,可输入0");
            return $this->redirect(['distribution-index?id=' . $params['id']]);
        }
        //获取任务内容
        $taskModel = DistributionTask::getDistributionTask($params['id']);
        //检测权限
        if (!$taskModel || trim($taskModel->assign_userid) != trim($this->userinfo['userid'])) {
            return $this->render('/site/error', ['message' => '该数据不存在或者您没有此操作权限']);
        }
        //任务类别(纯物料添加任务true|正常任务false)
        $taskTypeRes = FrontendDistributionTask::verifyTaskType($taskModel);
        if (!$taskTypeRes) {
            //检测清洗任务是否完成
            $cleanRes = FrontendDistributionTask::verifyClean($params, $taskModel);
            if (!$cleanRes) {
                Yii::$app->getSession()->setFlash("error", "请您完成清洗且上传至少6张图片");
                return $this->redirect(['distribution-index?id=' . $params['id']]);
            }
            //检测维修时间是否填写
            $repairRes = FrontendDistributionTask::verifyRepair($params, $taskModel);
            if (!$repairRes) {
                Yii::$app->getSession()->setFlash("error", "维修时间为必填选项");
                return $this->redirect(['distribution-index?id=' . $params['id']]);
            }
        }
        //检查加料或换料任务是否完成(蓝牙秤上传)
        if (empty($taskModel->bluetooth_upload)) {
            Yii::$app->getSession()->setFlash("error", "请用蓝牙秤上传物料");
            return $this->redirect(['distribution-index?id=' . $params['id']]);
        }
        $equipCode = Equipments::getEquipCode($taskModel->equip_id);
        //获取产品组料仓信息
        $proGroupStockInfo = Api::getEquipProductGroupStockInfo('equip-product-group-stock-info', '&equipCode=' . $equipCode);
        //获取料仓编号对应物料分类关系数组
        $stockMaterialType = Tools::map($proGroupStockInfo, 'stock_code', 'material_type_id', null, null);
        //获取物料信息
        $scmMaterial = ScmMaterial::getScmMaterial();

        //如果提交数据中补充物料杯子和杯盖的数量大于0则添加到运维任务bluetooth_upload字段里
        $taskModel->bluetooth_upload = FrontendDistributionTask::addBluetoothUpload($params, $taskModel, $stockMaterialType);
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        //数据验证完成后更新数据库
        $saveRes = FrontendDistributionTask::saveDistributionTask($taskTypeRes, $params, $taskModel, $scmMaterial);
        if ($saveRes !== true) {
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash("error", $saveRes);
            return $this->redirect(['distribution-index?id=' . $params['id']]);
        }
        // 保存清洗图片
        DistributionTaskImgurl::uplaodTaskImage($params, $taskModel->id);
        //发送消息通知配送主管和经理
        $username = WxMember::getMemberDetail("name", ['userid' => $taskModel->assign_userid])['name'];
        $url      = 'distribution-task/task-record-detail?id=' . $params['id'];
        $orgId    = WxMember::getOrgId($taskModel->assign_userid);
        $roles    = ArrayHelper::getColumn(WxMember::getRoleByOrg($orgId), 'userid');
        $userList = implode('|', $roles);
        $taskType = DistributionTask::getTaskType($taskModel->task_type);
        $taskRes  = SendNotice::sendWxNotice($userList, $url, $username . '的' . $taskType . '结果:' . DistributionTask::$taskResult[$taskModel->result] . '，请注意查看。', Yii::$app->params['equip_agentid']);
        if (!$taskRes) {
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash("error", "消息通知发送失败");
            return $this->redirect(['distribution-index?id=' . $params['id']]);
        }
        //事务通过
        $transaction->commit();
        return $this->redirect(['index']);
    }

    /**
     *  任务记录 （已完成的任务）
     *
     **/
    public function actionTaskRecordIndex()
    {
        $model  = new DistributionTask();
        $params = Yii::$app->request->get('DistributionTask');
        if (!$params) {
            $params['task_type']  = '';
            $params['start_time'] = date("Y-m-d", strtotime("-1 months"));
            $params['end_time']   = date("Y-m-d");
        }
        $query = DistributionTask::find()->where(['is_sue' => 2, 'assign_userid' => $this->userinfo['userid']])->orderBy("recive_time DESC");

        $query->andFilterWhere(['task_type' => $params['task_type']]);
        $query->andFilterWhere(['>=', 'end_delivery_date', $params['start_time']]);
        $query->andFilterWhere(['<=', 'end_delivery_date', $params['end_time']]);

        $distributeTaskArr   = $query->all();
        $distributeTaskCount = count($distributeTaskArr);

        $model->task_type  = $params['task_type'];
        $model->start_time = $params['start_time'];
        $model->end_time   = $params['end_time'];

        return $this->render('task-record-index', [
            'model'               => $model,
            'distributeTaskArr'   => $distributeTaskArr,
            'distributeTaskCount' => $distributeTaskCount,
        ]);
    }

    /**
     *  任务记录详情 页面
     *  @author wangxiwen
     *  @version 2018-10-31
     *  @param int $id 任务ID
     **/
    public function actionTaskRecordDetail($id)
    {
        $distributeTaskArr   = DistributionTask::getDistributionTask($id);
        $fillerArr           = DistributionFiller::getDistributionFiller($id);
        $fillerGram          = DistributionFillerGram::getDistributionFillerGram($id);
        $maintenanceArr      = DistributionMaintenance::getDistributionMaintenance($id);
        $equipTaskFittingArr = EquipTaskFitting::getEquipTaskFitting($id);
        $materialType        = ScmMaterialType::getIdNameArr(2);
        return $this->render('task-record-detail', [
            'distributeTaskArr'   => $distributeTaskArr,
            'fillerArr'           => $fillerArr,
            'fillerGram'          => $fillerGram,
            'materialType'        => $materialType,
            'maintenanceArr'      => $maintenanceArr,
            'equipTaskFittingArr' => $equipTaskFittingArr,
        ]);
    }

    /**
     * 个人数据统计
     * @version 2016-08-27
     * @return  [type]     [description]
     */
    public function actionUserDataSync()
    {
        $startDate = Yii::$app->request->get('startDate') ? Yii::$app->request->get('startDate') : '';
        $endDate   = Yii::$app->request->get('endDate') ? Yii::$app->request->get('endDate') : '';
        $author    = $this->userinfo['userid'];
        // 日完成台数
        $taskDateCount  = DistributionTask::find()->where(['is_sue' => 2, 'end_delivery_date' => date("Y-m-d"), 'assign_userid' => $author])->count();
        $equiptaskCount = EquipTask::getCount($author);
        $taskDateCount += $equiptaskCount;
        // 月完成台数
        $taskMonthCount      = FrontendDistributionTask::getMonthCount($author);
        $equipTaskMonthCount = EquipTask::getCount($author, 2);
        $taskMonthCount += $equipTaskMonthCount;
        // 获取工作时长
        $data = DistributionUser::workTime($author, $startDate, $endDate);

        // 获取领取的物料
        $data['material'] = DistributionUser::receiveMaterial($author, $startDate, $endDate);

        return $this->render('task-statistcs-index', ['data' => $data, 'startDate' => $startDate, 'endDate' => $endDate, 'author' => $author, 'taskDateCount' => $taskDateCount, 'taskMonthCount' => $taskMonthCount]);
    }

    /**
     * 配送员手机端确认领料功能
     * @author wangxiwen
     * @version 2018-10-10
     **/
    public function actionConfirmWarehouseOutIndex()
    {
        $model   = new ScmWarehouseOut();
        $outList = $model->getWarehouseOut($this->userinfo['userid']);
        //获取出库物料信息
        $packetArr = $model->getScmWarehouseOutMaterial($outList);

        $pages = new \yii\data\Pagination(['totalCount' => count($packetArr)]);
        $pages->setPageSize(10);
        $packetArr = array_slice($packetArr, $pages->offset, $pages->limit, true);

        return $this->render('confirm-warehouse-out-index', [
            'packetArr'   => $packetArr,
            'pages'       => $pages,
            'saveSuccess' => false,
        ]);
    }

    /**
     *  配送员点击确认，状态变为已领取状态。库存处理。
     *  @author wangxiwen
     *  @version 2018-10-10
     **/
    public function actionConfirm()
    {
        $params   = Yii::$app->request->post();
        $author   = $params['author']; //领料人
        $material = $params['material']; //物料信息
        //查询所属分公司和分公司下其他运维人员
        $orgId   = WxMember::getOrgId($author);
        $userArr = WxMember::getMemberIDArr($orgId);
        //查询运维使用物料的规格信息
        $scmMaterial = ScmMaterial::getScmMaterial();
        $model       = new ScmWarehouseOut();
        //除自己外的运维人员
        $userList = ScmWarehouseOut::getUserList($author, $userArr);
        //查询出库状态(其他运维人员)true全部已领料false存在未领料人员
        $outted = ScmWarehouseOut::getOutList($userList);
        //获取待确认的出库单列表
        $warehouseOutList = $model->getWarehouseOut($author);
        //开启事务
        $transaction = Yii::$app->db->beginTransaction();
        $status      = $outted ? ScmWarehouseOut::OUTTED : ScmWarehouseOut::OUTTING;
        //修改出库统计状态
        $saveOutStatsStatusRes = OutStatistics::saveStatusOutStatistics($status, $orgId);
        //修改出库状态为出库完成(其他人已完成领料时执行)
        $saveOutStatusRes      = $outted ? $model->saveStatusOut($userList) : true;
        $saveOutStatsStatusRes = 1;
        //修改当前领料人员的出库状态及领料信息
        $saveOutRes = $model->saveScmWarehouseOut($warehouseOutList, $material, $status, $scmMaterial);
        if (!$saveOutStatsStatusRes || !$saveOutStatusRes || !$saveOutRes) {
            Yii::$app->getSession()->setFlash("error", "物料领取失败");
            $transaction->rollBack();
            return $this->redirect(['confirm-warehouse-out-index']);
        }
        $transaction->commit();
        //所有运维人员领取物料成功后生成真实出库单
        if ($outted) {
            //获取最新出库单数据
            $materialInfo = OutStatistics::getRealOutStatistics($userArr, $scmMaterial);
            //生成真实出库单
            OutStatistics::saveRealOutStatistics($orgId, $materialInfo);
        }
        Yii::$app->getSession()->setFlash("success", "领料完成");
        return $this->redirect(['index']);
    }

    /**
     * 待分配任务列表
     * @author wangxl
     * @return string
     */
    public function actionWaitForTask()
    {
        $position = WxMember::getFiled('position', ['userid' => $this->userinfo['userid']]);
        //判断用户是否有权限进入
        if (Sysconfig::getConfig('waitForTask') == $position) {

            $this->layout = "main";
            $orgId        = WxMember::getOrgId($this->userinfo['userid']);
            //北京总部
            if ($orgId == Organization::HEAD_OFFICE) {
                $equipTaskArr = EquipTask::find()->where(['is_userid' => EquipTask::NO_USER])->asArray()->orderBy('create_time DESC')->all();
            } else {
                //分公司
                $equipTaskArr = EquipTask::getEquipTaskByOrgId($orgId);
            }

            return $this->render('wait-for-task', [
                'equipTaskArr'   => $equipTaskArr,
                'equipTaskCount' => count($equipTaskArr),
            ]);
        } else {
            return $this->renderContent('<h4>您没有访问操作权限</h4>');
        }
    }

    /**
     * 增加提交维修任务的功能
     * @return string
     */
    public function actionAddRepairTask()
    {
        $model = new EquipTask();
        if (Yii::$app->request->post()) {
            $param                = Yii::$app->request->post()['EquipTask'];
            $param['content']     = !$param['content'] ? '' : implode(',', $param['content']);
            $param['create_time'] = time();
            $param['create_user'] = $username = WxMember::getFiled('name', ['userid' => $this->userinfo['userid']]);
            $param['equip_id']    = Equipments::getField('id', ['build_id' => $param['build_id']]);
            $param['task_type']   = EquipTask::MAINTENANCE_TASK;
            $param['is_userid']   = 2;
            $param['remark']      = Tools::filterEmoji($param['remark']);

            $transaction = Yii::$app->db->beginTransaction();

            if (!$model->load(['EquipTask' => $param]) || $model->save() === false) {
                Yii::$app->getSession()->setFlash('error', '添加维修任务失败');
                return $this->render('add-repair-task', [
                    'model'  => $model,
                    'userId' => $this->userinfo['userid'],
                ]);
            }

            $orgId = Equipments::getField('org_id', ['build_id' => $param['build_id']]);
            //给公司配送主管发送通知
            if ($orgId) {
                $userId   = WxMember::getDisResponsibleFromOrg($orgId);
                $username = WxMember::getFiled('name', ['userid' => $userId]);
                SendNotice::sendWxNotice($userId, null, $username . "上报了一条新的设备维修任务，请注意查看", Yii::$app->params['equip_agentid']);
            }

            $transaction->commit();

            Yii::$app->getSession()->setFlash('success', '添加维修任务成功');

            return $this->render('add-repair-task', [
                'model'  => new EquipTask(),
                'userId' => $this->userinfo['userid'],
            ]);
        } else {

            return $this->render('add-repair-task', [
                'model'  => $model,
                'userId' => $this->userinfo['userid'],
            ]);
        }
    }

    /**
     * 输入楼宇显示相应内容
     * @param  [type] $build_id [description]
     * @return [type]           [description]
     */
    public function actionAjaxGetBuild()
    {
        if (Yii::$app->request->isAjax) {
            $type    = Yii::$app->request->get('type', 2);
            $buildId = Yii::$app->request->get('build_id');
            $userid  = Yii::$app->request->get('userid');

            if (!$buildId) {
                return json_encode([]);
            }

            $buildModel            = Building::findOne($buildId);
            $data['build_name']    = $buildModel->name;
            $data['build_number']  = $buildModel->build_number;
            $data['build_address'] = $buildModel->province . $buildModel->city . $buildModel->area . $buildModel->address . $buildModel->name;
            $data['equip_id']      = '';
            $data['equip_code']    = '';
            $data['equip_type']    = '';
            if ($buildModel->equip) {
                $data['equip_id']   = $buildModel->equip->id;
                $data['equip_code'] = $buildModel->equip->equip_code;
                $data['equip_type'] = isset($buildModel->equip->equipTypeModel->model) ? $buildModel->equip->equipTypeModel->model : '';
            } else {
                $data['equip_type'] = isset($buildModel->delivery->equipType->model) ? $buildModel->delivery->equipType->model : '';
            }

            // 指定配送人员
            $data['deliveryPersonArr'] = $this->getWorkDistributionUserArr($buildId, $userid, $type);
            return json_encode($data);
        } else {
            throw new NotFoundHttpException('不是ajax请求');
        }
    }

    /**
     * 获取上班可接单的配送员列表
     * @param   $type      1-获取设备加配送人员 2-获取设备人员
     * @return  [type]              [description]
     */
    public function getWorkDistributionUserArr($buildId, $userId, $type = 1)
    {
        // 获取楼宇所在分公司
        $orgId = Building::getField('org_id', ['id' => $buildId]);
        // 获取成员列表
        $userList = WxMember::distributionIdNameArr($orgId, $type);
        $html     = '<option value="">请选择</option>';
        if (!$userList) {
            return $html;
        }

        foreach ($userList as $userObj) {
            if (isset($userObj->distributionUser->user_status)) {
                if ($userObj->distributionUser->user_status == DistributionUser::WORK_ON) {
                    if (trim($userObj->userid) == trim($userId)) {
                        $html .= "<option value='" . $userObj->userid . " ' selected='selected'>" . $userObj->name . "</option>";
                    } else {
                        $html .= "<option value='" . $userObj->userid . "'>" . $userObj->name . "</option>";
                    }
                }
            } else {
                if (trim($userObj->userid) == trim($userId)) {
                    $html .= "<option value='" . $userObj->userid . " ' selected='selected'>" . $userObj->name . "</option>";
                } else {
                    $html .= "<option value='" . $userObj->userid . "'>" . $userObj->name . "</option>";
                }
            }
        }
        return $html;
    }

    /**
     * 新增加料任务
     * @author sulingling
     * @return string
     */
    public function actionMaterielAddRepairTask()
    {
        $model = new DistributionTask();
        if (Yii::$app->request->post()) {
            $param = Yii::$app->request->post();
            //查询是否存在未完成的任务
            $task = DistributionTask::find()->where(['build_id' => $param['DistributionTask']['build_id'], 'is_sue' => 1])->one();
            if (!empty($task)) {
                Yii::$app->getSession()->setFlash('error', '楼宇存在未完成任务');
                return $this->redirect(['index']);
            }
            $distributionTask                        = [];
            $distributionTask['content']             = '纯物料添加任务';
            $distributionTask['create_time']         = time();
            $distributionTask['author_id']           = Yii::$app->user->id;
            $distributionTask['equip_id']            = Equipments::getField('id', ['build_id' => $param['DistributionTask']['build_id']]);
            $distributionTask['task_type']           = (string) DistributionTask::DELIVERY;
            $distributionTask['build_id']            = $param['DistributionTask']['build_id'];
            $distributionTask['start_longitude']     = $param['start_longitude'];
            $distributionTask['start_latitude']      = $param['start_latitude'];
            $distributionTask['start_address']       = $param['start_address'];
            $distributionTask['recive_time']         = time();
            $distributionTask['start_delivery_time'] = time();
            $distributionTask['assign_userid']       = $this->userinfo['userid'];

            if (!$model->load(['DistributionTask' => $distributionTask]) || $model->save() === false) {
                Yii::$app->getSession()->setFlash('error', '新增加料任务失败');
                return $this->render('materiel-add-repair-task', [
                    'model'  => $model,
                    'userId' => $this->userinfo['userid'],
                ]);
            }

            $orgId = Equipments::getField('org_id', ['build_id' => $param['DistributionTask']['build_id']]);
            //给公司配送主管发送通知
            if ($orgId) {
                $userId   = WxMember::getDisResponsibleFromOrg($orgId);
                $username = WxMember::getFiled('name', ['userid' => $userId]);
                SendNotice::sendWxNotice($userId, null, $username . "上报了一条纯物料添加任务，请注意查看", Yii::$app->params['equip_agentid']);
            }
            Yii::$app->getSession()->setFlash('success', '添加纯物料任务成功');

            return $this->redirect(['distribution-index?id=' . $model->id]);
        }
        return $this->render('materiel-add-repair-task', [
            'model'  => $model,
            'userId' => $this->userinfo['userid'],
        ]);
    }
}
