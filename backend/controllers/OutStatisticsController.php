<?php

namespace backend\controllers;

use backend\models\EstimateStatistics;
use backend\models\Organization;
use backend\models\OutStatistics;
use backend\models\OutStatisticsSearch;
use backend\models\ScmMaterial;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * OutStatisticsController implements the CRUD actions for OutStatistics model.
 */
class OutStatisticsController extends Controller
{
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
     * Lists all OutStatistics models.
     * @return mixed
     */
    public function actionIndex()
    {
        $scmMaterial  = ScmMaterial::getScmMaterial();
        $organization = Organization::getOrganizationList();
        $searchModel  = new OutStatisticsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'scmMaterial'  => $scmMaterial,
            'organization' => $organization,
        ]);
    }

    /**
     * Displays a single OutStatistics model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看出库单')) {
            return $this->redirect(['site/login']);
        }
        //物料详情
        $scmMaterial = ScmMaterial::getScmMaterial();
        //出库单统计数据
        $model           = $this->findModel($id);
        $outMaterialList = $model && $model->material_info ? Json::decode($model->material_info) : [];
        //预估单统计数据
        $esModel              = EstimateStatistics::getEstimateStatistic($model->org_id, $model->date);
        $estimateMaterialList = $esModel && $esModel->material_info ? Json::decode($esModel->material_info) : [];
        //获取运维出库单详情
        $outStatisticsDetail = OutStatistics::getStatisticsDetail($outMaterialList, $scmMaterial);
        //获取运维预估单详情
        $estimateStatisticsDetail = OutStatistics::getStatisticsDetail($estimateMaterialList, $scmMaterial);
        //计算出库单和预估单统计数据物料数量差值
        $diffMaterialStr      = OutStatistics::getDiffMaterial($outMaterialList, $estimateMaterialList);
        $diffMaterialArr      = $diffMaterialStr ? Json::decode($diffMaterialStr) : [];
        $diffStatisticsDetail = OutStatistics::getDiffStatisticsDetail($diffMaterialArr, $scmMaterial);
        //查询运维出库单分表数据
        $outMaterialArray = OutStatistics::getScmWarehouseOut($model->date, $model->org_id);
        //查询运维预估单分表数据
        $estimateMaterialArray = EstimateStatistics::getScmWarehouseEstimate($model->date, $model->org_id);
        //出库单中运维人员
        $outAuthorArray = OutStatistics::getOutAuthor($model->date, $model->org_id);

        $collectMaterialDetail = OutStatistics::getCollectMaterialDetail($outMaterialArray, $estimateMaterialArray, $outAuthorArray);
        return $this->render('view', [
            'model'                    => $model,
            'outStatisticsDetail'      => $outStatisticsDetail,
            'estimateStatisticsDetail' => $estimateStatisticsDetail,
            'diffStatisticsDetail'     => $diffStatisticsDetail,
            'collectMaterialDetail'    => $collectMaterialDetail,
        ]);
    }

    /**
     * Updates an existing OutStatistics model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('复审出库单')) {
            return $this->redirect(['site/login']);
        }
        $model         = $this->findModel($id);
        $materialArray = $model && $model->material_info ? Json::decode($model->material_info) : [];
        //物料详情
        $scmMaterial           = ScmMaterial::getScmMaterial();
        $examineMaterialDetail = OutStatistics::getExamineMaterialDetail($materialArray, $scmMaterial);
        $params                = Yii::$app->request->post();
        if (!empty($params)) {
            //更新复审提交的物料出库数量
            $saveResult = OutStatistics::saveExamineMaterialNumber($model, $materialArray, $params['material_info']);
            if ($saveResult) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "运维出库单统计管理", \backend\models\ManagerLog::UPDATE, "复审运维出库单");
                return $this->redirect(['index']);
            }
            Yii::$app->getSession()->setFlash("error", "出库单复审失败");
        }
        return $this->render('update', [
            'model'                 => $model,
            'examineMaterialDetail' => $examineMaterialDetail,
        ]);
    }

    /**
     * Updates an existing OutStatistics model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionReview()
    {
        if (!Yii::$app->user->can('审核出库单')) {
            return $this->redirect(['site/login']);
        }
        $id            = Yii::$app->request->get('id');
        $result        = Yii::$app->request->get('result');
        $model         = $this->findModel($id);
        $model->status = $result == 1 ? OutStatistics::AUDIT_SUCCESS : OutStatistics::AUDIT_FAILURE;
        $model->save();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "运维出库单统计管理", \backend\models\ManagerLog::UPDATE, "审核运维出库单");
        return $this->redirect(['index']);
    }

    /**
     * Finds the OutStatistics model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OutStatistics the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OutStatistics::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
