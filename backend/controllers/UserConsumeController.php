<?php

namespace backend\controllers;

use backend\models\ManagerLog;
use backend\models\UserConsume;
use backend\models\UserConsumeSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UserConsumeController implements the CRUD actions for UserConsume model.
 */
class UserConsumeController extends Controller
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
     * Lists all UserConsume models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new UserConsumeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'   => $searchModel,
            'buildingArray' => $dataProvider['buildingArray'],
            'dataProvider'  => $dataProvider['refundList'],
            'realPrice'     => $dataProvider['realPrice'],
            'consumeAmount' => $dataProvider['consumeAmount'],
        ]);
    }
    /**
     *  消费记录导出
     * @Author   GaoYongli
     * @DateTime 2018-06-02
     * @param    [param]
     * @return   [type]     [description]
     */
    public function actionExport()
    {
        if (!Yii::$app->user->can('消费记录列表导出')) {
            return $this->redirect(['site/login']);
        }
        return UserConsume::getUserConsumesexport();
    }
    /**
     * Displays a single UserConsume model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('消费记录详情查看')) {
            return $this->redirect(['site/login']);
        }
        $UserConsumeID          = Yii::$app->request->get('id');
        $consumptionInformation = UserConsume::getConsumptionInformation($UserConsumeID);
        return $this->render('view', [
            'consumptionInformation' => $consumptionInformation,
        ]);
    }

    /**
     * Deletes an existing UserConsume model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionRefund($id)
    {
        $userConsume = UserConsume::updateRefundStatus($id);
        $userConsume = Json::decode($userConsume);
        if ($userConsume['data']) {
            if ($userConsume['data']['is_refund'] == UserConsume::REFUND_NO) {
                $msg = "从已退还改为已消费";
            } else {
                $msg = "从已消费改为已退还";
            }
            ManagerLog::saveLog(Yii::$app->user->id, "消费记录管理", ManagerLog::UPDATE, "将消费记录ID为" . $id . "的数据" . $msg);
        }
        return $this->redirect(['index']);
    }
}
