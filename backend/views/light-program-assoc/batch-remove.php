<?php
use backend\assets\AppAsset;
use backend\models\Organization;
use common\models\Api;

$this->title                   = '批量移除设备';
$this->params['breadcrumbs'][] = ['label' => '灯带方案管理', 'url' => ['/light-belt-program/index']];
$this->params['breadcrumbs'][] = '批量移除设备';
$this->registerCssFile("/css/batch_operation.css?v=1.2", [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);
$this->registerJsFile("/js/laypage.min.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/laytpl.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/batch-addition.js?v=1.2", ["depends" => [\yii\web\JqueryAsset::className()]]);

?>

<div class="form-inline">
	<input type="hidden" id="programID" value="<?php echo $id; ?>">
	<input type="hidden" id="selectType" value="0">
	<div class="form-group">
	    <label>楼宇名称</label>
	    <input type="text" name="buildingName" class="form-control" placeholder="关键字检索" maxlength="50">
  	</div>
  	<div class="form-group">
	    <label>楼宇类型</label>
	    <select name="buildingType" class="form-control">
	    	<?php foreach (Api::getBuildTypeList() as $key => $value) {?>
	    		<option value="<?php echo $key ?>"><?php echo $value ?></option>
	    	<?php }?>
	    </select>
  	</div>
  	<div class="form-group">
	    <label>分公司</label>
	    <select name="branch" class="form-control">
	    	<?php foreach (Organization::getManagerOrgIdNameArr() as $key => $value) {?>
	    		<option value="<?php echo $key ?>"><?php echo $value ?></option>
	    	<?php }?>
	    </select>
  	</div>
  	<div class="form-group">
	    <label>设备类型</label>
	    <select name="equipmentType" class="form-control">
	    	<?php foreach (Api::getEquipTypeList() as $key => $value) {?>
	    		<option value="<?php echo $key ?>"><?php echo $value ?></option>
	    	<?php }?>
	    </select>
  	</div>
  	<button class="btn btn-primary search" id="searchResult" type="button">检索</button>
</div>
<div class="block-a">
	<div class="searchResult">
		<h5>搜索结果</h5>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>序号</th>
					<th>楼宇名称</th>
					<th><button class="btn btn-primary allAdd" type="button">批量移除</button></th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
		<div class="pages"></div>
	</div>
	<div class="text-center no-data">搜索结果：暂无数据</div>
	<div class="addPreview">
		<h5>移除设备预览</h5>
		<form action="/light-program-assoc/batch-remove?id=<?php echo $id; ?>" method="post">
			<input type="hidden" name="_csrf" value="<?php echo Yii::$app->request->csrfParam; ?>">
			<div class="overflow">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>序号</th>
							<th>楼宇名称</th>
							<th><button class="btn btn-primary allDelete" type="button" disabled="disabled">批量移除</button></th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			<button type="submit" class="btn btn-success" disabled="disabled">保存</button>
		</form>
	</div>
</div>
<!--楼宇模板-->
<script id="building_template" type="text/html">
	{{# $.each(d,function(key,value){ }}
		<tr>
			<td class="SortId"  data-text=""></td>
			<td>{{value.buildingName}}</td>
			<td>
				<input type="hidden" name="buildingIdArr[]" value="{{value.buildingID}}"/>
				<button type="button" class="btn btn-primary btn-sm add">
				    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
				</button>
			</td>
		</tr>
	{{# })}}
</script>

