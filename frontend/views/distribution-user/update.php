<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = '修改申请';
?>
<div class="scm-user-surplus-material-sure-record-update">

<div class="scm-user-surplus-material-sure-record-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'material_id')->hiddenInput()->label(false)?>

    <?=$form->field($model, 'add_reduce')->dropDownList($model::$addReduce)->label('加还是减')?>

    <?=$form->field($model, 'material_num')->textInput()->label('物料数量('.$model->material->materialType->unit.')')?>

    <?=$form->field($model, 'reason')->textArea(['rows' => '8'])?>
    <div class="form-group">
        <?=Html::submitButton('提交', ['class' => 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>


</div>
