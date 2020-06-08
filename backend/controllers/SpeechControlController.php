<?php

namespace backend\controllers;

use backend\models\SpeechControl;
use backend\models\SpeechControlSearch;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * SpeechControlController implements the CRUD actions for SpeechControl model.
 */
class SpeechControlController extends Controller
{
    public $enableCsrfValidation = false;

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
                        'actions' => ['view', 'index', 'create', 'update', 'delete', 'examine', 'filter-build', 'check-build', 'save-speech-control'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all SpeechControl models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new SpeechControlSearch();
        $params       = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SpeechControl model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        //获取语音控制信息
        $model = SpeechControl::getSpeechInfo($id);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * 添加语音控制
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $id = 0;
        //获取语音控制初始化数据
        $model = SpeechControl::getSpeechControlInit($id);
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SpeechControl model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate()
    {
        $id = Yii::$app->request->get('id');
        //获取语音控制初始化数据
        $model = SpeechControl::getSpeechControlInit($id);
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 添加和编辑语音控制时更新数据
     *
     */
    public function actionSaveSpeechControl()
    {
        $param      = file_get_contents("php://input");
        $params     = Json::decode($param);
        $saveResult = SpeechControl::SaveSpeechControlInfo($params);
        $saveCode   = $saveResult ? Json::decode($saveResult) : [];
        if (!empty($saveCode) && !$saveCode['code']) {
            if ($params['id']) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "语音控制管理", \backend\models\ManagerLog::UPDATE, "编辑语音控制");
            } else {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "语音控制管理", \backend\models\ManagerLog::CREATE, "添加语音控制");
            }
        }
        return $saveResult;
    }

    /**
     * 审核语音控制
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionExamine()
    {
        $id     = Yii::$app->request->get('id');
        $result = Yii::$app->request->get('result');
        //获取语音控制信息
        $model             = SpeechControl::getSpeechInfo($id);
        $speechControlInfo = Json::decode($model);
        if ($result == 1) {
//审核通过
            //判断最终的状态
            $start_time = $speechControlInfo['speechControlInfo']['start_time'];
            $end_time   = $speechControlInfo['speechControlInfo']['end_time'];
            if (time() >= $start_time && time() < $end_time) {
                $status = SpeechControl::IS_ONLINE; //上线
            } elseif (time() < $start_time) {
                $status = SpeechControl::NO_ONLINE; //待上线
            } else {
                $status = SpeechControl::IS_DOWNLINE; //下线
            }
        } else {
//审核失败
            $status = SpeechControl::IS_REFUSE;
        }

        //更新语音控制状态
        $ret = SpeechControl::saveSpeechControlExamine($id, $status);
        if (!$ret) {
            Yii::$app->getSession()->setFlash('error', '审核操作执行失败');
            return $this->redirect(['index']);
        }
        return $this->redirect(['index']);
    }

    /**
     * 楼宇检测
     */
    public function actionCheckBuild()
    {
        $param  = file_get_contents("php://input");
        $params = Json::decode($param);
        return SpeechControl::checkBuild($params);
    }

    /**
     * 筛选楼宇
     */
    public function actionFilterBuild()
    {
        $param  = file_get_contents("php://input");
        $params = Json::decode($param);
        return SpeechControl::filterBuild($params);
    }

    /**
     * Deletes an existing SpeechControl model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "语音控制管理", \backend\models\ManagerLog::DELETE, "删除语音控制");
        return $this->redirect(['index']);
    }

    /**
     * Finds the SpeechControl model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SpeechControl the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SpeechControl::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
