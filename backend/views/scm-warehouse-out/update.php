<?php

use yii\helpers\Html;

$this->title = '修改出库单';
$this->params['breadcrumbs'][] = ['label' => '出库单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
$this->registerJs('
    $("#save").click(function(){
        var result = true;
        $(".packets").each(function(){
            if (!/^\d{0,5}$/.test($(this).val())) {
                       $(this).css("border","1px solid red");
                result = false;
                return false;
            }
        });
        $(".weights").each(function(){
            if (!/^\d{0,5}$/.test($(this).val())) {
                       $(this).css("border","1px solid red");
                result = false;
                return false;
            }
        });
        if (!result) {
            alert("所填数量必须是大于等于0且小于100000的整数");
            return false;
        }else{
            $("form").submit();
        }
    })
')
?>
<style>
    label{
        width: 20%;
    }
</style>
<div class="scm-warehouse-out-update">

    <h1><?= Html::encode($this->title) ?></h1>
    <form action="/scm-warehouse-out/update-save" method="post">
        <?php
        foreach ($warehouseOutList as $key => $warehouseOut) {
            ?>
            <div class="form-group form-inline">
                <label><?php  echo $warehouseOut['label'];?></label>

                <?= Html::hiddenInput('data['.$key.'][id]', $warehouseOut['id']) ?>
                <?= Html::hiddenInput('data['.$key.'][material_id]', $warehouseOut['material_id']) ?>
                <?= Html::hiddenInput('data['.$key.'][warehouse_id]', $warehouseOut['warehouse_id']) ?>
                <?= Html::hiddenInput('data['.$key.'][material_type_id]', $warehouseOut['material_type_id']) ?>
                <?= Html::textInput('data['.$key.'][packets]', $warehouseOut['material_out_num'], ['class' => 'packets', 'onblur' => 'checkVal(this,this.value)']) ?>
                <?php echo $warehouseOut['unit']; ?>
                <?php if($warehouseOut['weight_unit']):?>
                    <?= Html::textInput('data['.$key.'][material_out_gram]', $warehouseOut['material_out_gram'], ['class' => 'weights col-md-offset-1','onblur' => 'checkVal(this,this.value)']) ?>
                    <?php echo $warehouseOut['weight_unit']; ?>
                <?php endif;?>
            </div>
        <?php } ?>
        <div class="form-group">
            <?=Html::hiddenInput('date', $date) ?>
            <?=Html::hiddenInput('author', $author) ?>
            <?=Html::hiddenInput('_csrf', Yii::$app->getRequest()->getCsrfToken())?>
            <?=Html::button('保存', ['class' =>'btn btn-primary', 'id' => 'save'])?>
        </div>
    </form>
</div>
<script type="text/javascript">
    function checkVal(obj,value){
        if (/^\d{0,5}$/.test(value)) {
            obj.style.border = "";
        }
    }
</script>
