<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\PayType */

$this->title                   = $model->pay_type_id;
$this->params['breadcrumbs'][] = ['label' => 'Pay Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pay-type-view">

    <h1><?=Html::encode($this->title)?></h1>
    <p>
        <?=Html::a('Update', ['update', 'id' => $model->pay_type_id], ['class' => 'btn btn-primary'])?>
    </p>
    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        'pay_type_id',
        'pay_type_name',
        'log_pic',
        'bg_pic',
        'is_open',
        'is_support_discount',
        'discount_holicy_id',
        [
            'attribute' => 'discount_holicy_id',
            'value'     => $model->discount_holicy_id ? $model->create_time : '',
        ],
        'weight',
        [
            'attribute' => 'create_time',
            'value'     => $model->create_time ? date('Y-m-d H:i:s', $model->create_time) : '',
        ],
        [
            'attribute' => 'update_time',
            'value'     => $model->update_time ? date('Y-m-d H:i:s', $model->update_time) : '',
        ],
    ],
])?>

</div>
