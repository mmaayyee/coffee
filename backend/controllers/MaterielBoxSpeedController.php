<?php

namespace backend\controllers;

use backend\models\MaterielBoxSpeed;
use backend\models\MaterielBoxSpeedSearch;
use common\models\Api;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * MaterielBoxSpeedController implements the CRUD actions for MaterielBoxSpeed model.
 */
class MaterielBoxSpeedController extends Controller
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
     * Lists all MaterielBoxSpeed models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new MaterielBoxSpeedSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MaterielBoxSpeed model.
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
     * Creates a new MaterielBoxSpeed model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model  = new MaterielBoxSpeed();
        $params = Yii::$app->request->post();
        $model->setScenario('create');
        if ($params) {
            if ($model->load($params) && $model->validate() && $model->createMaterielBoxSpeedInfo($params)) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "料盒速度管理", \backend\models\ManagerLog::CREATE, "添加料盒速度");
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
     * Updates an existing MaterielBoxSpeed model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model  = $this->findModel($id);
        $params = Yii::$app->request->post();
        if ($model->load($params) && $model->saveMaterielBoxSpeedInfo($params)) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "料盒速度管理", \backend\models\ManagerLog::UPDATE, "编辑料盒速度");
            return $this->redirect(['index']);
        } else {
            $model->isNewRecord = 0;
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing MaterielBoxSpeed model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $result = Api::deleteMaterielBoxSpeedInfo($id);
        if (!empty($result) && $result) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "料盒速度管理", \backend\models\ManagerLog::DELETE, "删除料盒速度");
        }
        return json_encode($result);
    }

    /**
     * Finds the MaterielBoxSpeed model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return MaterielBoxSpeed the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MaterielBoxSpeed::findModel($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
