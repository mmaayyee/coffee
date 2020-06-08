<?php

use yii\helpers\Html;

use yii\widgets\LinkPager;
use yii\helpers\Url;

$this->title = "灯带方案管理";
$this->params['breadcrumbs'][] = '灯带方案管理';
$this->registerJsFile("/js/laytpl.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJs('
 	var data = '.$programList.';
    if (data) {
    	var gettpls = document.getElementById("program_template").innerHTML;
    	laytpl(gettpls).render(data,function(html){
    		$("tbody").append(html);
    	});
    } else {
        $("tbody").html("<tr class=\'text-center\'><td colspan=\'7\'>暂无数据</td></tr>");
    }
');
?>
<style type="text/css">
    .form-inline.btns{
        margin-bottom: 20px;
    }
    .form-inline.btns .form-group:last-child{
        margin-left: 20px;
    }
</style>
    <?php echo $this->render('_search', ['model' => $model]); ?>

    <div id="error" style="color: red; display: none; margin-bottom: 6px;">删除失败，请检测是否使用</div>

	<div class="form-inline btns">
		<div class="form-group">
    	<?php if(Yii::$app->user->can('添加灯带方案')){ ?>
	    	<?= Html::a('添加', ['create'], ['class' => 'btn btn-success']) ?>
		<?php } ?>
		</div>
		<div class="form-group">
	    <?php if (Yii::$app->user->can('查看灯带楼宇方案')) {?>
			<a href="/light-belt-program/build-program-view"><button class="btn btn-primary">查看灯带楼宇方案</button></a>
		<?php } ?>
		</div>
	</div>
	<input type="hidden" id="pageSize" value="<?php echo $pageSize; ?>">
	<input type="hidden" id="page" value="<?php echo $page; ?>">
<table class="table table-bordered">
	<thead>
		<tr>
			<th>序号</th>
			<th>灯带方案名称</th>
			<th>灯带场景</th>
			<th>灯带场景时间</th>
			<th>灯带策略</th>
			<th>包含的饮品组</th>
			<th>操作</th>
		</tr>
	</thead>
	<tbody>

	</tbody>
</table>
<script id="program_template" type="text/html">
	{{# $.each(d,function(index,item){ }}
	    {{# var length=item.scenarioArr.length+1;
	    	var page = $("#page").val();
			var pageSize = $("#pageSize").val();
	    }}
	     <tr>
			<td rowspan="{{length}}">{{ (page-1)*pageSize +index+1 }}</td>
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
		    {{# if(key>0){return true;}}
		    {{# }}}
			<td rowspan="{{length}}">
				<?php if (Yii::$app->user->can('查看灯带方案')) {?>
				<a href="/light-belt-program/view?id={{ item.program_id}}" title="查看"><span class="glyphicon glyphicon-eye-open"></span></a>
				<?php } ?>

				<?php if (Yii::$app->user->can('编辑灯带方案')) {?>
				<a href="/light-belt-program/update?id={{ item.program_id}}" title="编辑"><span class="glyphicon glyphicon-pencil"></span></a>
				<?php } ?>

		    	{{# if(item.isUse == 0){ }}
				<?php if (Yii::$app->user->can('删除灯带方案')) {?>
				<a href="#"><span class="glyphicon glyphicon-trash del_program " id="{{ item.program_id}}" title="删除"></span></a>
				<?php } ?>
				{{# } }}

				<?php if (Yii::$app->user->can('设置默认方案')) {?>
				{{# if(item.is_default != 1){ }}
				<a href="#" title="设置默认方案"><span class="glyphicon glyphicon-star default_program" id="{{ item.program_id}}"></span></a>
				{{# }else{ }}
				<a href="#" style="color: red;" title="设置默认方案"><span class="glyphicon glyphicon-star" id="{{ item.program_id}}"></span></a>
				{{# } }}
				<?php } ?>

				{{# if(item.release_status != 1 && item.isUse == 1){ }}
				<?php if (Yii::$app->user->can('发布方案')) {?>
				<a href="#" title="发布"><span class="glyphicon glyphicon-send release_program" id="{{ item.program_id}}"></span></a>
				<?php } ?>
				{{# } }}

				<?php if (Yii::$app->user->can('批量添加楼宇')) {?>
		        <a style="display: block; margin-bottom:6px;" href="/light-program-assoc/batch-add?id={{ item.program_id}}"><button class="btn btn-primary btn-sm">批量添加方案楼宇</button></a>
				<?php } ?>
				<?php if (Yii::$app->user->can('批量移除楼宇')) {?>
		        <a style="display: block; margin-bottom:6px;" href="/light-program-assoc/batch-remove?id={{ item.program_id}}"><button class="btn btn-primary btn-sm">批量删除方案楼宇</button></a>
				<?php } ?>
			</td>
		</tr>
		{{# }) }}
	{{# }) }}
</script>

<?=
    LinkPager::widget([
      'pagination' => $pages,
    ]);
?>

<?php
$url = Url::to(["light-belt-program/delete"]);
$defaultProgramUrl	=	Url::to(["light-belt-program/set-default-program"]);
$releaseProgramUrl	=	Url::to(['light-belt-program/release-program']);
$this->registerJs('
    $(".del_program").click(function(){
        if(!confirm("确认要删除？")){
            return false;
        } else {
            var programId = $(this).attr("id");
            $.post("' . $url . '",{id: programId},function(data){
                if(data == "false"){
                    $("#error").show();
                } else {
                    window.location.reload();
                }
            });
        }
    })

	// 设置默认方案
    $(".default_program").click(function(){
		if(!confirm("确认要设置为默认方案吗？")){
            return false;
        } else {
            var programId = $(this).attr("id");
            $.post("' . $defaultProgramUrl . '",{id: programId},function(data){
				window.location.reload();
            });
        }
    })
	// 发布方案
    $(".release_program").click(function(){
		if(!confirm("确认发布方案吗？")){
            return false;
        } else {
            var programId = $(this).attr("id");
            $.post("' . $releaseProgramUrl . '",{id: programId},function(data){
				window.location.reload();
            });
        }
    })
');

?>