<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\DistributionWaterSearch */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile('@web/js/jquery-1.9.1.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/bootstrap-datepicker/bootstrap-datepicker.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/bootstrap-datepicker/bootstrap-datepicker.zh-CN.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('@web/bootstrap-datepicker/bootstrap-datepicker3.min.css');
$this->registerJs('
    $("#distributiontask-start_delivery_time").datepicker({
        format: "yyyy-mm-dd",
        language: "zh-CN",
        autoclose:true,
    }).on("changeDate",function(){
        var startDate = $(this).val();
        var endDate = $("#distributiontask-end_delivery_time").val();
        if (endDate && endDate < startDate) {
            $("#distributiontask-end_delivery_time").val("");
        }
        $("#distributiontask-end_delivery_time").datepicker("setStartDate", $(this).val());

    })
    $("#distributiontask-end_delivery_time").datepicker({
        format: "yyyy-mm-dd",
        language: "zh-CN",
        autoclose:true,
    })
')
?>

<div class="distribution-task-search">

    <?php $form = ActiveForm::begin([
    'action' => 'index',
    'method' => 'get',
]);?>

    <div class="form-inline form-group">
        <?=$form->field($model, 'start_delivery_time')->textInput()->label('开始时间');?>

        <?=$form->field($model, 'end_delivery_time')->textInput()->label('结束时间');?>

        <div class="form-group">
            <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>

    <?php ActiveForm::end();?>

</div>
