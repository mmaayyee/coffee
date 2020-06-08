<?php

namespace backend\controllers;

use backend\models\WeeklyReturn;
use backend\models\WeeklyUser;
use Yii;
use yii\web\Controller;

/**
 * CoffeeProductSetupController implements the CRUD actions for CoffeeProductSetup model.
 */
class WeeklyReturnController extends Controller
{
    public function actionIndex()
    {
        if (!Yii::$app->user->can('周报复购数据')) {
             return $this->redirect(['site/login']);
         }
        if (!Yii::$app->user->can('周报复购数据查看')){
            return $this->redirect(['site/login']);
        }
        if (!Yii::$app->user->can('周报复购数据导出') && !Yii::$app->user->can('周报复购数据检索')) {
                return $this->render('index',['rules' => 0]);
        } elseif(!Yii::$app->user->can('周报复购数据检索'))  {
            return $this->render('index', [
                'rules' => 1,
            ]);
        }elseif(!Yii::$app->user->can('周报复购数据导出')){
            return $this->render('index', [
                'rules' => 2,
            ]);
        }
        return $this->render('index',['rules' => '']);
    }
    public function actionExport()
    {
        if (!Yii::$app->user->can('周报复购数据导出')) {
            return $this->redirect(['site/login']);
        }
        $date               = Yii::$app->request->get();
        $weeklyReturnList = WeeklyReturn::exportWeeklyReturnList($date);
        if (!empty($weeklyReturnList)) {
            return WeeklyReturn::export($weeklyReturnList);
        }
    }
}