<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\LightBeltProductGroup */

$this->title = 'Update Light Belt Product Group: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Light Belt Product Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="light-belt-product-group-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
