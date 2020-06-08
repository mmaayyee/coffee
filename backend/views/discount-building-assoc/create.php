<?php
use backend\assets\AppAsset;
use common\models\Api;
$this->title                   = '批量添加楼宇';
$this->params['breadcrumbs'][] = ['label' => '楼宇支付策略', 'url' => ['/build-pay-type/index']];
$this->params['breadcrumbs'][] = '批量添加楼宇';
$this->registerCssFile("/css/batch_operation.css?v=1.2", [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);
$this->registerJsFile("/js/laypage.min.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/laytpl.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/discount-holicy-batch-add.js?v=1.8", ["depends" => [\yii\web\JqueryAsset::className()]]);

?>

<button class="btn btn-primary search-building" disabled="disabled" style="background-color:#2e6da4;" type="button">搜索添加</button>
&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
<button class="btn btn-primary upload-building" type="button">上传楼宇</button>
<br/><br/>
<div class="form-inline">
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
	    	<?php foreach (json_decode(Api::getOrgListByType(0), true) as $key => $value) {?>
	    		<option value="<?php echo $key ?>"><?php echo $value ?></option>
	    	<?php }?>
	    </select>
  	</div>
  	<div class="form-group">
	    <label>代理商</label>
	    <select name="agent" class="form-control">
	    	<?php foreach (json_decode(Api::getOrgListByType(1), true) as $key => $value) {?>
	    		<option value="<?php echo $key ?>"><?php echo $value ?></option>
	    	<?php }?>
	    </select>
  	</div>
  	<div class="form-group">
	    <label>合作商</label>
	    <select name="partner" class="form-control">
	    	<?php foreach (json_decode(Api::getOrgListByType(2), true) as $key => $value) {?>
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
  	<div class="form-group">
	    <label>设备编号</label>
	    <input type="text" name="equipNumber" class="form-control" placeholder="设备编号" maxlength="50">
  	</div>
  	<button class="btn btn-primary search"  id="searchResult"  type="button">检索</button>
</div>
<div class="block-a">
	<div class="searchResult">
		<h5>搜索结果</h5>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>序号</th>
					<th>楼宇名称</th>
					<th><button class="btn btn-primary allAdd" type="button">批量添加</button></th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
		<div class="pages"></div>
	</div>
	<div class="text-center no-data">搜索结果：暂无数据</div>
	<form action="/discount-building-assoc/create" method="post" id="buildPayType">
	<div class="addPreview">
		<h5>添加楼宇预览</h5>
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
	</div>
	<div class="form-group" style="width: 30%;text-align:left;margin-top:20px;">
	    <label>楼宇支付策略名称</label>
	    <input type="text" name="buildPayTypeName" value="" class="form-control build-pay-type-name" maxlength="50">
  	</div>
  	<P class="name-tip" style="color:red;display: none;">请填写楼宇支付策略名称</P>
	<div class="unionPay">
		<h5>支付方式:</h5>
		<p style="color:red">设备端支付方式的显示顺序是根据序号从大到小排列的，且银联闪付和任务支付不能显示在首位</p>
		<?php foreach ($model->payment_list as $payTypeId => $payTypeName): ?>
            <label><?=$payTypeName?></label>
			<div class="row">
            	<div class="col-lg-6">
	                <div class="input-group">
	                    <span class="input-group-addon">
						<input name="payTypeId[]" type="checkbox" data-id="<?=$payTypeId?>" class="payment" value="<?=$payTypeId?>" <?php echo empty($disInfo[$payTypeId]) ? '' : 'checked'; ?>/>
	               	 	</span>
	                    <input type="input" name="weight[<?=$payTypeId?>]" class="form-control weight" value="<?php echo empty($disInfo[$payTypeId]) ? '' : $disInfo[$payTypeId]['weight']; ?>">
	                </div>
					<p class="weight-error" style="color:red;display: none;">请填写所选支付方式的前端展示顺序</p>
            	</div>
        	</div>
        	<div class="discount-holicy discount-holicy-<?=$payTypeId?>" holicy-type="<?=$payTypeId?>" style="display: none;">
           		<div style="margin: 10px 10px";>
               		<label><?=$payTypeName?></label>
               		<select name=holicyID[<?=$payTypeId?>] style="width:200px;height:30px">
	                    <option value="">请选择</option>
	                    	<?php if (!empty($payTypeHolic[$payTypeId]['discount_strategy'])): ?>
	                    		<?php foreach ($payTypeHolic[$payTypeId]['discount_strategy'] as $holicyId => $holicyName): ?>
	                    			<?php if (!empty($disInfo[$payTypeId]['holicy_id']) && $holicyId == $disInfo[$payTypeId]['holicy_id']): ?>
	                    			<option value="<?=$holicyId?>" selected="selected"><?=$holicyName?></option>
	                    			<?php else: ?>
	                    			<option value="<?=$holicyId?>"><?=$holicyName?></option>
	                    			<?php endif?>
	                    		<?php endforeach?>
	                    	<?php endif?>
                    </select>
                </div>
            </div>
        	<br/>
		 <?php endforeach;?>
		<div id="flag"></div>
		<input name="update-flag" value="0" type="hidden">
		<button type="submit" class="btn btn-success" disabled="disabled">保存</button>
		</form>
	</div>
</div>


<div id="upload-building"  style="display:none;">
	<form action="/discount-building-assoc/upload-create" id="infoLogoForm" enctype="multipart/form-data" method="post">
		<input type="hidden" name="_csrf" value="<?php echo Yii::$app->request->csrfParam; ?>">
		<div class="form-group" style="width: 30%;text-align:left;margin-top:20px;">
		    <label>楼宇支付策略名称</label>
		    <input type="text" name="buildPayTypeName" value="" class="form-control build-pay-type-name" maxlength="50">
	  	</div>
  		<P class="name-tip" style="color:red;display: none;">请填写楼宇支付策略名称</P>
		<div class="unionPay">
			<h5>
	    		<input type="file" name="upload_file" id="upload-building-excel">
	    		<span id="upload-building-excel-error" style="color:#A94442;margin-bottom:3%; display: none;" >文件格式不正确</span>
	    		<div class="hint-block">导入文件必须是TXT格式的，每个楼宇名称独占一行</div>
			</h5>
			<div style="display:none;color:red;" id="upload_error" ></div>
			<h5>支付方式:</h5>
			<?php foreach ($model->payment_list as $payTypeId => $payTypeName): ?>
	            <label><?=$payTypeName?></label>
				<div class="row">
	            	<div class="col-lg-6">
		                <div class="input-group">
		                    <span class="input-group-addon">
							<input name="payTypeId[]" type="checkbox" data-id="<?=$payTypeId?>" class="payment" value="<?=$payTypeId?>" value="<?=$payTypeId?>" <?php echo empty($disInfo[$payTypeId]) ? '' : 'checked'; ?>/>
		               	 	</span>
		                    <input type="input" name="weight[<?=$payTypeId?>]" class="form-control weight" value="<?php echo empty($disInfo[$payTypeId]) ? '' : $disInfo[$payTypeId]['weight']; ?>">
		                </div>
	                	<p class="weight-error" style="color:red;display: none;">请填写所选支付方式的前端展示顺序</p>
	            	</div>
	        	</div>
	        	<div class="discount-holicy discount-holicy-<?=$payTypeId?>" holicy-type="<?=$payTypeId?>" style="display: none;">
	           		<div style="margin: 10px 10px";>
	               		<label><?=$payTypeName?></label>
	               		<select name=holicyID[<?=$payTypeId?>] style="width:200px;height:30px">
		                    <option value="">请选择</option>
		                    	<?php if (!empty($payTypeHolic[$payTypeId]['discount_strategy'])): ?>
		                    		<?php foreach ($payTypeHolic[$payTypeId]['discount_strategy'] as $holicyId => $holicyName): ?>
		                    			<?php if (!empty($disInfo[$payTypeId]['holicy_id']) && $holicyId == $disInfo[$payTypeId]['holicy_id']): ?>
		                    			<option value="<?=$holicyId?>" selected="selected"><?=$holicyName?></option>
		                    			<?php else: ?>
		                    			<option value="<?=$holicyId?>"><?=$holicyName?></option>
		                    			<?php endif?>
		                    		<?php endforeach?>
		                    	<?php endif?>
	                    </select>
	                </div>
	            </div>
	        	<br/>
			 <?php endforeach;?>
		</div>
		<div style='clear:both;'>
		<button type="submit" class="btn btn-upload-success btn-success" id="upload-button" disabled="disabled">保存</button>
		</div>
	</form>
</div>

<!--楼宇模板-->
<script id="building_template" type="text/html">
	{{#
	$.each(d,function(key,value){ }}
		<tr>
			<td class="SortId" data-text=""></td>
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