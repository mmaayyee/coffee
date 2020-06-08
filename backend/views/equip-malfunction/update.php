<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipMalfunction */

$this->title = '更新故障信息: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '故障信息管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="equip-malfunction-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
