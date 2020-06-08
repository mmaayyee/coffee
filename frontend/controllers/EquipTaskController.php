<?php
namespace frontend\controllers;

use backend\models\EquipLightBoxDebug;
use backend\models\EquipSymptom;
use backend\models\Manager;
use backend\models\ScmEquipType;
use common\models\Building;
use common\models\EquipLightBoxAcceptanceTaskResult;
use common\models\EquipLightBoxRepair;
use common\models\Equipments;
use common\models\EquipTask;
use common\models\EquipTaskFitting;
use common\models\SendNotice;
use common\models\WxMember;
use common\models\EquipExtra;
use common\helpers\Tools;
use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use backend\models\EquipExtraLog;
use backend\models\EquipRepair;
use frontend\models\FrontendDistributionTask;

/**
 * Site controller
 */
class EquipTaskController extends BaseController
{
    /**
     * 获取任务信息
     * @return [type] [description]
     */
    public function actionIndex()
    {
        //获取维修结果
        $process_result = Yii::$app->request->get('process_result', 1);
        // 获取任务类型
        $task_type  = Yii::$app->request->get('task_type', 1);
        $task_list  = [];
        $task_count = 0;
        $view       = 'index';
        if ($task_type != 2) {
            // 查询条件
            if ($process_result == 1) {
                //需要维修的任务
                $where = ["process_result" => 1];
            } else {
                //维修过的任务
                $where = ['>', 'process_result', 1];
            }
            $where = ['and', ["assign_userid" => $this->userinfo['userid']], ["task_type" => $task_type], $where];
            // 要查询的字段
            $field = 'equip_task.id,build_id,equip_task.create_time, process_result';
            //获取符合查询条件的数据总数
            $task_count = EquipTask::find()->where($where)->count();
            //获取数据列表
            $task_list = EquipTask::getEquipTaskList($field, $where);
            //不同任务类型调用不同模板
            if ($task_type == EquipTask::LIGHTBOX_ACCEPTANCE_TASK) {
                $view = 'light-box-index';
            }elseif($task_type == EquipTask::EXTRA_TASK){
                //设备附件任务
                $view = 'extra-index';
            }
        }

        return $this->render($view, [
            'task_list'      => $task_list,
            'task_count'     => $task_count,
            'process_result' => $process_result,
        ]);
    }
    /**
     * 任务详情
     * @return [type] [description]
     */
    public function actionDetail()
    {
        $id          = Yii::$app->request->get('id');
        $task_detail = EquipTask::taskDetailObj(['equip_task.id' => $id, 'assign_userid' => $this->userinfo['userid']]);
        $title = '维修任务';
        if (!$task_detail || $task_detail['task_type'] == 2) {
            Yii::$app->getSession()->setFlash('error', '您没有此操作权限');
            return $this->redirect(['index']);
        }
        if ($task_detail['task_type'] == EquipTask::MAINTENANCE_TASK) {
            $task_detail['content'] = EquipSymptom::getSymptomNameStr($task_detail['content']);
        }elseif($task_detail['task_type'] == EquipTask::EXTRA_TASK){
            $title = '附件任务';
            $task_detail['content'] = EquipExtra::getExtraNameByID($task_detail['content']);
        }
        // 获取该用户所在分公司
        $orgId = WxMember::getOrgId($this->userinfo['userid']);

        return $this->render('detail', [
            'title'       => $title,
            'task_detail' => $task_detail,
            'orgId'       => $orgId
        ]);
    }

    /**
     * 维修数据保存
     * @return [type] [description]
     */
    public function actionTaskSave()
    {
        $data = Yii::$app->request->post();

        $transaction    = Yii::$app->db->beginTransaction();
        $equipTaskModel = EquipTask::findOne($data['id']);
        if (!$equipTaskModel || trim($equipTaskModel->assign_userid) != trim($this->userinfo['userid'])) {
            return $this->render('/site/error', ['message' => '该数据不存在或者您没有此操作权限']);
        }
        if ($equipTaskModel->process_result != EquipTask::UNTREATED) {
            return $this->redirect(['index']);
        }
        // 检查操作权限
        if ($equipTaskModel->task_type != 1) {
            Yii::$app->getSession()->setFlash('error', '您没有此操作权限');
            return $this->redirect(['index']);
        }

        // 提交的维修数据
        $equipTaskModel->end_repair_time         = time();
        $equipTaskModel->malfunction_reason      = implode(',', $data['malfunction_reason']);
        $equipTaskModel->malfunction_description = $data['malfunction_description'];
        $equipTaskModel->process_method          = $data['process_method'];
        $equipTaskModel->process_result          = $data['process_result'];

        // 任务结束时的经纬度
        $equipTaskModel->end_longitude = isset($data['end_longitude']) ? $data['end_longitude'] : '';
        $equipTaskModel->end_latitude  = isset($data['end_latitude']) ? $data['end_latitude'] : '';
        $equipTaskModel->end_address   = isset($data['end_address']) ? $data['end_address'] : '';

        // 获取该用户所在分公司
        $orgId = WxMember::getOrgId($this->userinfo['userid']);
        $title = '维修任务';

        // 维修用到的配件数据
        if (isset($data['fitting']) && !empty($data['fitting'])) {
            $equipTaskModel->is_use_fitting = 2;
            //添加配件
            $taskFittingRes = Yii::$app->db->createCommand()->batchInsert('equip_task_fitting', ['fitting_name', 'fitting_model', 'factory_number', 'num', 'remark', 'task_id'], $data['fitting'])->execute();

            if ($taskFittingRes == false) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '备件添加失败');
                return $this->render('detail', [
                    'task_detail' => $equipTaskModel,
                    'orgId'       => $orgId,
                    'title'       => $title
                ]);
            }
        } else {
            $equipTaskModel->is_use_fitting = 1;
        }

        // 保存设备任务数据
        if (!$equipTaskModel->save()) {
            $transaction->rollback();
            Yii::$app->getSession()->setFlash('error', '操作失败');
            return $this->render('detail', [
                'task_detail' => $equipTaskModel,
                'orgId'       => $orgId,
                'title'       => $title
            ]);
        }

        //添加水单
        $waterResult = FrontendDistributionTask::createDistributionWater(Yii::$app->request->post(), $equipTaskModel);

        if ($waterResult === false) {
            $transaction->rollback();
            Yii::$app->getSession()->setFlash('error', '添加水单失败');
            return $this->render('detail', [
                'task_detail' => $equipTaskModel,
                'orgId'       => $orgId,
                'title'       => $title
            ]);
        }

        //维修结果发送消息
        //发送消息通知主管和经理
        $username = WxMember::getMemberDetail("name", array('userid' => $equipTaskModel->assign_userid))['name'];
        $url = 'equip-task/repair-record-detail?id=' . $data['id'];
        $orgId = WxMember::getOrgId($equipTaskModel->assign_userid);
        $roles = ArrayHelper::getColumn(WxMember::getRoleByOrg($orgId),'userid');
        $userList = implode('|',$roles);
        $taskRet = SendNotice::sendWxNotice($userList, $url, $username . '的' . EquipTask::$task_type[$equipTaskModel->task_type] .'结果:'. EquipTask::$repair_result[$equipTaskModel->process_result]. '，请注意查看。', Yii::$app->params['distribution_agentid']);
        if (!$taskRet) {
            Yii::$app->getSession()->setFlash("error", "信息发送失败");
        }

        // 投放验收任务验收失败后重新生成的维修任务处理
        if ($equipTaskModel->relevant_id && $equipTaskModel->process_result == 2) {
            $equipRes = Equipments::editEquipStatus($equipTaskModel->equip_id);
            if ($equipRes === false) {
                $transaction->rollback();
                Yii::$app->getSession()->setFlash('error', '更新投放结果失败');
                return $this->render('detail', [
                    'task_detail' => $equipTaskModel,
                    'orgId'       => $orgId,
                    'title'       => $title
                ]);
            }
        }
        $transaction->commit();
        return $this->redirect(['index']);
    }

    /**
     * 配送附件保存
     * @return string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionExtraTaskSave()
    {
        $data = Yii::$app->request->post();
        $transaction    = Yii::$app->db->beginTransaction();
        $equipTaskModel = EquipTask::findOne($data['id']);
        if (!$equipTaskModel || trim($equipTaskModel->assign_userid) != trim($this->userinfo['userid'])) {
            return $this->render('/site/error', ['message' => '该数据不存在或者您没有此操作权限']);
        }
        if ($equipTaskModel->process_result != EquipTask::UNTREATED) {
            return $this->redirect(['index']);
        }
        // 检查操作权限
        if ($equipTaskModel->task_type != EquipTask::EXTRA_TASK) {
            Yii::$app->getSession()->setFlash('error', '您没有此操作权限');
            return $this->redirect(['index']);
        }

        // 提交数据
        $equipTaskModel->end_repair_time         = time();
        $equipTaskModel->process_result          = $data['process_result'];
        $equipTaskModel->malfunction_description = Tools::filterEmoji($data['remark'],'?');
        //故障描述存前台备注

        // 任务结束时的经纬度
        $equipTaskModel->end_longitude = isset($data['end_longitude']) ? $data['end_longitude'] : '';
        $equipTaskModel->end_latitude  = isset($data['end_latitude']) ? $data['end_latitude'] : '';
        $equipTaskModel->end_address   = isset($data['end_address']) ? $data['end_address'] : '';


         $equipTaskModel->is_use_fitting = 1;

        $orgId = WxMember::getOrgId($this->userinfo['userid']);
        $title = '附件任务';

        // 保存设备任务数据
        if (!$equipTaskModel->save()) {
            $transaction->rollback();
            Yii::$app->getSession()->setFlash('error', '操作失败');
            return $this->render('detail', [
                'task_detail' => $equipTaskModel,
                'orgId'       => $orgId,
                'title'       => $title
            ]);
        }
        //配送附件已完成和回收附件的情况插入附件记录
        if($equipTaskModel->process_result == EquipTask::RESULT_SUCCESS || $equipTaskModel->process_result == EquipTask::RESULT_RECYCLE){
            $extraList = explode(',', $equipTaskModel->content);
            foreach ($extraList as $item) {
                $createUser = WxMember::getNameOne($equipTaskModel->assign_userid);
                $result = EquipExtraLog::addExtraRecord(['build_id' => $equipTaskModel->build_id, 'equip_id' => $equipTaskModel->equip_id, 'equip_extra_id' => $item, 'create_user' => $createUser, 'create_time' => $equipTaskModel->start_repair_time,'process_result' => $equipTaskModel->process_result]);
                if (!$result) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', '操作附件任务记录失败');
                    return $this->render('detail', [
                        'task_detail' => $equipTaskModel,
                        'orgId'       => $orgId,
                        'title'       => $title
                    ]);
                }
            }
        }

        $username = WxMember::getMemberDetail("name", array('userid' => $equipTaskModel->assign_userid))['name'];
        $url = 'equip-task/extra-record-detail?id=' . $data['id'];
        $orgId = WxMember::getOrgId($equipTaskModel->assign_userid);
        $parentId = WxMember::getFiled('parent_id',['userid' => $equipTaskModel->assign_userid]);
        $userList = $parentId ? $parentId.'|'.$equipTaskModel->assign_userid : $equipTaskModel->assign_userid;

        // 发送消息 参数：人员
        $taskRet = SendNotice::sendWxNotice($userList, $url, $username . '的附件任务,'. EquipTask::$extra_result[$equipTaskModel->process_result] .'，请注意查看。', Yii::$app->params['equip_agentid']);
        if (!$taskRet) {
            Yii::$app->getSession()->setFlash("error", "信息发送失败");
            $transaction->rollBack();
            return $this->redirect($_SERVER['HTTP_REFERER']);
        }

        $waterResult = FrontendDistributionTask::createDistributionWater($data, $equipTaskModel);
        if (!$waterResult) {
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('error', '添加附件任务水单失败');
            return $this->render('detail', [
                'task_detail' => $equipTaskModel,
                'orgId'       => $orgId,
                'title'       => $title
            ]);
        }
        $transaction->commit();
        return $this->redirect(['index','task_type' => $equipTaskModel->task_type, 'process_result' => $equipTaskModel->process_result]);
    }


    /**
     * 灯箱验收数据保存
     * @return [type] [description]
     */
    public function actionAcceptanceTaskSave()
    {
        $data = Yii::$app->request->post();
        //开启事务
        $transaction    = Yii::$app->db->beginTransaction();
        $equipTaskModel = EquipTask::findOne($data['id']);
        if (!$equipTaskModel || trim($equipTaskModel->assign_userid) != trim($this->userinfo['userid'])) {
            return $this->render('/site/error', ['message' => '该数据不存在或者您没有此操作权限']);
        }

        if ($equipTaskModel->process_result != EquipTask::UNTREATED) {
            return $this->redirect(['index']);
        }

        //权限验证
        if ($equipTaskModel->assign_userid != $this->userinfo['userid'] || $equipTaskModel->task_type != 3) {
            Yii::$app->getSession()->setFlash('error', '您没有此操作权限');
            return $this->redirect(['index', 'task_type' => 3]);
        }

        //获取灯箱验收选项
        $light_box_debug = EquipLightBoxDebug::getLightBoxDebugArrFromEquipId($equipTaskModel->equip_id);
        // 初始化灯箱验收选项及对应的结果变量
        $acceptance_content_arr = [];
        if (isset($data['checkbox']) && (count($light_box_debug) == count($data['checkbox']))) {
            $equipTaskModel->process_result = 2; //处理结果处理成功
            // 组装灯箱验收选项及对应的结果
            foreach ($data['checkbox'] as $key => $value) {
                $acceptance_content_arr[$value] = 1;
            }
        } else {
            $equipTaskModel->process_result = 3; //处理结果失败
            // 组装灯箱验收选项及对应的结果
            foreach ($light_box_debug as $v) {
                if (isset($data['checkbox']) && !empty($data['checkbox']) && in_array($v['Id'], $data['checkbox'])) {
                    $acceptance_content_arr[$v['Id']] = 1;
                } else {
                    $acceptance_content_arr[$v['Id']] = 2;
                }
            }
        }

        //更新任务表
        $equipTaskModel->end_repair_time = time(); //结束打卡时间
        $orgId = WxMember::getOrgId($this->userinfo['userid']);
        $title = '灯箱验收任务';

        // 任务结束时的经纬度
        $equipTaskModel->end_longitude = isset($data['end_longitude']) ? $data['end_longitude'] : '';
        $equipTaskModel->end_latitude  = isset($data['end_latitude']) ? $data['end_latitude'] : '';
        $equipTaskModel->end_address   = isset($data['end_address']) ? $data['end_address'] : '';
        if (!$equipTaskModel->save()) {
            $transaction->rollback();
            Yii::$app->getSession()->setFlash('error', '更新投放结果失败');
            return $this->render('detail', [
                'task_detail' => $equipTaskModel,
                'orgId'       => $orgId,
                'title'       => $title
            ]);
        }

        if ($data['light_box_repair_id']) {
            //执行灯箱验收任务时更新灯箱维修结果
            $lightBoxRepairRes = $this->updateLightBoxRepairResult($data['light_box_repair_id'], $equipTaskModel->process_result);
            if (!$lightBoxRepairRes) {
                $transaction->rollback();
                Yii::$app->getSession()->setFlash('error', '更新投放结果失败');
                return $this->render('detail', [
                    'task_detail' => $equipTaskModel,
                    'orgId'       => $orgId,
                    'title'       => $title
                ]);
            }
        }

        //添加灯箱验收数据
        $data['acceptance_content'] = json_encode($acceptance_content_arr);
        $acceptanceRes              = $this->addLightBoxAcceptance($data);
        if (!$acceptanceRes) {
            $transaction->rollback();
            Yii::$app->getSession()->setFlash('error', '更新投放结果失败');
            return $this->render('detail', [
                'task_detail' => $equipTaskModel,
                'orgId'       => $orgId,
                'title'       => $title
            ]);
        }

        //添加水单
        $waterResult = FrontendDistributionTask::createDistributionWater($data, $equipTaskModel);
        if(!$waterResult){
            $transaction->rollback();
            Yii::$app->getSession()->setFlash('error', '更新投放结果失败');
            return $this->render('detail', [
                'task_detail' => $equipTaskModel,
                'orgId'       => $orgId,
                'title'       => $title
            ]);
        }

        $transaction->commit();
        return $this->redirect(['index', 'task_type' => 3]);
    }

    /**
     * 添加灯箱验收数据
     * @param array $data 要添加的数据
     */
    public function addLightBoxAcceptance($data = [])
    {
        if (!$data) {
            return false;
        }

        $accetanceResultModel                     = new EquipLightBoxAcceptanceTaskResult();
        $accetanceResultModel->task_id            = $data['id'];
        $accetanceResultModel->breaker_type       = $data['breaker_type'];
        $accetanceResultModel->ammeter_type       = $data['ammeter_type'];
        $accetanceResultModel->ammeter_number     = $data['ammeter_number'];
        $accetanceResultModel->acceptance_content = $data['acceptance_content'];
        //添加灯箱验收数据
        if (!$accetanceResultModel->save()) {
            return false;
        }

        return true;
    }

    /**
     * 执行灯箱验收任务时更新灯箱维修结果
     * @param  string $light_box_repair_id   灯箱维修id
     * @param  string $accetance_result 验收结果
     * @return bool
     */
    public function updateLightBoxRepairResult($light_box_repair_id = '', $accetance_result = '')
    {
        if (!$light_box_repair_id || ($accetance_result != 2 && $accetance_result != 3)) {
            return true;
        }

        $lightBoxRepairmodel               = EquipLightBoxRepair::findOne($light_box_repair_id);
        $lightBoxRepairmodel->process_time = time();

        if ($accetance_result == 2) //验收通过
        {
            $lightBoxRepairmodel->process_result = 9;
        } else //验收没有通过
        {
            $lightBoxRepairmodel->process_result = 5;
        }

        if ($lightBoxRepairmodel->save()) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * 打卡时间
     * @return [type] [description]
     */
    public function actionUpdateTaskReciveTime()
    {
        $id             = Yii::$app->request->get('id');
        $type           = Yii::$app->request->get('type');
        $startLongitude = Yii::$app->request->get('start_longitude', '');
        $startLatitude  = Yii::$app->request->get('start_latitude', '');
        $startAddress   = Yii::$app->request->get('start_address', '');
        $model          = EquipTask::findOne($id);
        if (!$model || trim($model->assign_userid) != trim($this->userinfo['userid'])) {
            return $this->render('/site/error', ['message' => '该数据不存在或者您没有此操作权限']);
        }
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        //更新接报时间
        if ($model->repair_id && $model->assign_userid) {
            $repair_model              = EquipRepair::findOne($model->repair_id);
            $repair_model->recive_time = time();
            if (!$repair_model->save()) {
                Yii::$app->getSession()->setFlash('error', '更新接报时间失败');
                return $this->redirect(['detail?id=' . $id]);
            }
        }

        if ($type == 1 && !$model->recive_time) {
            // 接受时间
            $model->recive_time = time();
        } else if ($type == 2 && !$model->start_repair_time) {
            // 打卡时间
            $model->start_repair_time = time();
            // 任务结束时的经纬度
            $model->start_longitude = $startLongitude;
            $model->start_latitude  = $startLatitude;
            $model->start_address   = $startAddress;
        }

        if ($model->save() === false) {
            $transaction->rollBack();
            Yii::$app->getSession->setFlash('error', '操作失败');
            return $this->redirect(['detail?id=' . $id]);
        } else {
            $transaction->commit();
            return $this->redirect(['detail?id=' . $id]);
        }
    }
    /**
     * 维修详情
     * @return [type] [description]
     */
    public function actionRepairRecordDetail()
    {
        $id          = Yii::$app->request->get('id');
        $task_detail = EquipTask::getEquipTaskDetail('*', ['equip_task.id' => $id, 'task_type' => EquipTask::MAINTENANCE_TASK]);
        if(isset($task_detail['build_id'])){
            $orgId = isset($task_detail['build_id']) ? Building::getField('org_id', ['id' => $task_detail['build_id']]) : 0;
            //不是指定的维修人员或者不是该公司的配送主管或经理则提示无权限
            $isOrganizationManager = WxMember::isOrganizationManager($orgId,trim($this->userinfo['userid']));
        }
        if (!$task_detail || (($task_detail['assign_userid'] != $this->userinfo['userid']) && !$isOrganizationManager)) {
            return $this->render('/site/error', ['message' => '该记录不存在或者您没有权限查看该记录']);
        }
        $taskFittingList = [];
        if ($task_detail['process_result'] != 1) {
            $taskFittingList = EquipTaskFitting::find()->where(['task_id' => $id, 'task_type' => 0])->asArray()->all();
        }
        return $this->render('repair-record-detail', [
            'task_detail'       => $task_detail,
            'task_fitting_List' => $taskFittingList,
        ]);
    }

    /**
     * 配送详情
     * @return string
     */
    public function actionExtraRecordDetail(){
        $id          = Yii::$app->request->get('id');
        $task_detail = EquipTask::getEquipTaskDetail('*', ['equip_task.id' => $id, 'task_type' => EquipTask::EXTRA_TASK]);
        $parentId = '';
        if(isset($task_detail['build_id'])){
            $orgId = isset($task_detail['build_id']) ? Building::getField('org_id', ['id' => $task_detail['build_id']]) : 0;
            //不是指定的维修人员或者不是该公司的配送主管或经理则提示无权限
            $isOrganizationManager = WxMember::isOrganizationManager($orgId,trim($this->userinfo['userid']));
            //判断是否是任务负责人的直属领导
            $parentId = WxMember::getFiled('parent_id',['userid' => $task_detail['assign_userid']]);
        }
        if (!$task_detail || (($task_detail['assign_userid'] != $this->userinfo['userid']) && !$isOrganizationManager && ($this->userinfo['userid'] != $parentId))) {
            return $this->render('/site/error', ['message' => '该记录不存在或者您没有权限查看该记录']);
        }
        return $this->render('extra-record-detail', [
            'task_detail'       => $task_detail,
        ]);
    }

    /**
     * 灯箱验收详情
     * @return [type] [description]
     */
    public function actionAcceptanceRecordDetail()
    {
        $id          = Yii::$app->request->get('id');
        $task_detail = EquipTask::getEquipTaskDetail('*', ['equip_task.id' => $id, 'task_type' => 3]);
        if(isset($task_detail['build_id'])){
            $orgId = isset($task_detail['build_id']) ? Building::getField('org_id', ['id' => $task_detail['build_id']]) : 0;
            //不是指定的维修人员或者不是该公司的配送主管或经理则提示无权限
            $isOrganizationManager = WxMember::isOrganizationManager($orgId,trim($this->userinfo['userid']));
        }
        if (!$task_detail || (($task_detail['assign_userid'] != $this->userinfo['userid']) && !$isOrganizationManager)) {
            return $this->render('/site/error', ['message' => '该记录不存在或者您没有权限查看该记录']);
        }
        //获取灯箱选项列表
        $light_box_debug = EquipLightBoxAcceptanceTaskResult::getAcceptanceContent($task_detail['process_result'], $id, $task_detail['equip_id']);
        return $this->render('acceptance-record-detail', [
            'task_detail'     => $task_detail,
            'light_box_debug' => $light_box_debug,
        ]);
    }

    /**
     *  投放单消息发送的链接（分配此设备任务的人员）
     *  @param $id;
     **/
    public function actionAssignedPersonnel()
    {
        $id    = isset(Yii::$app->request->get()['id']) ? Yii::$app->request->get()['id'] : '0';
        $model = EquipTask::findOne($id);
        //判断手机端链接权限（设备 经理、主管；配送 经理、主管）
        $wxMemberModel  = WxMember::findOne($this->userinfo['userid']);
        $permissionSign = false;
        if ($model) {
            // 设置验证场景
            $model->scenario = 'change';
            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();
            if ($wxMemberModel && ($wxMemberModel->position == WxMember::EQUIP_MANAGER || $wxMemberModel->position == WxMember::EQUIP_RESPONSIBLE || $wxMemberModel->position == WxMember::DISTRIBUTION_MANAGER || $wxMemberModel->position == WxMember::DISTRIBUTION_RESPONSIBLE)) {
                $permissionSign = true;
            }
            $param = Yii::$app->request->post();
            $equipModel = Equipments::findOne($model->equip_id);
            $equipType  = isset($equipModel->equipTypeModel->model) ? $equipModel->equipTypeModel->model : '';
            if (empty($equipType) && $model->relevant_id > 0) {
                $equipTypeModel   = ScmEquipType::findOne(['id' => $model->equipDelivery->equip_type_id]);
                $equipType = $equipTypeModel->model;
            }

            $title =  isset(EquipTask::$task_type[$model->task_type]) ? '分配' .EquipTask::$task_type[$model->task_type] : '';
            if ($param) {
                // 验收人
                $model->assign_userid = $param['EquipTask']['assign_userid'];
                // 是否有验收人
                $model->is_userid = 1;
                // 获取操作人信息
                $realname = Manager::getField('realname', ['userid' => $this->userinfo['userid']]);
                if ($realname) {
                    $model->create_user = $realname;
                }

                if (!$model->save()) {
                    Yii::$app->getSession()->setFlash('error', '添加指定人失败');
                    $transaction->rollBack();
                    return $this->redirect($_SERVER['HTTP_REFERER']);
                }
                $username = Manager::getField('realname', ['userid' => $this->userinfo['userid']]);
                //维系任务
                $url = 'equip-delivery/put-to-do-index';
                if($model->task_type == EquipTask::MAINTENANCE_TASK){
                    $url = 'equip-task/index?process_result=1';
                }elseif($model->task_type == EquipTask::EXTRA_TASK){
                    $url = 'equip-task/index?process_result=1&task_type=4';
                }

                // 发送消息 参数：人员
                $taskRet = SendNotice::sendWxNotice($model->assign_userid, $url, $username . '给您分配了一条新的'. EquipTask::$task_type[$model->task_type] .'，请注意查看。', Yii::$app->params['equip_agentid']);
                if (!$taskRet) {
                    Yii::$app->getSession()->setFlash("error", "信息发送失败");
                    $transaction->rollBack();
                    return $this->redirect($_SERVER['HTTP_REFERER']);
                }
                //事务通过
                $transaction->commit();
                return $this->render('assigned-personnel', [
                    'title'          => $title,
                    'model'          => $model,
                    'equipType'      => $equipType,
                    'permissionSign' => $permissionSign,
                ]);
            }

            return $this->render('assigned-personnel', [
                'title'          => $title,
                'model'          => $model,
                'equipType'      => $equipType,
                'permissionSign' => $permissionSign,
            ]);
        } else {
            return $this->render('/site/error', ['message' => '该数据不存在或已删除']);
        }

    }

}
