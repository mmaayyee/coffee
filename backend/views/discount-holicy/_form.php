<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DiscountHolicy */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="discount-holicy-form">

    <?php $form = ActiveForm::begin();?>
    <?=$form->field($model, 'holicy_name');?>
    <?php if ($model->isNewRecord): ?>
        <?=$form->field($model, 'holicy_payment')->dropDownList($model->getPaymentList(1));?>
    <?php else: ?>
        <?=$form->field($model, 'holicy_payment')->dropDownList($model->getPaymentList(), ['disabled' => true]);?>
        <?=$form->field($model, 'holicy_payment')->hiddenInput()->label(false);?>
    <?php endif;?>
    <?=$form->field($model, 'holicy_type')->dropDownList($model->holicy_type_list);?>
    <?=$form->field($model, 'holicy_price');?>
    <?=$form->field($model, 'holicy_id')->hiddenInput()->label(false);?>
    <?=$form->field($model, 'holicy_introduction')->textInput(['maxLength' => '3']);?>
    <div class="form-group">
        <?=Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>
    <?php ActiveForm::end();?>

</div>
