<?php
use frontend\models\JSSDK;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->registerJsFile('@web/js/vconsole.min.js');
$this->registerJsFile('http://map.qq.com/api/js?v=2.exp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('https://res.wx.qq.com/open/js/jweixin-1.2.0.js', ['depends' => ['frontend\assets\AppAsset']]);
$this->registerJsFile('@web/js/bootstrap3-validation.js?v=2', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/distribution-daily-task.js?v=6.0', ['depends' => [\yii\web\JqueryAsset::className()]]);
$jssdk       = new JSSDK(yii::$app->params['corpid'], yii::$app->params['secret']['address_book']);
$signPackage = $jssdk->GetSignPackage();
$this->title = '新增加料任务';
$this->registerJs('
    //var vConsole = new VConsole();
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
    .field-equiptask-content label {
        margin-right: 1.5rem;
    }
    .field-equiptask-content label input[type="checkbox"] {
        vertical-align: middle;
        margin: 0 0 .1rem;
    }
</style>
<div>
    <?php $form = ActiveForm::begin([
    'method' => 'post',
    'id'=>'w0'
    ]) ?>
    <?=$form->field($model, 'build_id')->widget(Select2::className(), [
    'data'    => \common\models\Building::getOperationBuildStore(1, $userId),
    'options' => ['placeholder' => '请选择楼宇'],
])?>
        <?=Html::hiddenInput('start_latitude', '', ['id' => 'start_latitude'])?>
        <?=Html::hiddenInput('start_longitude', '', ['id' => 'start_longitude'])?>
        <?=Html::hiddenInput('start_address', '', ['id' => 'start_address'])?>
    <div class="form-group">
        <?=Html::button($model->isNewRecord ? '创建' : '确定', ['class' => $model->isNewRecord ? 'btn btn-success btn-click' : 'btn btn-primary'])?>
    </div>
    <?php ActiveForm::end();?>
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
                <div class="form-group title">您确定要创建吗？</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button><button type="button" id="btn_submit" class="btn btn-primary" data-dismiss="modal">确定</button>
            </div>
        </div>
    </div>
</div>
<div class="loaded" style="display: none">
   <!--  <img src="/images/loading.gif"> -->
</div>
