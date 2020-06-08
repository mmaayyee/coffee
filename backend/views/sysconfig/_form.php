<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Sysconfig */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sysconfig-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <?= $form->field($model, 'config_desc')->textInput(['maxlength' => 255,'readonly'=>true]) ?>
    <?= $form->field($model, 'config_value')->textInput(['maxlength' => 255]) ?>



    <div class="form-group">
        <?= Html::submitButton( '确定', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
