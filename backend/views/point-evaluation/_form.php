<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PointEvaluation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="point-evaluation-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'point_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'org_id')->textInput() ?>

    <?= $form->field($model, 'point_applicant')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'point_position')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'point_level')->textInput() ?>

    <?= $form->field($model, 'cooperate')->textInput() ?>

    <?= $form->field($model, 'point_status')->textInput() ?>

    <?= $form->field($model, 'build_type_id')->textInput() ?>

    <?= $form->field($model, 'build_record_id')->textInput() ?>

    <?= $form->field($model, 'point_basic_info')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'point_score_info')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'point_other_info')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'point_licence_pic')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'point_position_pic')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'point_company_pic')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'point_plan')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
