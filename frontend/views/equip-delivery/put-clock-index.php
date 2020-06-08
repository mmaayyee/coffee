<?php
use frontend\models\JSSDK;
use yii\widgets\DetailView;
$this->title = '投放待办';
$this->registerJsFile('http://map.qq.com/api/js?v=2.exp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('http://res.wx.qq.com/open/js/jweixin-1.0.0.js', ['depends' => ['frontend\assets\AppAsset']]);
$jssdk       = new JSSDK(yii::$app->params['corpid'], yii::$app->params['secret']['address_book']);
$signPackage = $jssdk->GetSignPackage();
$this->registerJs('
    map = new qq.maps.Map(document.getElementById("allmap"),{
        center: new qq.maps.LatLng($(".latitude").val(),$(".longitude").val()),
        zoom: 18,
        disableDefaultUI: true
    });
    marker = new qq.maps.Marker({
        map:map,
        position: new qq.maps.LatLng($(".latitude").val(),$(".longitude").val())
    });
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
    document.body.addEventListener("touchmove", function (event) {
        if ($("body").attr("class")=="modal-open") {
            event.preventDefault();
        }
    });
    $("#task_start").click(function(){
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
                        // 提交数据
                        $("#Modal1").modal();
                        $("#Modal1 #btn_submit").click(function (){
                            $("#start_address").val(msg.result.formatted_addresses.recommend);
                            $("#start_longitude").val(res.longitude);
                            $("#start_latitude").val(res.latitude);
                            $("#acceptance_form").submit();
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
');
?>
<style>
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
</style>
<div>
	<div>
		<h5>楼宇名称：<?php echo isset($model->build->name) ? $model->build->name : ""; ?></h5>
	</div>
	<div>
		<h5>所在地址：<?php echo isset($model->build->address) ? $model->build->province.$model->build->city.$model->build->area.$model->build->address : ''; ?></h5>
	</div>

	<input type="hidden" class="longitude" value="<?php echo isset($model->build->longitude) ? $model->build->longitude : ''; ?>">
	<input type="hidden" class="latitude" value="<?php echo isset($model->build->latitude) ? $model->build->latitude : ''; ?>">

	<div id="allmap"  style="width:100%;height:200px;margin-bottom:1rem ;">北京</div>

    <div class="equip-delivery-view">
    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        [
            'attribute' => 'build_id',
            'value'     => isset($model->build->name) ? $model->build->name : '',
        ],
        [
            'attribute' => 'equip_type_id',
            'value'     => isset($model->equipType->model) ? $model->equipType->model : '',
        ],
        [
            'attribute' => 'delivery_time',
            'value'     => !empty($model->delivery_time) ? date('Y-m-d', $model->delivery_time) : '暂无',
        ],

        [
            'attribute' => 'sales_person',
            'value'     => $model->sales_person,
        ],
        [
            'attribute' => 'delivery_status',
            'value'     => $model->equipDeliveryStatusArray($model->delivery_status)[$model->delivery_status],
        ],
        [
            'attribute' => 'create_time',
            'value'     => !empty($model->create_time) ? date('Y-m-d H:i:s', $model->create_time) : '暂无',
        ],
        [
            'attribute' => 'is_ammeter',
            'value'     => $model->is_ammeter == 0 ? '否' : "是",
        ],
        [
            'attribute' => 'is_lightbox',
            'value'     => isset(\backend\models\EquipDelivery::getLightBoxArr()[$model->is_lightbox]) ? \backend\models\EquipDelivery::getLightBoxArr()[$model->is_lightbox] : '',
        ],
        'special_require',
        [
            'attribute' => 'update_time',
            'value'     => !empty($model->update_time) ? date('Y-m-d H:i:s', $model->update_time) : '暂无',
        ],
    ],
])?>
    </div>


    <div class="form-group">
        <form id="acceptance_form" action="/equip-delivery/acceptance" method="get">
        <input type="hidden" name="delivery_id" value="<?php echo $model->Id ?>" />
        <?php if (!$taskModel->recive_time) {?>
            <input type="submit" class="btn btn-block btn-success" value="接收任务"/>
        <?php } else {?>
            <input type="hidden" id="start_latitude" name="start_latitude" value="" />
            <input type="hidden" id="start_longitude" name="start_longitude" value="" />
            <input type="hidden" id="start_address" name="start_address" value="" />
            <input type="submit" id="task_start" class="btn btn-block btn-success" value="任务打卡"/>
        <?php }?>
        </form>
    </div>

</div>

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