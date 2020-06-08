<?php

use backend\models\ScmMaterialType;
use yii\helpers\Html;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionWaterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '开箱签到';
$this->params['breadcrumbs'][] = ['label' => '配送数据统计管理', 'url' => ['/distribution-task/data-statistics']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="distribution-water-index">

    <?php echo $this->render('_search', ['model' => $model, 'managerOrgId' => $managerOrgId]); ?>
    <p>
    <?=Html::a('返回上一页', ['/distribution-task/data-statistics'], ['class' => 'btn btn-success pull-left'])?>
    <?=Html::a('Excel导出', ['/distribution-sign-box/excel-expord', 'param' => isset($param) ? $param : ""], ['class' => 'btn btn-success btn-right-param'])?>
    <br/>
    </p>
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <style type="text/css">
        	tr{
        		text-align: center;
        	}
        	td{
        		border:1px solid black;
        		height:20px;
        	}
            .btn-right-param{
                margin-left: 10px;
            }
            p{
                height: 40px;
            }
        </style>
    </head>
	<body>
	<?php if (isset($taskArr)) {?>
		<table class="table table-bordered" width="100%" border="0" cellpadding="0" cellspacing="0">

    	<tr >
    		<td width="8%" rowspan="2">姓名</td>
    		<td width="8%" rowspan="2">楼宇</td>
    		<td width="8%" rowspan="2">开箱时间</td>
    		<td width="8%" rowspan="2">关箱时间</td>
    		<td width="60%" colspan="<?php echo $count; ?>">物料添加</td>

    	</tr>
    	<tr >
    	<?php foreach (ScmMaterialType::getMaterialTypeArray('', 'pieces') as $typeKey => $typeVal) {?>
    		<td><?php echo $typeVal; ?></td>
    	<?php }?>
    	</tr>
    	<?php foreach ($taskArr as $taskKey => $taskVal) {?>
    	<tr>
    		<td><?php echo $taskVal['assign_userid'] ?></td>
    		<td><?php echo $taskVal['build_id'] ?></td>
    		<td><?php echo date("Y-m-d H:i", $taskVal['start_delivery_time']); ?></td>
    		<td><?php echo date("Y-m-d H:i", $taskVal['end_delivery_time']); ?></td>
    		<?php foreach (ScmMaterialType::getMaterialTypeArray() as $typeKey => $typeVal) {?>
	    		<?php if (isset($taskVal['filler'][$typeKey])) {?>
	    			<td><?php echo $taskVal['filler'][$typeKey]; ?></td>
	    		<?php } else {?>
					<td>0</td>
    		<?php }}?>
    	</tr>
		<?php }?>
    </table>
    <?php }?>
    <?=
LinkPager::widget([
    'pagination' => $pages,
]);
?>
    </body>

</div>
