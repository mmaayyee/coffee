<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title = '添加申请';
$this->registerJs('
    $(".material-num").blur(function(){
        if ($(this).val() && /^[1-9][0-9]*$/.test($(this).val()) || !$(this).val()) {
            $(this).next().html("");
            $(this).parent().removeClass("has-error");
        } else {
            $(this).next().html("物料数量必须是正整数");
            $(this).parent().addClass("has-error");
        }
    })
    $(".material-gram").blur(function(){
        if ($(this).val() && /^[1-9][0-9]*$/.test($(this).val()) || !$(this).val()) {
            $(this).next().html("");
            $(this).parent().removeClass("has-error");
        } else {
            $(this).next().html("物料重量必须是正整数");
            $(this).parent().addClass("has-error");
        }
    })
    $("#materialSave").click(function(){
        var trueArr = 0;
        $(".material-num").each(function(e){
            if ($(this).val()) {
                if (/^[1-9][0-9]*$/.test($(this).val())) {
                    trueArr = 1;
                    $(".material-num").next().html("");
                    $(".material-num").parent().removeClass("has-error");
                } else {
                    $(this).next().html("物料数量必须是正整数");
                    $(this).parent().addClass("has-error");
                }
            }
        })
        $(".material-gram").each(function(e){
            if ($(this).val()) {
                if (/^[1-9][0-9]*$/.test($(this).val())) {
                    trueArr = 1;
                    $(".material-gram").next().html("");
                    $(".material-gram").parent().removeClass("has-error");
                } else {
                    $(this).next().html("物料重量必须是正整数");
                    $(this).parent().addClass("has-error");
                }
            }
        })

        if (trueArr == 0) {
            $(".material-num").next().html("不能同时为空且必须是正整数");
            $(".material-num").parent().addClass("has-error");
            $(".material-gram").next().html("不能同时为空且必须是正整数");
            $(".material-gram").parent().addClass("has-error");
            return false;
        }
        if (!$("#reason").val()) {
            $("#reason").next().html("修改原因不能为空");
            $("#reason").parent().addClass("has-error");
            return false;
        } else {
            $("#reason").next().html("");
            $("#reason").parent().removeClass("has-error");
            $("#materialSave").submit();
        }
    })

');

?>
<style>
.form-control {
    width: 28%;
}
.form-area {
    width: 100%;
}
</style>
<div class="scm-user-surplus-material-sure-record-create">
    <div class="scm-user-surplus-material-sure-record-form">
        <?php $form = ActiveForm::begin();?>
        <?php foreach ($surplusMaterialList as $key => $surplusMaterialObj) {?>
    <div class="form-group">
        <label>
        <?=$surplusMaterialObj->material->name . ' '?> <?=!$surplusMaterialObj->material->weight ? '' : $surplusMaterialObj->material->weight . $surplusMaterialObj->material->materialType->spec_unit;?>
        </label>
        <br/>
        <?=Html::hiddenInput('material[' . $key . '][material_id]', $surplusMaterialObj->material_id)?>
        <?=Html::dropDownList('material[' . $key . '][add_reduce]', '', $model::$addReduce, ['class' => 'form-control'])?>
        <?=Html::textInput('material[' . $key . '][material_num]', '', ['class' => 'form-control material-num']) . ' ' . $surplusMaterialObj->material->materialType->unit?>
        <div class="help-block"></div>
    </div>
        <?php }?>

        <?php foreach ($gramList as $key => $material) {?>
            <?php if($material->materialType->type == 1):?>
            <div class="form-group">
                <label>
                    <?php echo $material->materialType->material_type_name;?> <?= $material->supplier->name;?>
                </label>
                <br/>
                <?=Html::hiddenInput('material_gram[' . $key . '][supplier_id]', $material->supplier_id)?>
                <?=Html::hiddenInput('material_gram[' . $key . '][material_type_id]', $material->material_type_id)?>
                <?=Html::dropDownList('material_gram[' . $key . '][add_reduce]', '', $model::$addReduce, ['class' => 'form-control'])?>
                <?=Html::textInput('material_gram[' . $key . '][material_gram]', '', ['class' => 'form-control material-gram']) . ' ' . $material->materialType->weight_unit?>
                <div class="help-block"></div>
            </div>
                <?php endif;?>
        <?php }?>

        <div class="form-group">
            <label>修改原因</label><br/>
            <?=Html::textArea('reason', '', ['class' => 'form-control form-area', 'maxLength' => '500', 'rows' => '8', 'id' => 'reason'])?>
            <div class="help-block"></div>
        </div>
        <div class="form-group">
            <?=Html::button('提交', ['class' => 'btn btn-success', 'id' => 'materialSave'])?>
        </div>
        <?php ActiveForm::end();?>

    </div>

</div>
