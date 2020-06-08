<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\EquipExtra;

/* @var $this yii\web\View */
/* @var $model common\models\EquipExtra */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-extra-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'extra_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_del')->dropDownList(EquipExtra::$status) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '编辑', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
