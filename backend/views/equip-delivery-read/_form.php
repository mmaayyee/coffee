<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipDeliveryRead */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-delivery-read-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'userId')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'read_status')->textInput() ?>

    <?= $form->field($model, 'read_time')->textInput(['maxlength' => 11]) ?>

    <?= $form->field($model, 'read_feedback')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'delivery_id')->textInput(['maxlength' => 11]) ?>

    <?= $form->field($model, 'read_type')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
