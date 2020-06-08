<?php

namespace backend\controllers;

use backend\models\ProductIngredient;
use backend\models\ProductIngredientSearch;
use common\models\ProductIngredientApi;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ProductIngredientController implements the CRUD actions for ProductIngredient model.
 */
class ProductIngredientController extends Controller
{
    public $enableCsrfValidation = false;
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
     * Lists all ProductIngredient models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new ProductIngredientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ProductIngredient model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看单品成份')) {
            return $this->redirect(['site/login']);
        }
        //获取成份数据
        $ingredientDetail = ProductIngredientApi::getIngredientDetail($id);

        return $this->render('view', [
            'model' => $ingredientDetail,
        ]);
    }

    /**
     * Creates a new ProductIngredient model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加单品成份')) {
            return $this->redirect(['site/login']);
        }
        $params = Yii::$app->request->post();
        $model  = new ProductIngredient();
        if (!empty($params)) {
            //添加成份数据
            $saveIngredientResult = ProductIngredientApi::saveIngredient($params);
            if (!$saveIngredientResult) {
                Yii::$app->getSession()->setFlash('error', '成份添加失败');
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "单品成份管理", \backend\models\ManagerLog::CREATE, "添加单品成份");
            return $this->redirect(['index']);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ProductIngredient model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('修改单品成份')) {
            return $this->redirect(['site/login']);
        }
        $params = Yii::$app->request->post();
        if (!empty($params)) {
            //修改成份数据
            $saveIngredientResult = ProductIngredientApi::saveIngredient($params);
            if (!$saveIngredientResult) {
                Yii::$app->getSession()->setFlash('error', '成份修改失败');
            }
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "单品成份管理", \backend\models\ManagerLog::UPDATE, "编辑单品成份");
            return $this->redirect(['index']);
        }
        //获取成份数据
        $ingredientDetail = ProductIngredientApi::getIngredientDetail($id);
        return $this->render('update', [
            'model' => $ingredientDetail,
        ]);
    }

    /**
     * Deletes an existing ProductIngredient model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除单品成份')) {
            return $this->redirect(['site/login']);
        }
        //删除成份数据
        $deleteIngredientResult = ProductIngredientApi::deleteIngredient($id);
        if (!$deleteIngredientResult) {
            Yii::$app->getSession()->setFlash('error', '成份删除失败');
        }
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "单品成份管理", \backend\models\ManagerLog::DELETE, "删除单品成份");
        return $this->redirect(['index']);
    }

}
