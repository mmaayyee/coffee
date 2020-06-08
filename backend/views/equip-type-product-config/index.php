<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipTypeProductConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Equip Type Product Configs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-type-product-config-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Equip Type Product Config', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'equip_type_id',
            'product_id',
            'cf_choose_sugar',
            'half_sugar',
            // 'full_sugar',
            // 'brew_up',
            // 'brew_down',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
