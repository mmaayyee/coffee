<?php

use backend\models\ScmSupplier;
use backend\models\ScmWarehouse;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\ScmStockSearch */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile('@web/js/jquery-1.9.1.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/bootstrap-datepicker/bootstrap-datepicker.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/bootstrap-datepicker/bootstrap-datepicker.zh-CN.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('@web/bootstrap-datepicker/bootstrap-datepicker3.min.css');
$this->registerJs('
    $("#scmstocksearch-starttime").datepicker({
        format: "yyyy-mm-dd",
        language: "zh-CN",
        autoclose:true,
    }).on("changeDate",function(){
        var startDate = $(this).val();
        var endDate = $("#scmstocksearch-endtime").val();
        if (endDate && endDate < startDate) {
            $("#scmstocksearch-endtime").val("");
        }
        $("#scmstocksearch-endtime").datepicker("setStartDate", $(this).val());

    })
    $("#scmstocksearch-endtime").datepicker({
        format: "yyyy-mm-dd",
        language: "zh-CN",
        autoclose:true,
    })
')
?>

<div class="scm-stock-search">

    <?php $form = ActiveForm::begin(['action' => ['index'], 'method' => 'get']);?>
    <div class="form-group form-inline">
        <div class="form-group">
            <label>请选择分库</label>
            <div class="select2-search">
            <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'warehouse_id',
    'data'          => ScmWarehouse::getWarehouseList('*', ['use' => ScmSupplier::MATERIAL]),
    'options'       => ['placeholder' => '请选择分库'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>
        </div>
        <?=$form->field($model, 'reason')->dropDownList($model->getCompanyReasonArr())?>
        <?=$form->field($model, 'distribution_clerk_id')->dropDownList(\common\models\WxMember::getDistributionUserArr(3))?>
        <?=$form->field($model, 'startTime')->textInput();?>

        <?=$form->field($model, 'endTime')->textInput();?>

        <div class="form-group">
            <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>

    <?php ActiveForm::end();?>

</div>
