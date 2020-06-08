<?php

namespace backend\controllers;

use backend\models\ServiceCategory;
use backend\models\ServiceCategorySearch;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ServiceCategoryController implements the CRUD actions for ServiceCategory model.
 */
class ServiceCategoryController extends Controller
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ServiceCategory models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('查看类别')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new ServiceCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ServiceCategory model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ServiceCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加类别')) {
            return $this->redirect(['site/login']);
        }
        $model     = new ServiceCategory();
        $categorys = Yii::$app->request->post();
        if ($categorys) {
            $data = [];
            foreach ($categorys as $category => $cat) {
                $data = $cat;
            }
            $saveCategory = ServiceCategory::postCategoryCreate($data);
            if ($saveCategory) {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "类别管理", \backend\models\ManagerLog::CREATE, "添加类别");
                return $this->redirect('index');
            }
        } else {
            return $this->render('create', ['model' => $model]);
        }

    }

    /**
     * Updates an existing ServiceCategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate()
    {
        if (!Yii::$app->user->can('修改类别')) {
            return $this->redirect(['site/login']);
        }
        $ID = Yii::$app->request->get('id');
        // 获取分类ID
        $CategoryID          = Json::decode(ServiceCategory::getCategoryID($ID));
        $model               = new ServiceCategory;
        $model->id           = $CategoryID['id'];
        $model->category     = $CategoryID['category'];
        $model->status       = $CategoryID['status'];
        $model->created_time = $CategoryID['created_time'];
        $model->load(['ServiceCategory' => $CategoryID]);
        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes an existing ServiceCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found

    public function actionDelete($id)
    {
    $this->findModel($id)->delete();

    return $this->redirect(['index']);
    }
     */
    /**
     * Finds the ServiceCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ServiceCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ServiceCategory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
