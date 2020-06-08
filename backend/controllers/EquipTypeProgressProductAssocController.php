<?php

namespace backend\controllers;

use backend\models\CoffeeProduct;
use backend\models\EquipTypeProgressProductAssoc;
use backend\models\EquipTypeProgressProductAssocSearch;
use backend\models\ManagerLog;
use common\models\EquipProductGroupApi;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EquipTypeProgressProductAssocController implements the CRUD actions for EquipTypeProgressProductAssoc model.
 */
class EquipTypeProgressProductAssocController extends Controller
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
     * Lists all EquipTypeProgressProductAssoc models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('进度条管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new EquipTypeProgressProductAssocSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EquipTypeProgressProductAssoc model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看进度条')) {
            return $this->redirect(['site/login']);
        }
        $model                                         = new EquipTypeProgressProductAssoc();
        $progressList['EquipTypeProgressProductAssoc'] = EquipProductGroupApi::getEquipProgressProViewById($id);
        $model->load($progressList);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new EquipTypeProgressProductAssoc model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加进度条')) {
            return $this->redirect(['site/login']);
        }
        // 获取进度条单品数据
        $progressAssocList = EquipProductGroupApi::getProgressProductList();
        if ($progressAssocList) {
            $isCreateArray = implode(',', ArrayHelper::getColumn($progressAssocList, 'cf_product_id'));
        } else {
            $isCreateArray = '';
        }
        $model            = new EquipTypeProgressProductAssoc();
        $equipTypeProcess = EquipProductGroupApi::getEquipTypeProcessListByProductId();
        $param            = Yii::$app->request->post('EquipTypeProgressProductAssoc');
        if ($param) {
            $param['ord_product_id'] = 0;
            $ret                     = EquipProductGroupApi::saveEquipProgressBar($param);
            if (!$ret) {
                Yii::$app->getSession()->setFlash('error', '添加失败，请重试');
                $model->isNewRecord = '1';
                return $this->render('_form', [
                    'model'            => $model,
                    'equipTypeProcess' => Json::encode($equipTypeProcess),
                    'isCreateArray'    => $isCreateArray,
                ]);
            }
            $coffModel = CoffeeProduct::getCoffeeProductInfo($param['product_id']);
            ManagerLog::saveLog(Yii::$app->user->id, "进度条管理", ManagerLog::CREATE, $coffModel->cf_product_name);
            return $this->redirect(['index']);
        } else {
            $model->isNewRecord = '1';
            return $this->render('_form', [
                'model'            => $model,
                'equipTypeProcess' => Json::encode($equipTypeProcess),
                'isCreateArray'    => $isCreateArray,
            ]);
        }
    }

    /**
     * Updates an existing EquipTypeProgressProductAssoc model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id, $isCopy = 0)
    {
        if (!Yii::$app->user->can('编辑进度条')) {
            return $this->redirect(['site/login']);
        }
        $isCreateArray    = '';
        $equipTypeProcess = EquipProductGroupApi::getEquipTypeProcessListByProductId($id);
        $model            = new EquipTypeProgressProductAssoc();
        $param            = Yii::$app->request->post('EquipTypeProgressProductAssoc');
        if ($param) {
            if ($isCopy == 0) {
                $param['product_id'] = $id; // 原来的产品ID
            }
            $param['ord_product_id'] = $param['product_id'];
            $ret                     = EquipProductGroupApi::saveEquipProgressBar($param);
            if (!$ret) {
                Yii::$app->getSession()->setFlash('error', '修改失败，请重试');
                $model->isNewRecord = '';
                $model->product_id  = $id;
                return $this->render('_form', [
                    'model'            => $model,
                    'equipTypeProcess' => Json::encode($equipTypeProcess),
                    'isCreateArray'    => $isCreateArray,
                ]);
            }
            $coffModel = CoffeeProduct::getCoffeeProductInfo($id);
            ManagerLog::saveLog(Yii::$app->user->id, "进度条管理", ManagerLog::UPDATE, $coffModel->cf_product_name);
            return $this->redirect(['index']);
        } else {
            $model->isNewRecord = 1;
            if ($isCopy == 0) {
                $model->isNewRecord = '';
                $model->product_id  = $id;
            }
            return $this->render('_form', [
                'model'            => $model,
                'equipTypeProcess' => Json::encode($equipTypeProcess),
                'isCreateArray'    => $isCreateArray,
            ]);
        }
    }

    /**
     * 删除进度条
     * @author  zmy
     * @version 2017-09-08
     * @param   [type]     $id [单品ID]
     * @return  []         []
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除进度条')) {
            return $this->redirect(['site/login']);
        }
        $ret = EquipProductGroupApi::deleteEquipProcessBarByProId($id);
        if (!$ret) {
            Yii::$app->getSession()->setFlash('error', '删除进度条失败');
        } else {
            $coffModel = CoffeeProduct::getCoffeeProductInfo($id);
            ManagerLog::saveLog(Yii::$app->user->id, "进度条管理", ManagerLog::DELETE, $coffModel->cf_product_name);
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the EquipTypeProgressProductAssoc model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EquipTypeProgressProductAssoc the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {

        if (($model = EquipTypeProgressProductAssoc::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
