<?php

$this->registerJsFile("/js/laytpl.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/bootstrap3-validation.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
?>
<style type="text/css">

    @media only screen and (min-width: 768px) {
        #w0 .form-inline > .form-group{
            width: 45%;
        }
        .form-inline .control-label {
            width: 100px;
        }
    }
</style>
<form id="w0" action="/coffee-product/create.html" method="post" enctype="multipart/form-data">
    <div class="form-inline">
        <div class="form-group field-coffeeproduct-cf_product_name required">
            <label class="control-label" for="coffeeproduct-cf_product_name">单品名称</label>
            <input id="coffeeproduct-cf_product_name" class="form-control" name="CoffeeProduct[cf_product_name]" maxlength="255" type="text" check-type="required">
        </div>
        <div class="form-group">
            <label class="control-label" for="up_img">单品图片</label>
            <div class="form-group field-coffeeproduct-file">
                <input name="CoffeeProduct[file]" value="" type="hidden"><input id="up_img" name="CoffeeProduct[file]" type="file">
            </div>
            <div class="form-group" id="imgdiv">
                <img id="imgShow" width="100" height="100">
            </div>
        </div>
    </div>
    <div class="form-group  form-inline">
        <div class="form-group field-coffeeproduct-cf_product_price required">
            <label class="control-label" for="coffeeproduct-cf_product_price">手机端价格(元)</label>
            <input id="coffeeproduct-cf_product_price" class="form-control" name="CoffeeProduct[cf_product_price]" type="text" check-type="required">
        </div>
        <div class="form-group field-coffeeproduct-cf_product_cost">
            <label class="control-label" for="coffeeproduct-cf_product_cost">手机端特价(元)</label>
            <input id="coffeeproduct-cf_product_cost" class="form-control" name="CoffeeProduct[cf_product_cost]" type="text" check-type="required">
        </div>
    </div>
    <div class="form-group  form-inline">
        <div class="form-group field-equipmentproductgrouplist-discount_start_time" readonly="readonly">
            <label class="control-label" for="equipmentproductgrouplist-discount_start_time">特价开始时间</label>
            <input id="equipmentproductgrouplist-discount_start_time" class="form-control" name="EquipmentProductGroupList[discount_start_time]" value="" type="text" check-type="required" readonly="readonly">
        </div>
        <div class="form-group field-equipmentproductgrouplist-discount_end_time" readonly="readonly">
            <label class="control-label" for="equipmentproductgrouplist-discount_end_time">特价结束时间</label>
            <input id="equipmentproductgrouplist-discount_end_time" class="form-control" name="EquipmentProductGroupList[discount_end_time]" value="" type="text" check-type="required" readonly="readonly">
        </div>
    </div>
    <div class="form-group  form-inline">
        <div class="form-group field-coffeeproduct-cf_product_hot required">
            <label class="control-label" for="coffeeproduct-cf_product_hot">冷热类型</label>
            <select id="coffeeproduct-cf_product_hot" class="form-control" name="CoffeeProduct[cf_product_hot]" check-type="required">
                <option value="">请选择</option>
                <option value="0">热饮</option>
                <option value="1">冷饮</option>
            </select>
        </div>
        <div class="form-group field-coffeeproduct-cf_product_status required">
            <label class="control-label" for="coffeeproduct-cf_product_status">单品状态</label>
            <select id="coffeeproduct-cf_product_status" class="form-control" name="CoffeeProduct[cf_product_status]" check-type="required">
                <option value="">请选择</option>
                <option value="0">正常</option>
                <option value="1">下架</option>
            </select>
        </div>
    </div>
        <?=$this->render('_formula')?>
    <div class="form-group">
        <button type="button" class="btn btn-success">添加</button>
    </div>
</form>