<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CoffeeLabelSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="coffee-label-search">

    <?php $form = ActiveForm::begin([
    'method' => 'get',
]);?>
    <div class="form-group  form-inline">
    <?=$form->field($model, 'equip_type_id')->widget(Select2::classname(), [
    'data'          => $equipTypeIdNameList,
    'theme'         => 'bootstrap',
    'options'       => ['placeholder' => '请选择设备类型', 'id' => 'equip_type_id'],
    'pluginOptions' => ['allowClear' => true, 'width' => '200px']])->label('设备类型')?>
    <?=$form->field($model, 'config_key')->label('参数名称')?>
    <div class="form-group">
        <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
    </div>
    </div>
    <?php ActiveForm::end();?>

</div>
