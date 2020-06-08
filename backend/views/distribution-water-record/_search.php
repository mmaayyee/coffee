<?php

use backend\models\DistributionWater;
use backend\models\Organization;
use backend\models\ScmSupplier;
use kartik\select2\Select2;
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
    $("#distributionwatersearch-starttime").datepicker({
        format: "yyyy-mm-dd",
        language: "zh-CN",
        autoclose:true,
    }).on("changeDate",function(){
        var startDate = $(this).val();
        var endDate = $("#distributionwatersearch-endtime").val();
        if (endDate && endDate < startDate) {
            $("#distributionwatersearch-endtime").val("");
        }
        $("#distributionwatersearch-endtime").datepicker("setStartDate", $(this).val());

    })
    $("#distributionwatersearch-endtime").datepicker({
        format: "yyyy-mm-dd",
        language: "zh-CN",
        autoclose:true,
    })
')
?>

<div class="distribution-water-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-inline form-group">
        <?=$model->managerOrgId == 1 ? $form->field($model, 'orgId')->dropDownList(Organization::getOrgIdNameArr(['>', 'org_id', 1]))->label('请选择分公司') : ''?>

        <div class="form-group">
            <label>请选择楼宇</label>
            <div class="select2-search" >
                <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'build_id',
    'data'          => DistributionWater::getDistributionWaterBuildList(2, $model->orgId),
    'options'       => ['placeholder' => '请选择楼宇'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>
        </div>

        <?=$form->field($model, 'supplier_id')->dropDownList(ScmSupplier::getOrgWaterList())?>

        <?=$form->field($model, 'startTime')->textInput();?>
        <?=$form->field($model, 'endTime')->textInput();?>

        <div class="form-group">
            <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>

    <?php ActiveForm::end();?>

</div>
