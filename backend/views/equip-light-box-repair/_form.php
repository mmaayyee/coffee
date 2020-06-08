<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-light-box-repair-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'build_id')->textInput() ?>

    <?= $form->field($model, 'supplier_id')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'remark')->textInput(['maxlength' => 500]) ?>

    <?= $form->field($model, 'process_result')->textInput() ?>

    <?= $form->field($model, 'process_time')->textInput() ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
