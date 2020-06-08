<?php

use backend\models\ScmEquipType;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ProductGroupStockInfoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-group-stock-info-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-group form-inline">
        <?=$form->field($model, 'product_group_stock_name')?>

        <?=$form->field($model, 'equip_type_id')->dropDownList(ScmEquipType::getEquipTypeIdNameArr())?>

        <div class="form-group">
            <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        </div>

    </div>
    <?php ActiveForm::end();?>

</div>
