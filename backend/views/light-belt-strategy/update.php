<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\LightBeltStrategy */

$this->title = 'Update Light Belt Strategy: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Light Belt Strategies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="light-belt-strategy-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'lightBeltList'=>$lightBeltList,
    ]) ?>

</div>
