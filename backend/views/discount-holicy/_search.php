<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DiscountHolicySearch */
/* @var $form yii\widgets\ActiveForm */
$this->registerJs('
    $("#reset").click(function(){
        $("#w0 input").each(function(){
            $(this).attr("value","");
        });

         $("#w0 select").each(function(){
            $(this).find("option").each(function(){
                $(this).removeAttr("selected")
            });
        });
    })
')
?>

<div class="discount-holicy-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-inline">
        <?=$form->field($model, 'holicy_name')?>

        <?=$form->field($model, 'holicy_payment')->dropDownList(['' => '全部'] + $payTypeList)?>

        <?=$form->field($model, 'holicy_type')->dropDownList($model->holicy_type_list)?>

    <div class="form-group">
        <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
        <?=Html::resetButton('重置', ['class' => 'btn btn-primary', 'id' => 'reset'])?>
    </div>
    </div>
    <?php ActiveForm::end();?>

</div>
