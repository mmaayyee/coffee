<?php
use backend\models\EquipDelivery;
use backend\models\EquipSymptom;
use backend\models\ScmMaterial;
use backend\models\DistributionTask;
use frontend\models\JSSDK;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$deliveryModel = new EquipDelivery();
// $this->title = \backend\models\DistributionTask::$taskType[$taskType];
$this->registerJsFile('http://map.qq.com/api/js?v=2.exp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ', ['depends' => [\yii\web\JqueryAsset::className()]]);
// $this->registerJsFile('http://res.wx.qq.com/open/js/jweixin-1.0.0.js', ['depends' => ['frontend\assets\AppAsset']]);
$this->registerJsFile('https://res.wx.qq.com/open/js/jweixin-1.2.0.js', ['depends' => ['frontend\assets\AppAsset']]);
$this->registerJsFile('@web/js/distribution-daily-task.js?v=6.0', ['depends' => [\yii\web\JqueryAsset::className()]]);
$jssdk       = new JSSDK(yii::$app->params['corpid'], yii::$app->params['secret']['address_book']);
$signPackage = $jssdk->GetSignPackage();

$this->registerJs('
    var geocoder,map,marker = null;
    var address = $(".addr").val();
    map = new qq.maps.Map(document.getElementById("allmap"),{
        center: new qq.maps.LatLng(39.916527,116.397128),
        zoom: 12,
        disableDefaultUI: true
    });

    geocoder = new qq.maps.Geocoder({
        complete : function(result){
            if (marker)
                marker.setMap(null);
            map.setCenter(result.detail.location);
            map.zoomTo(18);
            marker = new qq.maps.Marker({
                map:map,
                position: result.detail.location
            });
        }
    });
    geocoder.getLocation(address);

    var appId = "' . $signPackage["appId"] . '",
        timestamp = ' . $signPackage["timestamp"] . ',
        nonceStr = "' . $signPackage["nonceStr"] . '",
        signature = "' . $signPackage["signature"] . '";
    //点击任务打卡使用微信接口获取经纬度定位
    wx.config({
        debug: false,
        appId: appId,
        timestamp: timestamp,
        nonceStr: nonceStr,
        signature: signature,
        jsApiList: [
            "getLocation"
        ]
    });
    wx.ready(function(){
    	$(".btn-click").show();
	    $(".btn-click").click(function(){
	    	$(".loaded").show();
	        // 微信定位获取坐标
	        wx.getLocation({
	            type: "gcj02",
	            success: function (res) {
	                // 根据坐标获取地址
	                $.ajax({
	                    type: "GET",
	                    url:"http://apis.map.qq.com/ws/geocoder/v1/?location="+res.latitude+","+res.longitude+"&output=jsonp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ",
	                    async: false,
	                    dataType: "jsonp",
	                    success: function (msg, textStatus) {
	                    	$(".loaded").hide();
	                        // 提交数据
	                        $("#Modal1").modal();
	                        $("#Modal1 #btn_submit").click(function (){
	                            $("#start_address").val(msg.result.formatted_addresses.recommend);
	                            $("#start_longitude").val(res.longitude);
	                            $("#start_latitude").val(res.latitude);
	                            $("#w0").submit();
	                        })
	                    }
	                })
	            }
	        })
	        wx.error(function (res) {
	            alert("获取定位失败，请重试");
	        });
	        return false;
	    });
	});
    document.body.addEventListener("touchmove", function (event) {
        if ($("body").attr("class")=="modal-open") {
            event.preventDefault();
        }
    });
');
?>
<style type="text/css">
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
	#allmap {
		width:100%;
		margin-bottom: 3%;
		height:200px;
	}
	.btn-click, .btn-accept{
		width:100%;
	}
	.btn-click{
		display:none;
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
</style>
<div id="task_detail">
	<div class="form-group">
		楼宇名称：
		<?php echo isset($distributeTaskArr->build->name) ? $distributeTaskArr->build->name : ''; ?></div>
	<div class="form-group">
		所在地址：
		<?php echo isset($distributeTaskArr->build) ? $distributeTaskArr->build->province . $distributeTaskArr->build->city . $distributeTaskArr->build->area . $distributeTaskArr->build->address : ''; ?></div>
	<div class="form-group">
		地图：
		<div id="allmap"></div>
	</div>
	<div class="form-group">
		创建时间：
		<?php echo date('Y-m-d H:i', $distributeTaskArr['create_time']); ?>
	</div>

	<?php if ($distributeTaskArr['task_type'] ) {
		?>
		<div class="form-group">
			维修内容：
			<br/>
			<?php echo EquipSymptom::getSymptomNameStr($distributeTaskArr['malfunction_task']); ?>
		</div>
		<div class="form-group">
			配送内容：
			<?php foreach (json_decode($distributeTaskArr['delivery_task'], true) as $distributeTask =>
						   $distributeTaskArray) {
				?>
				<div  class="form-group">
					<?php $materialObj = ScmMaterial::getMaterialObj(['id' =>
							$distributeTaskArray['material_id']]);
					echo '物料名称：' . $materialObj->name;

					echo !$materialObj->weight ? '' : "，规格:" . $materialObj->weight . $materialObj->materialType->spec_unit;

					echo "，数量：" . $distributeTaskArray['packets'] . " " . $materialObj->materialType->unit;?>
				</div>
			<?php }?>
		</div>
	<?php } else if ($distributeTaskArr['task_type']) {?>
		<div class="form-group">
			维修内容：
			<br/>
			<?php echo EquipSymptom::getSymptomNameStr($distributeTaskArr['malfunction_task']); ?>
		</div>
	<?php } else if ($distributeTaskArr['task_type']) {
		?>
		<div class="form-group">
			配送内容：
			<?php foreach (json_decode($distributeTaskArr['delivery_task'], true) as $distributeTask =>
						   $distributeTaskArray) {
				?>
				<div  class="form-group">
					<?php $materialObj = ScmMaterial::getMaterialObj(['id' =>
							$distributeTaskArray['material_id']]);
					echo '物料名称：' . $materialObj->name;
					echo !$materialObj->weight ? '' : "，规格:" . $materialObj->weight . $materialObj->materialType->spec_unit;
					echo "，数量：" . $distributeTaskArray['packets'] . ' ' . $materialObj->materialType->unit;?>
				</div>
			<?php }?>
		</div>
	<?php }?>

	<?php if($distributeTaskArr['task_type'] == DistributionTask::REFUEL):?>
		<div class="form-group">
			换料内容：
			<?php foreach (json_decode($distributeTaskArr['delivery_task'], true) as $distributeTask =>
						   $distributeTaskArray) {
				?>
				<div  class="form-group">
					<?php $materialObj = ScmMaterial::getMaterialObj(['id' =>
						$distributeTaskArray['material_id']]);
					echo '物料名称：' . $materialObj->name;

					echo !$materialObj->weight ? '' : "，规格:" . $materialObj->weight . $materialObj->materialType->spec_unit;

					echo "，数量：" . $distributeTaskArray['packets'] . " " . $materialObj->materialType->unit;?>
				</div>
			<?php }?>
		</div>
	<?php endif;?>
	<?php if ($distributeTaskArr['remark']) {?>
		<div class="form-group" style="word-break:break-all;">
			备注：<?php echo $distributeTaskArr['remark']; ?>
		</div>
	<?php }?>
	<input type="hidden" class="addr" value="<?php echo isset($distributeTaskArr->build) ? $distributeTaskArr->build->province . $distributeTaskArr->build->city . $distributeTaskArr->build->area . $distributeTaskArr->build->address . $distributeTaskArr->build->name : ''; ?>">
	<?php
	$form = ActiveForm::begin(['action' => ['distribution-task/daily-clock', 'id' => $distributeTaskArr['id']], 'method' => 'get']);
	if (!$distributeTaskArr['recive_time']) {
		?>
		<?=Html::button('接收任务', ['class' => 'btn btn-success btn-accept', 'type' => 'submit'])?>
	<?php } else if (!$distributeTaskArr['start_delivery_time']) {?>
		<?=Html::hiddenInput('start_latitude', '', ['id' => 'start_latitude'])?>
		<?=Html::hiddenInput('start_longitude', '', ['id' => 'start_longitude'])?>
		<?=Html::hiddenInput('start_address', '', ['id' => 'start_address'])?>
		<?=Html::button('任务打卡', ['class' => 'btn btn-success btn-click'])?>
	<?php }
	ActiveForm::end();?>
</div>
<input type="hidden" class="form-control"  id="location1" value="" />
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
