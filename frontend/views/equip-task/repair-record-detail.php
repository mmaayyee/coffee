<?php
$this->title = "维修详情";
$this->registerJsFile('http://map.qq.com/api/js?v=2.exp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ',['depends' => [\yii\web\JqueryAsset::className()]]);

$this->registerJsFile('@web/js/equiptask.js',['depends'=>['frontend\assets\AppAsset']]); 
?>
<style>
	div .table1:nth-child(6){
		
	}
	.table1{
		margin: 0;
		padding: 0 3px;
		border-left:1px solid #ddd;
		border-top:1px solid #ddd;
		border-right:1px solid #ddd;
		vertical-align: middle;
	}
	.table1 dt{
		padding:5px 0;
	}
	.table1 dd{
		padding-top:5px;
		padding-left:3px;
		border-left: 1px solid #ddd;
	}
</style>
<div id="task_detail">
    <dl class="dl-horizontal">
      <dt>楼宇名称：</dt>
      <dd><?php echo $task_detail['build']['name'];?></dd>
    </dl>
    <dl class="dl-horizontal">
      <dt>楼宇地址：</dt>
      <dd id="address"><?php echo $task_detail['build']['province'].$task_detail['build']['city'].$task_detail['build']['area'].$task_detail['build']['address']; ?></dd>
    </dl>
    <div id="allmap" style="width:100%;height:200px;"></div>
    <dl class="dl-horizontal">
      <dt>任务创建时间：</dt>
      <dd><?php echo $task_detail['create_time'] ? date('Y年m月d日 H点i分',$task_detail['create_time']) : ''?></dd>
    </dl>
    <dl class="dl-horizontal">
      <dt>故障现象：</dt>
      <dd><?php echo \common\models\EquipTask::getMalfunctionContent($task_detail['content'], $task_detail['task_type']) ?></dd>
    </dl>
    <dl class="dl-horizontal">
      <dt>备注：</dt>
      <dd><?php echo $task_detail['remark']; ?></dd>
    </dl>
    <div style="border-bottom:1px solid #ddd;">
    <dl class="dl-horizontal table1">
      <dt>开始修理时间：</dt>
      <dd><?php echo $task_detail['start_repair_time'] ? date('Y年m月d日 H点i分',$task_detail['start_repair_time']) : ''?></dd>
    </dl>
    <dl class="dl-horizontal table1">
      <dt>修理结束时间：</dt>
      <dd><?php echo $task_detail['end_repair_time'] ? date('Y年m月d日 H点i分',$task_detail['end_repair_time']) : ''?></dd>
    </dl>
    <dl class="dl-horizontal table1">
      <dt>故障原因：</dt>
      <dd><?php echo $task_detail['malfunction_reason'] ? \backend\models\EquipMalfunction::getMalfunctionReasonName($task_detail['malfunction_reason']): ''; ?></dd>
    </dl>   
    <dl class="dl-horizontal table1">
      <dt>故障描述：</dt>
      <dd><?php echo $task_detail['malfunction_description']; ?></dd>
    </dl>    
    <dl class="dl-horizontal table1">
      <dt>处理方法：</dt>
      <dd><?php echo $task_detail['process_method']; ?></dd>
    </dl>     
    <dl class="dl-horizontal table1">
      <dt>处理结果：</dt>
      <dd><?php echo $task_detail['process_result'] == 2 ? '故障已修复' : '故障未修复'; ?></dd>
    </dl>
    <?php if ($task_fitting_List) { foreach ($task_fitting_List as $k => $v) {
        echo "
            <dl class='dl-horizontal table1'>
                <dt>备件名称：</dt>   
                <dd>".$v['fitting_name']."</dd>
            </dl>
            <dl class='dl-horizontal table1'>
                <dt>备件编号：</dt>   
                <dd>".$v['fitting_model']."</dd>
            </dl>
            <dl class='dl-horizontal table1'>
                <dt>原厂编号：</dt>   
                <dd>".$v['factory_number']."</dd>
            </dl>
            <dl class='dl-horizontal table1'>
                <dt>备件数量：</dt>   
                <dd>".$v['num']."</dd>
            </dl>
            <dl class='dl-horizontal table1'>
                <dt>备注：</dt>   
                <dd>".$v['remark']."</dd>
            </dl>
        ";
     }}?>

</div>
</div>
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
</style>
