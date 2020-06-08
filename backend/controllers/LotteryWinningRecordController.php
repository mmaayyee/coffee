<?php

namespace backend\controllers;

use backend\models\LotteryWinningRecord;
use backend\models\LotteryWinningRecordSearch;
use common\models\ActivityApi;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * LotteryWinningRecordController implements the CRUD actions for LotteryWinningRecord model.
 */
class LotteryWinningRecordController extends Controller
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
                    // 'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all LotteryWinningRecord models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('参与活动记录管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel      = new LotteryWinningRecordSearch();
        $dataProvider     = $searchModel->nineLotterySearch(Yii::$app->request->queryParams);
        $activityTypeList = ActivityApi::getActivityTypeList(2, 1);
        $activityNameList = ActivityApi::getActivityIdToName(2);
        return $this->render('index', [
            'searchModel'      => $searchModel,
            'dataProvider'     => $dataProvider,
            'activityTypeList' => $activityTypeList,
            'activityNameList' => $activityNameList,
        ]);
    }

    /**
     * Displays a single LotteryWinningRecord model.
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
     * Finds the LotteryWinningRecord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return LotteryWinningRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LotteryWinningRecord::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
