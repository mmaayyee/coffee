<?php

namespace backend\controllers;

use backend\models\MonthlyUsers;
use Yii;
use yii\web\Controller;

/**
 * CoffeeProductSetupController implements the CRUD actions for CoffeeProductSetup model.
 */
class MonthlyUsersController extends Controller
{
    public function actionIndex()
    {
        if (!Yii::$app->user->can('月报用户数据')) {
            return $this->redirect(['site/login']);
        }
        if (!Yii::$app->user->can('月报用户数据查看')){
            return $this->redirect(['site/login']);
        }
        if (!Yii::$app->user->can('月报用户数据导出') && !Yii::$app->user->can('月报用户数据检索')) {
            return $this->render('index',['rules' => 0]);
        } elseif(!Yii::$app->user->can('月报用户数据检索'))  {
            return $this->render('index', [
                'rules' => 1,
            ]);
        }elseif(!Yii::$app->user->can('月报用户数据导出')){
            return $this->render('index', [
                'rules' => 2,
            ]);
        }
        return $this->render('index', ['rules' => '']);
    }
    public function actionExport()
    {
        if (!Yii::$app->user->can('月报用户数据导出')) {
            return $this->redirect(['site/login']);
        }
        $date               = Yii::$app->request->get();
        $monthlyUserList = MonthlyUsers::exportWeeklyUserList($date);
        if (!empty($monthlyUserList)) {
            return MonthlyUsers::export($monthlyUserList);
        }
    }
}