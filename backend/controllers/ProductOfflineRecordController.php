<?php

namespace backend\controllers;

use Yii;
use backend\models\ProductOfflineRecord;
use backend\models\ProductOfflineRecordSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ProductOfflineRecordController implements the CRUD actions for ProductOfflineRecord model.
 */
class ProductOfflineRecordController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],            
        ];
    }

    /**
     * Lists all ProductOfflineRecord models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('产品上下架记录管理')){
            return $this->redirect(['site/login']);
        }
        $searchModel = new ProductOfflineRecordSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the ProductOfflineRecord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProductOfflineRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProductOfflineRecord::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
