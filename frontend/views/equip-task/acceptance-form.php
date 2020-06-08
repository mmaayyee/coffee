<?php
use backend\models\ScmSupplier;

$this->registerJsFile('@web/js/bootstrap3-validation.js',['depends' => [\yii\web\JqueryAsset::className()]]); 
?>
<div class="panel panel-default"  id="repair_form"  <?php if ($task_detail['start_repair_time'] == 0) {echo 'style="display:none"';} ?>>
    <div class="panel-heading">
        <h3 class="panel-title" style="text-align:center;margin: 0 auto;">灯箱验收</h3>
    </div>
    <div class="panel-body">
    <p>请输入一下信息：</p>
    <form id="acceptance_process" role="form" action="acceptance-task-save" method="post">
        <div class="form-group">
            <label>漏电断路器型号</label>
            <input id="breaker_type" type="text" class="form-control" name="breaker_type" check-type="required" maxlength="64" />
        </div>
        <div class="form-group">
            <label>电表型号</label>
            <input id="ammeter_type" type="text" class="form-control" name="ammeter_type" check-type="required" maxlength="64" />
        </div>
        <div class="form-group">
            <label>电表初始值</label>
            <input id="ammeter_number" type="text" class="form-control" name="ammeter_number" check-type="required number" range="1.00~99999999.99"/>
        </div>
        <div class="form-group">
            <label>请勾选通过项目</label>
            <!--<input id="check_all" type="checkbox" /> 全选-->
        </div>
        <table class="table table-bordered">
           <tbody>
           <?php foreach(\backend\models\EquipLightBoxDebug::getLightBoxDebugArrFromEquipId($task_detail['equip_id']) as $key=>$v) { ?>
              <tr>
                 <td><input class="checkbox" type="checkbox" name="checkbox[]" value="<?php echo $v['Id']; ?>" /></td>
                 <td><?php echo $key+1; ?></td>
                 <td><?php echo $v['debug_item']; ?></td>
              </tr>
            <?php } ?>
           </tbody>
        </table>
        <div><label class="h5">水单信息</label></div>
        <div class="table-bordered">
            <span class="label1">当前水量<em class="text-primary">(范围值：0~99.9)</em></span>
            <div class="form-group line1">
                <input name="distributionWater[surplusWater]" id="surplusWater" maxlength="4" class="form-control" type="text" check-type="number1"/>
            </div>
            <span class="line2">桶</span>
            <div class="form-group">
                <span class="label1">选择供水商</span>
                <select name="distributionWater[supplierId]"  class="form-control" id="supplierWater">
                    <?php foreach (ScmSupplier::getSupplierArray(['and', ['type' =>
                        ScmSupplier::WATER], ['like', 'org_id', '-' . $orgId . '-']]) as $key => $value) { ?>
                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                    <?php } ?>
                </select>
            </div>
            <span class="label1">需水量<em class="text-primary">(范围值：0~99)</em></span>
            <div class="form-group line1">
                <input name="distributionWater[needWater]" maxlength="3" class="form-control" type="text" check-type="number2" id="needWater">
            </div>
            <span class="line2">桶</span>
        </div>
        <br/>

        <input type="hidden" name="id" value="<?php echo $task_detail['id']; ?>" />
        <input type="hidden" name="end_latitude" id="end_latitude" value="" />
        <input type="hidden" name="end_longitude" id="end_longitude" value="" />
        <input type="hidden" name="end_address" id="end_address" value="" />
        <input type="hidden" name="light_box_repair_id" value="<?php echo $task_detail['light_box_repair_id']; ?>" />
        <input type="hidden" value="<?php echo Yii::$app->request->getCsrfToken(); ?>" name="_csrf" />
        <button type="submit" id="acceptance-save" class="btn btn-block btn-success">提交</button>
    </form>
    </div>
</div>
