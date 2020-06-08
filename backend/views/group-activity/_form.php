<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\GroupActivity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="group-activity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'main_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'subhead')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'begin_time')->textInput() ?>

    <?= $form->field($model, 'end_time')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'new_type')->textInput() ?>

    <?= $form->field($model, 'duration')->textInput() ?>

    <?= $form->field($model, 'price_ladder')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'drink_num')->textInput() ?>

    <?= $form->field($model, 'drink_ladder')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'original_cost')->textInput() ?>

    <?= $form->field($model, 'activity_img')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'activity_details_img')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'group_sort')->textInput() ?>

    <?= $form->field($model, 'residue_num')->textInput() ?>

    <?= $form->field($model, 'group_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
