<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\EquipTraffickingSuppliers */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '投放商列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-trafficking-suppliers-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('更新', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确定要删除该投放商吗?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'attribute' => 'userid',
                'value' => $model->user ? $model->user->name : ''
            ],
            'mobile',
            'email:email',
            'org_id'
        ],
    ]) ?>

</div>
