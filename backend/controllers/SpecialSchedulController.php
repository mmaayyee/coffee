<?php

namespace backend\controllers;

use backend\models\ManagerLog;
use backend\models\SpecialSchedul;
use backend\models\SpecialSchedulSearch;
use common\models\EquipProductGroupApi;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * SpecialSchedulController implements the CRUD actions for SpecialSchedul model.
 */
class SpecialSchedulController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    // 'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all SpecialSchedul models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('查看设备端活动')) {
            return $this->redirect(['site/login']);
        }
        //获取设备端活动发布状态
        // $releaseStatusArray = EquipProductGroupApi::getSpecialSchedulReleaseStatus();
        $searchModel  = new SpecialSchedulSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            // 'releaseStatusArray' => $releaseStatusArray,
        ]);
    }

    /**
     * Displays a single SpecialSchedul model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看设备端活动')) {
            return $this->redirect(['site/login']);
        }
        // 根据ID，查询出所有的设备端活动数据
        $model = new SpecialSchedul();
        $model->getModel($id);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * 发布设备端活动
     * @param integer $id
     * @return mixed
     */
    public function actionRelease()
    {
        if (!Yii::$app->user->can('发布设备端活动')) {
            return $this->redirect(['site/login']);
        }
        $id            = Yii::$app->request->get('id');
        $releaseResult = EquipProductGroupApi::releaseSpecialSchedul($id);
        if (!$releaseResult) {
            Yii::$app->getSession()->setFlash('error', '发布失败');
        }
        return $this->redirect(['index']);
    }

    /**
     * 根据ID，查询出所有的设备端活动单品数组
     * @author      tuqiang
     * @version     2017-10-18
     * @param       $id
     */
    public function actionProductDetailsList($id)
    {
        $productList = EquipProductGroupApi::getSpecialSchedulProductListByID($id);
        return $this->render('product', [
            'productList' => $productList,
        ]);
    }

    /**
     * 根据ID，查询出所有的设备端活动楼宇数组
     * @author      tuqiang
     * @version     2017-10-18
     * @param       $id
     */
    public function actionBuilding($id)
    {
        $building = EquipProductGroupApi::getSpecialSchedulEquipAssoc($id);
        return $this->render('building', [
            'building' => $building,
        ]);
    }

    /**
     * Creates a new SpecialSchedul model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SpecialSchedul();
        $model->getModel();
        $model->restriction_type = $model->restriction_type ? $model->restriction_type : Json::encode(['1' => 0]);
        $model->start_time       = $model->end_time       = '';
        $model->isCopy           = 0;
        $model->copySpecialID    = 0;
        return $this->render('_form', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SpecialSchedul model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑设备端活动')) {
            return $this->redirect(['site/login']);
        }
        $isCopy = Yii::$app->request->get('isCopy');
        $model  = new SpecialSchedul();
        $model->getModel($id, 1);
        $model->isCopy        = 0;
        $model->copySpecialID = 0;
        if ($isCopy) {
            $model->isCopy        = 1;
            $model->copySpecialID = $model->id;
            $model->id            = 0;
        }
        return $this->render('_form', [
            'model' => $model,
        ]);
    }

    /**
     * 搜索楼宇信息,
     * @author  zmy
     * @version 2017-09-29
     * @return  [type]     [description]
     */
    public function actionSearchBuild()
    {
        $data      = Yii::$app->request->post();
        $buildList = EquipProductGroupApi::getSpecialSchedulBuildList($data);
        return !$buildList ? [] : Json::encode($buildList);
    }

    /**
     * Deletes an existing SpecialSchedul model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $specialInfo = EquipProductGroupApi::getSpecialSchedulInfo($id);
        $ret         = EquipProductGroupApi::deleteSpecialSchedul($id);
        if (!$ret) {
            Yii::$app->getSession()->setFlash('error', '对不起，删除失败，请重新操作');
        } else {
            ManagerLog::saveLog(Yii::$app->user->id, "设备端活动", ManagerLog::DELETE, $specialInfo['specialSchedul']['special_schedul_name']);
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the SpecialSchedul model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SpecialSchedul the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SpecialSchedul::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
     * 添加日志
     * @author   tuqiang
     * @version  2017-11-14
     */
    public function actionSaveLog()
    {
        $type               = Yii::$app->request->get('type');
        $specialSchedulName = Yii::$app->request->get('specialSchedulName');
        if ($type == 0) {
            $type = ManagerLog::CREATE;
        } else {
            $type = ManagerLog::UPDATE;
        }
        ManagerLog::saveLog(Yii::$app->user->id, "设备端活动", $type, $specialSchedulName);
    }

}
