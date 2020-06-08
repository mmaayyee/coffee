<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\ClearEquip */

$this->title = $model->clear_equip_id;
$this->params['breadcrumbs'][] = ['label' => 'Clear Equips', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clear-equip-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->clear_equip_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->clear_equip_id], [
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
            'clear_equip_id',
            'equip_type_id',
            'code',
            'remark',
            'consum_total',
        ],
    ]) ?>

</div>
