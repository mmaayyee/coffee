<?php

namespace backend\controllers;

use backend\models\CoffeeLanguage;
use backend\models\CoffeeLanguageSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CoffeeLanguageController implements the CRUD actions for CoffeeLanguage model.
 */
class CoffeeLanguageController extends Controller
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
     * Lists all CoffeeLanguage models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('咖语管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel      = new CoffeeLanguageSearch();
        $dataProvider     = $searchModel->search(Yii::$app->request->queryParams);
        $productNameList  = CoffeeLanguage::getAllProductName();
        $languageTypeList = CoffeeLanguage::getLanguageTypeList();
        $onlineStaticList = CoffeeLanguage::getOnlineStaticList();
        return $this->render('index', [
            'searchModel'      => $searchModel,
            'dataProvider'     => $dataProvider,
            'productNameList'  => $productNameList,
            'languageTypeList' => $languageTypeList,
            'onlineStaticList' => $onlineStaticList,
        ]);
    }

    /**
     * Displays a single CoffeeLanguage model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看咖语详细信息')) {
            return $this->redirect(['site/login']);
        }
        $coffeeLanguage          = CoffeeLanguage::getCoffeeLanguageInfo($id);
        $coffeeLanguageInfoModel = CoffeeLanguage::getViewsCoffeeLanguageObj($coffeeLanguage);
        return $this->render('view', [
            'model' => $coffeeLanguageInfoModel,
        ]);
    }

    /**
     * Creates a new CoffeeLanguage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加咖语信息')) {
            return $this->redirect(['site/login']);
        }
        $model = new CoffeeLanguage();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "咖语管理", \backend\models\ManagerLog::CREATE, "添加咖语");
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CoffeeLanguage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑咖语信息')) {
            return $this->redirect(['site/login']);
        }
        $coffeeLanguage          = CoffeeLanguage::getCoffeeLanguageInfo($id);
        $coffeeLanguageInfoModel = CoffeeLanguage::getObjCoffeeLanguageInfo($coffeeLanguage);
        return $this->render('update', [
            'model' => $coffeeLanguageInfoModel,
        ]);
    }

    /**
     * Deletes an existing CoffeeLanguage model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除指定咖语信息')) {
            return $this->redirect(['site/login']);
        }
        $this->findModel($id)->delete();
        \backend\models\ManagerLog::saveLog(Yii::$app->user->id, "咖语管理", \backend\models\ManagerLog::DELETE, "删除咖语");
        return $this->redirect(['index']);
    }

    /**
     * Finds the CoffeeLanguage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CoffeeLanguage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CoffeeLanguage::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
