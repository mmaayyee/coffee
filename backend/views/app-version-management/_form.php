<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\AppVersionManagement */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="app-version-management-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'big_screen_version')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'small_screen_version')->textInput(['maxlength' => 100]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
