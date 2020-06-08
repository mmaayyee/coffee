<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="equip-abnormal-task-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'equip_type_id')->widget(Select2::classname(), [
    'data'          => $equipTypeIdNameList,
    'options'       => ['placeholder' => '请选择设备类型', 'id' => 'equip_type_id'],
    'pluginOptions' => ['allowClear' => true]])->label('设备类型')?>

    <?=$form->field($model, 'config_key')->textInput()?>

    <?=$form->field($model, 'config_value')->textInput()?>
    <?=$form->field($model, 'setup_id')->hiddenInput()->label(false)?>

    <div class="form-group">
        <?=Html::submitButton('保存', ['class' => 'btn btn-success'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
