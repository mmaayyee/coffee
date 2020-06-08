<?php

namespace backend\controllers;

use backend\models\CoffeeLabel;
use backend\models\CoffeeLabelSearch;
use common\models\CoffeeProductApi;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CoffeeLabelController implements the CRUD actions for CoffeeLabel model.
 */
class CoffeeLabelController extends Controller
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all CoffeeLabel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new CoffeeLabelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CoffeeLabel model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $data = CoffeeLabel::getCoffeeLabelDetail(['id' => $id]);
        if (!$data) {
            return $this->redirect(['index']);
        }
        $coffeeProductList = CoffeeProductApi::getCoffeeProductFieldList(['cf_product_id' => $data['coffeeProductList']]);
        //查询单品信息
        return $this->render('view', [
            'data'              => $data,
            'coffeeProductList' => array_column($coffeeProductList, 'cf_product_name'),
        ]);
    }

    /**
     * Creates a new CoffeeLabel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (Yii::$app->request->isPost) {
            //执行新增
            $data   = Yii::$app->request->post();
            $result = CoffeeProductApi::updateCoffeeLabel($data);
            if ($result['status'] == 'success') {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "标签管理", \backend\models\ManagerLog::CREATE, "添加标签");

                return $this->redirect(['index']);
            }
            //Yii::$app->getSession()->setFlash('error', $result['msg']);
            return '<script>alert("' . $result['msg'] . '");history.back(-1);</script>';
        }
        //获取单品列表
        $productList = CoffeeLabel::getProducts();
        return $this->render('create', [
            'productList' => $productList,
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
        if (Yii::$app->request->isPost) {
            //执行新增
            $data   = Yii::$app->request->post();
            $result = CoffeeProductApi::updateCoffeeLabel($data);
            if ($result['status'] == 'success') {
                \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "标签管理", \backend\models\ManagerLog::UPDATE, "编辑标签");
                return $this->redirect(['index']);
            }
            //Yii::$app->getSession()->setFlash('error', $result['msg']);
            return '<script>alert("' . $result['msg'] . '");history.back(-1);</script>';
        }
        $data = CoffeeLabel::getCoffeeLabelDetail(['id' => $id]);
        if (!$data) {
            return $this->redirect(['index']);
        }
        //获取单品列表
        $productList = CoffeeLabel::getProducts();
        return $this->render('update', [
            'data'        => $data,
            'productList' => $productList,
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
        $result = CoffeeProductApi::delCoffeeLabel(['id' => $id]);
        if ($result['status'] == 'success') {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "标签管理", \backend\models\ManagerLog::DELETE, "删除标签");
        }
        return json_encode($result);
    }
    /**
     * 上线操作
     */
    public function actionOnline($id)
    {
        $data['id']            = $id;
        $data['online_status'] = 1;
        $result                = CoffeeProductApi::updateFieldCoffeeLabel($data);
        return json_encode($result);
    }
    /**
     * 编辑排序
     */
    public function actionChangeSort($id, $sort)
    {
        $data['id']   = $id;
        $data['sort'] = $sort;
        $result       = CoffeeProductApi::updateFieldCoffeeLabel($data);
        return json_encode($result);
    }
}
