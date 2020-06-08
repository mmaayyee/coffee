<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\GroupBeginTeam */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="group-begin-team-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'group_id')->textInput() ?>

    <?= $form->field($model, 'main_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'u_id')->textInput() ?>

    <?= $form->field($model, 'group_booking_status')->textInput() ?>

    <?= $form->field($model, 'begin_datatime')->textInput() ?>

    <?= $form->field($model, 'end_datatime')->textInput() ?>

    <?= $form->field($model, 'group_booking_num')->textInput() ?>

    <?= $form->field($model, 'group_booking_price')->textInput() ?>

    <?= $form->field($model, 'drink_ladder')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'begin_time')->textInput() ?>

    <?= $form->field($model, 'activity_img')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'activity_details_img')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'subhead')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'original_cost')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
