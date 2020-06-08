<?php

use backend\models\Organization;
use common\models\WxMember;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$organization = Organization::getBranchArray();
unset($organization[1]);
/* @var $this yii\web\View */
/* @var $model backend\models\scmwarehouseout */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile('@web/js/jquery-1.9.1.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/bootstrap-datepicker/bootstrap-datepicker.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/bootstrap-datepicker/bootstrap-datepicker.zh-CN.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('@web/bootstrap-datepicker/bootstrap-datepicker3.min.css');
$this->registerJs('
    $("#scmwarehouseout-starttime").datepicker({
        format: "yyyy-mm-dd",
        language: "zh-CN",
        autoclose:true,
    }).on("changeDate",function(){
        var startDate = $(this).val();
        var endDate = $("#scmwarehouseout-endtime").val();
        if (endDate && endDate < startDate) {
            $("#scmwarehouseout-endtime").val("");
        }
        $("#scmwarehouseout-endtime").datepicker("setStartDate", $(this).val());

    })
    $("#scmwarehouseout-endtime").datepicker({
        format: "yyyy-mm-dd",
        language: "zh-CN",
        autoclose:true,
    })
')
?>

<div class="distribution-water-search">

    <?php $form = ActiveForm::begin([
    'action' => $action,
    'method' => 'get',
]);?>

    <div class="form-inline form-group">
        <?=$managerOrgId == 1 ? $form->field($model, 'orgId')->dropDownList($organization) : '';?>
        <div class="form-group">
            <label>请选择人员</label>
            <div class="select2-search">
            <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'author',
    'data'          => WxMember::getDistributionUserArr(3, $model->orgId),
    'options'       => ['placeholder' => '请选择人员'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>
        </div>

        <?=$form->field($model, 'startTime')->textInput();?>

        <?=$form->field($model, 'endTime')->textInput();?>


        <div class="form-group">
            <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>

    <?php ActiveForm::end();?>

</div>
