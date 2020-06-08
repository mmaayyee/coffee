<?php
use backend\models\EquipMalfunction;
use frontend\models\JSSDK;
$this->title = '任务完成';
$this->registerCssFile('@web/css/mobiscroll_date.css');
$this->registerCssFile('@web/js/select2/select2.min.css');
$this->registerCssFile('@web/js/select2/select2-bootstrap.min.css');
$this->registerCssFile('@web/css/daily-task-detail.css');
$this->registerJsFile('@web/js/bootstrap3-validation.js?v=2.2', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/mobiscroll_date.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('http://map.qq.com/api/js?v=2.exp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/distribution-daily-task.js?v=6.0', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/select2/select2.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/select2/select2-zh-CN.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$jssdk       = new JSSDK(\Yii::$app->params['corpid'], \Yii::$app->params['secret']['address_book']);
$signPackage = $jssdk->GetSignPackage();
?>
<script src="/js/rem.js"></script>
<script src="/js/vconsole.min.js"></script>
<script type="text/javascript">
    if(window.location.host.split(".")[0]!="erp") {
      var vConsole = new VConsole();
    }
    var taskType = 0;
    var appId = '<?php echo $signPackage["appId"]; ?>',
        timestamp = <?php echo $signPackage["timestamp"]; ?>,
        nonceStr = '<?php echo $signPackage["nonceStr"]; ?>',
        signature = '<?php echo $signPackage["signature"]; ?>',
        isRepair='<?php echo $isRepair; ?>'
</script>
<form id="w0" action="/distribution-task/task-execution?id=<?php echo $taskId; ?>" method="post">
    <input type="hidden" value="<?php echo Yii::$app->request->csrfToken; ?>" name="_csrf" />
    <input  type="hidden" id="hide_distribution_id" value="<?php echo $taskId; ?>" />
    <ul id="myTab" class="nav nav-tabs">
        <li class="active">
            <a href="#home" data-toggle="tab">日常任务</a>
        </li>
        <li>
            <a href="#ios" data-toggle="tab" class="tab">维修任务</a>
        </li>
    </ul>
    <div id="myTabContent" class="tab-content">
        <div class="tab-pane fade in active" id="home">
            <div class="distribution">
                <!-- <p>请选择填写以下数据并单击上传完成任务!</p> -->
                <!-- 配送任务/配送维修任务 -->
                <div>

                    <div class="block-a">
                        <div>
                            <a href="javascript:void()" id="clearCache">重新上传图片</a>
                            <h4 style="margin-bottom: 25px;">上传照片</h4>
                        </div>
                        <?php for ($i = 0; $i < 8; $i++) {
    $src = !empty($imgList[$i]) ? Yii::$app->params['frontend'] . $imgList[$i] : '/images/uploads-icon.jpg';
    echo '<div style="float:left"><img class="img" src="' . $src . '"><input type="hidden" name="taskimg[]" value /></div>';
}?>
                    </div>
                    <div class="block-b">
                        <div id="taskList"></div>
                        <div class="task-list-state" style="text-align:left">
                            <div class="wash" style="padding:15px">清洗任务:
                                <span class="wash-state">未完成</span>
                                <div class="wash-date"></div>
                            </div>
                            <div class="change" style="padding:15px">加料 / 换料任务:
                                <span class="change-state">未完成</span>
                                <div class="change-date"></div>
                                <div class="change-list"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-primary" id="detectionBnt" style="margin-top:30px">任务检测</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="supplement">
                <div style="margin-left:-225px;padding:15px">运维后物料量</div>
                <ul class="supplement-list" style="list-style: none; text-align:left">
                    <li style="padding-bottom:15px">水量：
                        <span>添加：</span><input type="number" class="water_add" style="width:50px;border-radius: 5px;outline:none" name="add_water" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">桶<b class="red" style="color:red;margin-right:15px;">*</b>
                        <br>　　　&nbsp;剩余：<input type="number" class="water_remain" style="width:50px;border-radius: 5px;outline:none" name="surplus_water" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">桶<b class="red" style="color:red">*</b>
                    </li>
                    <li style="padding-bottom:15px">杯子：
                        <span>添加：</span><input type="number" class="cups_add" style="width:50px;border-radius: 5px;outline:none" name="add_cups" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">个<b class="red" style="color:red;margin-right:15px;">*</b>
                        <br>　　　&nbsp;剩余：<input type="number" class="cups_remain" style="width:50px;border-radius: 5px;outline:none" name="surplus_cups" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">个<b class="red" style="color:red">*</b>
                    </li>
                    <li style="padding-bottom:15px">杯盖：
                        <span>添加：</span><input type="number" class="cover_add" style="width:50px;border-radius: 5px;outline:none" name="add_cover" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">个<b class="red" style="color:red; margin-right:15px;">*</b>
                        <br>　　　&nbsp;剩余：<input type="number" class="cover_remain" style="width:50px;border-radius: 5px;outline:none" name="surplus_cover" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')">个<b class="red" style="color:red">*</b>
                    </li>
                    <li style="padding:15px">电表读数：
                        <input type="text" class="electric" style="width:50px;border-radius: 5px;outline:none" name="electric" onkeyup="value=value.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g,'')">度
                    </li>
                </ul>
            </div>
        </div>
        <div class="tab-pane fade" id="ios">
            <div class="maintenance">
                <p>请选择填写以下数据并单击上传完成任务!</p>
                <div class="form-group">
                    <label>开始修理时间</label>
                    <input id="start_repair_time" <?php echo $isRepair ? 'check-type="required"  required-message="此项不可为空！"' : ''; ?> type="text" name="maintenance[start_repair_time]" class="form-control"/>
                </div>
                <div class="form-group">
                    <label>修理结束时间</label>
                    <input id="end_repair_time" <?php echo $isRepair ? 'check-type="required"  required-message="此项不可为空！"' : ''; ?> type="text" name="maintenance[end_repair_time]" class="form-control"/>
                </div>
                <div class="form-group">
                    <label class="tex2">故障原因</label>
                    <button id="add_malfunction" data-malfunction='<?php echo $malfunction_reason_list = json_encode(EquipMalfunction::getMalfunctionReasonList()); ?>' type="button" class=" btn btn-primary pull-right">新增故障原因</button>
                </div>
                <div id="select_malfunciton" class="form-group">
                    <dl>
                        <select id="malfunction_reason" style="width:100%;" name="malfunction_reason[]" class="form-control">
                            <option value="">请选择</option>
                            <?php foreach (EquipMalfunction::getMalfunctionReasonList() as $value): ?>
                                <option value='$key'><?php echo $value ?></option>
                            <?php endforeach?>
                        </select>
                    </dl>
                </div>
                <div class="form-group">
                    <span class="label1">故障描述</span>
                    <textarea id="malfunction_description" name="maintenance[malfunction_description]" class="form-control" rows='4' ></textarea>
                </div>
                <div class="form-group">
                    <span class="label1">处理方法</span>
                    <textarea  id="process_method" name="maintenance[process_method]" class="form-control" rows='4'></textarea>
                </div>
                <div class="form-group">
                    <label>处理结果</label>
                    <select id="process_result" style="width:100%;margin-bottom: 3%;" class="form-control" name="maintenance[process_result]" >
                        <option value="3">故障已修复</option>
                        <option value="2">故障未修复</option>
                    </select>
                </div>
                <div id="fitting-list"></div>
            </div>
        </div>
    </div>
    <input type="hidden" name="id" value="<?php echo $taskId; ?>"/>
    <input type="hidden" id="end_latitude" name="end_latitude" value="" />
    <input type="hidden" id="end_longitude" name="end_longitude" value="" />
    <input type="hidden" id="end_address" name="end_address" value="" />
    <div class="form-group-btn">
    <?php if ($isRepair == 1): ?>
        <input type="button" id="next" class="btn btn-block btn-success load-acceptance" value="下一步" />
        <input type="submit" id="save" class="btn btn-block btn-success load-acceptance btn-end-click" value="保存" style="display:none"/>
    <?php else: ?>
        <input type="submit" id="save" class="btn btn-block btn-success load-acceptance btn-end-click" value="保存" />
    <?php endif?>
    </div>
</form>
<script src="/js/operations/layout.js"></script>
<script src="/js/jquery-1.7.1.min.js"></script>
<script src="/js/operations/laytpl.js"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script type="text/javascript">
    var taskId=<?php echo $taskId; ?>;
</script>
<script type="text/javascript" src="/js/daily-task-detail.js?v=20190211181"></script>
<div class="modal fade" id="Modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 id="myModalLabel">提示框</h4>
            </div>
            <div class="modal-body">
                <div class="form-group title">确认完成?</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button><button type="button" id="btn_submit" class="btn btn-primary" data-dismiss="modal">确定</button>
            </div>
        </div>
    </div>
</div>
<div class="loaded" style="display: none">
    <img src="/images/loading.gif">
</div>

