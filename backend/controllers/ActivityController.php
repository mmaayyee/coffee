<?php

namespace backend\controllers;

use backend\models\Activity;
use backend\models\ActivitySearch;
use backend\models\LotteryWinningRecord;
use backend\models\LotteryWinningRecordSearch;
use backend\models\ManagerLog;
use common\models\ActivityApi;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ActivityController implements the CRUD actions for Activity model.
 */
class ActivityController extends Controller
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
     * Lists all Activity models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('营销游戏管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new ActivitySearch();
        $dataProvider = $searchModel->nineLotterySearch(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 抽奖活动详情页
     * @author  zmy
     * @version 2017-12-09
     * @return  [type]     [description]
     */
    public function actionDetail($id)
    {
        if (!Yii::$app->user->can('营销游戏查看')) {
            return $this->redirect(['site/login']);
        }
        $model        = new Activity();
        $activityList = ActivityApi::getNineLotteryActivityList($id);
        $model->load(['Activity' => $activityList]);
        $model->activity_id = $id;
        return $this->render('detail', [
            'model'        => $model,
            'gridList'     => isset($activityList['gridList']) ? $activityList['gridList'] : [],
            'awardSetList' => isset($activityList['awardSetList']) ? $activityList['awardSetList'] : [],
        ]);
    }

    /**
     * 中奖信息页面
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('中奖信息查看')) {
            return $this->redirect(['site/login']);
        }
        $model        = new LotteryWinningRecord();
        $searchModel  = new LotteryWinningRecordSearch();
        $dataProvider = $searchModel->nineLotterySearch(Yii::$app->request->queryParams, $id);

        $activityList = ActivityApi::getLotteryWinningRecordList($id);
        $model->load(['LotteryWinningRecord' => $activityList]);
        $model->activity_id = $id;
        return $this->render('view', [
            'model'        => $model,
            'activityList' => $activityList,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 修改发货状态 为已发货状态
     * @author  zmy
     * @version 2017-12-04
     * @return  [type]     [description]
     */
    public function actionShip()
    {
        $recordId = Yii::$app->request->post("record_id");
        if ($recordId) {
            $ret = ActivityApi::updateLotteryActivityShip($recordId);
            echo $ret;die();
        }
        echo false;
    }

    /**
     * Creates a new Activity model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('营销游戏添加')) {
            return $this->redirect(['site/login']);
        }
        $model = new Activity();
        return $this->render('_form', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Activity model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('营销游戏编辑')) {
            return $this->redirect(['site/login']);
        }
        $params       = Yii::$app->request->post();
        $activityList = ActivityApi::getNineLotteryActivityList($id);
        $model        = new Activity();

        $copy = Yii::$app->request->get('copy', 0);
        $model->load(['Activity' => $activityList]);
        $model->start_time = date("Y-m-d H:i:s", $model->start_time);
        $model->end_time   = date("Y-m-d H:i:s", $model->end_time);
        $isCopy            = '';
        if ($copy) {
            $model->activity_name = '';
            $model->start_time    = '';
            $model->end_time      = '';
            $model->status        = '';
            $model->isCopy        = $copy;
        }
        if ($model->load($params)) {
            return $this->redirect(['index']);
        } else {
            return $this->render('_form', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Activity model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('营销游戏删除')) {
            return $this->redirect(['site/login']);
        }
        $ret = ActivityApi::deleteActivity($id);
        if (!$ret) {
            Yii::$app->getSession()->setFlash('error', '删除活动失败');
        } else {
            ManagerLog::saveLog(Yii::$app->user->id, "营销游戏管理", ManagerLog::DELETE, '删除营销游戏记录');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Activity model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Activity the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Activity::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
