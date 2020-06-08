<?php
use common\models\EquipTask;
use frontend\models\JSSDK;
$this->title = isset($title) ? $title : '维修任务';
$this->registerJsFile('http://map.qq.com/api/js?v=2.exp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ', ['depends' => [\yii\web\JqueryAsset::className()]]);
$jssdk       = new JSSDK(Yii::$app->params['corpid'], Yii::$app->params['secret'][Yii::$app->params['equip_agentid']]);
$signPackage = $jssdk->GetSignPackage();

$this->registerJsFile('http://res.wx.qq.com/open/js/jweixin-1.0.0.js', ['depends' => ['frontend\assets\AppAsset']]);
$this->registerJsFile('@web/js/equiptask.js?v=1.8', ['depends' => ['frontend\assets\AppAsset']]);
?>
<script type=text/javascript src=/js/vconsole.min.js></script>
  <script>
    if(window.location.host.split(".")[0]!="erp") {
      var vConsole = new VConsole();
    }
  </script>
<script type="text/javascript">
	var	appId = '<?php echo $signPackage["appId"]; ?>',
	    timestamp = <?php echo $signPackage["timestamp"]; ?>,
	    nonceStr = '<?php echo $signPackage["nonceStr"]; ?>',
	    signature = '<?php echo $signPackage["signature"]; ?>';
</script>
<style>
	.dl-horizontal dt{
		width:105px;
		text-align: left;
		white-space:normal;
	}
	.dl-horizontal dd {
   	 margin-left: 105px;
	}
	dl {
   		margin-bottom: 10px;
	}
	.modal{
		height:100%;
		overflow: hidden;
		filter: Alpha(opacity=50);
		background:rgba(0,0,0,0.5);
	}
	.modal-header {
		border-color:#2e6da4;
		padding: 10px;
	}
	h4{
		font-size: 22px;
		margin: 0;
	}
	.modal-backdrop{
		display: none;
	}
	.modal-dialog{
		width:80%;
		margin-top: 25%;
		margin-left: 10%;
	}
	.modal .title{
		font-size: 18px;
		margin-bottom: 5px;
		letter-spacing: 2px;
	}
	.modal-footer{
		text-align: center;
	}
	.modal-footer .btn{
		width:35%;
	}
	.modal-footer .btn + .btn {
    	margin-left: 25px;
	}
	.loaded{
		height: 100%;
        position: fixed;
	    top: 0;
	    right: 0;
	    bottom: 0;
	    left: 0;
	    z-index: 1050;
	    display: none;
	    overflow: hidden;
	    -webkit-overflow-scrolling: touch;
	    outline: 0;
	}
	.loaded img{
		margin-top:80% ;
		margin-left: 45%;
	}
	.delivery_content,.table-bordered{
		border:1px solid #ccc;
		padding: 10px;
		overflow: hidden;
	}
	.line1{
		display: inline-block;
		width:90%;
	}
</style>
<div id="task_detail">
    <dl class="dl-horizontal">
        <dt>楼宇名称：</dt>
        <dd>
            <?php echo $task_detail['build']['name']; ?></dd>
    </dl>
    <dl class="dl-horizontal">
        <dt>楼宇地址：</dt>
        <dd id="address">
            <?php echo $task_detail['build']['province'] . $task_detail['build']['city'] . $task_detail['build']['area'] . $task_detail['build']['address']; ?></dd>
    </dl>
    <div id="allmap" style="width:100%;height:200px;"></div>
    <dl class="dl-horizontal">
        <dt>任务创建时间：</dt>
        <dd>
            <?php echo $task_detail['create_time'] ? date('Y年m月d日 H点i分', $task_detail['create_time']) : '' ?></dd>
    </dl>
    <dl class="dl-horizontal">
        <dt>任务详情：</dt>
        <dd><?php echo $task_detail['content']; ?></dd>
    </dl>
    <?php $result_form = 'acceptance-form';?>

	<?php if ($task_detail['task_type'] == EquipTask::MAINTENANCE_TASK) {?>
	<?php $result_form = 'repair-form';?>
    <dl class="dl-horizontal">
        <dt>备注：</dt>
        <dd>
            <?php echo $task_detail['remark']; ?></dd>
    </dl>
    <?php } elseif ($task_detail['task_type'] == EquipTask::EXTRA_TASK) {?>
		<?php $result_form = 'extra-form';?>
		<dl class="dl-horizontal">
			<dt>备注：</dt>
			<dd>
				<?php echo $task_detail['remark']; ?></dd>
		</dl>
	<?php }?>
   <?php if (!$task_detail['start_repair_time']) {if ($task_detail['recive_time']) {?>
         <form action="/equip-task/update-task-recive-time" method="get" id="reciveSave">
            <input type="hidden" name="id" value="<?php echo $task_detail['id']; ?>" />
            <input type="hidden" name="start_longitude" value="" id="start_longitude"/>
            <input type="hidden" name="start_latitude" value="" id="start_latitude"/>
            <input type="hidden" name="start_address" value="" id="start_address"/>
            <input type="hidden" name="type" value="2" />
            <button id="task_start" type="button" class="btn btn-info btn-block">任务打卡</button>
        </form>
    <?php } else {?>
         <form action="/equip-task/update-task-recive-time" method="get" id="reciveSave">
            <input type="hidden" name="id" value="<?php echo $task_detail['id']; ?>" />
            <input type="hidden" name="type" value="1" />
            <button type="submit" class="btn btn-info btn-block">接收任务</button>
        </form>
    <?php }}?>
</div>
<?=$this->render($result_form, ['task_detail' => $task_detail, 'orgId' => $orgId]);?>

<!--打卡提示-->
<div class="modal fade" id="Modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<h4 id="myModalLabel">提示框</h4>
			</div>
			<div class="modal-body">
				<div class="form-group title">您确定要打卡吗？</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">取消</button><button type="button" id="btn_submit" class="btn btn-primary" data-dismiss="modal">确定</button>
			</div>
		</div>
	</div>
</div>
<div class="loaded">
	<img src="/images/loading.gif">
</div>