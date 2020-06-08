<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipDebug */

$this->title = '修改设备调试项: ' . ' ' . $model->Id;
$this->params['breadcrumbs'][] = ['label' => '设备调试项管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Id, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="equip-debug-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
