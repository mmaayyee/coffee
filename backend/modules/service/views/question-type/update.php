<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<h3>修改问题类型</h3>
<?php $form = ActiveForm::begin();?>
	<?=$form->field($model, 'question_type_id')->hiddenInput()->label(false)?>
    <?=$form->field($model, 'question_type_name')->label('名称')?>
    <?=$form->field($model, 'is_show')->label('状态')->textInput()->radioList(['1' => '上线', '0' => '下线'])?>
	<?=$form->field($model, 'advisory_type_id')->dropDownList($questionTypes)->label('咨询类型');?>
    <div class="form-group">
        <?=Html::submitButton('修改', ['class' => 'btn btn-primary'])?>
    </div>
<?php ActiveForm::end();?>


