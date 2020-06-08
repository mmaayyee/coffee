<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\LightBeltProgram */

$this->title = 'Update Light Belt Program: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Light Belt Programs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="light-belt-program-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
