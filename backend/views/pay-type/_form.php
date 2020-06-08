<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PayType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pay-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pay_type_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'log_pic')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bg_pic')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_open')->textInput() ?>

    <?= $form->field($model, 'is_support_discount')->textInput() ?>

    <?= $form->field($model, 'discount_holicy_id')->textInput() ?>

    <?= $form->field($model, 'weight')->textInput() ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <?= $form->field($model, 'update_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
