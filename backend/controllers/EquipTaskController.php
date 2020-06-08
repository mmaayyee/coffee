<?php

namespace backend\controllers;

use backend\models\DistributionTask;
use backend\models\DistributionUser;
use backend\models\DistributionUserSchedule;
use backend\models\EquipDelivery;
use backend\models\EquipMalfunction;
use backend\models\EquipRepair;
use backend\models\EquipTaskSearch;
use backend\models\Manager;
use backend\models\ManagerLog;
use backend\models\ScmEquipType;
use common\models\Building;
use common\models\EquipExtra;
use common\models\EquipLightBoxAcceptanceTaskResult;
use common\models\EquipLightBoxRepair;
use common\models\EquipTask;
use common\models\SendNotice;
use common\models\WxMember;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EquipTaskController implements the CRUD actions for EquipTask model.
 */
class EquipTaskController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post', 'get'],
                ],
            ],
        ];
    }

    /**
     * Lists all EquipTask models.
     * @return mixed
     */
    public function actionIndex()
    {
        // if (!Yii::$app->user->can('设备任务列表')) {
        //     return $this->redirect(['site/login']);
        // }

        $searchModel = new EquipTaskSearch();

        $params       = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        if (isset($params['EquipTaskSearch']['type'])) {
            if ($params['EquipTaskSearch']['type'] == EquipTask::MAINTENANCE_TASK) {
                $view = 'repair_record';
            } else if ($params['EquipTaskSearch']['type'] == EquipTask::LIGHTBOX_ACCEPTANCE_TASK) {
                $view = 'light_box_acceptance_record';
            } else if ($params['EquipTaskSearch']['type'] == EquipTask::EXTRA_TASK) {
                $view = 'extra_record';
            }

        } else {
            $view = 'index';
        }
        return $this->render($view, [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EquipTask model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看设备任务')) {
            return $this->redirect(['site/login']);
        }

        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Displays a single EquipTask model.
     * @param integer $id
     * @return mixed
     */
    public function actionDetail()
    {
        if (!Yii::$app->user->can('查看设备任务')) {
            return $this->redirect(['site/login']);
        }

        $id = Yii::$app->request->get('id');
        if (!$id) {
            return json_encode(['error' => 1, 'msg' => '请选择要查看的任务', 'res' => '']);
        }
        $detail = [];
        //获取任务详情
        $taskDetail = EquipTask::taskDetailObj(['id' => $id]);
        if ($taskDetail) {
            //获取设备类型
            $detail['equiptype'] = isset($taskDetail->equip->equipTypeModel->model) ? $taskDetail->equip->equipTypeModel->model : '';
            // 设备编号
            $detail['equip_code'] = isset($taskDetail->equip->equip_code) ? $taskDetail->equip->equip_code : '';
            // 开始维修时间
            $detail['start_repair_time'] = date('Y-m-d H:i:s', $taskDetail['start_repair_time']);
            // 结束维修时间
            $detail['end_repair_time'] = date('Y-m-d H:i:s', $taskDetail['end_repair_time']);
            // 处理结果
            $detail['process_result'] = EquipTask::$repair_result[$taskDetail['process_result']];
            // 维修人
            $detail['assign_userid'] = WxMember::getNameOne($taskDetail['assign_userid']);
            // 故障描述
            $detail['malfunction_description'] = $taskDetail->malfunction_description;
            // 故障原因
            $detail['malfunction_reason'] = EquipMalfunction::getMalfunctionReasonName($taskDetail['malfunction_reason']);
            // 处理方法
            $detail['process_method']   = $taskDetail->process_method;
            $detail['equipTaskFitting'] = [];
            // 备件信息
            if ($taskDetail->equipTaskFitting) {
                foreach ($taskDetail->equipTaskFitting as $key => $fittArr) {
                    if ($fittArr->task_type == 0) {
                        $detail['equipTaskFitting'][$key]['fitting_name']   = $fittArr->fitting_name;
                        $detail['equipTaskFitting'][$key]['fitting_model']  = $fittArr->fitting_model;
                        $detail['equipTaskFitting'][$key]['factory_number'] = $fittArr->factory_number;
                        $detail['equipTaskFitting'][$key]['num']            = $fittArr->num;
                        $detail['equipTaskFitting'][$key]['remark']         = $fittArr->remark;
                    }
                }
            }
        }
        // echo "<pre/>";print_r(json_encode($detail));die;

        return json_encode(['error' => 0, 'msg' => '', 'res' => $detail]);
    }

    /**
     * 设备附件详情展示
     * @param integer $id
     * @return mixed
     */
    public function actionExtraDetail()
    {
        if (!Yii::$app->user->can('查看设备任务')) {
            return $this->redirect(['site/login']);
        }

        $id = Yii::$app->request->get('id');
        if (!$id) {
            return json_encode(['error' => 1, 'msg' => '请选择要查看的任务', 'res' => '']);
        }
        $detail = [];
        //获取任务详情
        $taskDetail = EquipTask::taskDetailObj(['id' => $id]);
        if ($taskDetail) {
            //获取设备类型
            $detail['equiptype'] = isset($taskDetail->equip->equipTypeModel->model) ? $taskDetail->equip->equipTypeModel->model : '';
            // 设备编号
            $detail['equip_code'] = isset($taskDetail->equip->equip_code) ? $taskDetail->equip->equip_code : '';
            // 开始配送时间
            $detail['start_repair_time'] = date('Y-m-d H:i:s', $taskDetail['start_repair_time']);
            // 结束配送时间
            $detail['end_repair_time'] = date('Y-m-d H:i:s', $taskDetail['end_repair_time']);
            // 处理结果
            $detail['process_result'] = EquipTask::$extra_result[$taskDetail['process_result']];
            // 配送人
            $detail['assign_userid'] = WxMember::getNameOne($taskDetail['assign_userid']);
            //  配送人备注
            $detail['malfunction_description'] = $taskDetail->malfunction_description;
            //配送内容
            $detail['content'] = str_replace('<br/>', ',', EquipExtra::getExtraNameByID($taskDetail->content));
        }
        // echo "<pre/>";print_r(json_encode($detail));die;

        return json_encode(['error' => 0, 'msg' => '', 'res' => $detail]);
    }

    /**
     * 灯箱验收详情
     * @return [type] [description]
     */
    public function actionAcceptanceRecord()
    {
        $processResult = Yii::$app->request->get('process_result');
        $id            = Yii::$app->request->get('id');
        $equipId       = Yii::$app->request->get('equipId');
        $detail        = EquipLightBoxAcceptanceTaskResult::getAcceptanceContent($processResult, $id, $equipId);
        return json_encode(['error' => 0, 'msg' => '', 'res' => $detail]);
    }

    /**
     * 添加维修任务
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加设备任务')) {
            return $this->redirect(['site/login']);
        }
        $model = new EquipTask();
        $data  = Yii::$app->request->post('EquipTask');
        if ($data) {
            $csrf = Yii::$app->request->post('_csrf');
            if ($csrf == Yii::$app->session->get("_csrf")) {
                return $this->redirect(['index']);
            }
            $data['content']     = !$data['content'] ? '' : implode(',', $data['content']);
            $data['create_time'] = $data['update_time'] = time();
            $data['create_user'] = Yii::$app->user->identity->realname;
            $data['equip_id']    = $data['equip_id'] ? $data['equip_id'] : 0;
            $data['task_type']   = EquipTask::MAINTENANCE_TASK;
            if ($data['assign_userid']) {
                $data['is_userid'] = 1;
            }

            $transaction = Yii::$app->db->beginTransaction();
            if (!$model->load(['EquipTask' => $data]) || $model->save() === false) {
                Yii::$app->getSession()->setFlash('error', '添加维修任务失败');
                return $this->render('create', [
                    'model' => $model,
                    '_form' => '_form',
                ]);
            }
            Yii::$app->session->set("_csrf", $csrf);
            //添加操作日志
            $managerLogres = ManagerLog::saveLog(Yii::$app->user->id, "设备任务管理", ManagerLog::CREATE, '任务id为：' . $model->id);
            if (!$managerLogres) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '操作日志添加失败');
                return $this->render('create', [
                    'model' => $model,
                    '_form' => '_form',
                ]);
            }

            //发送通知
            if ($model->assign_userid) {
                $username = Manager::getField('realname', ['id' => Yii::$app->user->id]);
                SendNotice::sendWxNotice($model->assign_userid, "equip-task/index?task_type=" . EquipTask::MAINTENANCE_TASK, $username . "给您分配了一条新的维修任务，请注意查收", Yii::$app->params['equip_agentid']);
            }

            $transaction->commit();
            return $this->redirect(['view', 'id' => $model->id]);

        } else {
            $model->build_id = Yii::$app->request->get('build_id');
            return $this->render('create', [
                'model' => $model,
                '_form' => '_form',
            ]);
        }
    }

    /**
     * 添加设备附件临时任务
     * @author wangxl
     * @return string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreateExtraTask()
    {
        if (!Yii::$app->user->can('添加设备任务')) {
            return $this->redirect(['site/login']);
        }
        $data  = Yii::$app->request->post('EquipTask');
        $model = new EquipTask();
        if ($data) {

            $data['content']     = !$data['content'] ? '' : implode(',', $data['content']);
            $data['create_time'] = $data['update_time'] = time();
            $data['create_user'] = Yii::$app->user->identity->realname;
            $data['equip_id']    = $data['equip_id'] ? $data['equip_id'] : 0;
            $data['task_type']   = EquipTask::EXTRA_TASK;
            if ($data['assign_userid']) {
                $data['is_userid'] = 1;
            }

            if ($model->load(['EquipTask' => $data]) && $model->save()) {

                //发送通知
                if ($model->assign_userid) {
                    $username = Manager::getField('realname', ['id' => Yii::$app->user->id]);
                    SendNotice::sendWxNotice($model->assign_userid, "equip-task/index?task_type=" . EquipTask::EXTRA_TASK, $username . "给您分配了一条新的附件任务，请注意查收", Yii::$app->params['equip_agentid']);
                }

                //添加操作日志
                $managerLogres = ManagerLog::saveLog(Yii::$app->user->id, "设备任务管理-附件任务", ManagerLog::CREATE, '任务id为：' . $model->id);
                if (!$managerLogres) {
                    Yii::$app->getSession()->setFlash('error', '操作日志添加失败');
                    return $this->render('create', [
                        'model' => $model,
                        '_form' => 'extra_task_form',
                    ]);
                }
                return $this->redirect(['view', 'id' => $model->id]);

            } else {
                Yii::$app->getSession()->setFlash('error', '添加附件任务失败');
                return $this->render('create', [
                    'model' => $model,
                    '_form' => 'extra_task_form',
                ]);
            }

        } else {
            $model->build_id = Yii::$app->request->get('build_id');
            return $this->render('create', [
                'model' => $model,
                '_form' => 'extra_task_form',
            ]);
        }

    }

    /**
     * 添加灯箱验收任务
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAddLightBoxAcceptance()
    {
        if (!Yii::$app->user->can('添加设备任务')) {
            return $this->redirect(['site/login']);
        }

        $model = new EquipTask();
        $data  = Yii::$app->request->post('EquipTask');
        if ($data) {
            $transaction = Yii::$app->db->beginTransaction();

            $data['create_time'] = time();
            $data['update_time'] = time();
            $data['create_user'] = Yii::$app->user->identity->realname;
            $data['task_type']   = EquipTask::LIGHTBOX_ACCEPTANCE_TASK;
            if ($data['assign_userid']) {
                $data['is_userid'] = 1;
            }
            //添加灯箱验收任务
            if (!$model->load(['EquipTask' => $data]) || $model->save() === false) {
                Yii::$app->getSession()->setFlash('error', '灯箱验收任务更新失败');
                return $this->render('create', [
                    'model' => $model,
                    '_form' => 'light_box_acceptance_form',
                ]);
            }

            //更新灯箱维修处理结果
            if ($model->light_box_repair_id) {

                $lightBoxRepairModel                 = EquipLightBoxRepair::findOne($model->light_box_repair_id);
                $lightBoxRepairModel->process_result = 4;
                $lightBoxRepairModel->process_time   = time();

                if ($lightBoxRepairModel->save() === false) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', '灯箱维修结果更新失败');
                    return $this->render('create', [
                        'model' => $model,
                        '_form' => 'light_box_acceptance_form',
                    ]);
                }
            }

            //添加操作日志
            $managerLogres = ManagerLog::saveLog(Yii::$app->user->id, "设备任务管理", ManagerLog::CREATE, '任务id为：' . $model->id);
            if (!$managerLogres) {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '操作日志添加失败');
                return $this->render('create', [
                    'model' => $model,
                    '_form' => 'light_box_acceptance_form',
                ]);
            }

            //发送通知
            if ($model->assign_userid) {
                $username = Manager::getField('realname', ['id' => Yii::$app->user->id]);
                //发送通知
                SendNotice::sendWxNotice($model->assign_userid, "equip-task/index?task_type=" . EquipTask::LIGHTBOX_ACCEPTANCE_TASK, $username . "给您分配了一条新的灯箱验收任务，请注意查收", Yii::$app->params['equip_agentid']);
            }

            $transaction->commit();
            return $this->redirect(['view', 'id' => $model->id]);

        } else {
            $model->build_id            = Yii::$app->request->get('build_id');
            $model->equip_id            = Yii::$app->request->get('equip_id');
            $model->light_box_repair_id = Yii::$app->request->get('light_box_repair_id');
            return $this->render('create', [
                'model' => $model,
                '_form' => 'light_box_acceptance_form',
            ]);
        }
    }

    /**
     * Updates an existing EquipTask model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑设备任务')) {
            return $this->redirect(['site/login']);
        }

        $model           = $this->findModel($id);
        $data            = Yii::$app->request->post('EquipTask');
        $oldAssignUserid = $model->assign_userid;
        if ($data) {
            if ($model->task_type == EquipTask::MAINTENANCE_TASK || $model->task_type == EquipTask::EXTRA_TASK) {
                //维修任务
                $data['content'] = !$data['content'] ? '' : implode(',', array_filter($data['content']));
            }

            $transaction = Yii::$app->db->beginTransaction();
            //更新接报时间
            if ($model->repair_id && $data['assign_userid']) {
                $repair_model              = EquipRepair::findOne($model->repair_id);
                $repair_model->recive_time = time();
                if (!$repair_model->save()) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', '更新接报时间失败');
                    return $this->render('update', [
                        'model' => $model,
                    ]);
                }
            }

            $assign_userid       = $model->assign_userid;
            $data['equip_id']    = $data['equip_id'] ? $data['equip_id'] : 0;
            $data['update_time'] = time();
            if ($data['assign_userid']) {
                $data['is_userid'] = 1;
            } else {
                $data['is_userid'] = 2;
            }
            if ($data['assign_userid'] != $assign_userid) {
                $data['recive_time'] = 0;
            }
            $data['create_user'] = Yii::$app->user->identity->realname;
            if ($model->load(['EquipTask' => $data]) && $model->save()) {
                //添加操作日志
                $managerLogres = ManagerLog::saveLog(Yii::$app->user->id, "设备任务管理", ManagerLog::UPDATE, '任务id为：' . $model->id);
                //发通知给指定的设备人员
                if ($model->assign_userid && ($assign_userid != $model->assign_userid)) {
                    if ($model->task_type == EquipTask::TRAFFICKING_TASK) {
                        $send_url = 'equip-delivery/put-to-do-index';
                        $send_msg = '投放验收';
                    } else {
                        $send_url = 'equip-task/index?task_type=' . $model->task_type;
                        $send_msg = $model->task_type == EquipTask::LIGHTBOX_ACCEPTANCE_TASK ? "灯箱验收" : "维修";
                    }
                    if (trim($data['assign_userid']) != trim($oldAssignUserid)) {
                        $username = Manager::getField('realname', ['id' => Yii::$app->user->id]);
                        //发送通知
                        SendNotice::sendWxNotice($model->assign_userid, $send_url, $username . "给您分配了一条" . $send_msg . "任务，请注意查收", Yii::$app->params['equip_agentid']);
                        //发送通知
                        SendNotice::sendWxNotice($oldAssignUserid, $send_url, $username . "给您撤销了一条" . $send_msg . "任务，请查阅", Yii::$app->params['equip_agentid']);
                    }
                }

                $transaction->commit();
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                if ($model->task_type == EquipTask::MAINTENANCE_TASK || $model->task_type == EquipTask::EXTRA_TASK) {
                    $model->content = explode(',', $model->content);
                }
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } else {
            if ($model->task_type == EquipTask::MAINTENANCE_TASK || $model->task_type == EquipTask::EXTRA_TASK) {
                $model->content = explode(',', $model->content);
            }

            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EquipTask model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除设备任务')) {
            return $this->redirect(['site/login']);
        }

        $transaction = Yii::$app->db->beginTransaction();
        $model       = $this->findModel($id);

        $content       = isset(EquipTask::$task_type[$model->task_type]) ? EquipTask::$task_type[$model->task_type] : '';
        $managerLogres = ManagerLog::saveLog(Yii::$app->user->id, "设备任务管理", ManagerLog::DELETE, $content);
        if (!$managerLogres) {
            die('操作日志添加失败');
        }

        if (!$this->findModel($id)->delete()) {
            $transaction->rollBack();
            die('删除失败');
        }
        $transaction->commit();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EquipTask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EquipTask the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipTask::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
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
            $buildId = Yii::$app->request->get('build_id');
            $userid  = Yii::$app->request->get('userid');
            //任务类型
            $taskType = Yii::$app->request->get('taskType');
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
            //如果是换设备投放验收任务设备类型应该是投放中的设备类型
            if ($taskType == EquipTask::TRAFFICKING_TASK) {
                $equipTypeId        = EquipDelivery::getField('equip_type_id', ['delivery_status' => EquipDelivery::TRAFFICKING_IN, 'build_id' => $buildId]);
                $data['equip_type'] = ScmEquipType::getModel($equipTypeId);
            }
            // 指定维修人员
            $data['memberArr'] = $this->getWorkDistributionUserArr($buildId, $userid, 3);
            // 指定配送人员
            $data['deliveryPersonArr']   = $this->getWorkDistributionUserArr($buildId, $userid, 2);
            $data['userSurplusMaterial'] = '';
            //通过build_id判断该楼宇是否存在未完成且已打卡的运维任务
            $distributionTask = DistributionTask::getClockInTask($buildId);
            if (!empty($distributionTask)) {
                //判断该任务中的运维人员存在且是上班状态
                $userStatusRes = DistributionUserSchedule::verifyUserWorkStatus($distributionTask['assign_userid']);
                if ($userStatusRes) {
                    $user                      = WxMember::getUserName($distributionTask['assign_userid']);
                    $html                      = "<option value=\"" . $distributionTask['assign_userid'] . "\">" . $user->name . "</option>";
                    $data['deliveryPersonArr'] = $html;
                    //运维人员手中剩余物料
                    $data['userSurplusMaterial'] = DistributionTask::getSurplusMaterial($distributionTask['assign_userid']);
                }
            }
            return json_encode($data);
        } else {
            throw new NotFoundHttpException('不是ajax请求');
        }
    }

    /**
     * 获取上班可接单的配送员列表
     * @author  zgw
     * @version 2016-09-14
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
     * 任务位置地图定位
     * @author  zgw
     * @version 2016-09-28
     * @param   [type]     $lat [description]
     * @param   [type]     $lng [description]
     * @return  [type]          [description]
     */
    public function actionTaskMap($lat, $lng)
    {
        return $this->render('task_map', ['lat' => $lat, 'lng' => $lng]);
    }

    /**
     * 故障记录列表
     * @param $equip_id
     * @return string
     */
    public function actionTroubleList()
    {
        if (!Yii::$app->user->can('故障记录统计')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new EquipTaskSearch();
        $dataProvider = $searchModel->searchCheckTrouble(Yii::$app->request->queryParams);

        return $this->render('trouble', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }

}
