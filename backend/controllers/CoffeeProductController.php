<?php

namespace backend\controllers;

use backend\models\CoffeeProduct;
use backend\models\CoffeeProductSearch;
use backend\models\ManagerLog;
use backend\models\ScmEquipTypeMatstockAssoc;
use common\models\CoffeeProductApi;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CoffeeProductController implements the CRUD actions for CoffeeProduct model.
 */
class CoffeeProductController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'view', 'delete', 'save-log', 'release'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all CoffeeProduct models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('单品管理')) {
            return $this->redirect(['site/login']);
        }
        $releaseStatus = CoffeeProductApi::getProductRelaseStatus();
        $searchModel   = new CoffeeProductSearch();
        $typeString    = '';
        $dataProvider  = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'   => $searchModel,
            'dataProvider'  => $dataProvider,
            'typeString'    => $typeString,
            'releaseStatus' => $releaseStatus,
        ]);
    }

    /**
     * Displays a single CoffeeProduct model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看单品')) {
            return $this->redirect(['site/login']);
        }
        //获取已经选中的成份ID
        // $checkedIngredient     = ProductIngredientApi::getCheckedIngredientName($id);
        $cofProStockRecipeList = CoffeeProductApi::getCofProStockRecipeList($id);
        // 获取设备类型和料仓数组
        $equipTypeStockList = ScmEquipTypeMatstockAssoc::getEquipTypeStockListAll();
        $model              = $this->findModel($id);
        return $this->render('view', [
            'model'                 => $model,
            'cofProStockRecipeList' => $cofProStockRecipeList,
            'equipTypeStockList'    => $equipTypeStockList,
            // 'checkedIngredient'     => $checkedIngredient,
        ]);
    }

    /**
     * Creates a new CoffeeProduct model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加单品')) {
            return $this->redirect(['site/login']);
        }
        $model = new CoffeeProduct();
        //获取成份数组
        // $ingredientArray = ProductIngredientApi::getIngredientArray();
        //单品配方
        $cofProStockRecipeList = CoffeeProductApi::getCofProStockRecipeList();
        // 获取设备类型和料仓数组
        $equipTypeStockList = ScmEquipTypeMatstockAssoc::getEquipTypeStockListAll();
        return $this->render('create', [
            'model'                 => $model,
            // 'ingredientArray'       => $ingredientArray,
            'equipTypeStockList'    => Json::encode($equipTypeStockList),
            'cofProStockRecipeList' => Json::encode($cofProStockRecipeList),
        ]);
    }

    /**
     * Updates an existing CoffeeProduct model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $isCopy = 0)
    {
        if (!Yii::$app->user->can('编辑单品')) {
            return $this->redirect(['site/login']);
        }
        //获取成份数组
        // $ingredientArray = ProductIngredientApi::getIngredientArray();
        //获取已经选中的成份ID
        // $checkedIngredient = ProductIngredientApi::getCheckedIngredient($id);
        //单品配方
        $cofProStockRecipeList   = CoffeeProductApi::getCofProStockRecipeList($id);
        $equipTypeStockList      = ScmEquipTypeMatstockAssoc::getEquipTypeStockListAll();
        $model                   = $this->findModel($id);
        $model->price_start_time = $model->price_start_time > 0 ? date('Y-m-d H:i', $model->price_start_time) : '';
        $model->price_end_time   = $model->price_end_time > 0 ? date('Y-m-d H:i', $model->price_end_time) : '';
        $view                    = 'update';
        if ($isCopy == 1) {
            $view                 = 'create';
            $model->cf_product_id = '';
        }
        return $this->render($view, [
            'model'                 => $model,
            // 'ingredientArray'       => $ingredientArray,
            // 'checkedIngredient'     => $checkedIngredient,
            'equipTypeStockList'    => Json::encode($equipTypeStockList),
            'cofProStockRecipeList' => Json::encode($cofProStockRecipeList),
        ]);

    }

    /**
     * Deletes an existing CoffeeProduct model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除单品')) {
            return $this->redirect(['site/login']);
        }
        $model = CoffeeProduct::getCoffeeProductInfo($id);
        $ret   = CoffeeProductApi::delCoffeeProduct($id);
        if (!$ret) {
            Yii::$app->getSession()->setFlash('error', '单品删除失败。');
        }
        ManagerLog::saveLog(Yii::$app->user->id, "单品管理", ManagerLog::DELETE, $model->cf_product_name);
        return $this->redirect(['coffee-product/index']);
    }

    /**
     * Finds the CoffeeProduct model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CoffeeProduct the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CoffeeProduct::getCoffeeProductInfo($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 发布单品配方
     * @author   wangxiwen
     * @version  2018-12-17
     */
    public function actionRelease($id)
    {
        if (!Yii::$app->user->can('发布单品')) {
            return $this->redirect(['site/login']);
        }
        $releaseResult = CoffeeProductApi::releaseProductFormula($id);
        if (!$releaseResult) {
            Yii::$app->getSession()->setFlash('error', '发布进度条失败');
        }
        return $this->redirect(['index']);
    }

    /**
     * 添加日志
     * @author   tuqiang
     * @version  2017-11-14
     */
    public function actionSaveLog()
    {
        $type          = Yii::$app->request->get('type');
        $cfProductName = Yii::$app->request->get('cfProductName');
        if ($type == 0) {
            $type = ManagerLog::CREATE;
        } else {
            $type = ManagerLog::UPDATE;
        }
        ManagerLog::saveLog(Yii::$app->user->id, "单品管理",
            $type, $cfProductName);
    }
}
