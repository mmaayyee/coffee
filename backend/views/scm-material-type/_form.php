<?php

use backend\models\ScmMaterialType;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ScmMaterialType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="scm-material-type-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'material_type_name')->textInput(['maxlength' => true])?>

    <?=$form->field($model, 'unit')->textInput(['maxlength' => true])?>

    <?=$form->field($model, 'weight_unit')->textInput(['maxlength' => true])?>

    <?=$form->field($model, 'spec_unit')->textInput(['maxlength' => true])?>

    <?=$form->field($model, 'new_spec_unit')->textInput(['maxlength' => true])?>

    <?=$form->field($model, 'type')->dropDownList(ScmMaterialType::$type)?>

    <div class="form-group">
        <?=Html::submitButton('确定', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
