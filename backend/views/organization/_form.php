<?php

use backend\models\Organization;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Organization */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="organization-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'org_name')->textInput(['maxlength' => true])?>
	<?php if (!$model->isNewRecord && $model->org_id == 1): ?>
    	<?=$form->field($model, 'parent_id')->dropDownList(Organization::getBranchArray(), ['disabled' => true])?>
    	<?=$form->field($model, 'parent_id')->hiddenInput()->label(false);?>
    <?php else: ?>
		<?=$form->field($model, 'parent_id')->dropDownList(Organization::getBranchArray())?>
	<?php endif;?>
    <?=$form->field($model, 'org_city')->textInput(['maxlength' => true])->hint('请填写城市全称，如北京市不是北京')?>
    <?=$form->field($model, 'organization_type')->dropDownList(Organization::$organizationType)?>
    <?=$form->field($model, 'is_replace_maintain')->dropDownList($model->instead)?>
    <div class="form-group">
        <?=Html::submitButton('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
