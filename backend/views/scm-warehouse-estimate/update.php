<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ScmWarehouseEstimate */

$this->title = 'Update Scm Warehouse Estimate: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Scm Warehouse Estimates', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="scm-warehouse-estimate-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
