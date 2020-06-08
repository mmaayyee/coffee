<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CoffeeProductSetupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Coffee Product Setups';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coffee-product-setup-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Coffee Product Setup', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'setup_id',
            'product_id',
            'equip_type_id',
            'order_number',
            'water',
            // 'delay',
            // 'volume',
            // 'stir',
            // 'stock_code',
            // 'blanking',
            // 'mixing',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
