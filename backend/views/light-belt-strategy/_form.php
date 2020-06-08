<?php
use backend\assets\AppAsset;
use yii\widgets\ActiveForm;
use backend\models\LightBeltStrategy;
use yii\helpers\Url;
use yii\helpers\Json;

$this->title = '灯带策略';
$this->params['breadcrumbs'][] = ['label' => '灯带策略管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


$this->registerCssFile("/css/jquery.minicolors.css", [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);
$this->registerCssFile("/css/band-strategy.css", [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);
$this->registerJsFile("/js/laytpl.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/bootstrap3-validation.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/jquery.minicolors.min.js", ["depends" => [\yii\web\JqueryAsset::className()]]);

$this->registerJsFile("/js/band-strategy.js?v=1.0", ["depends" => [\yii\web\JqueryAsset::className()]]);

$lightBeltType = LightBeltStrategy::$lightBeltTypeArr;
unset($lightBeltType['']);
$lightStatus   = LightBeltStrategy::$lightStatusArr;
unset($lightStatus['']);
?>

<script type = 'text/javascript'>
	var bandStrategy=	<?php echo $strategyArr?>;
	var lightData 	= 	<?php echo $lightBeltList?>;
</script>

<?php if(isset($id) && $id){ ?>
	<form action="/light-belt-strategy/update?id=<?php echo $id; ?>" method="post">
<?php }else{ ?>
	<form action="/light-belt-strategy/create" method="post">
<?php } ?>
	<div class="form-inline">
		<input type="hidden" name="_csrf" value="<?php echo Yii::$app->request->csrfParam; ?>">
		<div class="form-group">
			<label>灯带策略名称</label>
			<input class="form-control" type="text" name="strategy_name" check-type="required" maxlength="50"/>
		</div>
		<div class="form-group">
			<label>灯带控制类型</label>
			<select class="form-control" name="light_belt_type">
				<?php foreach ($lightBeltType as $key => $value) { ?>
					<?php if ($key == 1) { ?>
						<option value="<?php echo $key ?>" selected="selected"><?php echo $value; ?></option>
					<?php }else{ ?>
						<option value="<?php echo $key ?>"><?php echo $value; ?></option>
					<?php } ?>
				<?php  } ?>
			</select>
		</div>
	</div>
	<div class="single">
		<div class="form-inline cycle">
			<div class="form-group">
				<label>灯带周期</label>
				<input class="form-control" type="text" name="total_length_time" check-type="required number ints" range="0~60000" maxlength="5"/>
			</div>
			<span>毫秒</span>
		</div>
		<div class="checkbox choice_band">
			<label>选择灯带<i class="star-mark"> *</i></label>
			<label><input type="checkbox" id="allcheck" name="" />全选</label>
		</div>
		<div class="checkbox" id="band"></div>
		<div class="gradient_group"></div>
		<button type="button" class="btn btn-primary add">
		    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
		</button>
	</div>
	<div class="whole">
		<div class="form-group">
			<label for="">灯带是否亮灯</label>
			<select class="form-control" name="light_status">
				<?php foreach ($lightStatus as $key => $value) { ?>
					<?php if($key != 2){ ?>
						<option value="<?php echo $key ?>"><?php echo $value; ?></option>
					<?php }else{ ?>
						<option value="<?php echo $key ?>" selected="selected"><?php echo $value; ?></option>
					<?php } ?>
				<?php }  ?>
			</select>
		</div>
		<div class="form-group">
			<label>闪烁频率时间(单位：毫秒)</label>
			<input class="form-control" name="flicker_frequency" check-type="required number ints" range="0~2550"/>
		</div>
		<div class="form-group">
			<label>灯带颜色</label>
			<input class="form-control color" data-control="hue" value="#ffffff" name="light_belt_color" check-type="required" maxlength="10"/>
		</div>
	</div>
	<input type="button" class="btn btn-success" value="保存"/>
</form>
<!--提示框-->
<div class="modal fade bs-example-modal-sm" id="tsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        	<h4 id="myModalLabel"></h4>
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

<!--灯带组遍历-->
<script id="band_template" type="text/html">
	{{# for(var i=0;i<d.length;i++){ }}
		<label><input type="checkbox" name="lightNameArr[{{i}}]" value="{{i}}" />{{d[i]}}</label>
	{{# }}}
</script>
<!--颜色渐变组模板-->
<script id="gradient_template" type="text/html">
	{{# var index=$("input[name^='lightName']:checked").val(); }}
	{{# if(d["ligth"+index]){ }}
		{{# $.each(d["ligth"+index],function(key,value){ }}
		<div class="bordered form-inline">
		    <div class="start">
				<label>开始：</label>
				<span>时间<i class="star-mark"> *</i> 第</span>
				<div class="form-group">
					<input type="text" name="startTimeArr[]" class="form-control" check-type="required number ints" range="0~60000" maxlength="5" value="{{value.start_time}}"/>
				</div><span class="margins">毫秒</span>
				<span>颜色<i class="star-mark"> *</i></span>
				<div class="form-group">
					<input type="text" class="form-control color" name="startColorArr[]" check-type="required" maxlength="10" data-control="hue" value="{{value.start_color}}"/>
				</div>
			</div>
			 <div class="end">
				<label>结束：</label>
				<span>时间<i class="star-mark"> *</i> 第</span>
				<div class="form-group">
					<input type="text" name="endTimeArr[]" class="form-control" check-type="required number ints " range="0~60000" maxlength="5" value="{{value.end_time}}" />
				</div><span class="margins">毫秒</span>
				<span>颜色<i class="star-mark"> *</i></span>
				<div class="form-group">
					<input type="text" class="form-control color" name="endColorArr[]" check-type="required" maxlength="10" data-control="hue" value="{{value.end_color}}"/>
				</div>
			</div>
			{{# if(key!=0){ }}
				<button type="button" class="btn btn-danger del">删除</button>
			{{# }}}
		</div>
		{{# }) }}
	{{# }else{ }}
	<div class="bordered form-inline">
	    <div class="start">
			<label>开始：</label>
			<span>时间<i class="star-mark"> *</i> 第</span>
			<div class="form-group">
				<input type="text" name="startTimeArr[]" class="form-control" check-type="required number ints" range="0~60000" maxlength="5" value="0" />
			</div><span class="margins">毫秒</span>
			<span>颜色<i class="star-mark"> *</i></span>
			<div class="form-group">
				<input type="text" class="form-control color" name="startColorArr[]" check-type="required" maxlength="10" data-control="hue" value="#ffffff"/>
			</div>
		</div>
		 <div class="end">
			<label>结束：</label>
			<span>时间<i class="star-mark"> *</i> 第</span>
			<div class="form-group">
				<input type="text" name="endTimeArr[]" class="form-control" check-type="required number ints " range="0~60000" maxlength="5"  value="0"/>
			</div><span class="margins">毫秒</span>
			<span>颜色<i class="star-mark"> *</i></span>
			<div class="form-group">
				<input type="text" class="form-control color" name="endColorArr[]" check-type="required" maxlength="10" data-control="hue" value="#ffffff"/>
			</div>
		</div>
		{{# if($(".gradient_group .bordered").length>0){ }}
			<button type="button" class="btn btn-danger del">删除</button>
		{{# }}}
	{{#}}}
	</div>
</script>
