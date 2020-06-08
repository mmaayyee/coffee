<?php
use backend\models\DistributionTask;
use backend\models\EquipDelivery;
use backend\models\ScmMaterial;
use backend\models\ScmSupplier;
use frontend\models\FrontendDistributionTask;

$deliveryModel = new EquipDelivery();
$this->title   = "任务详情";
$this->registerJsFile('http://map.qq.com/api/js?v=2.exp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ', ['depends' => [\yii\web\JqueryAsset::className()]]);

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
      margin:3% 0;
      width:100%;
      height:200px;
   }
</style>
<?php
// if (in_array(DistributionTask::SERVICE,$distributeTaskArr['task_type']) ) {
//     echo  DistributionTask::getMaintenanceData($distributeTaskArr['id']);
// }elseif(in_array(DistributionTask::DELIVERY,$distributeTaskArr['task_type']) || in_array(DistributionTask::REFUEL,$distributeTaskArr['task_type'])){
//     echo DistributionTask::getDistributionData($distributeTaskArr['id']);
// }
?>
<div id="task_detail">
    <div class="form-group">
      <label>楼宇名称：</label>
      <?php echo isset($distributeTaskArr->build->name) ? $distributeTaskArr->build->name : ''; ?>
    </div>
    <div class="form-group">
      <label>所在地址：</label>
      <?php echo isset($distributeTaskArr->build) ? $distributeTaskArr->build->province . $distributeTaskArr->build->city . $distributeTaskArr->build->area . $distributeTaskArr->build->address : ''; ?>
    </div>
    <div class="form-group">
      <label>地图：</label>
      <div id="allmap"></div>
    </div>

    <div class="form-group">
      <label>任务开始时间：</label>
      <?php if ($distributeTaskArr->start_delivery_time) {echo date('Y-m-d H:i:s', $distributeTaskArr->start_delivery_time);} else {echo "";}?>
    </div>
    <div class="form-group">
      <label>任务结束时间：</label>
      <?php if ($distributeTaskArr->end_delivery_date) {echo $distributeTaskArr->end_delivery_date;} else {echo "";}?>
    </div>

    <div class="form-group">
      <label>任务类别：</label>
      <?php
echo $distributeTaskArr->task_type ? DistributionTask::getTaskType($distributeTaskArr->task_type) : '';
?>
    </div>

    <div class="form-group">
      <label>任务内容：</label>
        <?php if (!empty($distributeTaskArr->content)) {echo $distributeTaskArr->content;} else {echo "暂无";}?>
    </div>

    <div class="form-group" style="word-break:break-word">
      <label>备注：</label>
        <?php if (!empty($distributeTaskArr->remark)) {echo $distributeTaskArr->remark;} else {echo "暂无";}?>
    </div>

    <div class="form-group">
      <label>电表读数(度)：</label>
      <?php if ($distributeTaskArr->meter_read) {
    echo $distributeTaskArr->meter_read;
} else {
    echo "暂无";
}?>
    </div>
    <div class="form-group">
      <label>添加水量(桶)：</label>
      <?php if ($distributeTaskArr->add_water) {
    echo $distributeTaskArr->add_water;
} else {
    echo "暂无";
}?>
</div>
   <div class="form-group">
      <label>添加后剩余水量(桶)：</label>
      <?php if ($distributeTaskArr->surplus_water) {
    echo $distributeTaskArr->surplus_water;
} else {
    echo "暂无";
}?>
    </div>
    <!-- 配送数据 -->
    <div class="form-group">
      <?php if ($fillerArr) {?>
          <label>添料整包数据：</label>
          <table class="table table-bordered">
            <tr>
              <td>物料名称</td>
              <td>物料规格</td>
              <td>数量</td>
            </tr>
          <?php foreach ($fillerArr as $key => $value) {?>
          <?php $materialObj = ScmMaterial::getMaterialObj(['id' => $value['material_id']]);?>
            <tr>
              <td>
                <?php echo $materialObj->name; ?>
              </td>
              <td>
                <?php echo $materialObj->weight ? $materialObj->weight . $materialObj->materialType->spec_unit : ''; ?>
              </td>
              <td>
                <?php echo $value['number']; ?>
              </td>
            </tr>
          <?php }?>
          </table>
      <?php }?>
    </div>
    <div class="form-group">
    <!-- 散料-->
    <?php if ($fillerGram): ?>
            <label>添料散料数据：</label>
            <table class="table table-bordered">
                <tr>
                    <td>物料分类</td>
                    <td>供应商</td>
                    <td>重量(克/个)</td>
                </tr>
                <?php foreach ($fillerGram as $k => $gram): ?>
                    <tr>
                        <td><?php echo isset($materialType[$gram['material_type_id']]) ? $materialType[$gram['material_type_id']] : '' ?></td>
                        <td><?php echo ScmSupplier::getField('name', ['id' => $gram['supplier_id']]); ?></td>
                        <td><?php echo $gram['gram'] ?></td>
                    </tr>

                <?php endforeach;?>
            </table>
    <?php endif;?>
    </div>
    <!-- 维修数据 -->
    <div class="form-group">
      <?php if ($maintenanceArr) {
    ?>
        <label>维修：</label>
        <table class="table table-bordered">
            <tr>
                <td>开始维修时间</td>
                <td>
                    <?php if ($distributeTaskArr->start_delivery_time) {
        echo date("Y-m-d H:i", $distributeTaskArr->start_delivery_time);
    } else {
        echo "暂无";
    }?>
                </td>
            </tr>
            <tr>
                <td>结束维修时间</td>
                <td>
                    <?php if ($distributeTaskArr->end_delivery_time) {
        echo date("Y-m-d H:i", $distributeTaskArr->end_delivery_time);
    } else {
        echo "暂无";
    }?></td>
            </tr>
        	<?php foreach ($maintenanceArr as $key => $value) {
        ?>
            <tr>
              <td>故障原因</td>
              <td>
                <?php if ($value['malfunction_reason']) {
            echo FrontendDistributionTask::getMalfunctionReasonStr($value['malfunction_reason']);
        } else {
            echo "暂无";
        }?></td>
            </tr>
            <tr>
              <td>故障描述</td>
              <td>
                <?php if ($value['malfunction_description']) {
            echo $value['malfunction_description'];
        } else {
            echo "暂无";
        }?></td>
            </tr>
            <tr>
              <td>处理方法</td>
              <td>
                <?php if ($value['process_method']) {
            echo $value['process_method'];
        } else {
            echo "暂无";
        }?></td>
            </tr>
            <tr>
              <td>处理结果</td>
              <td>
                <?php if ($value['process_result'] == 2) {
            echo "<b style='color:#e4393c; width:100px;height:50px;'>未修复</b>";
        } else if ($value['process_result'] == 3) {
            echo "<p>已修复</p>";
        } else {
            echo "暂无";
        }?>
              </td>
            </tr>
          <?php }?>
        </table>
      <?php }?>
    </div>
</div>
    <input type="hidden" class="addr" value="<?php echo isset($distributeTaskArr->build) ? $distributeTaskArr->build->province . $distributeTaskArr->build->city . $distributeTaskArr->build->area . $distributeTaskArr->build->address . $distributeTaskArr->build->name : ''; ?>">
