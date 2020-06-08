<?php
/**
 * Created by PhpStorm.
 * User: wangxl
 * Date: 17/5/16
 * Time: 上午11:29
 */
use backend\models\ScmSupplier;

$this->registerJsFile('@web/js/My97DatePicker/WdatePicker.js');
$this->registerJsFile('@web/js/bootstrap3-validation.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerJsFile('@web/js/select2/select2.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/select2/select2-zh-CN.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('@web/js/select2/select2.min.css');
$this->registerCssFile('@web/js/select2/select2-bootstrap.min.css');
?>
<style type="text/css">
	#repair_submit{
		margin-top: 10px;
	}
</style>
<div class="panel panel-default" id="repair_form" <?php if (!$task_detail['start_repair_time']) {echo 'style="display:none"';}?>>
    <div class="panel-heading">
        <h3 class="panel-title" style="text-align:center;margin: 0 auto;">设备附件任务</h3>
    </div>
    <div class="panel-body">
        <form id="task_process" role="form" action="extra-task-save" method="post">
            <div class="form-group">
                <label style="width:20%">任务状态</label>
                <select id="process_result" style="width:70%;" class="form-control" name="process_result" check-type = "required">
                    <option value="2">配送附件已完成</option>
                    <option value="3">配送附件未完成</option>
                    <option value="4">完成附件回收</option>
                </select>
            </div>
            <div>
                <label>备注</label>
                <span id="autoreqmark" style="color:#FF9966"> *</span>
                <textarea class="form-control" name="remark" rows="3" maxlength="100"></textarea>
            </div>
            <br/>
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
            <input type="hidden" maxlength="50" name="id" value="<?php echo $task_detail['id']; ?>" />
            <input type="hidden" maxlength="50" value="<?php echo Yii::$app->request->getCsrfToken(); ?>" name="_csrf" />
            <input type="hidden" name="end_latitude" class="form-control" value="" id="latitude1" />
            <input type="hidden" name="end_longitude" class="form-control" value="" id="longitude1" />
            <input type="hidden" name="end_address" class="form-control" value="" id="end_address" />
            <button type="submit" id="repair_submit" class="btn btn-block btn-success">提交</button>
        </form>
    </div>
</div>
