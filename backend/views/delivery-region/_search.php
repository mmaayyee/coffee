<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DeliveryBuildingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="delivery-building-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
 <div class="form-group  form-inline">
     <?=$form->field($model, 'region_name')?>
     <?=$form->field($model, 'business_status')->dropDownList($model->business, ['prompt' => '请选择'])->label('营业状态')?>
    <div class="form-group">
        <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
    </div>
</div>
    <?php ActiveForm::end();?>

</div>
