<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\BuildPayTypeSearch */
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

<div class="build-pay-type-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
	<div class="form-inline">
    <?=$form->field($model, 'build_pay_type_name')?>

    <div class="form-group">
        <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        <?=Html::resetButton('重置', ['class' => 'btn btn-primary', 'id' => 'reset'])?>
    </div>
</div>
    <?php ActiveForm::end();?>

</div>
