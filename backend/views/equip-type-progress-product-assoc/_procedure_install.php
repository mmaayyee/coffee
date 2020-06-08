<?php
$this->registerJsFile("/js/laytpl.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/bootstrap3-validation.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/regular_verification.js?v=1.1", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/set_up_progress_bar.js?v=1.2", ["depends" => [\yii\web\JqueryAsset::className()]]);

?>

<div class="" id="equipmentType"></div>
<!--提示框-->
<?=$this->render('/coupon-send-task/_tip.php');?>
<script id="equipmentTypeTpl" type="text/html">
{{# $.each(d, function(index, item){ }}
    <div class="panel panel-default" id="{{index}}">
        <div class="panel-heading form-inline">
            {{# if (item.isUpdateSelect) { }}
            <label> 设备类型：<input type="checkbox" class="equipmentType" name="" value="{{index}}" checked="checked"/>{{ item.equipTypeName }}</label>
            {{# } else { }}
            <label> 设备类型：<input type="checkbox" class="equipmentType" name="" value="{{index}}" />{{ item.equipTypeName }}</label>
            {{# } }}
        </div>
        <div class="panel-body installProcedure"> </div>
    </div>
{{# }) }}
</script>
<script id="installProcedureTpl" type="text/html">
    {{# $.each(d.equipTypeProcess, function(index, item){ }}
        {{# if (d.equipmentType && index == d.equipmentType) { }}
        <h5>请选择工序并设置对应时间以及顺序</h5>
        <table class="table table-bordered table-striped">
            <tr>
                <th>工序名称</th>
                <th>时间（s）</th>
                <th>顺序</th>
            </tr>
            {{# $.each(item.processList, function(key, value){ }}
            <tr>
                {{# if (value.enter_time) { }}
                    <td><label><input type="checkbox" onclick="checkClick(this)" class="procedureName"  name="EquipTypeProgressProductAssoc[progressList][{{d.equipmentType}}][{{key}}][process_id]" value="{{key}}" checked="checked"/>{{value.process_name}}</label></td>
                    <td class="form-group"><input class="form-control" type="text" name="EquipTypeProgressProductAssoc[progressList][{{d.equipmentType}}][{{key}}][enter_time]" value="{{value.enter_time}}" maxlength="10" check-type="required plus number number5"/></td>
                    <td class="form-group"><input class="form-control sort" type="text" name="EquipTypeProgressProductAssoc[progressList][{{d.equipmentType}}][{{key}}][enter_sort]" value="{{value.enter_sort}}" maxlength="3" check-type="required ints number number5" onchange="sortDistinct(this)"/></td>
                {{# } else{ }}
                    <td><label><input onclick="checkClick(this)" type="checkbox"  class="procedureName" name="EquipTypeProgressProductAssoc[progressList][{{d.equipmentType}}][{{key}}][process_id]" value="{{key}}"/>{{value.process_name}}</label></td>
                    <td class="form-group"><input class="form-control" type="text" name="EquipTypeProgressProductAssoc[progressList][{{d.equipmentType}}][{{key}}][enter_time]" value="" maxlength="10" check-type=" plus"/></td>
                    <td class="form-group"><input class="form-control sort" type="text" name="EquipTypeProgressProductAssoc[progressList][{{d.equipmentType}}][{{key}}][enter_sort]" value="" maxlength="3" check-type=" ints" onchange="sortDistinct(this)"/></td>
                {{# } }}
            </tr>
            {{# }) }}
        </table>
        {{# } }}
    {{# }) }}
</script>
