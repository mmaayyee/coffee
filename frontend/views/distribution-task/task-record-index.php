<?php
use backend\models\DistributionTask;
use common\models\Building;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
$this->title = "任务记录";

$this->registerJs('
    $("#search_r").click(function(){
        // 提交数据
        $("#myModal").modal();
    })
')

?>
<style type="text/css">
    .panel-default > .panel-heading {
    			text-align: center;
          display: table;
          width:100%;
    }
    .panel-heading h3{
	    width: 60%;
        }
	#search_r{
		display: table-cell;
    vertical-align: middle;
    font-size: 1.6rem;
    width: 8%;
	}
	.modal-header{
		padding-bottom: 0;
		border-bottom:none;
	}
	.modal-body{
		padding-top: 0;
	}
	.modal-dialog{
		margin-top:20% ;
	}
</style>
<div class="panel panel-default">
   <div class="panel-heading">
      <h3 class="panel-title" style="text-align:center;margin: 0 auto;">
		   		 任务记录
      		<span class="badge"><?php echo $distributeTaskCount; ?></span>
      </h3>
      <span id="search_r" class="glyphicon glyphicon-search text-primary"></span>
   </div>
<!-- 搜索条件开始 -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    	<div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      </div>
		  <div class="modal-body task-record-search">
		      <?php $form = ActiveForm::begin([
    'action' => ['task-record-index'],
    'method' => 'get',
]);?>
		      <div class="form-group form-inline">
		          <?=$form->field($model, 'task_type')->dropDownList(DistributionTask::getTaskTypeList('true'))?>
		          <?=$form->field($model, 'start_time')->textInput(['type' => 'date'])?>

		          <?=$form->field($model, 'end_time')->textInput(['type' => 'date'])?>
		          <?=Html::submitButton('检索', ['class' => 'btn btn-primary btn-block'])?>
		      </div>
		      <?php ActiveForm::end();?>
		  </div>
		</div>
	</div>
</div>
<!-- 搜索条件结束 -->
  <div class="panel-body">
    <?php foreach ($distributeTaskArr as $key => $value) {
    ?>
      <div class="row" >
	        <div class="col-xs-4">
	           <p style="margin-top:15px;">
                <?php if ($value['result'] == 1) {
        echo "<p>完成任务</p>";
    } else {
        echo "<b style='color:#e4393c; width:100px;height:50px;'>维修失败</b>";
    }?>
             </p>
	        </div>
        <a href="<?php echo Url::to(['distribution-task/task-record-detail', 'id' => $value['id']]) ?>">
         <div class="col-xs-8">
            <div class="row">
               <div class="col-xs-12" >
                  <p><?php echo Building::getBuildingDetail("name", ['id' => $value['build_id']])['name'] ?></p>
               </div>
               <div class="col-xs-12" >
                  <p><?php echo date("Y-m-d H:i", $value['create_time']) ?></p>
               </div>
            </div>
         </div>
         </a>
      </div>
      <hr/>
    <?php }?>
  </div>
</div>

