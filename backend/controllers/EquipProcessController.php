<?php

namespace backend\controllers;

use Yii;
use backend\models\EquipProcess;
use backend\models\EquipProcessSearch;
use common\models\EquipProductGroupApi;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\ManagerLog;

/**
 * EquipProcessController implements the CRUD actions for EquipProcess model.
 */
class EquipProcessController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    // 'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all EquipProcess models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EquipProcessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EquipProcess model.
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
     * Creates a new EquipProcess model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EquipProcess();
        $param = Yii::$app->request->post();
        if ($param && EquipProductGroupApi::saveEquipProcess($param)) {
            ManagerLog::saveLog(Yii::$app->user->id, "设备工序管理", ManagerLog::CREATE, $param['EquipProcess']['process_name']);
            return $this->redirect(['index']);
        } else {
            $model->isNewRecord = '1';
            return $this->render('_form', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EquipProcess model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $param = Yii::$app->request->post();
        if ($param) {
            $param['id'] =  $id;
            $ret = EquipProductGroupApi::saveEquipProcess($param);
            if(!$ret){
                Yii::$app->getSession()->setFlash('error', '修改工序失败');
            }
            ManagerLog::saveLog(Yii::$app->user->id, "设备工序管理", ManagerLog::UPDATE, $param['EquipProcess']['process_name']);
            return $this->redirect(['index']);
        } else {
            $model->isNewRecord = '';
            return $this->render('_form', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 删除
     * @author  tuqinag 
     * @version 2017-10-17 
     * @param   integer $id
     */
    public function actionDeleteVerify()
    {
        $ID          = Yii::$app->request->get('id');
        $processName = Yii::$app->request->get('processName');
        if(EquipProductGroupApi::getEquipTypeProgressProductAssocByWhere(array("process_id" => $ID))){
            return false;
        }else{
            if(EquipProductGroupApi::deleteEquipProcess($ID)){
                ManagerLog::saveLog(Yii::$app->user->id, "设备工序管理", ManagerLog::DELETE, $processName);
                return true;
            }else{
                return false;
            }
            
        }
    }

    /**
     * Deletes an existing EquipProcess model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $ret = EquipProductGroupApi::deleteEquipProcess($id);
        if(!$ret){
            Yii::$app->getSession()->setFlash('error', '删除工序失败');
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the EquipProcess model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EquipProcess the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EquipProcess::getEquipProcessById($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
