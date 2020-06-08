<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\UserRefund */

$this->title = 'Update User Refund: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'User Refunds', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-refund-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
