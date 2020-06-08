<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipDelivery */

$this->title = '修改投放单: ' . ' ' . $model->Id;
$this->params['breadcrumbs'][] = ['label' => '销售投放管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="equip-delivery-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
