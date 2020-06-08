<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipTypeProgressProductAssoc */

$this->title =  $model->product_name;
$this->params['breadcrumbs'][] = ['label' => '进度条管理', 'url' => ['index']];
?>
<div class="equip-type-progress-product-assoc-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?php if (Yii::$app->user->can('编辑进度条')) :?>
            <?= Html::a('编辑', ['update', 'id' => $model->product_id], ['class' => 'btn btn-primary']) ?>
        <?php endif;?>
        <?php if (Yii::$app->user->can('删除进度条')) :?>
            <?= Html::a('删除', ['delete', 'id' => $model->product_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => '确定删除吗?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif;?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'product_name',
            [
                'label' => '进度条信息',
                'format'    => 'raw',
                'value'     => $model->getequipTypeDetailsTable(),
            ]
        ],
    ]) ?>

</div>
