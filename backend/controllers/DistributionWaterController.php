<?php

namespace backend\controllers;

use backend\models\DistributionWater;
use backend\models\DistributionWaterSearch;
use backend\models\ScmSupplier;
use common\models\Building;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * DistributionWaterController implements the CRUD actions for DistributionWater model.
 */
class DistributionWaterController extends Controller
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
                        'actions' => ['view', 'index', 'create', 'update', 'delete', 'order', 'batch-order', 'water-supplier'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all DistributionWater models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('水单管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new DistributionWaterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new DistributionWater model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加水单')) {
            return $this->redirect(['site/login']);
        }
        $model = new DistributionWater();
        if ($model->load(Yii::$app->request->post())) {
            $model->create_time = time();
            if ($model->save()) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "水单管理", \backend\models\ManagerLog::CREATE, "添加水单");
                return $this->redirect(['index']);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing DistributionWater model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑水单')) {
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "水单管理", \backend\models\ManagerLog::UPDATE, "楼宇：" . Building::getBuildingDetail('name', ['id' => $model->build_id])['name'] . "，供应商：" . ScmSupplier::getSurplierDetail('name', ['id' => $model->supplier_id])['name'] . "，送水量：" . $model->need_water . '桶');
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing DistributionWater model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionOrder($id)
    {
        if (!Yii::$app->user->can('水单管理')) {
            return $this->redirect(['site/login']);
        }
        //事务开始
        $transaction       = Yii::$app->db->beginTransaction();
        $model             = $this->findModel($id);
        $model->order_time = time();
        $model->completion_status = DistributionWater::WAIT_SEND;
        $ret               = $model->save();
        if (!$ret) {
            Yii::$app->getSession()->setFlash('error', '下单添加失败');
            $transaction->rollBack();
            return $this->redirect(['index']);
        }
        // 微信供水商发送
        if (!DistributionWater::sendWaterNews($model)) {
            Yii::$app->getSession()->setFlash('error', '水单信息发送失败');
            $transaction->rollBack();
            return $this->redirect(['index']);
        }
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "水单管理", \backend\models\ManagerLog::CREATE, "下发水单");
        // 事务结束
        $transaction->commit();
        return $this->redirect(['index']);
    }

    /**
     *  批量下(水)单
     **/
    public function actionBatchOrder()
    {
        if (!Yii::$app->user->can('水单管理')) {
            return $this->redirect(['site/login']);
        }
        //事务开始
        $transaction = Yii::$app->db->beginTransaction();
        if (Yii::$app->request->isAjax) {
            $sign          = true;
            $keys          = Yii::$app->request->post("keys");
            $memberNameArr = array();
            foreach ($keys as $value) {
                $model             = DistributionWater::findOne($value);
                $model->order_time = time();
                $model->completion_status = DistributionWater::WAIT_SEND;
                $ret               = $model->save();
                if (!$ret) {
                    $sign = false;
                    $transaction->rollBack();
                }
                $memberNameArr = DistributionWater::getMemberNameArr($model, $memberNameArr);
            }
            // 循环发送消息
            foreach ($memberNameArr as $key => $value) {
                DistributionWater::sendContentInfo($value);
            }
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "水单管理", \backend\models\ManagerLog::CREATE, "批量下发水单");
            // 事务结束
            $transaction->commit();
            return json_encode($sign);
        }
    }

    /**
     * Deletes an existing DistributionWater model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "水单管理", \backend\models\ManagerLog::DELETE, "删除水单");
        return $this->redirect(['index']);
    }

    /**
     * Finds the DistributionWater model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DistributionWater the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DistributionWater::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 根据楼宇所在分公司获取供水商信息
     * @author  zgw
     * @version 2016-12-02
     * @return  [type]     [description]
     */
    public function actionWaterSupplier($buildId)
    {
        $orgId         = Building::getField('org_id', ['id' => $buildId]);
        $waterSupplier = ScmSupplier::getOrgWaterList($orgId);
        unset($waterSupplier['']);
        return json_encode($waterSupplier);
    }
}
