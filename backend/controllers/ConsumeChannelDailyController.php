<?php

namespace backend\controllers;

use backend\models\ConsumeChannelDaily;
use Yii;
use yii\web\Controller;

/**
 * CoffeeProductSetupController implements the CRUD actions for CoffeeProductSetup model.
 */
class ConsumeChannelDailyController extends Controller
{
    public function actionIndex()
    {
        if (!Yii::$app->user->can('渠道日报')) {
            return $this->redirect(['site/login']);
        }
        if (!Yii::$app->user->can('渠道日报查看')){
            return $this->redirect(['site/login']);
        }
        if (!Yii::$app->user->can('渠道日报导出') && !Yii::$app->user->can('渠道日报检索')) {
                return $this->render('index',['rules' => 0]);
        } elseif(!Yii::$app->user->can('渠道日报检索'))  {
            return $this->render('index', [
                'rules' => 1,
            ]);
        }elseif(!Yii::$app->user->can('渠道日报导出')){
            return $this->render('index', [
                'rules' => 2,
            ]);
        }
        return $this->render('index',['rules' => '']);
    }

    public function actionExport()
    {
        if (!Yii::$app->user->can('渠道日报导出')) {
            return $this->redirect(['site/login']);
        }
        $date               = Yii::$app->request->get('date');
        $consumeChanneDaily = ConsumeChannelDaily::exportUserConsumeList($date);
        if (!empty($consumeChanneDaily)) {
            return ConsumeChannelDaily::export($consumeChanneDaily);
        }
    }
}
