<?php

namespace backend\controllers;

use backend\models\ClearEquip;
use backend\models\ClearEquipSearch;
use common\models\Api;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ClearEquipController implements the CRUD actions for ClearEquip model.
 */
class ClearEquipController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                ],
            ],
        ];
    }

    /**
     * Lists all ClearEquip models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('清洗设备类型列表')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new ClearEquipSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ClearEquip model.
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
     * Creates a new ClearEquip model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('清洗设备类型添加')) {
            return $this->redirect(['site/login']);
        }
        $model  = new ClearEquip();
        $params = Yii::$app->request->post();
        $model->setScenario('create');
        if ($params) {
            if ($model->load($params) && $model->validate() && $model->createClearEquipInfo($params)) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "清洗设备类型管理", \backend\models\ManagerLog::CREATE, "添加清洗设备类型");

                return $this->redirect(['index']);
            } else {
                $model->isNewRecord = 1;
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            $model->isNewRecord = 1;
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ClearEquip model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('清洗设备类型编辑')) {
            return $this->redirect(['site/login']);
        }
        $model  = $this->findModel($id);
        $params = Yii::$app->request->post();
        if ($model->load($params) && $model->saveClearEquipInfo($params)) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "清洗设备类型管理", \backend\models\ManagerLog::UPDATE, "编辑清洗设备类型");
            return $this->redirect(['index']);
        } else {
            $model->isNewRecord = 0;
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ClearEquip model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $data = Api::deleteClearEquipInfo($id);
        if (!empty($data)) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "清洗设备类型管理", \backend\models\ManagerLog::DELETE, "删除清洗设备类型");
        }
        return json_encode($data);
    }

    /**
     * Finds the ClearEquip model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ClearEquip the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ClearEquip::findModel($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
