<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\EquipExtra */

$this->title = '编辑设备附件: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '设备附件', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="equip-extra-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
