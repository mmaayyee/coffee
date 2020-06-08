<?php

namespace backend\controllers;

use backend\models\DistributionTask;
use backend\models\EquipAbnormalTask;
use common\models\Equipments;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * DistributionTemporaryTaskController implements the CRUD actions for DistributionTask model.
 */
class DistributionTemporaryTaskController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view', 'index', 'create', 'update', 'delete', 'get-delivery-task', 'get-distribution-content', 'get-check-build-task', 'assign', 'get-user-surplus-material'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Creates a new DistributionTask model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('运维任务管理')) {
            return $this->redirect(['site/login']);
        }
        $model = new DistributionTask();
        if (Yii::$app->request->post()) {
            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();

            $param = Yii::$app->request->post()['DistributionTask'];
            // 首先判断是否为什么类型的任务
            $deliveryTask     = isset($_POST['delivery_task']) ? $_POST['delivery_task'] : [];
            $model->author_id = Yii::$app->user->id;
            $model            = DistributionTask::saveDistributionTask($param, $model, $deliveryTask);
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "运维任务", \backend\models\ManagerLog::CREATE, $model->content);
            // 发送微信消息
            DistributionTask::detailSendWxInfo($model, $param);

            //事务通过
            $transaction->commit();
            return $this->redirect(['distribution-task/index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing DistributionTask model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('运维任务管理')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);
        if (Yii::$app->request->post()) {
            $param = Yii::$app->request->post()['DistributionTask'];
            // 首先判断是否为什么类型的任务
            $delivery_task    = isset($_POST['delivery_task']) ? $_POST['delivery_task'] : [];
            $model->author_id = Yii::$app->user->id;
            //楼宇ID不可更改
            if ($model->build_id != $param['build_id']) {
                Yii::$app->getSession()->setFlash("error", "楼宇不可修改");
                return $this->redirect(["distribution-task/index"]);
            }
            $model = DistributionTask::saveDistributionTask($param, $model, $delivery_task);

            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "运维任务", \backend\models\ManagerLog::UPDATE, $model->content);

            //发送微信通知
            DistributionTask::detailSendWxInfo($model, $param);
            return $this->redirect(['distribution-task/index']);
        } else {
            if ($model->malfunction_task) {
                $model->malfunction_task = explode(",", $model->malfunction_task);
            }
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    /**
     * Deletes an existing EquipAbnormalTask model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionAssign($task_id)
    {
        if (!Yii::$app->user->can('运维任务管理')) {
            return $this->redirect(['site/login']);
        }
        $model        = new DistributionTask();
        $abnormalTask = EquipAbnormalTask::find()->where(['task_id' => $task_id])->one();

        if (Yii::$app->request->post()) {
            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();
            $param       = Yii::$app->request->post()['DistributionTask'];
            // 首先判断是否为什么类型的任务
            $deliveryTask     = isset($_POST['delivery_task']) ? $_POST['delivery_task'] : "";
            $model->author_id = Yii::$app->user->id;

            //下发的异常报警信息
            $param['abnormal_id'] = $abnormalTask->abnormal_id;
            //楼宇ID不可更改
            if ($abnormalTask->build_id != $param['build_id']) {
                Yii::$app->getSession()->setFlash("error", "楼宇不可修改");
                return $this->redirect(["equip-abnormal-task/index"]);
            }
            $model = DistributionTask::saveDistributionTask($param, $model, $deliveryTask);
            if ($model) {
                //更新设备故障列表任务状态为已下发
                EquipAbnormalTask::updateAll(['task_status' => EquipAbnormalTask::LowerHair], ['task_id' => $task_id]);
            }
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "运维任务", \backend\models\ManagerLog::CREATE, $model->content);

            // 发送微信消息
            DistributionTask::detailSendWxInfo($model, $param);
            //事务通过
            $transaction->commit();
            return $this->redirect(['distribution-task/index']);
        } else {
            //通过设备编号获取设备ID
            $equipments                         = Equipments::getEquipBuildDetail('id', ['equip_code' => $abnormalTask->equip_code]);
            $distributionTask                   = new DistributionTask();
            $distributionTask->equip_id         = $equipments->id;
            $distributionTask->build_id         = $abnormalTask->build_id;
            $distributionTask->author_id        = Yii::$app->user->id;
            $distributionTask->create_time      = $abnormalTask->create_time;
            $distributionTask->abnormal         = $abnormalTask->abnormal_id;
            $abnormalTask->repair               = Json::decode($abnormalTask->repair);
            $distributionTask->malfunction_task = $abnormalTask->repair;
            $distributionTask->is_daily_task    = 2;
            return $this->render('create', [
                'model' => $distributionTask,
            ]);
        }

    }

    /**
     * Deletes an existing DistributionTask model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('运维任务管理')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);
        $model->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "运维任务", \backend\models\ManagerLog::DELETE, $model->content);
        return $this->redirect(['index']);
    }

    /**
     * Finds the DistributionTask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return DistributionTask the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DistributionTask::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     *  ajax　传build_id
     *  获取临时配送任务 配送内容
     **/
    public function actionGetDistributionContent()
    {
        if (Yii::$app->request->isAjax) {
            $equipId = Yii::$app->request->get('equipId');
            $taskId  = Yii::$app->request->get('taskId');

            $distributionContent = DistributionTask::distributionContent(['id' => $taskId]);

            $content = DistributionTask::equipMaterialTypeArr($equipId, $distributionContent);
            echo $content;
        }
    }

    /**
     * 查询未分配的任务
     * @throws NotFoundHttpException
     */
    public function actionGetCheckBuildTask()
    {
        if (Yii::$app->request->isAjax) {
            $buildId = Yii::$app->request->get('buildId');
            //查询未分配的维修,配送,维修配送任务
            $task = DistributionTask::find()->select('id')->where(['build_id' => $buildId])
                ->andWhere(['recive_time' => 0])
                ->andWhere(['is_sue' => DistributionTask::NO_FINISH])
                ->andWhere(['task_type' => [DistributionTask::DELIVERY, DistributionTask::SERVICE, DistributionTask::URGENT, DistributionTask::CLEAN, DistributionTask::REFUEL]])
                ->asArray()->one();
            echo json_encode($task);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 获取运维人员手中剩余物料显示信息
     * @author wangxiwen
     * @version 2018-10-30
     * @param array $materialTypeArr 物料分类信息
     * @param array $surplusMaterial 剩余整料
     * @param array $surplusMaterialGram 剩余散料
     * @param string
     */
    public function actionGetUserSurplusMaterial()
    {
        $userid = Yii::$app->request->get('userId');
        echo DistributionTask::getSurplusMaterial($userid);
    }

}
