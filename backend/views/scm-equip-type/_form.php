<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->registerJs('
    var equipTypeId = "' . $model->id . '";
    // 料仓编号和序号添加必填属性
    $("#scmequiptype-readable_attribute").find("input[value=stock_code]").attr({"checked":"checked", "onclick":"return false;"});
    $("#scmequiptype-readable_attribute").find("input[value=order_number]").attr({"checked":"checked", "onclick":"return false;"})
    if (equipTypeId) {
        $("#scmequiptype-matstock input:checked").each(function(){
            var stockId = $(this).val();
            $.get(
                "/scm-material/ajax-material-list",
                {stockId:stockId, equipTypeId:equipTypeId},
                function(data) {
                    $(".delivery_content").append(data);
                    $("#stockId_10 select, #stockId_11 select, #stockId_12 select, #stockId_13 select").blur(function(){
                        if ($(this).val().length < 1) {
                            $(this).parent().addClass("has-error");
                            $(this).next().show();
                            return false;
                        } else {
                            $(this).parent().removeClass("has-error");
                            $(this).next().hide();
                        }
                    })
                }
            );
        })
    }

    $("#scmequiptype-matstock input[type=checkbox]").click(function(){
        var stockId = $(this).val();
        if ($(this).is(":checked")) {
            $(this).parent().find(".empty-weight").show();
            $.get(
                "/scm-material/ajax-material-list",
                {stockId:stockId},
                function(data) {
                    $(".delivery_content").append(data);
                    $("#stockId_10 select, #stockId_11 select, #stockId_12 select, #stockId_13 select").blur(function(){
                        if ($(this).val().length < 1) {
                            $(this).parent().addClass("has-error");
                            $(this).next().show();
                            return false;
                        } else {
                            $(this).parent().removeClass("has-error");
                            $(this).next().hide();
                        }
                    })
                }
            );
        } else {
            $(this).parent().find(".empty-weight").hide();
            $("#stockId_"+stockId).remove();
        }
    })


    $("form").submit(function(){
         var idArr = [10,11,12,13];
        var result = true;
       var checkStock = 0;
        $("#scmequiptype-matstock input:checked").each(function(){
            checkStock+=1;
        })
        if(checkStock == 0) {
            $(".stock-info").html("请选择料仓信息");
            $("#scmequiptype-matstock").addClass("has-error");
            return false;
        }
        for(i in idArr) {
            if ($("#stockId_"+idArr[i]+" select").val() == "") {
                $("#stockId_"+idArr[i]).addClass("has-error");
                $("#stockId_"+idArr[i]+" .help-block").show();
                result = false;
            } else {
                $("#stockId_"+idArr[i]).removeClass("has-error");
                $("#stockId_"+idArr[i]+" .help-block").hide();
            }
        }
        return result;
    });


    $("#scmequiptype-matstock input:checkbox").on("click",function(){
        $("#scmequiptype-matstock input:checkbox").each(function(i,obj){
            if($(this).is(":checked")){
                console.log(7);
                $("#save").attr({"disabled":false});
                $(".stock-info").html("");
            }
        })
    })

');

?>
<style>
    .empty-hide{
        display: none;
    }
</style>
<div class="scm-equip-type-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'model')->textInput(['maxlength' => 20])?>

    <?=$form->field($model, 'supplier_id')->dropDownList($model->getSupplierArray())?>

    <?=$form->field($model, 'equip_type_alias')->textInput(['maxlength' => 10, 'placeholder' => '输入正确格式:X40、X50、X51(其中X40代表四代设备、X50代表五代设备、X51代表5.1代设备)'])?>

    <?=$form->field($model, 'readable_attribute')->checkBoxList($model::getReadableAttribute())?>


    <div id="scmequiptype-matstock">
        <label class="control-label" for="scmequiptype-model">料仓信息</label><br/>
    <?php
foreach ($model->getmaterialStockArray() as $stockId => $stockName) {
    $checked = '';
    if (!empty($model->matstock)) {
        $checked = in_array($stockId, $model->matstock) ? 'checked="checked"' : '';
    }
    echo '<label><input type="checkbox"  name="ScmEquipType[matstock][]" value="' . $stockId . '" ' . $checked . '>' . $stockName;
    if (in_array($stockId, ['1', '2', '3', '4', '5', '6', '7', '8', '9'])) {
        $isHide          = $checked ? '' : 'empty-hide';
        $stockEmptyValue = $model->empty_box_weight[$stockId] ?? 0;
        echo ' <input class="empty-weight ' . $isHide . '" name="ScmEquipType[empty_box_weight][' . $stockId . ']" value="' . $stockEmptyValue . '">';
    }
    echo '</label><br/>';
}
?>
        <div class="help-block stock-info"></div>
    </div>
    <div class="delivery_content">
    </div>

    <input type="hidden" id="hide_id" value="<?php echo $model->id; ?>">
	<div class="form-group">
        <?=Html::submitButton('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'save'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
