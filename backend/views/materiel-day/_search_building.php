<?php

use backend\models\MaterielDay;
use backend\models\Organization;
use common\models\Api;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\MaterielDaySearch */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile('@web/js/jquery-1.9.1.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/bootstrap-datepicker/bootstrap-datepicker.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/bootstrap-datepicker/bootstrap-datepicker.zh-CN.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
//$this->registerJsFile('@web/js/materiel-day.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('@web/bootstrap-datepicker/bootstrap-datepicker3.min.css');
$this->registerJs('
    $("#materieldaysearch-starttime").datepicker({
        format: "yyyy-mm-dd",
        language: "zh-CN",
        autoclose:true,
    }).on("changeDate",function(){
        var startDate = $(this).val();
        var endDate = $("#materieldaysearch-endtime").val();
        if (endDate && endDate < startDate) {
            $("#materieldaysearch-endtime").val("");
        }
        $("#materieldaysearch-endtime").datepicker("setStartDate", $(this).val());

    })
    $("#materieldaysearch-endtime").datepicker({
        format: "yyyy-mm-dd",
        language: "zh-CN",
        autoclose:true,
    })
')
?>

<div class="materiel-day-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index-build'],
    'method' => 'get',
]);?>
    <div class="form-group  form-inline">
    <?=$form->field($model, 'build_name')?>
    <?=$form->field($model, 'orgId')->dropDownList(Organization::getBranchArray(0))?>
    <?=$form->field($model, 'build_type')->dropDownList(Api::getBuildTypeList())?>
    <?=$form->field($model, 'equip_type_id')->dropDownList(Api::getEquipTypeList())?>
    <?=$form->field($model, 'material_type_id')->dropDownList(Api::getMaterialTypeList())?>
    <?=$form->field($model, 'userId')->dropDownList(MaterielDay::getDistributionUserName($model->orgId))?>
    <?=$form->field($model, 'startTime')->textInput();?>
    <?=$form->field($model, 'endTime')->textInput();?>
    <?php // echo $form->field($model, 'consume_total') ?>

    <div class="form-group">
        <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
    </div>
    </div>
    <?php ActiveForm::end();?>

</div>
