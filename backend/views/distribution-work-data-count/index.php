<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\export\ExportMenu;
use common\models\Building;
use backend\models\ScmMaterialType;
use backend\models\Organization;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionWaterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '工作数据统计（配送）';
$this->params['breadcrumbs'][] = ['label' => '配送数据统计管理', 'url' => ['/distribution-task/data-statistics']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
p{
	height: 40px;
}
table td:nth-child(1){
	width:30%;
}
table tr .title{
	width:15%;
	text-align: center;
	vertical-align: middle;
}
.title label{
	display: block;
	margin-top:43% ;
}
</style>
<div class="distribution-waterial-record">

    <?php echo $this->render('_search', ['model' => $model]); ?>
	<p><?= Html::a('返回上一页',['/distribution-task/data-statistics'], ['class' => 'btn btn-success pull-left']) ?> </p>

		<?php if(isset($workDataCountArr)){ ?>
		<?php foreach ($workDataCountArr as $orgName => $countArr) { ?>
		<table class="table table-bordered"> 
			<tr >
				<td class="title" rowspan="5">
					<label><?php echo $orgName == "北京总部" ? "全国" : $orgName; ?></label>
				</td>
			</tr>
			<tr>
				<td>总任务时长：</td>
				<td>
					<?php echo ceil($countArr['totalTime']/3600) ?> 小时
				</td>
			</tr>
			<tr>
				<td>总修理时长：</td>
				<td>
					<?php echo ceil($countArr['repairTime']/3600) ?> 小时
				</td>
			</tr>
			<tr>
				<td>总台次：</td>
				<td>
					<?php echo $countArr['taiCi'] ?> 台
				</td>
			</tr>
			<tr>
				<td>人均台次：</td>
				<td>
					<?php echo $countArr['userCount'] == 0 ? 0 : number_format($countArr['taiCi']/$countArr['userCount'], 1) ?> 台
				</td>
			</tr>
		</table>
		<?php }} ?>
</div>
			