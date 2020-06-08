<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipLightBox */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-light-box-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'light_box_name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
