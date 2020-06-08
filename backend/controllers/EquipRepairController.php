<?php

namespace backend\controllers;

use backend\models\EquipAbnormalTask;
use backend\models\EquipRepair;
use backend\models\EquipRepairSearch;
use backend\models\EquipWarn;
use backend\models\Manager;
use backend\models\ManagerLog;
use common\models\Equipments;
use common\models\EquipTask;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EquipRepairController implements the CRUD actions for EquipRepair model.
 */
class EquipRepairController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all EquipRepair models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('客服上报管理')) {
            return $this->redirect(['site/login']);
        }
        $params       = Yii::$app->request->queryParams;
        $searchModel  = new EquipRepairSearch();
        $dataProvider = $searchModel->search($params);
        $equipId      = isset($params['EquipRepairSearch']['equip_id']) ? $params['EquipRepairSearch']['equip_id'] : 0;
        return $this->render('index', [
            'equipId'      => $equipId,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new EquipRepair model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('上报新故障')) {
            return $this->redirect(['site/login']);
        }

        $model = new EquipRepair();
        $data  = Yii::$app->request->post('EquipRepair');
        if ($data) {
            $data['content']     = !$data['content'] ? '' : implode(',', array_filter($data['content']));
            $data['author']      = Yii::$app->user->identity->realname;
            $data['create_time'] = time();
        }
        $transaction = Yii::$app->db->beginTransaction();
        if ($model->load(['EquipRepair' => $data]) && $model->save()) {
            //设备任务表中同步上报数据
            $data['repair_id']   = $model->id;
            $data['task_type']   = EquipTask::MAINTENANCE_TASK;
            $data['create_user'] = Yii::$app->user->identity->realname;
            $taskModel           = new EquipTask();
            if ($taskModel->load(['EquipTask' => $data]) && $taskModel->save()) {
                //操作日志
                $managerLogres = ManagerLog::saveLog(Yii::$app->user->id, "客服上报管理", ManagerLog::CREATE, Yii::$app->user->identity->realname);
                if (!$managerLogres) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', '操作日志添加失败');
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
                //发送通知
                $model->sendRepairNotice($data['build_id'], $taskModel->id);
                $transaction->commit();
                //添加到故障任务表中
                $abnormalTask              = new EquipAbnormalTask();
                $equip_code                = Equipments::getEquipmentCode($data['equip_id']);
                $abnormalTask->equip_code  = $equip_code['equip_code'];
                $abnormalTask->build_id    = $data['build_id'];
                $abnormalTask->org_id      = Manager::getManagerBranchID();
                $abnormalTask->create_time = time();
                $abnormalTask->task_status = 1;
                $abnormalTask->repair      = Json::encode(explode(',', $data['content']));
                $abnormalTask->type        = 2;
                $abnormalTaskInfo          = EquipWarn::getAbnormalTask($abnormalTask->build_id);
                if ($abnormalTaskInfo) {
                    //对原数据中repair字段更新
                    $repair               = EquipWarn::setInsertTaskList($abnormalTask, $abnormalTaskInfo);
                    $abnormalTask->repair = Json::encode($repair);
                    $abnormalTask->updateAll(['repair' => $abnormalTask->repair], ['equip_code' => $abnormalTask->equip_code]);
                } else {
                    $abnormalTask->load(['EquipAbnormalTask' => $abnormalTask]);
                    $abnormalTask->save();
                }
                return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->setFlash('error', '添加维修任务失败');
                $transaction->rollBack();
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            if ($data) {
                $model->build_id = $data['build_id'];
            }
            $transaction->rollBack();
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the EquipRepair model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EquipRepair the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipRepair::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionRepairList()
    {
        if (!Yii::$app->user->can('查看上报记录')) {
            return $this->redirect(['site/login']);
        }
        $searchModel         = new EquipRepairSearch();
        $dataProvider        = $searchModel->searchRepair(Yii::$app->request->queryParams);
        $searchModel['type'] = Yii::$app->request->get('type');

        return $this->render('index', [
            'equipId'      => Yii::$app->request->get('equip_id'),
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }
}
