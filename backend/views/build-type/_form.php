<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\BuildType */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="build-type-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'id')->hiddenInput()->label(false)?>
    <?=$form->field($model, 'type_name')->textInput(['maxlength' => true])?>
    <?PHP if (!$model->id): ?>
        <?=$form->field($model, 'type_code')->textInput(['maxlength' => true])?>
    <?PHP else: ?>
        <?=$form->field($model, 'type_code')->textInput(['disabled' => true])?>
    <?PHP endif?>
    <div class="form-group">
        <?=Html::submitButton('保存', ['class' => 'btn btn-success btn-block'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
