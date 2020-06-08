<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '系统设置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sysconfig-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            //'config_key',
            'config_desc',
            'config_value',


            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
                 'buttons' => [
                    'update' =>function ($url, $model, $key) {                  
                            return !\Yii::$app->user->can('编辑系统设置') ?  '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                        },
                ], 
            ],
        ],
    ]); ?>

</div>
