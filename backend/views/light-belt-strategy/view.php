<?php
use backend\assets\AppAsset;
use yii\helpers\Json;

$this->title = '灯带策略详情';
$this->params['breadcrumbs'][] = ['label' => '灯带策略管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile("/css/jquery.minicolors.css", [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);
$this->registerJsFile("/js/jquery.minicolors.min.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/laytpl.js", ["depends" => [\yii\web\JqueryAsset::className()]]);

$this->registerJs('
var gettpls = document.getElementById("strategy_template").innerHTML;
	var bandStrategy = '.$strategyArr.';
	laytpl(gettpls).render(bandStrategy,function(html){
		$(".view").html(html);
	});
	$(".color").each( function() {
		$(this).minicolors({
			control: $(this).attr("data-control") || "hue",
			defaultValue: $(this).attr("data-defaultValue") || " ",
			inline: $(this).attr("data-inline") === "true",
			letterCase: $(this).attr("data-letterCase") || "lowercase",
			opacity: $(this).attr("data-opacity"),
			position: $(this).attr("data-position") || "bottom left",
			change: function(hex, opacity) {
				if( !hex ) return;
				if( opacity ) hex += ", " + opacity;
				try {
				} catch(e) {}
			},
			theme: "bootstrap"
		});
	});
');
?>
<style>
	.col-md-4,.col-md-8,.col-md-12{
		padding: 8px;
	    line-height: 1.42857143;
	    vertical-align: top;
	    border: 1px solid #ddd;
	}
	.form-control[disabled]{
		border:none;
		outline: none;
		box-shadow:none;
		background-color:#fff;
		cursor:text;
	}
	.band-color{
		padding: 1px;
	}
	.text-bold{
		font-weight: bold;
	}
</style>
<div class="view"> </div>
<script id="strategy_template" type="text/html">
	    <div class="row text-center">
	      	<div class="col-md-4"><span class="text-bold">灯带策略名称：</span>{{d.strategy_name}}</div>
	    	{{# if(d.light_belt_type==1){ }}
	       	<div class="col-md-4"><span class="text-bold">灯带控制类型：</span>灯带控制</div>
	      	<div class="col-md-4"><span class="text-bold">灯带周期：</span>{{d.total_length_time}}</div>
			
			<table class="table table-bordered">
				<tr><th>灯带名称</th><th>开始时间(单位：毫秒)</th><th>开始颜色</th><th>结束时间(单位：毫秒)</th><th>结束颜色</th></tr>
				{{# $.each(d.lightBeltArr,function(key,value){ }}
				     <tr><td rowspan="{{value.length+1}}">{{ Number(key) +1}}号灯带</td></tr>
					{{# $.each(value,function(index,item){ }}
				     	<tr>
				     		<td>
					     		{{item.start_time}}
				     		</td>
				     		<td>
				     			<input class="form-control color"  data-control="hue" value="{{item.start_color}}" disabled="disabled" />
				     		</td>
				     		<td>
					     		{{item.end_time}}
				     		</td>
				     		<td>
				     			<input class="form-control color"  data-control="hue" value="{{item.end_color}}	" disabled="disabled" />
				     		</td>
				     	</tr>
				     	{{# }) }}
				{{# }) }}
			</table>
		{{# }else{ }}
	      	<div class="col-md-8"><span class="text-bold">灯带控制类型：</span>整体控制</div>
			{{# if(d.light_status==2){}}
			<div class="col-md-4"><span class="text-bold">灯带是否亮灯：</span>亮（闪烁）</div>
	      	<div class="col-md-4"><span class="text-bold">闪烁频率时间：</span>{{d.flicker_frequency}}毫秒</div>
	      	<div class="col-md-4 band-color"><span class="text-bold">灯带颜色：</span><input class="form-control color"  data-control="hue" value="{{d.light_belt_color}}" disabled="disabled" /></div>
			{{# }else if(d.light_status==1){ }}
				<div class="col-md-4"><span class="text-bold">灯带是否亮灯：</span>亮（不闪烁）</div>
				<div class="col-md-8 band-color"><span class="text-bold">灯带颜色：</span><input class="form-control color"  data-control="hue" value="{{d.light_belt_color}}" disabled="disabled" /></div>
			{{# }else{ }}
				<div class="col-md-12"><span class="text-bold">灯带是否亮灯：</span>不亮</div>
			{{# }}}
		{{# }}}
	</div>
</script>