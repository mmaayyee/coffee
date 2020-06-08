<?php

namespace backend\controllers;

use backend\models\EquipLightBoxRepairSearch;
use common\models\EquipLightBoxRepair;
use common\models\SendNotice;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EquipLightBoxRepairController implements the CRUD actions for EquipLightBoxRepair model.
 */
class EquipLightBoxRepairController extends Controller
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
     * Lists all EquipLightBoxRepair models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new EquipLightBoxRepairSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EquipLightBoxRepair model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new EquipLightBoxRepair model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EquipLightBoxRepair();
        $data  = Yii::$app->request->post();

        $data['EquipLightBoxRepair']['create_time'] = time();
        if ($model->load($data) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "灯箱报修管理", \backend\models\ManagerLog::CREATE, "添加灯箱维修任务");
            //发送通知
            SendNotice::sendWxNotice($model->supplier_id, "light-box-repair/index", "您有一条新的灯箱维修任务请注意查收", Yii::$app->params['surpplier_agentid']);
            echo "<script>history.go(-1);</script>";
        } else {
            echo "<script>alert('操作失败');history.go(-1);</script>";
        }
    }

    /**
     * Finds the EquipLightBoxRepair model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EquipLightBoxRepair the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipLightBoxRepair::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
