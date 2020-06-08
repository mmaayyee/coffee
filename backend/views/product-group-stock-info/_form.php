<?php

use backend\models\ScmEquipType;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ProductGroupStockInfo */
/* @var $form yii\widgets\ActiveForm */
$this->title                   = '';
$this->params['breadcrumbs'][] = ['label' => '产品组料仓信息管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile("/js/laytpl.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/bootstrap3-validation.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/product_group_stock_info.js", ["depends" => [\yii\web\JqueryAsset::className()]]);

?>
<style type="text/css">
	.product-group-stock-info-form table {
        counter-reset:sectioncounter;
    }
    .product-group-stock-info-form .SortId:before {
       display: inline-block;
       content: counter(sectioncounter);
       counter-increment: sectioncounter;
    }
    .btn.btn-info.btn-sm {
        margin-bottom: 10px;
    }
</style>
<div class="product-group-stock-info-form">

    <?php $form = ActiveForm::begin();?>
	<div class="form-inline form-group">

    <?=$form->field($model, 'product_group_stock_name')->textInput(['maxlength' => true])?>

    <?=$form->field($model, 'equip_type_id')->dropDownList(ScmEquipType::getEquipTypeIdNameArr())?>

	</div>
	<div id="stockInfo"></div>
    <div class="form-group">
        <?=Html::Button($model->isNewRecord ? '保存' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success'])?>
    </div>

    <input type="hidden" name="ProductGroupStockInfo[id]" value="<?php echo $model->id; ?>">
    <?php ActiveForm::end();?>
</div>
<script>
    var upDate  = <?php echo $stockList ? $stockList : json_encode([]); ?>;
</script>
<?=$this->render('_tip')?>
<script id="stockInfoTpl" type="text/html">
 <div class="field-equipmentproductgroupstock-stock_code">
            <h5>填写产品组料仓信息</h5>
            <button class="btn btn-info btn-sm" type="button" onclick="addStockInfo()">添加料仓</button>
             <table class="table table-bordered table-striped">
                <tr>
                    <th>编号</th>
                    <th>料盒信息</th>
                    <th>容量上限</th>
                    <th>物料名称</th>
                    <th>容量下限</th>
                    <th>预警值</th>
                    <th>出料速度(单位：克/秒)</th>
                    <th>是否运维使用</th>
                    <th>操作</th>
                </tr>
                {{# console.log(d.stockList)}}
                {{# if (!d.stockList) { }}
                <tr>
                    <td class="SortId"></td>
                        <td>
                        <select class="form-control material-stock stock_code_change" onchange="stockCodeChange()" name="ProductGroupStockInfo[stockList][{{ d.num }}][stock_code]">
                            {{# $.each(d.data.equipTypeStockList, function(index, item) { }}
                                <option value="{{item.stockCode}}">{{ item.stockName }}</option>
                            {{# }) }}
                        </select>
                    <td class="form-group"><input class="form-control" name="ProductGroupStockInfo[stockList][{{ d.num }}][top_value]" maxlength="4" type="text" check-type="required plus"></td>
                    <td>
                        <select class="form-control materialTypeSugarUnique" onchange="sugarUnique(this)" class name="ProductGroupStockInfo[stockList][{{ d.num }}][material_type_id]">
                        {{# $.each(d.data.materialTypeList, function(key, value){ }}
                            <option value="{{key}}">{{value}}</option>
                        {{# }) }}
                        </select>
                    </td>
                    <td class="form-group"><input class="form-control" name="ProductGroupStockInfo[stockList][{{ d.num }}][bottom_value]" type="text" maxlength="4" check-type="required plus compare"></td>
                    <td class="form-group"><input class="form-control" name="ProductGroupStockInfo[stockList][{{ d.num }}][warning_value]" type="text" maxlength="4" check-type="required plus"/></td>
                    <td class="form-group"><input class="form-control" name="ProductGroupStockInfo[stockList][{{ d.num }}][blanking_rate]" type="text" maxlength="4"  check-type="required plus"></td>
                    <td class="form-group">
                        <select class="form-control" name="ProductGroupStockInfo[stockList][{{ d.num }}][is_operation]">
                            <option value="1">是</option>
                            <option value="0">否</option>
                        </select>
                    </td>
                    <td></td>
                </tr>
                {{# } else { }}
                    {{# $.each(d.stockList, function(idx, items) { }}
                    <tr>
                        <td class="SortId"></td>
                            <td>
                            <select class="form-control material-stock stock_code_change" onchange="stockCodeChange()" name="ProductGroupStockInfo[stockList][{{ idx }}][stock_code]">
                                {{# $.each(d.data.equipTypeStockList, function(index, item) { }}
                                    {{# if (items.stock_code === item.stockCode) { }}
                                        <option value="{{item.stockCode}}" selected="selected">{{ item.stockName }}</option>
                                    {{# } else { }}
                                        <option value="{{item.stockCode}}">{{ item.stockName }}</option>
                                    {{# } }}
                                {{# }) }}
                            </select>
                        <td class="form-group"><input class="form-control" name="ProductGroupStockInfo[stockList][{{ idx }}][top_value]" maxlength="4" type="text" check-type="required plus" value="{{items.stock_volume_bound }}"></td>
                        <td>
                            <select class="form-control materialTypeSugarUnique" onchange="sugarUnique(this)" name="ProductGroupStockInfo[stockList][{{ idx }}][material_type_id]">
                            {{# $.each(d.data.materialTypeList, function(key, value){ }}
                                {{# if (items.materiel_id == key) { }}
                                <option value="{{key}}" selected="selected">{{value}}</option>
                                {{# } else { }}
                                <option value="{{key}}">{{value}}</option>
                                {{# } }}
                            {{# }) }}
                            </select>
                        </td>
                        <td class="form-group"><input class="form-control" name="ProductGroupStockInfo[stockList][{{ idx }}][bottom_value]" type="text" maxlength="4" check-type="required plus compare" value="{{ items.bottom_value }}"/></td>
                        <td class="form-group"><input class="form-control" name="ProductGroupStockInfo[stockList][{{ idx }}][warning_value]" type="text" maxlength="4" check-type="required plus" value="{{ items.warning_value }}"/></td>
                        <td class="form-group"><input class="form-control" name="ProductGroupStockInfo[stockList][{{ idx }}][blanking_rate]" type="text" maxlength="4"  check-type="required plus" value="{{ items.blanking_rate }}"/></td>
                        <td class="form-group">
                            <select class="form-control" name="ProductGroupStockInfo[stockList][{{ idx }}][is_operation]">
                                {{# if(items.is_operation == 1){ }}
                                <option value="1" selected="selected">是</option>
                                <option value="0">否</option>
                                {{# } else { }}
                                <option value="1">是</option>
                                <option value="0" selected="selected">否</option>
                                {{# } }}
                            </select>
                        </td>
                        <td>
                            {{#if(idx > 0){ }}
                            <button class="btn btn-primary btn-sm" type="button" onclick="delStockInfo(this)"><span class="glyphicon glyphicon-minus"></span></button>
                            {{# } }}
                        </td>
                    </tr>
                    {{# }) }}
                {{# } }}
             </table>
        </div>
</script>
<script id="addStockInfoTpl" type="text/html">
    <tr>
        <td class="SortId"></td>
        <td>
            <select class="form-control material-stock stock_code_change" onchange="stockCodeChange()" name="ProductGroupStockInfo[stockList][{{ d.num }}][stock_code]">
                {{# $.each(d.data.equipTypeStockList, function(index, item) { }}
                    <option value="{{item.stockCode}}">{{ item.stockName }}</option>
                {{# }) }}
            </select>
        <td class="form-group"><input class="form-control" name="ProductGroupStockInfo[stockList][{{ d.num }}][top_value]" maxlength="4" type="text" check-type="required plus"></td>
        <td>
            <select class="form-control materialTypeSugarUnique" onchange="sugarUnique(this)" name="ProductGroupStockInfo[stockList][{{ d.num }}][material_type_id]">
            {{# $.each(d.data.materialTypeList, function(key, value){ }}
                <option value="{{key}}">{{value}}</option>
            {{# }) }}
            </select>
        </td>
        <td class="form-group"><input class="form-control" name="ProductGroupStockInfo[stockList][{{ d.num }}][bottom_value]" type="text" maxlength="4" check-type="required plus compare"></td>
        <td class="form-group"><input class="form-control" name="ProductGroupStockInfo[stockList][{{ d.num }}][warning_value]" type="text" maxlength="4" check-type="required plus"/></td>
        <td class="form-group"><input class="form-control" name="ProductGroupStockInfo[stockList][{{ d.num }}][blanking_rate]" type="text" maxlength="4"  check-type="required plus"></td>
        <td class="form-group">
            <select class="form-control" name="ProductGroupStockInfo[stockList][{{ d.num }}][is_operation]">
                <option value="1">是</option>
                <option value="0">否</option>
            </select>
        </td>
        <td><button class="btn btn-primary btn-sm" type="button" onclick="delStockInfo(this)"><span class="glyphicon glyphicon-minus"></span></button></td>
    </tr>
</script>