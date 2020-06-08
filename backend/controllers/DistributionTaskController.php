<?php

namespace backend\controllers;

use backend\models\DistributionTask;
use backend\models\DistributionTaskSearch;
use backend\models\EquipAbnormalTask;
use common\models\Building;
use common\models\Equipments;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * DistributionTaskController implements the CRUD actions for DistributionTask model.
 */
class DistributionTaskController extends Controller
{
    public $enableCsrfValidation = false;
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
                        'actions' => ['view', 'index', 'create', 'update', 'delete', 'statistics', 'abolish', 'change-user'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all DistributionTask models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('运维任务管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new DistributionTaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $buildName    = Building::getPreDeliveryBuildList();

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'buildName'    => $buildName,
        ]);
    }

    /**
     * Displays a single DistributionTask model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('运维任务管理')) {
            return $this->redirect(['site/login']);
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
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
        if ($model->load(Yii::$app->request->post())) {
            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();
            $param       = Yii::$app->request->post('DistributionTask');

            if (isset($_POST["_csrf"])) {
                $csrf = $_POST['_csrf'];
                if ($csrf == Yii::$app->session->get("_csrf_old")) {
                    return $this->redirect(['index']);
                } else {
                    Yii::$app->session->set("_csrf_old", $_POST['_csrf']);
                    $model->author_id = Yii::$app->user->id;
                    //添加紧急任务
                    $model = DistributionTask::createTask($param, $model);
                }
            } else {
                Yii::$app->getSession()->setFlash("error", "对不起，缺失_csrf参数，非法提交，请检测");
                return $this->redirect(['create']);
            }

            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "紧急任务", \backend\models\ManagerLog::CREATE, $model->content);

            // 发送微信消息
            DistributionTask::detailSendWxInfo($model, $param);
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
            // 开启事务
            $transaction      = Yii::$app->db->beginTransaction();
            $param            = Yii::$app->request->post()['DistributionTask'];
            $model->equip_id  = Equipments::find()->where(['build_id' => $model->build_id])->one()->id;
            $model->author_id = Yii::$app->user->id;
            //楼宇ID不可更改
            if ($model->build_id != $param['build_id']) {
                Yii::$app->getSession()->setFlash("error", "楼宇不可修改");
                return $this->redirect(["distribution-task/index"]);
            }
            $model = DistributionTask::createTask($param, $model);
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "紧急运维任务", \backend\models\ManagerLog::UPDATE, $model->content);
            //微信发送消息
            DistributionTask::detailSendWxInfo($model, $param);
            // 事务通过
            $transaction->commit();
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
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
        //删除之前需要将故障任务列表中该楼宇的状态改为未处理
        $taskId = EquipAbnormalTask::find()
            ->andWhere(['build_id' => $model->build_id])
            ->andWhere(['!=', 'task_status', EquipAbnormalTask::COMPLETE])
            ->one();
        if ($taskId) {
            //更新故障任务状态为未处理
            $ret = EquipAbnormalTask::updateAll(
                ['task_status' => EquipAbnormalTask::Untreated],
                ['task_id' => $taskId->task_id]
            );
        }
        $model->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "配送任务", \backend\models\ManagerLog::DELETE, $model->content);
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing DistributionTask model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionAbolish($id)
    {
        if (!Yii::$app->user->can('作废运维任务')) {
            return $this->redirect(['site/login']);
        }
        $id            = Yii::$app->request->get('id');
        $result        = Yii::$app->request->get('result');
        $reason        = Yii::$app->request->get('reason');
        $model         = $this->findModel($id);
        $model->is_sue = $result == 1 ? DistributionTask::ABOLISH : DistributionTask::NO_FINISH;
        $model->reason = trim($reason);
        $model->save();
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
     *  配送数据统计管理页面
     *
     */
    public function actionStatistics()
    {
        if (!Yii::$app->user->can('运维任务统计管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new DistributionTaskSearch();
        $dataProvider = $searchModel->statisticsSearch(Yii::$app->request->queryParams);
        return $this->render('statistics', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 更换运维人员的负责人
     * @author wangxiwen
     * @version 2018-10-27
     * @param
     * @return
     */
    public function actionChangeUser()
    {
        $taskId = Yii::$app->request->get('taskId', '');
        $userId = Yii::$app->request->get('userId', '');
        if (empty($taskId) || empty($userId)) {
            return false;
        }
        //批量更新运维任务的负责人
        $changeUserResult = DistributionTask::saveTaskUser($taskId, $userId);
        if (!$changeUserResult) {
            return false;
        }
        return true;
    }

}
