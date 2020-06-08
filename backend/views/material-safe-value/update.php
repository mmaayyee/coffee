<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\MaterialSafeValue */

$this->title = '编辑料仓预警值: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '料仓预警值管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['update', 'equipmentId' => $model->equipment_id]];
$this->params['breadcrumbs'][] = '编辑';
?>
<div class="material-safe-value-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
