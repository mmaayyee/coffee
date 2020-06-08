<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderGoods */

$this->title = $model->goods_id;
$this->params['breadcrumbs'][] = ['label' => 'Order Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-goods-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->goods_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->goods_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'goods_id',
            'order_id',
            'user_id',
            'source_price',
            'source_status',
            'source_id',
            'source_number',
            'created_at',
            'goods_type',
            'source_type',
            'goods_source_type',
        ],
    ]) ?>

</div>
