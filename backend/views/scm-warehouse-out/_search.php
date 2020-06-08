<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\controllers\ScmWarehouseOutSearch */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile('@web/js/jquery-1.9.1.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/bootstrap-datepicker/bootstrap-datepicker.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/bootstrap-datepicker/bootstrap-datepicker.zh-CN.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('@web/bootstrap-datepicker/bootstrap-datepicker3.min.css');
$this->registerJs('
    $("#scmwarehouseoutsearch-starttime").datepicker({
        format: "yyyy-mm-dd",
        language: "zh-CN",
        autoclose:true,
    }).on("changeDate",function(){
        var startDate = $(this).val();
        var endDate = $("#scmwarehouseoutsearch-endtime").val();
        if (endDate && endDate < startDate) {
            $("#scmwarehouseoutsearch-endtime").val("");
        }
        $("#scmwarehouseoutsearch-endtime").datepicker("setStartDate", $(this).val());

    })
    $("#scmwarehouseoutsearch-endtime").datepicker({
        format: "yyyy-mm-dd",
        language: "zh-CN",
        autoclose:true,
    })
')
?>

<div class="scm-warehouse-out-search">

    <?php $form = ActiveForm::begin(['method' => 'get']);?>

    <div class="form-group form-inline">

         <div class="form-group">
            <label>请选择领料人</label>
            <div class="select2-search" >
            <?php echo \kartik\select2\Select2::widget([
    'model'         => $model,
    'attribute'     => 'author',
    'data'          => \common\models\WxMember::getDistributionUserArr(3),
    'options'       => ['multiple' => false, 'placeholder' => '请选择领料人'],
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
