<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\LightProgramAssoc */

$this->title = 'Update Light Program Assoc: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Light Program Assocs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="light-program-assoc-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
