<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\export\ExportMenu;
use common\models\Building;
use backend\models\ScmMaterialType;
use yii\widgets\LinkPager;
 
/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionWaterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '物料配送记录';
$this->params['breadcrumbs'][] = ['label' => '配送数据统计管理', 'url' => ['/distribution-task/data-statistics']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="distribution-material-record">

    <?php echo $this->render('_search', ['model' => $model]); ?>
    <p>
    <?= Html::a('返回上一页',['/distribution-task/data-statistics'], ['class' => 'btn btn-success pull-left']) ?> 
    <?= Html::a('Excel导出', ['/distribution-material-record/excel-expord', 'param'=> isset($param)?$param:""], ['class' => 'btn btn-success btn-right-param']) ?>
    </p>
    <style type="text/css">
        .btn-right-param{
            margin-left: 10px;
        }
        p{
            height: 40px;
        }
    </style>

	<?php if (isset($taskFillerArr)) { ?>
	<table class="table table-bordered" >
		<tr>
			<td>楼宇名称</td>
			<?php foreach (ScmMaterialType::getMaterialTypeArray("", 'pieces') as $typeKey => $typeVal) { ?>
    			<td><?php echo $typeVal; ?></td>
    		<?php } ?>
		</tr>
		<?php foreach ($taskFillerArr as $taskFillerKey => $taskFillerVal) { ?>
		<tr>
				<td><?php echo Building::getBuildingDetail('name', ['id'=>$taskFillerKey])['name']?></td>
				<?php foreach (ScmMaterialType::getMaterialTypeArray() as $typeKey => $typeVal) {  ?>
	    		<?php  if (isset($taskFillerVal[$typeKey])) { ?>
	    			<td><?php echo $taskFillerVal[$typeKey]; ?></td>
	    		<?php }else{ ?>
					<td>0</td>
    		<?php }} ?>
		</tr>
		<?php  }?>
	</table>
	<?php } ?>
	<?=
		LinkPager::widget([
	      'pagination' => $pages,
	    ]);
	?>
	
</div>
			