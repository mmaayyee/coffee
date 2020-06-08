<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ScmTotalInventory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="scm-total-inventory-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'warehouse_id')->textInput(['maxlength' => 11])?>

    <?=$form->field($model, 'material_id')->textInput(['maxlength' => 11])?>

    <?=$form->field($model, 'total_number')->textInput(['maxlength' => 11])?>

    <div class="form-group">
        <?=Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
