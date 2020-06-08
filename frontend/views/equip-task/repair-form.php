<?php
use backend\models\ScmSupplier;

$this->registerJsFile('@web/js/My97DatePicker/WdatePicker.js');
$this->registerJsFile('@web/js/bootstrap3-validation.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerJsFile('@web/js/select2/select2.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/select2/select2-zh-CN.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('@web/js/select2/select2.min.css');
$this->registerCssFile('@web/js/select2/select2-bootstrap.min.css');
$this->registerJs('
    $("#select_malfunciton select").select2({
        placeholder: "请选择故障原因",
        allowClear: true,
        theme: "bootstrap"
    });
')
?>
<style type="text/css">
    .select2-container--bootstrap {
        display: inline-block;
    }
</style>
<div class="panel panel-default" id="repair_form" <?php if (!$task_detail['start_repair_time']) {echo 'style="display:none"';}?>>
    <div class="panel-heading">
        <h3 class="panel-title" style="text-align:center;margin: 0 auto;">维修任务</h3>
    </div>
    <div class="panel-body">
    <p style="color:red">*当发现故障时填写</p>
    <form id="task_process" role="form" action="task-save" method="post">
        <div class="form-group">
          <label>故障原因</label>
          <button id="add_malfunction" data-malfunction='<?php echo $malfunction_reason_list = json_encode(\backend\models\EquipMalfunction::getMalfunctionReasonList()); ?>' type="button" class="btn btn-primary pull-right">新增故障原因</button>
        </div>
        <div id="select_malfunciton" class="form-group">
            <dl><select id="malfunction_reason" style="width:100%;" name="malfunction_reason[]" class="form-control" check-type = "required">
            <option value="">请选择</option>
            <?php foreach (\backend\models\EquipMalfunction::getMalfunctionReasonList() as $key => $value) {
    echo "<option value='$key'>$value</option>";
}?>
            </select></dl>
        </div>
        <div class="form-group">
            <label>故障描述</label>
            <textarea id="malfunction_description" maxlength="300" name="malfunction_description" class="form-control" rows='4' maxlength="500" check-type = "required"></textarea>
        </div>
        <div class="form-group">
            <label>处理方法</label>
            <textarea id="process_method" maxlength="200" name="process_method" class="form-control" rows='4' maxlength="500" check-type = "required"></textarea>
        </div>
        <div class="form-group">
            <label style="width:25%">处理结果</label>
            <select id="process_result" style="width:70%;" class="form-control" name="process_result" check-type = "required">
                <option value="2">故障已修复</option>
                <option value="3">故障未修复</option>
            </select>
        </div>
        <div id="fitting-list"></div>
        <div class="form-group">
            <button id="add_fitting" data-id="<?php echo $task_detail['id']; ?>" type="button" class="btn btn-primary">新增维修项</button>
        </div>

        <?=$this->render('/site/water-form',['orgId' => $orgId]);?>

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

<!--删除新增备件提示弹框-->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        	<h4 id="myModalLabel">提示框</h4>
        </div>
        <div class="modal-body">
          <div class="form-group title">您确定要删除吗？</div>
          <div id="bd">

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
          <button type="button" id="btn_submit" class="btn btn-primary" data-dismiss="modal">确定</button>
        </div>
      </div>
    </div>
  </div>