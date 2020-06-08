<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipWarn */

$this->title = '编辑异常报警设置: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '异常报警设置', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="equip-warn-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
