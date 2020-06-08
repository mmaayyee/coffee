<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\UserConsume */

$this->title = 'Update User Consume: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'User Consumes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-consume-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
