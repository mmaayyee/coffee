<?php

use backend\models\Organization;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\WxMember */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="wx-member-form">

    <?php $form = ActiveForm::begin();?>
    <?php if ($model->isNewRecord) {?>
    <?=$form->field($model, 'userid')->textInput(['maxlength' => 64])->hint('企业微信中用户的唯一标识')?>
    <?php } else {?>
    <?=$form->field($model, 'userid')->textInput(['maxlength' => 64, 'readonly' => 'readonly'])->hint('企业微信中用户的唯一标识')?>
    <?php }?>

    <?=$form->field($model, 'name')->textInput(['maxlength' => 64])?>

    <?=$form->field($model, 'org_id')->dropDownList(Organization::getBranchArray())?>

    <?=$form->field($model, 'department_id')->dropDownList(['' => '请选择'], ['data-id' => $model->department_id])?>

    <?=$form->field($model, 'position')->dropDownList(['' => '请选择'], ['data-id' => $model->position])?>

    <?=$form->field($model, 'supplier_id')->dropDownList(['' => '请选择'], ['data-id' => $model->supplier_id])?>

    <?=$form->field($model, 'parent_id')->dropDownList(['' => '请选择'], ['data-id' => $model->parent_id])?>

    <?=$form->field($model, 'mobile')->textInput(['maxlength' => 11])?>

    <?=$form->field($model, 'gender')->dropDownList(array('0' => '请选择', 1 => '男', 2 => '女'))?>

    <?=$form->field($model, 'email')->textInput(['maxlength' => 64])?>

    <?=$form->field($model, 'weixinid')->hint('微信号必须是真实微信号且不能重复')->textInput(['maxlength' => 64])?>

    <div class="form-group">
        <?=Html::submitButton($model->isNewRecord ? '新建成员' : '更新成员', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>

<?php
$html = "";
$this->registerJs('
        $(".field-wxmember-supplier_id").hide();
        var optionHtml = "<option value=\'\'>请选择</option>";
        //修改是初始化部门列表
        $.get(
            "/wx-department/parent-depart-list",
            {org_id:$("#wxmember-org_id").val(),depart_id:$("#wxmember-department_id").data("id"), type: 2},
            function(data) {
                $("#wxmember-department_id").html(data);
            }
        )
        //根据分公司获取部门
        $("#wxmember-org_id").change(function(){
            $("#wxmember-parent_id").html(optionHtml);
            $("#wxmember-position").html(optionHtml);
            $("#wxmember-supplier_id").html(optionHtml);
            $.get(
                "/wx-department/parent-depart-list",
                {"org_id":$(this).val(),depart_id:$("#wxdepartment-parentid").data("id"), type:2},
                function(data) {
                    $("#wxmember-department_id").html(data);
                }
            )
        })

        // 修改时初始化直属领导和供水商
        $.get(
            "/wx-member/parent-member",
            {
                department_id: $("#wxmember-department_id").data("id"),
                member_id: $("#wxmember-parent_id").data("id"),
                supplier_id: $("#wxmember-supplier_id").data("id"),
                position_id: $("#wxmember-position").data("id"),
            },
            function(data) {
                $("#wxmember-parent_id").html(data.parentIdOption);
                $("#wxmember-position").html(data.positionOption);
                if (data.supplierOption) {
                    $(".field-wxmember-supplier_id").show();
                    $("#wxmember-supplier_id").html(data.supplierOption);
                } else {
                    $(".field-wxmember-supplier_id").hide();
                    $("#wxmember-supplier_id").html("<option value=\'\'>请选择</option>");
                }
            },
            "json"
        )
        $("#wxmember-department_id").change(function(){
            $.get(
                "/wx-member/parent-member",
                {department_id:$(this).val()},
                function(data) {
                    $("#wxmember-parent_id").html(data.parentIdOption);
                    $("#wxmember-position").html(data.positionOption);
                    if (data.supplierOption) {
                        $(".field-wxmember-supplier_id").show();
                        $("#wxmember-supplier_id").html(data.supplierOption);
                    } else {
                        $(".field-wxmember-supplier_id").hide();
                        $("#wxmember-supplier_id").html("<option value=\'\'>请选择</option>");
                    }
                },
                "json"
            )
        })
    ');
?>
