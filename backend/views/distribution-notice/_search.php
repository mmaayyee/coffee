<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\DistributionNoticeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="distribution-notice-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'Id') ?>

    <?= $form->field($model, 'create_time') ?>

    <?= $form->field($model, 'sender') ?>

    <?= $form->field($model, 'content') ?>

    <?= $form->field($model, 'send_num') ?>

    <?php // echo $form->field($model, 'receiver') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
