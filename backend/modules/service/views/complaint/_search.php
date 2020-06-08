<?php

use janisto\timepicker\TimePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
$this->registerJsFile('/assets/f0269b11/jquery.min.js', ['position' => View::POS_HEAD]);
$this->registerJs('
    $("#search").click(function(){
        $("#complaintForm").attr("action","' . Url::to(["/service/complaint/index"]) . '");
        $("#complaintForm").submit();
    });
    $("#export").click(function(){
        $("#complaintForm").attr("action","' . Url::to(["/service/complaint/export"]) . '");
        $("#complaintForm").submit();
    });
    var orgBuildingList = ' . Json::encode($orgBuildingList) . ';
    $("#complaintsearch-org_id").change(function() {
      var orgId = $(this).val();
      getBuilding(orgId);
    });

    function getBuilding(orgId){
        $("#complaintsearch-building_id").html("<option value=\"\">请搜索指定点位</option>");
        if (orgId>1){
            var buildingList = orgBuildingList[orgId];
        } else {
            var buildingList = ' . Json::encode($buildingList) . '
        }
        for(var i in buildingList){
            $("#complaintsearch-building_id").append("<option value=\'"+i+"\'>"+buildingList[i]+"</option>");
        }
    }
');
?>

<style>
    .btn-primary {
        width: 100px;
    }
    .btn-success {
        margin-bottom: 0px;
    }
</style>


<div class="customer-service-complaint-search">
    <div class="form-group  form-inline">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'id'     => 'complaintForm',
]);?>

        <?=$form->field($model, 'complaint_code')?>
        <?=$form->field($model, 'manager_name')?>
        <?php echo $form->field($model, 'order_code') ?>
        <?php echo $form->field($model, 'register_mobile')->label('手机号码') ?>
    <?php echo $form->field($model, 'advisory_type_id')->dropDownList($advisoryList, ['prompt' => '请选择']) ?>
    <?php echo $form->field($model, 'question_type_id')->dropDownList($questionList, ['prompt' => '请选择']) ?>
    <?=$form->field($model, 'customer_type')->dropDownList(['' => '请选择'] + $model::$customerTypeList)->label('客户区分')?>
    <?php echo $form->field($model, 'solution_id')->dropDownList($solution, ['prompt' => '请选择']) ?>
        <?php echo $form->field($model, 'process_status')->dropDownList($processStatus, ['prompt' => '请选择'])->label('处理进度') ?>
        <?=$form->field($model, 'begin_time')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ],
]);
?>
        <?=$form->field($model, 'end_time')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'hour'       => 23,
        'minute'     => 59,
        'second'     => 59,
        'showSecond' => true,
    ],
]);
?>
        <div class="form-group">
            <label >机构名称</label>
                <div class="select2-search">
                    <?=Select2::widget([
    'model'         => $model,
    'attribute'     => 'org_id',
    'data'          => $orgList,
    'options'       => ['multiple' => false, 'placeholder' => '请搜索指定分公司'],
    'pluginOptions' => ['allowClear' => true]]);?>
                </div>
            <label >点位名称</label>
            <div class="select2-search">
                <?=Select2::widget([
    'model'         => $model,
    'attribute'     => 'building_id',
    'data'          => $orgBuildingList[$model->org_id] ?? $buildingList,
    'options'       => ['multiple' => false, 'placeholder' => '请搜索指定楼宇'],
    'pluginOptions' => ['allowClear' => true]]);?>
            </div>
        </div>

    <div class="form-group">
        <?=Html::Button('检索', ['class' => 'btn btn-primary', 'id' => 'search'])?>
        <?php if (Yii::$app->user->can('客诉记录导出')): ?>
        <?=Html::Button('导出', ['class' => 'btn btn-success', 'id' => 'export'])?>
        <?php endif;?>
    </div>

    <?php ActiveForm::end();?>
    </div>
</div>