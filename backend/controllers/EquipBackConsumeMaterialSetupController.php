<?php

namespace backend\controllers;

use backend\models\CoffeeLabel;
use backend\models\ManagerLog;
use backend\models\ScmEquipType;
use common\models\EquipBackConsumeMaterialSetup;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CoffeeLabelController implements the CRUD actions for CoffeeLabel model.
 */
class EquipBackConsumeMaterialSetupController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * Lists all CoffeeLabel models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('查看工厂模式物料消耗设置')) {
            return $this->redirect(['site/login']);
        }
        $searchModel         = new EquipBackConsumeMaterialSetup();
        $dataProvider        = $searchModel->getSetupList(Yii::$app->request->queryParams);
        $equipTypeIdNameList = ScmEquipType::getEquipTypeIdNameArr();
        return $this->render('index', [
            'searchModel'         => $searchModel,
            'dataProvider'        => $dataProvider,
            'equipTypeIdNameList' => $equipTypeIdNameList,
        ]);
    }

    /**
     * Creates a new CoffeeLabel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加工厂模式物料消耗设置')) {
            return $this->redirect(['site/login']);
        }
        $setupModel = new EquipBackConsumeMaterialSetup();
        if (Yii::$app->request->isPost) {
            //执行新增
            $data   = Yii::$app->request->post();
            $result = $setupModel->saveSetup($data);
            if ($result['error_code'] == 0) {
                ManagerLog::saveLog(Yii::$app->user->id, "工厂模式物料消耗设置", ManagerLog::CREATE, "添加工厂模式物料消耗设置");

                return $this->redirect(['index']);
            }
            return '<script>alert("' . $result['msg'] . '");history.back(-1);</script>';
        }
        $equipTypeIdNameList = ScmEquipType::getEquipTypeIdNameArr();
        //获取单品列表
        return $this->render('create', [
            'model'               => $setupModel,
            'equipTypeIdNameList' => $equipTypeIdNameList,
        ]);
    }

    /**
     * Updates an existing CoffeeLabel model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑工厂模式物料消耗设置')) {
            return $this->redirect(['site/login']);
        }
        $setupModel = EquipBackConsumeMaterialSetup::getSetupInfo($id);
        if (Yii::$app->request->isPost) {
            //执行新增
            $data   = Yii::$app->request->post();
            $result = $setupModel->saveSetup($data);
            if ($result['error_code'] == 0) {
                ManagerLog::saveLog(Yii::$app->user->id, "工厂模式物料消耗设置", ManagerLog::UPDATE, "编辑工厂模式物料消耗设置");
                return $this->redirect(['index']);
            }
            return '<script>alert("' . $result['msg'] . '");history.back(-1);</script>';
        }
        if (!$setupModel) {
            return $this->redirect(['index']);
        }
        $equipTypeIdNameList = ScmEquipType::getEquipTypeIdNameArr();
        return $this->render('update', [
            'model'               => $setupModel,
            'equipTypeIdNameList' => $equipTypeIdNameList,
        ]);
    }

    /**
     * Deletes an existing CoffeeLabel model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDel($id)
    {
        if (!Yii::$app->user->can('删除工厂模式物料消耗设置')) {
            return $this->redirect(['site/login']);
        }
        $result = EquipBackConsumeMaterialSetup::deleteSetup($id);
        if ($result) {
            ManagerLog::saveLog(Yii::$app->user->id, "工厂模式物料消耗设置", ManagerLog::DELETE, "删除工厂模式物料消耗设置");
            return $this->redirect(['index']);
        }
        return '<script>alert("' . $result['msg'] . '");history.back(-1);</script>';
    }

}
