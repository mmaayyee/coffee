<?php 
$this->title = "灯箱报修";
$this->registerJsFile('http://map.qq.com/api/js?v=2.exp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ',['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/lightBoxRepair.js',['depends'=>['frontend\assets\AppAsset']]); 
use common\models\EquipLightBoxRepair;
?>
<style>
	.dl-horizontal dt{
		width:105px;
		text-align: left;
		white-space:normal;
	}
	.dl-horizontal dd {
   	 margin-left: 105px;
	}
	dl {
   		margin-bottom: 10px;
	}
	.btn-lg{
			margin-top: 8%;
			width:100%;
		    padding:6px 12px;
	}
</style>
<div id="task_detail">
    <dl class="dl-horizontal">
      <dt>楼宇名称：</dt>
      <dd><?php echo $task_detail->build ? $task_detail->build->name : '';?></dd>
    </dl>
    <dl class="dl-horizontal">
      <dt>楼宇地址：</dt>
      <dd id="address"><?php echo $task_detail->build ? $task_detail->build->province.$task_detail->build->city.$task_detail->build->area.$task_detail->build->address : '';?></dd>
    </dl>
    <div id="allmap" style="width:100%;height:200px;margin-bottom:3% ;"></div>
    <dl class="dl-horizontal">
      <dt>任务创建时间：</dt>
      <dd><?php echo $task_detail['create_time'] ? date('Y年m月d日 H点i分',$task_detail['create_time']) : ''?></dd>
    </dl>
    <dl class="dl-horizontal">
        <dt>处理结果：</dt>
        <dd><?php echo $task_detail['process_result'] ? EquipLightBoxRepair::$process_result[$task_detail['process_result']] : ''?></dd>
    </dl>

    <dl class="dl-horizontal">
        <dt>处理时间：</dt>
        <dd><?php echo $task_detail['process_time'] ? date('Y年m月d日 H点i分',$task_detail['process_time']) : ''?></dd>
    </dl>
    <dl class="dl-horizontal">
        <dt>备注：</dt>
        <dd><?php echo $task_detail['remark']; ?></dd>
    </dl>
    <?php if ($task_detail['process_result'] < 8 && $task_detail['process_result'] != 4) {?>
        <button type="button" data-id="<?php echo $task_detail['id']; ?>" data-type='8' class="btn btn-success btn-lg">维修成功</button>
    <?php if ($task_detail['process_result'] != 3) {?>
        <button type="button" data-id="<?php echo $task_detail['id']; ?>" data-type='3' class="btn btn-danger btn-lg">拉回工厂</button>
    <?php }} ?>
</div>
