<?php  
use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Building;
use backend\models\EquipDelivery;
use yii\helpers\Url;
$deliveryModel = new EquipDelivery();

$this->title = '水单详情';
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
');
?>
<style type="text/css">
   #allmap{
      margin: 2% 0 3%;
   }
</style>
<div id="task_detail">
    <div class="form-inline">
      楼宇名称：<?php echo isset($distributeWaterArr->build->name) ? $distributeWaterArr->build->name : ''; ?>
    </div>
    <div class="form-inline">
      所在地址：<?php echo isset($distributeWaterArr->build) ? $distributeWaterArr->build->address : ''; ?>
    </div>
    <div class="form-inline">
      地图：<div id="allmap"  style="width:100%;height:200px;"></div>
    </div>
    <div class="form-inline">
      接收任务时间：<?php echo date('Y-m-d H:i', $distributeWaterArr['order_time']); ?>
    </div>
    <div class="form-inline">
      需水量：<?php echo floatval($distributeWaterArr['need_water']); ?>桶
    </div>

    <input type="hidden" class="addr" value="<?php echo isset($distributeWaterArr->build) ? $distributeWaterArr->build->province.$distributeWaterArr->build->city.$distributeWaterArr->build->area.$distributeWaterArr->build->address.$distributeWaterArr->build->name : ''; ?>">
    
    <div class="form-group" style="margin-top: 15%;">
      <form action="<?php echo Url::to(['distribution-water/delivery-complete-detail']) ?>" method ='get'>
        <input type="hidden" class="id" name="id" value="<?php echo $distributeWaterArr['Id'] ?>">
        <?= Html::submitButton( '配送完成' , ['class' => 'btn btn-block btn-success btn-start-click' ]) ?>
      </form>
    </div>
</div>