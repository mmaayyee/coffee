<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipRepair */

$this->title = 'Update Equip Repair: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Equip Repairs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="equip-repair-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
