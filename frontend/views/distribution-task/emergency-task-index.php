<?php
use frontend\models\JSSDK;
use yii\helpers\Html;
use backend\models\ScmSupplier;
use yii\helpers\Url;
use backend\models\DistributionTask;
use common\models\Equipments;
$this->registerJsFile('http://map.qq.com/api/js?v=2.exp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('http://res.wx.qq.com/open/js/jweixin-1.2.0.js?v=2.0', ['depends' => ['frontend\assets\AppAsset']]);
$this->registerJsFile('@web/js/bootstrap3-validation.js?v=2', ['depends' => [\yii\web\JqueryAsset::className()]]);
$jssdk       = new JSSDK(yii::$app->params['corpid'], yii::$app->params['secret']['address_book']);
$signPackage = $jssdk->GetSignPackage();
$this->title = '任务打卡|紧急任务完成';
$this->registerJs('

    //水单验证
    $("#emergency-form").validation();

    var geocoder,map,marker = null;
    var address = $(".addr").val();
    map = new qq.maps.Map(document.getElementById("allmap"),{
        center: new qq.maps.LatLng(39.916527,116.397128),
        zoom: 12,
        disableDefaultUI: true
    });

    geocoder = new qq.maps.Geocoder({
        complete : function(result){
            if (marker) {
                marker.setMap(null);
            }
            map.setCenter(result.detail.location);
            map.zoomTo(18);
            marker = new qq.maps.Marker({
                map:map,
                position: result.detail.location
            });
        }
    });

    $(".btn-start-click").show();
    $(".btn-start-click").click(function(){
        $(".loaded").show();
        getLocation();
        })
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
    function getLocation(){
        wx.ready(function(){
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
                                $("#emergency-form").submit();
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
        }

    document.body.addEventListener("touchmove", function (event) {
        if ($("body").attr("class")=="modal-open") {
            event.preventDefault();
        }
    });

    $(".btn-end-click").click(function(){

        //验证水单全填或者全不填
       var surplusWater = $("#surplusWater").val(),    // 剩余水量
                supplierWater = $("#supplierWater").val(),  // 供水商
                needWater = $("#needWater").val();          // 需水量

            if (surplusWater || supplierWater || needWater) {
                if (!surplusWater) {
                    $(\'#surplusWater\').parent().removeClass(\'has-success\').addClass(\'has-error\');
                    if ($(\'#surplusWater\').next().attr(\'class\')) {
                        $(\'#surplusWater\').next().html(\'请填写剩余水量！\');
                    } else {
                        $(\'#surplusWater\').parent().append(\'<span class="help-block" id="valierr">请填写剩余水量！</span>\');
                    }
                    return false;
                }
                if (!supplierWater) {
                    $(\'#supplierWater\').parent().removeClass(\'has-success\').addClass(\'has-error\');
                    if ($(\'#supplierWater\').next().attr(\'class\')) {
                        $(\'#supplierWater\').next().html(\'请填写供水商！\');
                    } else {
                        $(\'#supplierWater\').parent().append(\'<span class="help-block" id="valierr">请填写供水商！</span>\');
                    }
                    return false;
                }
                if (!needWater) {
                    $(\'#needWater\').parent().removeClass(\'has-success\').addClass(\'has-error\');
                    if ($(\'#needWater\').next().attr(\'class\')) {
                        $(\'#needWater\').next().html(\'请填写需水量！\');
                    } else {
                        $(\'#needWater\').parent().append(\'<span class="help-block" id="valierr">请填写需水量！</span>\');
                    }
                    return false;
                }
            }


        $("#emergency-form").validation();
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
                        $("#end_address").val(msg.result.formatted_addresses.recommend);
                        $("#end_longitude").val(res.longitude);
                        $("#end_latitude").val(res.latitude);
                        $("#emergency-form").submit();
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
<script src="/js/rem.js"></script>
<script src="/js/vconsole.min.js"></script>
<script type="text/javascript">
    if(window.location.host.split(".")[0]!="erp") {
      var vConsole = new VConsole();
    }
</script>
<style type="text/css">
   #allmap{
    width:100%;
    margin-top: .1rem;
    margin-bottom: 3%;
    height:200px;"
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
   .form-group.title {
        font-size: .28rem;
        margin-bottom: .2rem;
   }
   .form-group.txt {
    color: #999;
   }
    .btn-success {
        /*height: 3.2rem;*/
        width:100%;
        font-size: .3rem;
        color: #fff;
        background-color: #3399ff;
    }
</style>
<div id="task_detail">
    <div class="form-group title">
      楼宇名称：<?php echo isset($distributeTaskArr->build->name) ? $distributeTaskArr->build->name : ''; ?>
    </div>
    <div class="form-group txt">
      所在地址：<?php echo isset($distributeTaskArr->build) ? $distributeTaskArr->build->province . $distributeTaskArr->build->city . $distributeTaskArr->build->area . $distributeTaskArr->build->address : ''; ?>
    </div>
    <div class="form-group txt">
      地图：<div id="allmap"></div>
    </div>
    <?php
        $taskTypeArr = explode(',',$distributeTaskArr->task_type);
        if(count($taskTypeArr)==1&&in_array(DistributionTask::URGENT,$taskTypeArr)){
    ?>
    <div class="form-group txt">
       任务详情 : <?php echo $distributeTaskArr->content;?>
    </div>
    <?php }else{ ?>
    <div class="form-group txt">
        故障现象：
        <?php
        $abnormalContent = '';
        if (in_array(DistributionTask::SERVICE,$taskTypeArr)) {
            //维修
            $abnormalContent .= DistributionTask::getMaintenanceData($distributeTaskArr->id).'<br/>';
        }
        echo $abnormalContent;
        ?>
    </div>
    <div class="form-group txt">
        设备最新日志：<?php echo Equipments::find()->where(['build_id'=>$distributeTaskArr->build_id])->select('last_log')->scalar();?>
    </div>
    <div class="form-group txt">
        配送物料：<?php
                    $deliveryContent = '';
                    if (in_array(DistributionTask::DELIVERY,$taskTypeArr) || in_array(DistributionTask::REFUEL,$taskTypeArr)) {
                        //配送
                        $deliveryContent .=DistributionTask::getDeliveryShowData($distributeTaskArr->id);
                    }
       echo  $deliveryContent;
                ?>
    </div>
    <div class="form-group txt">
       任务详情 : <?php echo $distributeTaskArr->content;?>
    </div>
    <div class="form-group txt">
       配送备注 : <?php echo $distributeTaskArr->remark;?>
    </div>
    <?php }?>
    <input type="hidden" class="addr" value="<?php echo isset($distributeTaskArr->build) ? $distributeTaskArr->build->province . $distributeTaskArr->build->city . $distributeTaskArr->build->area . $distributeTaskArr->build->address . $distributeTaskArr->build->name : ''; ?>">
    <br/>
    <div class="form-group">
        <?php if($distributeTaskArr->start_delivery_time == 0){?>
        <form id="emergency-form" action="<?php echo Url::to(['/distribution-task/emergency-clock'])?>" method="get">
            <?=Html::hiddenInput('id', $distributeTaskArr['id']);?>
            <?=Html::hiddenInput('start_latitude', '', ['id' => 'start_latitude']);?>
            <?=Html::hiddenInput('', $distributeTaskArr['id']);?>
            <?=Html::hiddenInput('start_longitude', '', ['id' => 'start_longitude']);?>
            <?=Html::hiddenInput('start_address', '', ['id' => 'start_address']);?>
            <?=Html::button('任务打卡', ['class' => 'btn btn-success btn-start-click'])?>
        </form>
    <?php }elseif (in_array(DistributionTask::URGENT,$taskTypeArr)&&count($taskTypeArr) == 1&&$distributeTaskArr->start_delivery_time > 0) {?>
         <form id="emergency-form" action="<?php echo Url::to(['/distribution-task/urgent-complete'])?>" method="get">
            <?=Html::hiddenInput('id', $distributeTaskArr['id']);?>
            <?=Html::hiddenInput('end_latitude', '', ['id' => 'end_latitude']);?>
            <?=Html::hiddenInput('', $distributeTaskArr['id']);?>
            <?=Html::hiddenInput('end_longitude', '', ['id' => 'end_longitude']);?>
            <?=Html::hiddenInput('end_address', '', ['id' => 'end_address']);?>
            <?=Html::button('任务完成', ['class' => 'btn btn-success btn-end-click'])?>
        </form>
    <?php }?>
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
<div class="loaded">
    <img src="/images/loading.gif">
</div>
