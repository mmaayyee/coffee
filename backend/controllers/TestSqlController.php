<?php
namespace backend\controllers;
use Yii;
use yii\web\Controller;

class TestSqlController extends Controller{


    public function actionIndex()
    {
        Yii::$app->db->createCommand()->insert('tsql', [
            'name' => 'NING',
            'age' => '10',
        ])->execute();
}
}