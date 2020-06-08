<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipDebug */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-debug-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'debug_item')->textarea(['maxlength' => 1000, 'rows' => 6]) ?>

    <?= $form->field($model, 'equip_type_id')->dropDownList(\backend\models\ScmEquipType::getEquipTypeIdNameArr()) ?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
