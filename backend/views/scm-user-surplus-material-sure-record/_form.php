<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ScmUserSurplusMaterialSureRecord */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="scm-user-surplus-material-sure-record-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'author')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'material_id')->textInput() ?>

    <?= $form->field($model, 'add_reduce')->textInput() ?>

    <?= $form->field($model, 'material_num')->textInput() ?>

    <?= $form->field($model, 'date')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'createTime')->textInput() ?>

    <?= $form->field($model, 'is_sure')->textInput() ?>

    <?= $form->field($model, 'sure_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
