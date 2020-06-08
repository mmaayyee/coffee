<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ActiveBuySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="active-buy-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>

    <div class="form-group  form-inline">
    <?=$form->field($model, 'cf_product_name')->textInput()?>
    <?=$form->field($model, 'cf_texture')->textInput()?>
    <?=$form->field($model, 'cf_product_hot')->dropDownList($model->getTypeArray())?>
    <?=$form->field($model, 'cf_product_status')->dropDownList($model->getStatusArray())?>
    <?=$form->field($model, 'cf_product_type')->dropDownList($model->productType)?>
    <?=$form->field($model, 'equipment_type')->dropDownList($model->getEquipmentType())?>
    <div class="form-group">
        <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
    </div>
    </div>
    <?php ActiveForm::end();?>

</div>
