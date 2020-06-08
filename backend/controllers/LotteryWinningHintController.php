<?php

namespace backend\controllers;

use backend\models\LotteryWinningHint;
use backend\models\LotteryWinningHintSearch;
use backend\models\ManagerLog;
use common\models\ActivityApi;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * LotteryWinningHintController implements the CRUD actions for LotteryWinningHint model.
 */
class LotteryWinningHintController extends Controller
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
     * Lists all LotteryWinningHint models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('活动提示语信息管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new LotteryWinningHintSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LotteryWinningHint model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('活动提示语信息查看')) {
            return $this->redirect(['site/login']);
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new LotteryWinningHint model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('活动提示语信息添加')) {
            return $this->redirect(['site/login']);
        }
        $params = Yii::$app->request->post();
        $model  = new LotteryWinningHint();
        $model->setScenario("create");
        return $this->render('_form', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing LotteryWinningHint model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('活动提示语信息编辑')) {
            return $this->redirect(['site/login']);
        }
        $params = Yii::$app->request->post();
        $data   = ActivityApi::getLoteryWinningHintListByHintId($id);
        $model  = new LotteryWinningHint();
        $model->load(['LotteryWinningHint' => $data]);
        $model->hint_success_photo      = Yii::$app->params['fcoffeeUrl'] . $model->hint_success_photo;
        $model->hint_error_photo        = Yii::$app->params['fcoffeeUrl'] . $model->hint_error_photo;
        $model->second_button_photo     = Yii::$app->params['fcoffeeUrl'] . $model->second_button_photo;
        $model->thank_participate_photo = Yii::$app->params['fcoffeeUrl'] . $model->thank_participate_photo;
        return $this->render('_form', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LotteryWinningHint model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('活动提示语信息删除')) {
            return $this->redirect(['site/login']);
        }
        $ret = ActivityApi::deleteLotteryWinningHintByHintId($id);
        if (!$ret) {
            Yii::$app->getSession()->setFlash('error', '删除提示语失败');
        }
        ManagerLog::saveLog(Yii::$app->user->id, "活动提示语", ManagerLog::DELETE, "删除活动提示语");
        return $this->redirect(['index']);
    }

    /**
     * Finds the LotteryWinningHint model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return LotteryWinningHint the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LotteryWinningHint::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
