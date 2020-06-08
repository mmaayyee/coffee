<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipSymptom */

$this->title = '修改故障现象: ' . $model->symptom;
$this->params['breadcrumbs'][] = ['label' => '故障现象', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="equip-symptom-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
