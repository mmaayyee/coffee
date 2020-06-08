<?php
use yii\helpers\Html;

$this->registerJsFile('http://map.qq.com/api/js?v=2.exp&libraries=drawing&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/equip-map.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerJsFile('@web/js/select2/select2.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/select2/select2-zh-CN.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('@web/js/select2/select2.min.css');
$this->registerCssFile('@web/js/select2/select2-bootstrap.min.css');
$this->registerJs('
    $("#build_name").select2({
        placeholder: "请选择楼宇",
        allowClear: true,
        theme: "bootstrap"
    });
    // $("#map").height($(window).height()-160);
')
?>
<script type="text/javascript">
var org_city = "<?php echo $city; ?>";
</script>
<p>
    <?=Html::a('返回上一页', '/equipments/index', ['class' => 'btn btn-success'])?>
</p>
<div class="form-group form-inline">
    <div class="form-group">
        <label>城市</label>
        <input type="hidden" id ='checkedProvice' value="<?php echo $city; ?>">
        <?=Html::dropDownList('city', $city, $buildCity, ['class' => 'form-control', 'id' => 'city']);?>
    </div>
    <div class="form-group">
        <label>楼宇</label>
        <select id="build_name" class="form-control js-example-basic-multiple" name="build_name">
            <?php echo $buildIdNameOption; ?>
        </select>
    </div>

    <div class="form-group">
        <label>地址</label>
        <input type="text" class="form-control" id ='address' value="">
    </div>

    <div class="form-group">
        <input class="equip_status1 normal" name="equipment_status1" type="checkbox" checked="checked" value="1" />显示正常未锁定设备(<span style="color:green">绿</span>)
        <input class="equip_status2 malfunction" name="equipment_status2" type="checkbox"  checked="checked" value="2" />显示故障未锁定设备(<span style="color:red">红</span>)
        <input class="equip_status3 locking" name="equipment_status3" type="checkbox"  checked="checked" value="2" />显示已锁定设备(<span style="color:blue">蓝</span>)
    </div>


</div>

<!-- 设备详情页中跳转过来的默认传输的build_id值 -->
<input type="hidden" id="build_id" value="<?php echo $build_id ?>">

<div id="build_list" data-list='<?php echo json_encode($build_list); ?>'></div>
<div id="map"></div>
<div id="build_list" data-list='<?php echo json_encode($build_list); ?>'></div>
<style>
.modal {
    left:auto;
    right:20px;
    top:20px;
}
.del_build {
    float:right;
    cursor:pointer;
}
.modal-body label {
    display: block;
}
.step_1, .step_2, .step_3 {
    display: none;
}
#map {
    width: 100%;
    max-width: 1200px;
    height:600px;
}
</style>