<?php  
use backend\models\EquipDelivery;
use common\models\Building;
use common\models\Equipments;
use yii\helpers\Url;
$deliveryModel = new EquipDelivery();

$this->title = '投放详情';
$this->registerJsFile('http://map.qq.com/api/js?v=2.exp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ',['depends' => [\yii\web\JqueryAsset::className()]]);

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

    var deliveryId = $(".deliveryId").val();
    function ajaxByDeliveryId(ident){
        $.get(
            "/equip-delivery/ajax-equip-acceptance", 
            {"delivery_id": deliveryId,"detail": ident},
            function(data){
                $(".details_table tbody").empty();
                if (data){
                    var j = 1;
                    for (var i in data) {
                        if(data[i].ret_result == "true"){
                            var tr  = "<tr><td>"+j+"</td><td style=\'width:200px\'>"+data[i].debug_item+"</td><td style=\'text-align:center;\'><img style=\'max-width:17px\' src=\'/images/right.jpeg\'/></td></tr>";
                        }else{
                            var tr  = "<tr><td>"+j+"</td><td style=\'width:200px\'>"+data[i].debug_item+"</td><td style=\'text-align:center;\'><img style=\'max-width:20px\' src=\'/images/error.png\'/></td></tr>";
                        }
                        j++;
                        $(".details_table tbody").append(tr);
                    }
                }
            },
           "json"
        );
   }
   ajaxByDeliveryId("equip_detail");
   $(".equip_detail").click(function() {
      ajaxByDeliveryId("equip_detail");
   });

   $(".lightbox_detail").click(function() {
      ajaxByDeliveryId("lightbox_detail");
   });
');
?>
<style type="text/css">
   #allmap{
     width:100%;
     height:200px;
   }
   .table > thead > tr > th{
   		vertical-align: middle;
   		text-align: center;
   }
   .equip_detail,.lightbox_detail{
   	margin-left:2% ;
   }
</style>
<div id="task_detail">

   <div class="form-group">
      楼宇名称：<?php echo Building::getBuildingDetail('name', ['id'=>$deliveryArr['build_id']])['name']; ?>
    </div>
    <div class="form-group">
      所在地址：<?php echo Building::getBuildingDetail('province,city,area,address', ['id'=>$deliveryArr['build_id']])['province'].Building::getBuildingDetail('province,city,area,address', ['id'=>$deliveryArr['build_id']])['city'].Building::getBuildingDetail('province,city,area,address', ['id'=>$deliveryArr['build_id']])['area'].Building::getBuildingDetail('province,city,area,address', ['id'=>$deliveryArr['build_id']])['address'] ?>
    </div>
    <div class="form-group">
        设备编号：<?php echo Equipments::getField('equip_code',['build_id' => $deliveryArr['build_id']]);?>
    </div>
    <div class="form-group">
      地图：<div id="allmap"></div>
    </div>
    <div class="form-group">
      投放结果：<?php  
                echo $deliveryModel->deliveryResultArray()[$deliveryArr['delivery_result']];
              ?>
    </div>
    <div class="form-group">
      投放状态：<?php
                echo $deliveryModel->equipDeliveryStatusArray()[$deliveryArr['delivery_status']];
              ?>
    </div>
    <div class="form-group">
        浓度值：<?php
        echo Equipments::getField('concentration',['build_id' => $deliveryArr['build_id']]);
        ?>
    </div>

    <?php if($waterInfo):?>
        <div class="form-group">
            当前水量：<?php
            echo intval($waterInfo['surplus_water']).'桶';
            ?>
        </div>
        <div class="form-group">
            供水商：<?php
            echo $waterInfo['supplier_id'] ? \backend\models\ScmSupplier::getField('name',['id' => $waterInfo['supplier_id']]) : '';
            ?>
        </div>
        <div class="form-group">
            需水量：<?php
            echo intval($waterInfo['need_water']).'桶';
            ?>
        </div>
    <?php endif;?>
    <!-- 投放成功未运营 -->
    <?php if ($deliveryArr['delivery_status'] == EquipDelivery::UN_TRAFFICK_SUCCESS){ ?> 
      <div class="form-group">
        原因：<?php echo $deliveryArr['reason'] ?>
      </div>
      
      <div class="form-group" style="word-break:break-word;">
        备注：<?php echo $deliveryArr['remark'] ?>
      </div>
    <?php }elseif ($deliveryArr['delivery_status'] == EquipDelivery::DELIVERY_FAILURE) { ?>
      <?php if($repair_remark){ ?>
      <div class="form-group" style="word-break:break-word;">
        备注：<?php echo $repair_remark ?>
      </div>
    <?php } }?>

    <div class="form-group">
      验收开始时间：<?php echo date("Y年m月d日 H时i分", $recive_time) ?>
    </div>
    <div class="form-group">
      验收结束时间：<?php echo date("Y年m月d日 H时i分", $end_repair_time) ?>
    </div>
    <div class="form-group">
      设备验收结果：<?php if($acceptEptanceArr['accept_result']==1 ||$acceptEptanceArr['accept_result']==3){echo '通过';}else{ echo "不通过";} ?>
            <a class="equip_detail btn btn-success">详情</a>
    </div>
    <?php if($deliveryArr->is_lightbox > 0) {?>
    <div class="form-group">
      灯箱验收结果：<?php if($acceptEptanceArr['accept_result']==2 || $acceptEptanceArr['accept_result']==3){echo '通过';}else{ echo "不通过";} ?>
            <a class="lightbox_detail btn btn-success">详情</a>
    </div>
    <?php } ?>
    <table class="table table-bordered details_table">
        <thead style="text-align: center;">
        	<tr><th>序号</th><th>验收项</th><th>结果</th></tr>
        </thead>
        <tbody></tbody>
    </table>

    <input type="hidden" class="deliveryId" value="<?php echo $deliveryArr['Id']; ?>">
    <input type="hidden" class="addr" value="<?php echo Building::getBuildAddress($deliveryArr['build_id']) ?>">
</div>
