<?php
$this->title = "灯箱验收详情";
$this->registerJsFile('http://map.qq.com/api/js?v=2.exp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ',['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJs('
    var address = $("#address").html(),
        geocoder,map,marker = null;
    map = new qq.maps.Map(document.getElementById("allmap"),{
        center: new qq.maps.LatLng(39.916527,116.397128),
        zoom: 12,
        disableDefaultUI: true
    });

    geocoder = new qq.maps.Geocoder({
        complete : function(result){
            map.setCenter(result.detail.location);
            map.zoomTo(18);
            marker = new qq.maps.Marker({
                map:map,
                position: result.detail.location
            });
        }
    });

    geocoder.getLocation(address);
');
?>

<style>
	.dl-horizontal dt{
    	float: left;
    	width:105px;
    	overflow: hidden;
    	clear: left;
    	text-align: left;
	}
	.dl-horizontal dd{
   		 margin-left:105px;
	}
	.form-control {
   		 display: initial;
	}
	dl {
    	margin-bottom: 10px;
	}
	.modal-body{
		display: none;
		padding:0;
	}
	#btna{
		margin-left:5%;
		text-decoration:underline ;
	}
	.table > thead > tr > th{
   		vertical-align: middle;
   		text-align: center;
   }
</style>
<div id="task_detail">
    <dl class="dl-horizontal">
        <dt>楼宇名称：</dt>
        <dd>
            <?php echo $task_detail['build']['name'];?></dd>
    </dl>
    <dl class="dl-horizontal">
        <dt>楼宇地址：</dt>
        <dd id="address">
            <?php echo $task_detail['build']['province'].$task_detail['build']['city'].$task_detail['build']['area'].$task_detail['build']['address']; ?></dd>
    </dl>
    <div id="allmap" style="width:100%;height:200px;margin-bottom: 2%;"></div>
    <dl class="dl-horizontal">
        <dt>开始打开时间：</dt>
        <dd>
            <?php echo $task_detail['recive_time'] ? date('Y年m月d日 H点i分',$task_detail['recive_time']) : ''?></dd>
    </dl>
    <dl class="dl-horizontal">
        <dt>结束打卡时间：</dt>
        <dd>
            <?php echo $task_detail['end_repair_time'] ? date('Y年m月d日 H点i分',$task_detail['end_repair_time']) : ''?></dd>
    </dl>
    <dl class="dl-horizontal">
        <dt>验收结果：</dt>
        <dd>
            <?php echo $task_detail['process_result'] == 2 ? '验收通过' : '未通过'; ?>
        </dd>
    </dl>
</div>
<!--验收详情-->
    <div class="modal-body" style="display: block;">
        <table id="detail" class="table table-bordered">
        	<thead>
        		<tr><th>序号</th><th>验收项</th><th>结果</th></tr>
        	</thead>
        	<tbody>
            <?php if ($light_box_debug) { foreach ($light_box_debug as $k=>
                $v) { ?>
                <tr>
                   <td>
                        <?php echo $k+1; ?></td>
                    <td>
                        <?php echo $v['debug_item']; ?></td>
                    <td>
                        <div style="display:inline-block;" class='<?php echo isset($v['result']) && $v['result'] == 1 ? "rightImg" : "errorImg"; ?>'></div>
                    </td>
                </tr>
            <?php } } ?>      
            </tbody>	      	
        </table>
    </div>
