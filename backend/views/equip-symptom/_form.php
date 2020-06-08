<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipSymptom */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-symptom-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'symptom')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
