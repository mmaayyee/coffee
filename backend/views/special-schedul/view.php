<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\SpecialSchedul */

$this->title                   = $model->id;
$this->params['breadcrumbs'][] = ['label' => '设备端活动', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="special-schedul-view">
    <h1><?=Html::encode($this->title)?></h1>
    <p>
        <?=$model->end_time < time() ? '' : Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])?>
        <?=$model->end_time < time() ? '' : Html::a('删除', ['delete', 'id' => $model->id], [
    'class' => 'btn btn-danger',
    'data'  => [
        'confirm' => '确定要删除吗',
        'method'  => 'get',
    ],
])?>
    </p>

    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        'id',
        'special_schedul_name',
        'start_time',
        'end_time',
        [
            'attribute' => 'state',
            'value'     => $model->getState(),
        ],
        [
            'attribute' => 'is_coupons',
            'value'     => $model->getIsCoupon(),
        ],
        [
            'attribute' => 'user_type',
            'value'     => $model->getUserType(),
        ],
        [
            'label'  => '限购方式',
            'format' => 'raw',
            'value'  => $model->getRestriction(),
        ],
        'buy_total',
    ],
])?>

</div>
