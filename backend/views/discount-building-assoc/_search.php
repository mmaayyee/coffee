<?php
use backend\models\DiscountHolicy;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\BuildingSearch */
/* @var $form yii\widgets\ActiveForm */
$disModel = new DiscountHolicy();
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

<div class="discount-building-assoc-search">

    <?php $form = ActiveForm::begin(['action' => ['index'], 'method' => 'get']);?>
    <div class="form-inline">
        <?=$form->field($model, 'holicy_payment')->dropDownList(['' => '请选择'] + $payTypeList)?>
    	<?=$form->field($model, 'build_pay_type_name')->widget(\yii\jui\AutoComplete::classname(), ['clientOptions' => ['source' => $buildPayTypeNameList], 'options' => ['class' => 'form-control']])?>
        <?=$form->field($model, 'holicy_type')->dropDownList($disModel->holicy_type_list)?>
        <?=$form->field($model, 'build_name')?>
        <div class="form-group">
            <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
            <?=Html::resetButton('重置', ['class' => 'btn btn-primary', 'id' => 'reset'])?>
        </div>
    </div>
    <?php ActiveForm::end();?>

</div>
