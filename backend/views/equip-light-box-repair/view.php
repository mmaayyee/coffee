<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Equip Light Box Repairs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-light-box-repair-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'build_id',
            'supplier_id',
            'remark',
            'process_result',
            'process_time:datetime',
            'create_time:datetime',
        ],
    ]) ?>

</div>
