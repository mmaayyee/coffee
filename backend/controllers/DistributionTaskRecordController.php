<?php

namespace backend\controllers;

use backend\models\DistributionTask;
use backend\models\DistributionTaskSearch;
use backend\models\EquipMalfunction;
use backend\models\ScmMaterial;
use backend\models\ScmMaterialStock;
use common\models\Building;
use common\models\EquipTask;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * DistributionTaskController implements the CRUD actions for DistributionTask model.
 */
class DistributionTaskRecordController extends Controller
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
                        'actions' => ['view', 'index', 'create', 'update', 'delete', 'detail', 'repair-task-record', 'filler', 'distribution-task-record'],
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
        if (!Yii::$app->user->can('配送任务记录')) {
            return $this->redirect(['site/login']);
        }

        $ret          = Building::buildStatusNameArr();
        $searchModel  = new DistributionTaskSearch();
        $dataProvider = $searchModel->searchRecord(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'equip_id'     => Yii::$app->request->queryParams['DistributionTaskSearch']['equip_id'],
        ]);
    }

    /**
     * Displays a single DistributionTask model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
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
     *  配送任务记录查询
     **/
    public function actionDistributionTaskRecord()
    {
        $searchModel  = new \backend\models\DistributionTaskSearch();
        $dataProvider = $searchModel->searchRecord(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'equip_id'     => Yii::$app->request->queryParams['DistributionTaskSearch']['equip_id'],
        ]);
    }

    /**
     * 显示任务的列表 条件：开始配送，结束配送均为空.
     * @return mixed
     */
    public function actionRepairTaskRecord()
    {
        if (!Yii::$app->user->can('配送维修记录')) {
            return $this->redirect(['site/login']);
        }

        $searchModel  = new \backend\models\DistributionTaskSearch();
        $dataProvider = $searchModel->repairSearch(Yii::$app->request->queryParams);
        return $this->render('repair_record', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionFiller()
    {
        $id = Yii::$app->request->get('id');
        if (!$id) {
            return json_encode(['error' => 1, 'msg' => '请选择要查看的任务', 'res' => '']);
        }
        $detail = [];
        //获取任务详情
        $taskDetail = DistributionTask::getDetail(['id' => $id]);
        if ($taskDetail) {
            // 物料添加信息
            if (isset($taskDetail->filler)) {
                $detail['distributionFiller'] = [];
                foreach ($taskDetail->filler as $key => $fillerArr) {
                    $materialObj = ScmMaterial::getMaterialObj(['id' => $fillerArr->material_id]);
                    if ($taskDetail->task_type == 1 || $taskDetail->task_type == 3) {
                        // 物料分类名称
                        $detail['distributionFiller'][$key]['material_type'] =
                        $materialObj->materialType->material_type_name;
                        // 物料名称
                        $detail['distributionFiller'][$key]['material_id'] = $materialObj->name;
                        // 物料规格
                        $detail['distributionFiller'][$key]['weight'] = !$materialObj->weight ? '' : $materialObj->weight . $materialObj->materialType->spec_unit;
                        // 料仓名称
                        $detail['distributionFiller'][$key]['stock_id'] = ScmMaterialStock::getMaterialStockDetail("*", ['id' => $fillerArr->stock_id])['name'];
                        // 物料数量
                        $detail['distributionFiller'][$key]['number'] = $fillerArr->number . ' ' . $materialObj->materialType->unit;
                        // 添加日期
                        $detail['distributionFiller'][$key]['create_date'] = $fillerArr->create_date;
                        // 添加人
                        $detail['distributionFiller'][$key]['add_material_author'] = $fillerArr->assignUser->name;
                    }
                }
            }
        }
        return json_encode(['error' => 0, 'msg' => '', 'res' => $detail]);
    }

    /**
     * Displays a single EquipTask model.
     * @param integer $id
     * @return mixed
     */
    public function actionDetail()
    {
        $id = Yii::$app->request->get('id');
        if (!$id) {
            return json_encode(['error' => 1, 'msg' => '请选择要查看的任务', 'res' => '']);
        }
        $detail = [];
        //获取任务详情
        $taskDetail = DistributionTask::getDetail(['id' => $id]);
        if ($taskDetail) {
            //获取设备类型
            $detail['equiptype'] = isset($taskDetail->equip->equipTypeModel->model) ? $taskDetail->equip->equipTypeModel->model : '';
            // 设备编号
            $detail['equip_code'] = isset($taskDetail->equip->equip_code) ? $taskDetail->equip->equip_code : '';
            if ($taskDetail->task_type == DistributionTask::DELIVERY && $taskDetail->maintenance) {
                // 开始维修时间
                $detail['start_repair_time'] = date('Y-m-d H:i:s', $taskDetail->maintenance->start_repair_time);
                // 结束维修时间
                $detail['end_repair_time'] = date('Y-m-d H:i:s', $taskDetail->maintenance->end_repair_time);
            } else {
                // 开始维修时间
                $detail['start_repair_time'] = date('Y-m-d H:i:s', $taskDetail->start_delivery_time);
                // 结束维修时间
                $detail['end_repair_time'] = date('Y-m-d H:i:s', $taskDetail->end_delivery_time);
            }
            // 处理结果
            // $detail['process_result'] = EquipTask::$repair_result[$taskDetail->maintenance->process_result];
            $detail['process_result'] = $taskDetail->result == 1 ? "维修成功" : "维修失败";
            // 维修人
            $detail['assign_userid'] = $taskDetail->user->name;
            // 故障描述
            $detail['malfunction_description'] = $taskDetail->maintenance->malfunction_description;
            // 故障原因
            $detail['malfunction_reason'] = EquipMalfunction::getMalfunctionReasonName($taskDetail->maintenance->malfunction_reason);
            // 处理方法
            $detail['process_method']   = $taskDetail->maintenance->process_method;
            $detail['equipTaskFitting'] = [];
            // 备件信息
            if (isset($taskDetail->fitting)) {
                foreach ($taskDetail->fitting as $key => $fittArr) {
                    if ($fittArr->task_type == 1) {
                        $detail['equipTaskFitting'][$key]['fitting_name']   = $fittArr->fitting_name;
                        $detail['equipTaskFitting'][$key]['fitting_model']  = $fittArr->fitting_model;
                        $detail['equipTaskFitting'][$key]['factory_number'] = $fittArr->factory_number;
                        $detail['equipTaskFitting'][$key]['num']            = $fittArr->num;
                        $detail['equipTaskFitting'][$key]['remark']         = $fittArr->remark;
                    }
                }
            }
        }
        return json_encode(['error' => 0, 'msg' => '', 'res' => $detail]);
    }

}
