<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\CoffeeProductSetup */

$this->title = $model->setup_id;
$this->params['breadcrumbs'][] = ['label' => 'Coffee Product Setups', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coffee-product-setup-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->setup_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->setup_id], [
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
            'setup_id',
            'product_id',
            'equip_type_id',
            'order_number',
            'water',
            'delay',
            'volume',
            'stir',
            'stock_code',
            'blanking',
            'mixing',
        ],
    ]) ?>

</div>
