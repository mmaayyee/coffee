<?php
use backend\models\DistributionSparePackets;
use backend\models\ScmMaterial;
use backend\models\ScmSupplier;
?>
<div class="modal fade" id="spare-packets" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">设置备用料包</h4>
            </div>
            <div class="modal-body">
                <form id="spare-packet-form" method="post" action="/scm-warehouse-out/spare-packets">
                <?php
// 备用料包
$sparePacketsList = DistributionSparePackets::getMaterialIdSpacketArr();
// 设备物料信息（设置备用料包时显示用）
$materialList = ScmMaterial::getScmMaterial();
//仓库名称
$supplierList = ScmSupplier::getSupplier();
if ($materialList) {
    $html = '';
    foreach ($materialList as $materialTypeId => $material) {
        $type             = $material['type']; //是否放入料仓中的物料 1是2否
        $materialTypeName = $material['material_type_name'];
        $materialName     = $material['material_name'];
        $specUnit         = $material['spec_unit'];
        $weight           = $material['weight'];
        $supplierName     = $supplierList[$material['supplier_id']] ?? '';
        $showSpecUnit     = $weight > 1 ? '-' . $weight . $specUnit : '';
        $html .= '<div class="form-group form-inline"><input type="hidden" value="' . $material['material_id'] . '" name="data[' . $materialTypeId . '][material_id]" /><label>' . $materialTypeName . ':' . $supplierName . '-' . $materialName . $showSpecUnit . '</label><input type="text" class="form-control packets" name="data[' . $materialTypeId . '][packets]" value="';
        $html .= $sparePacketsList[$material['material_id']] ?? 0;
        $html .= '"/> ' . $material['unit'] . '</div>';
    }
    echo $html;
}
?>
                <input type="hidden" name="_csrf" value="<?=Yii::$app->getRequest()->getCsrfToken();?>" />
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" id="submit" class="btn btn-primary spare-packets">确定</button>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
        $("#submit").click(function(){
            var verify = true;
            $(".packets").each(function(e){
                if ($(this).val()) {
                if (!/^\d+$/.test($(this).val())) {
                    alert("请填写所有内容且只能为零或者正整数类型");
                    verify = false;
                    return false;
                }
                }
            })
            if (verify == false) {
                return false;
            }
            $("#spare-packets form").submit();
        });
    ');
?>
<style>
#spare-packets label{
    min-width: 50%;
}
#spare-packet-form label{
    width: 80%;
}
#spare-packet-form input{
    width: 10%;
}
</style>
