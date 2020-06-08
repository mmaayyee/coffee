<?php

use yii\widgets\LinkPager;


$this->title = "灯带楼宇方案管理";
$this->params['breadcrumbs'][] = '灯带楼宇方案管理';
$this->registerJsFile("/js/laytpl.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJs('
  	var data = '.$buildList.';
  	if(!data)
	{
		$("tbody").html("<tr class=\'text-center\'><td colspan=\'7\'>暂无数据</td></tr>");
	}
	var gettpls = document.getElementById("program_template").innerHTML;		
	laytpl(gettpls).render(data,function(html){
		$("tbody").append(html);	
	});

');
?>
<style type="text/css">
	tbody {
   		counter-reset:sectioncounter;
	}                      
	.SortId:before {
	   content:counter(sectioncounter); 
	   counter-increment:sectioncounter;
	}
</style>
<?php echo $this->render('build_program_search', ['model' => $model]); ?>

<input type="hidden" id="pageSize" value="<?php echo $pageSize; ?>">
<input type="hidden" id="page" value="<?php echo $page; ?>">
<table class="table table-bordered">
	<thead>
		<tr>
			<th>序号</th>
			<th>楼宇名称</th>
			<th>灯带方案名称</th>
			<th>灯带场景</th>
			<th>灯带场景时间</th>
			<th>灯带策略</th>
			<th>包含的饮品组</th>
		</tr>		
	</thead>
	<tbody>
	</tbody>
</table>

<?php if($buildList == ""){ ?>
    <div style="margin-left: 50%; ">暂无数据。</div>
<?php } ?>

<script id="program_template" type="text/html">
	{{# $.each(d,function(index,item){ }}
	    {{# var length=item.scenarioArr.length+1;
        var page = $("#page").val();
        var pageSize = $("#pageSize").val();
      }}
	     <tr>
			<td rowspan="{{length}}">{{ (page-1)*pageSize +index+1 }}</td>
			<td rowspan="{{length}}">{{ item.build_name}}</td>
			<td rowspan="{{length}}" >
				<!--<a href="">-->
					{{ item.program_name}}
				<!--</a>-->
			</td>
		</tr>
		{{# $.each(item.scenarioArr,function(key,value){ }}
		<tr>
			<td>{{value.scenario_name}}</td>
			<td>{{value.start_time}}--{{value.end_time}}</td>
			<td>{{value.strategy_name}}</td>
			<td>{{value.product_group_name}}</td>			
		</tr>
		{{# }) }}
	{{# }) }}

</script>
    <?=
        LinkPager::widget([
          'pagination' => $pages,
        ]);
    ?>