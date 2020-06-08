<?php

namespace backend\controllers;

use Yii;
use backend\models\DistributionWater;
use backend\models\DistributionWaterSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\helpers\WXApi\WxMessage;
use common\models\Building;
use common\models\WxMember;
use backend\models\ScmSupplier;

/**
 * DistributionWaterController implements the CRUD actions for DistributionWater model.
 */
class DistributionWaterRecordController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view', 'index', 'create', 'update', 'delete', 'order'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all DistributionWater models.
     * @return mixed
     */
    public function actionIndex()
    {   
        if (!Yii::$app->user->can('水单记录管理')) {
            return $this->redirect(['site/login']);
        }
        $searchModel = new DistributionWaterSearch();
        $dataProvider = $searchModel->searchRecord(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

 
}
