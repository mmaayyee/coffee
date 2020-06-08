<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ScmWarehouseEstimateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Scm Warehouse Estimates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-warehouse-estimate-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Scm Warehouse Estimate', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'author',
            'warehouse_id',
            'material_id',
            'material_out_num',
            //'status',
            //'date',
            //'time',
            //'material_type_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
