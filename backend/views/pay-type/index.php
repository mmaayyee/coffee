<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PayTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '支付方式';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pay-type-index">
    <h1><?=Html::encode($this->title)?></h1>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        [
            'attribute' => 'pay_type_id',
            'value'     => function ($model) {
                return $model->pay_type_id;
            },
        ],
        [
            'attribute' => 'weight',
            'value'     => function ($model) {
                return $model->weight;
            },
        ],
        [
            'attribute' => 'pay_type_name',
            'value'     => function ($model) {
                return $model->pay_type_name;
            },
        ],
        [
            'attribute' => 'is_open',
            'value'     => function ($model) {
                return $model->is_open;
            },
        ],
        [
            'attribute' => 'is_support_discount',
            'value'     => function ($model) {
                return $model->is_support_discount;
            },
        ],
        [
            'attribute' => 'logo_pic',
            'format'    => 'raw',
            'value'     => function ($model) {
                return "<img src='" . Yii::$app->params['fcoffeeUrl'] . $model->logo_pic . "' width=100 height=100 />";
            },
        ],
        [
            'attribute' => 'bg_pic',
            'format'    => 'raw',
            'value'     => function ($model) {
                return "<img src='" . Yii::$app->params['fcoffeeUrl'] . $model->bg_pic . "' width=100 height=100 />";
            },
        ],
        [
            'attribute' => 'discount_holicy_id',
            'value'     => function ($model) {
                return $model->discount_holicy_id ? $model->discount_holicy_id : '';
            },
        ],
        [
            'attribute' => 'create_time',
            'value'     => function ($model) {
                return $model->create_time ? date('Y-m-d H:i:s', $model->create_time) : '';
            },
        ],
        [
            'attribute' => 'update_time',
            'value'     => function ($model) {
                return $model->update_time ? date('Y-m-d H:i:s', $model->update_time) : '';
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update}',
            'buttons'  => [
                'update' => function ($url, $model, $key) {
                    return !Yii::$app->user->can('编辑支付方式') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                },

            ],
        ],
    ],
]);?>
</div>
