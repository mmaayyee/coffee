<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Api;

/* @var $this yii\web\View */
/* @var $model app\models\ClearEquip */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="clear-equip-form">

    <?php $form = ActiveForm::begin(); ?>

	<?php if($model->isNewRecord):?>
    
    <?= $form->field($model, 'code')->dropDownList(Api::getClearTypeList(1)) ?>

	 <?= $form->field($model, 'equip_type_id')->dropDownList(Api::getEquipTypeList()) ?>

	<?= $form->field($model, 'remark')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'consum_total')->textInput() ?>

	<?php else: ?>

	


    <?= $form->field($model, 'remark')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'consum_total')->textInput() ?>

	<?=$form->field($model, 'clear_equip_id')->hiddenInput()->label(false);?>

	<?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
