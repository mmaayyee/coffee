<?php

namespace backend\controllers;

use backend\models\DistributionDailyTask;
use backend\models\EstimateStatistics;
use backend\models\EstimateStatisticsSearch;
use backend\models\Organization;
use backend\models\ScmMaterial;
use backend\models\ScmWarehouseEstimate;
use common\models\WxMember;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EstimateStatisticsController implements the CRUD actions for EstimateStatistics model.
 */
class EstimateStatisticsController extends Controller
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
     * Lists all EstimateStatistics models.
     * @return mixed
     */
    public function actionIndex()
    {
        $scmMaterial  = ScmMaterial::getScmMaterial();
        $organization = Organization::getOrganizationList();
        $searchModel  = new EstimateStatisticsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'scmMaterial'  => $scmMaterial,
            'organization' => $organization,
        ]);
    }

    /**
     * 运维预估单发送功能
     * @author wangxiwen
     * @version 2018-06-21
     * @param  int $id 任务ID
     * @return
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('修改预估单')) {
            return $this->redirect(['site/login']);
        }
        $model  = $this->findModel($id);
        $params = Yii::$app->request->post();
        if (!empty($params['material_info'])) {
            $materialInfo = $params['material_info'];
            $transaction  = Yii::$app->db->beginTransaction();
            //更新运维预估单分表数据
            $saveEstimateResult = ScmWarehouseEstimate::saveEstimate($model->date, $materialInfo);
            if (!$saveEstimateResult) {
                Yii::$app->getSession()->setFlash("error", "预估单修改失败");
                $transaction->rollBack();
            }
            //更新预估单统计表数据
            $saveEstimateStatisticResult = EstimateStatistics::saveEstimateStatistics($model, $materialInfo);
            if (!$saveEstimateStatisticResult) {
                Yii::$app->getSession()->setFlash("error", "预估单统计修改失败");
                $transaction->rollBack();
            }
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "运维预估单统计管理", \backend\models\ManagerLog::UPDATE, "发送运维预估单");
            $transaction->commit();
            return $this->redirect(['index']);
        }
        $materialArray = $model && $model->material_info ? Json::decode($model->material_info) : [];
        //物料详情
        $scmMaterial            = ScmMaterial::getScmMaterial();
        $estimateMaterialDetail = EstimateStatistics::getEstimateMaterialDetail($materialArray, $scmMaterial);
        $estimateData           = EstimateStatistics::getScmWarehouseEstimate($model->date, $model->org_id);
        $userNameArray          = WxMember::getUserInfo($model->org_id);
        $buildNameArray         = DistributionDailyTask::getBuildArray($model->org_id, $model->date);
        //组合预估单表单展示
        $estimateShowData = EstimateStatistics::getEstimateShowData($estimateData, $userNameArray, $buildNameArray);
        return $this->render('update', [
            'model'                  => $model,
            'estimateShowData'       => $estimateShowData,
            'estimateMaterialDetail' => $estimateMaterialDetail,
        ]);
    }

    /**
     * 预估单配货
     * @author wangxiwen
     * @datetime 2018-06-13
     * return
     */
    public function actionDistribution($id)
    {
        if (!Yii::$app->user->can('预估单配货')) {
            return $this->redirect(['site/login']);
        }
        $model  = $this->findModel($id);
        $params = Yii::$app->request->post();
        if (!empty($params)) {
            $model->status            = EstimateStatistics::DISTRIBUTED;
            $model->distribution_date = date('Y-m-d');
            $ret                      = $model->save();
            if ($ret) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "运维预估单统计管理", \backend\models\ManagerLog::UPDATE, "运维预估单配货");
                return $this->redirect(['index']);
            }
        }
        return $this->render('distribution', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the EstimateStatistics model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EstimateStatistics the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EstimateStatistics::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
