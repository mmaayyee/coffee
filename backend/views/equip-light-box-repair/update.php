<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Update Equip Light Box Repair: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Equip Light Box Repairs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="equip-light-box-repair-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
