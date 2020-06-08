<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EquipTraffickingSuppliers */

$this->title = '修改投放商信息: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '投放商列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新数据';
?>
<div class="equip-trafficking-suppliers-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
