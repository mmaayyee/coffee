<?php
use backend\assets\AppAsset;
use backend\models\LightBeltScenario;
use backend\models\LightBeltProductGroup;
use backend\models\LightBeltStrategy;
use kartik\select2\Select2;

$this->title = '添加灯带方案';
$this->params['breadcrumbs'][] = ['label' => '灯带方案管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile("/css/form-style.css?v=1.0", [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);
$this->registerJsFile("/js/laytpl.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/bootstrap3-validation.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/add-band-scheme.js?v=1.0", ["depends" => [\yii\web\JqueryAsset::className()]]);
?>
<?php if(isset($id) && $id){ ?>
	<form action="/light-belt-program/update?id=<?php echo $id; ?>" method="post">
<?php }else{ ?>
	<form action="/light-belt-program/create" method="post">
<?php } ?>
	<div class="form-inline form-group">
		<label>灯带方案名称</label><input name="program_name" class="form-control" type="text" maxlength="50" check-type="required"/>
	</div>
	<input type="hidden" name="_csrf" value="<?php echo Yii::$app->request->csrfParam; ?>">
	<div class="table-bordered">
		<p>选择灯带场景：</p>
		<div class="form-inline">
			<div class="group">
				<span>按时间筛选 <b class="text-danger">(范围：0-24小时)</b></span><div class="form-group"><input class="form-control" id="start_time" type="text" check-type="ints number compareSize" range="0~24" maxlength="2" /></div><span class="to">至</span><div class="form-group"><input class="form-control" id="end_time" type="text" check-type="ints number compareSize" range="0~24" maxlength="2"/></div>
			</div>
			<div class="form-group">
				按灯带场景名称筛选<input class="form-control" type="text" id="scenario_name" maxlength="50"/>
			</div>
			<div class="form-group">
				按触发条件进行筛选
				<select class="form-control" id="equip_scenario_name">
					<?php foreach (LightBeltScenario::$equipScenarioNameArr as $id => $name) { ?>
						<option value="<?php echo $id; ?>"><?php echo $name; ?></option>
					<?php }?>
				</select>

				<select class="form-control" id="product_group_id" >
					<?php foreach (LightBeltProductGroup::getProGroupList() as $id => $name) { ?>
						<option value="<?php echo $id; ?>"><?php echo $name; ?></option>
					<?php }?>
				</select>
			</div>
		</div>
		<button type="button" class="btn btn-primary search">检索</button>
	</div>
	<div class="form-inline table-bordered scheme">
	    <div>
	        <h4>搜索到的场景</h4>
	        <div class="checkbox"></div>
	        <button type="button" class="btn btn-primary add_scene"><span class="glyphicon glyphicon-plus"></span></button>
	    </div>
	    <div>
	        <h4>添加的场景</h4>
	        <ul class="list-group"></ul>
	    </div>
	</div>
	<div class="form-inline form-group default_strategy_id">
		<label>选择默认灯带策略</label>
        <?php
            echo Select2::widget([
                'model' => $model,
                'attribute' => 'default_strategy_id',
                'data' => LightBeltStrategy::getStrategyNameList(),
                'options' => [
                    'placeholder' => '策略名称',
                    // "multiple"  => true,
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]);
        ?>
 		<div class="help-block"></div>
	</div>
	<button type="button" class="btn btn-success">保存</button>
</form>
<!--提示框-->
<div class="modal fade bs-example-modal-sm" id="tsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        	<h4 id="myModalLabel">提示框</h4>
        </div>
        <div class="modal-body">
          <h4 class="form-group title"></h4>
        </div>
        <div class="modal-footer">
          <button type="button" id="btn_submit" class="btn btn-primary" data-dismiss="modal">确定</button>
        </div>
      </div>
    </div>
</div>
<!--检索出的灯带场景html模板-->
<script id="scene_template" type="text/html">
	{{# $.each(d,function(key,value){ }}
	    <label>
	      <input type="checkbox" data-value="{{value.scenario_id}}"> {{value.scenario_name}}
	    </label>
	{{# })}}
</script>
<!--选中的灯带场景html模板-->
<script id="checked_scene_template" type="text/html">
	{{# $.each(d,function(key,value){ }}
	    <li class='list-group-item'>{{value.scenario_name}}<input name='scenarioArr[]' type='hidden' value='{{value.scenario_id}}'/><span class="glyphicon glyphicon-trash del-scene"></span></li>
	{{# })}}
</script>
<script type="text/javascript">
    //修改灯带方案
    var schemeData = <?php echo $programList?>;
</script>