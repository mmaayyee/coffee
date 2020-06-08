<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipLightBoxDebug */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-light-box-debug-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'debug_item')->textarea(['maxlength' => 255, 'rows' => 6]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
