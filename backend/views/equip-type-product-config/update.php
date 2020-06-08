<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipTypeProductConfig */

$this->title = 'Update Equip Type Product Config: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Equip Type Product Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="equip-type-product-config-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
