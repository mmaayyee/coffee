<?php

namespace backend\controllers;

use backend\models\EquipCheckDelivery;
use backend\models\EquipDelivery;
use backend\models\EquipDeliveryRead;
use backend\models\EquipDeliveryReadSearch;
use backend\models\EquipDeliverySearch;
use backend\models\ManagerLog;
use common\models\Building;
use common\models\EquipTask;
use common\models\SendNotice;
use common\models\WxMember;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EquipDeliveryController implements the CRUD actions for EquipDelivery model.
 */
class EquipDeliveryController extends Controller
{
    /**
     * Lists all EquipDelivery models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('销售投放管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new EquipDeliverySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EquipDelivery model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看销售投放')) {
            return $this->redirect(['site/login']);
        }
        $deliveryModel   = EquipDelivery::find()->where(['Id' => $id])->one();
        $delivery_status = $deliveryModel->delivery_status;
        $delivery_id     = $deliveryModel->Id;
        $searchModel     = new EquipDeliveryReadSearch();
        $param           = Yii::$app->request->get('sign');
        //待审批
        if ($delivery_status == 0) {
            $dataProvider = $searchModel->searchSign(Yii::$app->request->queryParams, 0, $delivery_id);
            return $this->render('view', [
                'model'             => $this->findModel($id),
                'delivery_status'   => $delivery_status,
                'sign'              => isset($param) ? $param : '0',
                'deliveryReadModel' => $this->findDeliveryReadModel(0, $delivery_id),
                'dataProvider'      => $dataProvider,
            ]);
        } else {
            $dataProvider = $searchModel->searchSign(Yii::$app->request->queryParams, 1, $delivery_id);
            return $this->render('view', [
                'model'             => $this->findModel($id),
                'sign'              => isset($param) ? $param : '0',
                'deliveryReadModel' => $this->findDeliveryReadModel(1, $delivery_id),
                'dataProvider'      => $dataProvider,
            ]);
        }
    }

    /**
     * Creates a new EquipDelivery model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加销售投放')) {
            return $this->redirect(['site/login']);
        }
        $model = new EquipDelivery();
        $param = Yii::$app->request->post('EquipDelivery');
        if ($param) {
            $csrf      = Yii::$app->request->post('_csrf');
            $cacheCsrf = Yii::$app->cache->get('csrf-' . Yii::$app->user->id);
            if ($cacheCsrf != $csrf) {
                Yii::$app->cache->set('csrf-' . Yii::$app->user->id, $csrf, 5);
            } else {
                return $this->redirect(['index']);
            }
            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();
            if ($model->load(['EquipDelivery' => $param]) && $model->validate()) {
                // 添加投放申请
                $model->delivery_time   = strtotime($param['delivery_time']);
                $model->sales_person    = Yii::$app->user->identity->realname;
                $model->update_time     = time();
                $model->create_time     = time();
                $model->delivery_number = 1;
                if (!$model->save()) {
                    Yii::$app->getSession()->setFlash("error", "投放单添加失败");
                    $transaction->rollBack();
                    return $this->redirect($_SERVER['HTTP_REFERER']);
                }
                // 添加日志数据
                $managerLogRes = ManagerLog::saveLog(Yii::$app->user->id, "销售投放管理", ManagerLog::CREATE, $model->Id);

                //修改楼宇状态为投放中
                $buildModel = Building::findOne($param['build_id']);
                if ($buildModel->build_status == Building::PRE_DELIVERY) {
                    $retBuildModel = EquipDelivery::updateBuildStats($param['build_id'], Building::TRAFFICKING_IN);
                    if (!$retBuildModel) {
                        Yii::$app->getSession()->setFlash("error", "楼宇状态修改失败");
                        $transaction->rollBack();
                        return $this->render('create', ['model' => $model]);
                    }
                };

                // 获取阅读人员
                $memberNameArr = EquipDelivery::getMemberNameArr($param['build_id']);
                //插入相关人员阅读表
                $readRecordRes = EquipDelivery::createDeliveryRead($memberNameArr, $model->Id);
                if (!$readRecordRes) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash("error", "相关人员阅读数据插入失败");
                    return $this->render('create', ['model' => $model]);
                }
                //处理并发送消息
                $ret = EquipDelivery::sendWxInfo($param, '', $model);
                if (!$ret) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash("error", "消息发送失败");
                    return $this->render('create', ['model' => $model]);
                }
            } else {
                Yii::$app->getSession()->setFlash("error", "添加失败");
                return $this->render('create', ['model' => $model]);
            }
            //事务通过
            $transaction->commit();
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EquipDelivery model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑销售投放')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);
        $param = Yii::$app->request->post('EquipDelivery');
        if ($param) {
            // 开启事务
            $transaction       = Yii::$app->db->beginTransaction();
            list($model, $msg) = EquipDelivery::updateDelivery($model, $param, $transaction);
            if ($msg) {
                $model->delivery_time = date("Y-m-d", $model->delivery_time);
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
            ManagerLog::saveLog(Yii::$app->user->id, "销售投放管理", ManagerLog::UPDATE, '销售投放ID：' . $model->Id);
            EquipTask::deleteDeliveryTask($id);
            //事务通过
            $transaction->commit();
            return $this->redirect(['index']);
        } else {
            $model->delivery_time = date("Y-m-d", $model->delivery_time);
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EquipDelivery model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除销售投放')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);
        $model->delete();
        ManagerLog::saveLog(Yii::$app->user->id, "销售投放管理", ManagerLog::DELETE, $model->sales_person);
        return $this->redirect(['index']);
    }

    /**
     * Finds the EquipDelivery model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return EquipDelivery the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipDelivery::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the EquipDelivery model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return EquipDelivery the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findDeliveryReadModel($read_type, $delivery_id)
    {
        $model = EquipDeliveryRead::find()->where(['read_type' => $read_type, 'delivery_id' => $delivery_id])->asArray()->all();
        if ($model !== null) {
            return $model;
        } else {
            return false;
        }
    }

    /**
     * 审核失败
     * @param $id
     * @return index
     */
    public function actionRefuseDes($id)
    {
        $transaction            = Yii::$app->db->beginTransaction();
        $param                  = Yii::$app->request->post()['EquipDelivery'];
        $model                  = EquipCheckDelivery::findOne($id);
        $model->delivery_status = EquipDelivery::TURN_DOWN;
        $model->grounds_refusal = $param['grounds_refusal'];
        $deliveryRes            = $model->save();
        $buildStatusChangeRes   = EquipDelivery::updateBuildStats($model->build_id, Building::PRE_DELIVERY);
        if (!$deliveryRes || !$buildStatusChangeRes) {
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('error', '审核失败');
            return $this->render('update', [
                'model' => $model,
            ]);
        }

        //通知销售投放人
        $user  = WxMember::getUserIdArr(['name' => $model->sales_person]);
        $build = Building::getField('name', ['id' => $model->build_id]);
        $users = empty($user[0]) ? '' : $user[0];
        //通知配送主管
        $distributionCharge = Building::getDistributionManager($model->build_id);
        $ret                = SendNotice::sendWxNotice($users . '|' . $distributionCharge, '', $build . '投放申请被驳回,理由:' . $param['grounds_refusal'], Yii::$app->params['equip_agentid']);

        if (!$ret) {
            Yii::$app->getSession()->setFlash('error', '消息发送失败');
            $transaction->rollBack();
        }

        $transaction->commit();
        return $this->redirect(['index']);
    }

    /**
     * 审核成功
     * @param $id $build_id
     * @return index
     */
    public function actionCheckSuccess($id, $build_id = '')
    {
        $cacheCsrf = Yii::$app->cache->get('csrf-' . Yii::$app->user->id);
        if ($cacheCsrf != $id) {
            Yii::$app->cache->set('csrf-' . Yii::$app->user->id, $id, 5);
        } else {
            return $this->redirect(['index']);
        }
        //判断是否有投放中的
        if ($build_id != '') {
            $deviceExist = EquipCheckDelivery::findOne(['build_id' => $build_id, 'delivery_status' => EquipDelivery::TRAFFICKING_IN]);
            if ($deviceExist != null) {
                Yii::$app->getSession()->setFlash('error', '对不起，存在投放中的投放单，请修改');
                return $this->redirect(['index']);
            }
        }

        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        // 修改投放单状态为投放中
        $equipDeliveryModel                  = EquipCheckDelivery::findOne($id);
        $equipDeliveryModel->delivery_status = EquipDelivery::TRAFFICKING_IN;
        if ($equipDeliveryModel->save() === false) {
            Yii::$app->getSession()->setFlash('error', '对不起，修改投放单状态失败，请检测');
            $transaction->rollBack();
        }
        // 添加投放验收任务
        $retTask = EquipDelivery::createTaskInfo($equipDeliveryModel);
        if ($retTask["ret"] === false) {
            Yii::$app->getSession()->setFlash('error', '添加投放验收任务错误');
            $transaction->rollBack();
        }
        // 插入相关人员阅读表
        $memberNameArr = EquipDelivery::getMemberNameArr($build_id);
        $readRecordRes = EquipDelivery::createDeliveryRead($memberNameArr, $equipDeliveryModel->Id, 1);
        if ($readRecordRes === false) {
            Yii::$app->getSession()->setFlash('error', '相关人员阅读数据插入失败');
            $transaction->rollBack();
        }
        //微信发送消息
        $ret = EquipDelivery::sendWxInfo('', $build_id, $equipDeliveryModel, $retTask['equipTaskId']);
        if (!$ret) {
            Yii::$app->getSession()->setFlash('error', '消息发送失败');
            $transaction->rollBack();
        }
        //事务通过
        $transaction->commit();
        return $this->redirect(['index']);
    }
    /**
     * 获取发送人的信息进行展示
     **/
    public function actionGetSender()
    {
        $buildId    = $_GET['buildId'];
        $buildModel = Building::findOne($buildId);
        $manager    = EquipDelivery::getWxMemberArr($buildModel->org_id);
        echo json_encode($manager);
    }

}
