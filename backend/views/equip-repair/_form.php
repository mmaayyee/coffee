<?php

use backend\models\EquipSymptom;
use common\models\Building;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipTask */
/* @var $form yii\widgets\ActiveForm */

$this->registerJs('
$.get(
    "' . Yii::$app->request->baseUrl . '/equip-task/ajax-get-build",
    {build_id:$("#build_id").val()},
    function(data) {
        if (data) {
            $("#build_name").html(data.build_name);
            $("#equip_code").html(data.equip_code);
            $("#equip_model").html(data.equip_type);
            $("#equip_id").val(data.equip_id);
            $("#building_name").val(data.build_name);
            $("#build_address").val(data.build_address);
        } else {
            $("#build_name").html("");
            $("#equip_code").html("");
            $("#equip_model").html("");
            $("#equip_id").val("");
            $("#building_name").val("");
            $("#build_address").val("");
        }
    },
    "json"
);
$("#build_id").change(function(){
    if ($(this).val()) {
        $(this).parent().removeClass("has-error");
        $(this).parent().find(".help-block").html("");
    }
    $.get(
        "' . Yii::$app->request->baseUrl . '/equip-task/ajax-get-build",
        {build_id:$(this).val()},
        function(data) {
            if (data) {
                $("#build_name").html(data.build_name);
                $("#equip_code").html(data.equip_code);
                $("#equip_model").html(data.equip_type);
                $("#equip_id").val(data.equip_id);
                $("#building_name").val(data.build_name);
                $("#build_address").val(data.build_address);
            } else {
                $("#build_name").html("");
                $("#equip_code").html("");
                $("#equip_model").html("");
                $("#equip_id").val("");
                $("#building_name").val("");
                $("#build_address").val("");
            }
        },
        "json"
    );
})
');
?>
<div class="equip-task-form">
    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'build_id')->widget(\kartik\select2\Select2::classname(), ['data' => Building::getOperationBuildList(),
    'options'                                                                                     => ['placeholder' => '请选择楼宇', 'id' => 'build_id'],
    'pluginOptions'                                                                               => [
        'allowClear' => true,
    ]])->label('请选择楼宇');?>

    <?=Html::hiddenInput('EquipRepair[equip_id]', '', array('id' => 'equip_id'));?>
    <?=Html::hiddenInput('EquipRepair[build_name]', '', array('id' => 'building_name'));?>
    <?=Html::hiddenInput('EquipRepair[build_address]', '', array('id' => 'build_address'));?>

    <p>所选楼宇：<span id="build_name"></span></p>
    <p>设备编号：<span id="equip_code"></span></p>
    <p>设备类型：<span id="equip_model"></span></p>

    <?=$form->field($model, 'content')->checkboxList(EquipSymptom::getSymptomIdNameArr())?>

    <?=$form->field($model, 'remark')->textarea(['rows' => 5])?>

    <div class="form-group">
        <?=Html::submitButton($model->isNewRecord ? '创建' : '确定', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
