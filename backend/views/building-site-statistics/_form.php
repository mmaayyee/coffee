<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\BuildingSiteStatistics */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="building-site-statistics-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'build_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'operation_state')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sales_volumes')->textInput() ?>

    <?= $form->field($model, 'equipment_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'operation_mode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sales_amount')->textInput() ?>

    <?= $form->field($model, 'create_date')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
