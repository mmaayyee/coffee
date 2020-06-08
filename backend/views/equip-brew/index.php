<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipBrewSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '设备冲泡器时间';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-brew-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        'equip_code',
        [
            'attribute' => 'build_name',
            'value'     => function ($model) {
                return !isset($model->equip->build->name) ? '' : $model->equip->build->name;
            },
        ],
        [
            'attribute' => 'product_id',
            'value'     => function ($model) use ($productIdNameArr) {
                return !isset($productIdNameArr[$model->product_id]) ? '' : $productIdNameArr[$model->product_id];
            },
        ],
        'brew_time',
        [
            'attribute' => 'create_time',
            'value'     => function ($model) {
                return !$model->create_time ? '' : date('Y-m-d H:i:s', $model->create_time);
            },
        ],

        // ['class' => 'yii\grid\ActionColumn'],
    ],
]);?>
</div>
