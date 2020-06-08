<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<h3>修改咨询类型</h3>
<?php $form = ActiveForm::begin();?>
	<?=$form->field($model, 'advisory_type_id')->hiddenInput()->label(false)?>
    <?=$form->field($model, 'advisory_type_name')->label('咨询类型')?>
    <?=$form->field($model, 'is_show')->label('状态')->textInput()->radioList(['1' => '上线', '0' => '下线'])?>
    <div class="form-group">
        <?=Html::submitButton('修改', ['class' => 'btn btn-primary'])?>
    </div>
<?php ActiveForm::end();?>


