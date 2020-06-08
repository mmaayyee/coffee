<?php
use backend\models\ScmSupplier;

?>
<div class="table-bordered">
    <span class="label1">当前水量<em class="text-primary">(范围值：0~99.9)</em></span>

    <div class="form-group line1">
        <input name="distributionWater[surplusWater]" id="surplusWater" maxlength="4" class="form-control" type="text"
               check-type="number1"/>
    </div>

    <span class="line2">桶</span>
    <div class="form-group">
        <span class="label1">选择供水商</span>
        <select name="distributionWater[supplierId]" class="form-control"
                                                  id="supplierWater">
            <?php foreach (ScmSupplier::getSupplierArray(['and', ['type' =>
                ScmSupplier::WATER], ['like', 'org_id', '-' . $orgId . '-']]) as $key => $value) { ?>
                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
            <?php } ?>
        </select>
    </div>

    <span class="label1">需水量<em class="text-primary">(范围值：0~99)</em></span>
    <div class="form-group line1">
        <input name="distributionWater[needWater]" maxlength="3" class="form-control" type="text" check-type="number2"
               id="needWater">
    </div>
    <span class="line2">桶</span>
</div>