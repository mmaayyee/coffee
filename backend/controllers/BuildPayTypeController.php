<?php

namespace backend\controllers;

use backend\models\BuildPayType;
use backend\models\BuildPayTypeSearch;
use backend\models\ManagerLog;
use backend\models\PayTypeApi;
use common\models\Api;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * BuildPayTypeController implements the CRUD actions for BuildPayType model.
 */
class BuildPayTypeController extends Controller
{
    /**
     * Lists all BuildPayType models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('优惠楼宇查看')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new BuildPayTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'   => $searchModel,
            'buildTypeList' => Api::getBuildTypeList(),
            'equipTypeList' => Api::getEquipTypeList(),
            'dataProvider'  => $dataProvider,
        ]);
    }

    /**
     * 根据楼宇支付策略ID获取对应的支付方式及优惠策略列表
     * @author zhenggangwei
     * @date   2018-12-27
     * @param  integer     $buildPayTypeId 楼宇支付方式ID
     * @return string
     */
    public function actionGetBuildPayHolicyList($buildPayTypeId)
    {
        echo PayTypeApi::getBuildPayHolicyList($buildPayTypeId);die;
    }

    /**
     * Deletes an existing BuildPayType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('优惠楼宇删除')) {
            return $this->redirect(['site/login']);
        }
        $delRes = PayTypeApi::deleteBuildPayType($id);
        ManagerLog::saveLog(Yii::$app->user->id, "楼宇支付方式", ManagerLog::DELETE, '删除楼宇支付方式');
        return $this->redirect(['index']);
    }

}
