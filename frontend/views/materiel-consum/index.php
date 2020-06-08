<?php
use common\models\Building;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerJsFile('@web/js/materiel-consum-index.js?v=0.2', ['depends' => ['frontend\assets\AppAsset']]);
$this->title = '所属范围-楼宇列表';
?>

<table class="table table-bordered">
	<?php foreach( $buildingList as $k => $v): ?>
	<tr>
		<td class="getBuildingInfo" buildId=<?=$v['id']?> ><?=$v['name']?><span style="float:right;">&#10140</span></td>
	</tr>
	<?php endforeach; ?>
</table>



<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">物料详情</h4>
      </div>
      <div class="modal-body">
      	<div id="buildName">
			
      	</div>
      	<div id="time">
			
      	</div>
      	<div id="materielTypeName">
			
      	</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>