<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderInfo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-info-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'pay_type')->textInput() ?>

    <?= $form->field($model, 'order_status')->textInput() ?>

    <?= $form->field($model, 'order_type')->textInput() ?>

    <?= $form->field($model, 'total_fee')->textInput() ?>

    <?= $form->field($model, 'actual_fee')->textInput() ?>

    <?= $form->field($model, 'gift_fee')->textInput() ?>

    <?= $form->field($model, 'discount_fee')->textInput() ?>

    <?= $form->field($model, 'order_cups')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'pay_at')->textInput() ?>

    <?= $form->field($model, 'order_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'paid')->textInput() ?>

    <?= $form->field($model, 'changes')->textInput() ?>

    <?= $form->field($model, 'is_company')->textInput() ?>

    <?= $form->field($model, 'equipment_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_version')->textInput() ?>

    <?= $form->field($model, 'beans_num')->textInput() ?>

    <?= $form->field($model, 'beans_amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_refunds')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
