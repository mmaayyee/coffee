<?php

namespace backend\controllers;

use backend\models\WeeklyRevenue;
use Yii;
use yii\web\Controller;

/**
 * CoffeeProductSetupController implements the CRUD actions for CoffeeProductSetup model.
 */
class WeeklyRevenueController extends Controller
{
    public function actionIndex()
    {
        if (!Yii::$app->user->can('周报营收数据')) {
            return $this->redirect(['site/login']);
        }
        if (!Yii::$app->user->can('周报营收数据查看')){
            return $this->redirect(['site/login']);
        }
        if (!Yii::$app->user->can('周报营收数据导出') && !Yii::$app->user->can('周报营收数据检索')) {
                return $this->render('index',['rules' => 0]);
        } elseif(!Yii::$app->user->can('周报营收数据检索'))  {
            return $this->render('index', [
                'rules' => 1,
            ]);
        }elseif(!Yii::$app->user->can('周报营收数据导出')){
            return $this->render('index', [
                'rules' => 2,
            ]);
        }
        return $this->render('index',['rules' => '']);

    }
    public function actionExport()
    {
        if (!Yii::$app->user->can('周报营收数据导出')) {
            return $this->redirect(['site/login']);
        }
        $date               = Yii::$app->request->get();
        $weeklyRevenueList = WeeklyRevenue::exportWeeklyRevenueList($date);
        if (!empty($weeklyRevenueList)) {
            return WeeklyRevenue::export($weeklyRevenueList);
        }
    }
}