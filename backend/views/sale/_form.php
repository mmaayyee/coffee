<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use common\models\Sale;

use yii\widgets\ActiveForm;
?>

<div class="distribution-water-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'sale_name')->textInput()?>

    <?=$form->field($model, 'sale_phone')->textInput()?>
    
    <?=$form->field($model, 'sale_email')->textInput()?>
    <?=$form->field($model, 'sale_id')->hiddenInput()->label(false)?>
    <div class="form-group">
        <?=Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
