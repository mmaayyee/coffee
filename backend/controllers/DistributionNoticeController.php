<?php

namespace backend\controllers;

use Yii;
use backend\models\DistributionNotice;
use backend\models\DistributionNoticeSearch;
use backend\models\DistributionNoticeReadSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Manager;
use common\models\WxMember;
use common\helpers\WXApi\WxMessage;
use backend\models\DistributionNoticeRead;
use yii\filters\AccessControl;
/**
 * DistributionNoticeController implements the CRUD actions for DistributionNotice model.
 */
class DistributionNoticeController extends Controller
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
                        'actions' => ['view', 'index', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all DistributionNotice models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('配送通知管理')){
            return $this->redirect(['site/login']);
        }
        $searchModel = new DistributionNoticeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DistributionNotice model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('配送通知管理')){
            return $this->redirect(['site/login']);
        }
        $searchModel = new DistributionNoticeReadSearch();
        $dataProvider = $searchModel->searchById(Yii::$app->request->queryParams, $id);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new DistributionNotice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('配送通知管理')){
            return $this->redirect(['site/login']);
        }

        $model = new DistributionNotice();
        //获取当前用户什么角色，显示其角色下的人员
        $userid   = yii::$app->user->identity->id;
        $wxMemberArr    =   DistributionNotice::getDisAttendanceList($userid);
        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();
        if ($model->load(Yii::$app->request->post()) ) {
            $param  =   Yii::$app->request->post();
            if(!$param['DistributionNotice']['receiver']){
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '配送信息缺少接收人。');
                return $this->redirect($_SERVER['HTTP_REFERER']);
            }
            $model->create_time =   time();
            $model->send_num    =   count($param['DistributionNotice']['receiver']);
            $model->receiver    =   implode('|', $param['DistributionNotice']['receiver']);
            $model->sender      =   yii::$app->user->identity->username;
            $ret = $model->save();
            if(!$ret){
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '配送信息发送入库失败');
                return $this->redirect($_SERVER['HTTP_REFERER']);
            }
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "配送通知", \backend\models\ManagerLog::CREATE, $model->content);

            //微信发送，并 插入阅读数据表
            if ($param['DistributionNotice']['receiver'] ) {
                DistributionNotice::dealReadRelated($param, $model, $transaction);
            }else{
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', '此人不存在');
                return $this->redirect($_SERVER['HTTP_REFERER']);
            }
            //事务通过
            $transaction->commit();
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'wxMemberArr'=> $wxMemberArr,
            ]);
        }
    }

    /**
     * Updates an existing DistributionNotice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "配送通知", \backend\models\ManagerLog::UPDATE, $model->content);
            return $this->redirect(['view', 'id' => $model->Id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing DistributionNotice model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('配送通知管理')){
            return $this->redirect(['site/login']);
        }
        $model = $this->findModel($id);
        $model->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "配送通知", \backend\models\ManagerLog::DELETE, $model->content);
        return $this->redirect(['index']);
    }

    /**
     * Finds the DistributionNotice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DistributionNotice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DistributionNotice::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
