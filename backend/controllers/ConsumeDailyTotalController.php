<?php

namespace backend\controllers;

use backend\models\ConsumeDailyTotal;
use Yii;
use yii\web\Controller;

/**
 * CoffeeProductSetupController implements the CRUD actions for CoffeeProductSetup model.
 */
class ConsumeDailyTotalController extends Controller
{
    public function actionIndex()
    {
        if (!Yii::$app->user->can('日报总表')) {
            return $this->redirect(['site/login']);
        }
        if (!Yii::$app->user->can('日报总表查看')){
            return $this->redirect(['site/login']);
        }
        if (!Yii::$app->user->can('日报总表导出') && !Yii::$app->user->can('日报总表检索')) {
                return $this->render('index',['rules' => 0]);
        } elseif(!Yii::$app->user->can('日报总表检索'))  {
            return $this->render('index', [
                'rules' => 1,
            ]);
        }elseif(!Yii::$app->user->can('日报总表导出')){
            return $this->render('index', [
                'rules' => 2,
            ]);
        }
        return $this->render('index',['rules' => '']);
    }

    public function actionExport()
    {
        if (!Yii::$app->user->can('日报总表导出')) {
            return $this->redirect(['site/login']);
        }
        $date               = Yii::$app->request->get('date');
        $consumeChanneDaily = ConsumeDailyTotal::exportConsumeDailyTotalList($date);
        if (!empty($consumeChanneDaily)) {
            return ConsumeDailyTotal::export($consumeChanneDaily);
        }
    }
}
