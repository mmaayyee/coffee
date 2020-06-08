<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\BuildingRecord */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="building-record-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'creator_id')->textInput() ?>

    <?= $form->field($model, 'creator_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'org_id')->textInput() ?>

    <?= $form->field($model, 'building_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'build_type_id')->textInput() ?>

    <?= $form->field($model, 'building_status')->textInput() ?>

    <?= $form->field($model, 'province')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'area')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'floor')->textInput() ?>

    <?= $form->field($model, 'business_circle')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'build_longitude')->textInput() ?>

    <?= $form->field($model, 'build_latitude')->textInput() ?>

    <?= $form->field($model, 'contact_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'contact_tel')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'build_public_info')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'build_special_info')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'build_appear_pic')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'build_hall_pic')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
