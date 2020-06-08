<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = '修改申请';
?>
<div class="scm-user-surplus-material-sure-record-update">

    <div class="scm-user-surplus-material-sure-record-form">

        <?php $form = ActiveForm::begin();?>

        <?=$form->field($model, 'material_type_id')->hiddenInput()->label(false)?>

        <?=$form->field($model, 'supplier_id')->hiddenInput()->label(false)?>

        <?=$form->field($model, 'add_reduce')->dropDownList($model::$addReduce)->label('加还是减')?>

        <?=$form->field($model, 'material_gram')->textInput()->label('散料重量(克)')?>

        <?=$form->field($model, 'reason')->textArea(['rows' => '8'])?>
        <div class="form-group">
            <?=Html::submitButton('提交', ['class' => 'btn btn-primary'])?>
        </div>

        <?php ActiveForm::end();?>

    </div>


</div>
