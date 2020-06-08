<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<h3>修改解决方案</h3>
<?php $form = ActiveForm::begin();?>
	<?=$form->field($model, 'solution_id')->hiddenInput()->label(false)?>
    <?=$form->field($model, 'solution_name')->label('解决方案')?>
    <?=$form->field($model, 'is_show')->label('状态')->textInput()->radioList(['1' => '上线', '0' => '下线'])?>
    <div class="form-group">
        <?=Html::submitButton('修改', ['class' => 'btn btn-primary'])?>
    </div>
<?php ActiveForm::end();?>


