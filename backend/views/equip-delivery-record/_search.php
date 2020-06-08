<?php

use common\models\Building;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipDeliveryRecordSearch */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile('@web/js/jquery-1.9.1.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/bootstrap-datepicker/bootstrap-datepicker.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/bootstrap-datepicker/bootstrap-datepicker.zh-CN.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('@web/bootstrap-datepicker/bootstrap-datepicker3.min.css');
$this->registerJs('
    $("#equipdeliveryrecordsearch-start_time").datepicker({
        format: "yyyy-mm-dd",
        language: "zh-CN",
        autoclose:true,
    }).on("changeDate",function(){
        var startDate = $(this).val();
        var endDate = $("#equipdeliveryrecordsearch-end_time").val();
        if (endDate && endDate < startDate) {
            $("#equipdeliveryrecordsearch-end_time").val("");
        }
        $("#equipdeliveryrecordsearch-end_time").datepicker("setStartDate", $(this).val());

    })
    $("#equipdeliveryrecordsearch-end_time").datepicker({
        format: "yyyy-mm-dd",
        language: "zh-CN",
        autoclose:true,
    });
')
?>

<div class="equip-delivery-record-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-group form-inline">
        <div class="form-group">
            <label>请选择楼宇</label>
            <div class="select2-search" >
            <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'build_id',
    'data'          => Building::getOperationBuildList(2),
    'options'       => ['placeholder' => '请选择楼宇'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>
        </div>
        <?=$form->field($model, 'start_time')->textInput()->label('开始投放时间')?>
        <?=$form->field($model, 'end_time')->textInput()->label('结束投放时间')?>
        <?=$form->field($model, 'equip_code')->textInput()->label('设备编号')?>
        <?=$form->field($model, 'factory_code')->textInput()->label('出厂编号')?>

        <?=Html::hiddenInput('type', Yii::$app->request->get('type', 0))?>

        <div class="form-group">
            <?=Html::submitButton('搜索', ['class' => 'btn btn-primary', "name" => "export-btn", 'value' => 0])?>
            <?=Html::submitButton('导出', ['class' => 'btn btn-primary', "name" => "export-btn", 'value' => 1])?>
        </div>
    </div>
    <?php ActiveForm::end();?>

</div>
