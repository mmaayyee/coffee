<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\BuildingRecord */

$this->title = 'Update Building Record: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Building Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="building-record-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
