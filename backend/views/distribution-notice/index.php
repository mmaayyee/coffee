<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\Manager;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionNoticeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '配送通知';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-notice-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Yii::$app->user->can('添加配送通知') ? Html::a('添加配送通知', ['create'], ['class' => 'btn btn-success']) : ''; ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'sender',
                'format'=>'text',
                'value' => function ($model){
                    return Manager::getUserName($model->sender);
                },
            ],
            'content',
            [
                'attribute' => 'create_time',
                'format'=>'text',
                'value' => function ($model){
                    return date("Y-m-d H:i:s", $model->create_time);
                },
            ],
            'send_num',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                    'view' =>function ($url, $model, $key) {               
                        return !\Yii::$app->user->can('查看配送通知') ?  '' : Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url);
                    },
                ],
            ],

        ],
    ]); ?>
</div>
