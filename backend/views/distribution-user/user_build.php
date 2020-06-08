<?php
use backend\models\Organization;
use yii\helpers\Html;

$this->registerJsFile('http://map.qq.com/api/js?v=2.exp&libraries=drawing&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/user_build.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
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
    $("#map").height($(window).height()-143);
')
?>


<div class="form-inline">
    <div class="form-group">
        <?=Html::dropDownList('org_id', $org_id, Organization::getManagerOrgIdNameArr(['>', 'org_id', 1]), ['class' => 'form-control', 'id' => 'org_id']);?>
    </div>

    <div class="form-group select2-search">
        <select id="build_name" class="form-control" name="build_name" style="width: 100%">
        <?php echo $buildIdNameOption; ?>
        </select>
        <?=Html::hiddenInput('org_city', Organization::getOrgCity($org_id), ['id' => 'org_city']);?>
    </div>
    <div class="form-group">

        <button type="button" class="btn default-btn" id="assignUser"><?php echo $type == 1 ? '开始配送分工' : '取消配送分工'; ?></button>
        <button type="button" class="btn default-btn" id="showHideBuild">隐藏已分配的楼宇</button>
    </div>
</div>
<div id="build_list" data-list='<?php echo json_encode($build_list); ?>'></div>
<br/>
<div id="map"></div>
<?=$this->render('user_build_dialog', [
    'userArr'    => $userArr,
    'build_list' => $build_list,
])?>
<style>
.modal {
    left:auto;
    right:20px;
    top:20px;
}
.del_build{
    float:right;
    cursor:pointer;
}
.modal-body label{
    display: block;
}
.step_1, .step_2, .step_3{
    display: none;
}
#map{
    width: 100%;
    height:500px;
}
.modal-backdrop{
	display: none;
}
</style>