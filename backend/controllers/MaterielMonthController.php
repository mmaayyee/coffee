<?php

namespace backend\controllers;

use backend\models\MaterielMonth;
use backend\models\MaterielMonthSearch;
use common\models\Api;
use Yii;
use yii\data\Pagination;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * MaterielMonthController implements the CRUD actions for MaterielMonth model.
 */
class MaterielMonthController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all MaterielMonth models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('物料消耗差异值列表')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new MaterielMonthSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $total        = isset($dataProvider['total']) && $dataProvider['total'] > 0 ? $dataProvider['total'] : 0;
        $pages        = new Pagination(['totalCount' => $total]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel'  => $searchModel,
            'pages'        => $pages,
        ]);
    }

    /**
     * Displays a single MaterielMonth model.
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
     * Creates a new MaterielMonth model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MaterielMonth();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "物料消耗差异值管理", \backend\models\ManagerLog::CREATE, "添加物料消耗差异值");
            return $this->redirect(['view', 'id' => $model->materiel_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing MaterielMonth model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($buildId, $createAt)
    {
        $info       = Api::getMaterielMonthInfo(array('buildId' => $buildId, 'create_at' => $createAt));
        $postParams = Yii::$app->request->post();
        if ($postParams) {
            $postParams['buildId']  = $buildId;
            $postParams['createAt'] = $createAt;
            if (Api::saveMaterielMonthInfo($postParams)) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "物料消耗差异值管理", \backend\models\ManagerLog::UPDATE, "编辑物料消耗差异值");
                return $this->redirect(['index']);
            } else {
                return $this->render('update', [
                    'model' => $info,
                ]);
            }
        } else {
            return $this->render('update', [
                'model' => $info,
            ]);
        }
    }

    /**
     * Deletes an existing MaterielMonth model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "物料消耗差异值管理", \backend\models\ManagerLog::DELETE, "删除物料消耗差异值");
        return $this->redirect(['index']);
    }

    /**
     * Finds the MaterielMonth model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MaterielMonth the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MaterielMonth::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
