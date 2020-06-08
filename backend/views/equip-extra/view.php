<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\EquipExtra;

/* @var $this yii\web\View */
/* @var $model common\models\EquipExtra */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '设备附件', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-extra-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'extra_name',
            [
                'attribute' => 'is_del',
                'value' => $model->is_del ? EquipExtra::$status[$model->is_del] : ''
            ]
        ],
    ]) ?>

</div>
