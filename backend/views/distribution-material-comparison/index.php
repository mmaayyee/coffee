<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\export\ExportMenu;
use common\models\Building;
use backend\models\ScmMaterialType;
use backend\models\ScmSupplier;
use common\models\WxMember;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionWaterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '物料对比统计';
$this->params['breadcrumbs'][] = ['label' => '配送数据统计管理', 'url' => ['/distribution-task/data-statistics']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-water-index">

    <?php echo $this->render('_search', ['model' => $model]); ?>
    <p>
    <?= Html::a('返回上一页',['/distribution-task/data-statistics'], ['class' => 'btn btn-success pull-left']) ?> 
    <?= Html::a('Excel导出', ['/distribution-material-comparison/excel-expord', 'param'=> isset($param) ? $param : ""], ['class' => 'btn btn-success btn-right-param']) ?>
    <br/>
    </p>
	<head> 
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">  
        <style type="text/css">
        	tr{
        		text-align: center;
        	}
        	td{
        		width:5%;
        		border:1px solid black;
        		height:20px;
            }
            .btn-right-param{
                margin-left: 10px;
            }
            p{
                height:40px;
            }
        </style>
    <head>    
	<body>
		<table class="table table-bordered" width="100%" border="0" cellspacing="0" cellpadding="0">
    	<tr >
    		<td rowspan="4" colspan="2">姓名</td>
    		<td rowspan="4" colspan="2">项目</td>
    		<?php foreach ($materialSpecificationArr as $materialSpecificationK => $materialSpecificationV) { ?>
    			<td colspan="<?php echo count($materialSpecificationV); ?>"><?php echo ScmMaterialType::getMaterialTypeDetail('material_type_name', ['id'=>$materialSpecificationK])['material_type_name']; ?></td>
    		<?php } ?>
    	</tr>
    	<tr >
    		<?php foreach ($materialSpecificationArr as $materialSpecificationK => $materialSpecificationV) {?>
    		<?php foreach ($materialSpecificationV as $key => $value) { ?>
    			<td><?php echo ScmSupplier::getSurplierDetail('name', ['id'=>$value['supplier_id']])['name'].'-'.$value['weight'] ?></td>
			<?php }} ?>
    	</tr>
    	<tr >
    		<?php foreach ($materialSpecificationArr as $materialSpecificationK => $materialSpecificationV) {?>
    		<?php foreach ($materialSpecificationV as $key => $value) { ?>
    			<td><?php echo $value['unit']?></td>
			<?php }} ?>
    	</tr>
    	<tr >
    		<?php foreach ($materialSpecificationArr as $materialSpecificationK => $materialSpecificationV) {?>
    		<?php foreach ($materialSpecificationV as $key => $value) { ?>
    			<td>数量</td>
			<?php }} ?>
    	</tr>
		
		<?php if(isset($materialComparisonArr)){ ?>
		<?php foreach ($materialComparisonArr as $userId => $materialKindArr) { ?>
		<tr>
			<td rowspan="5" colspan="2">
				<?php echo WxMember::getWxMemberNameList("*",array('userid'=>$userId))['name']; ?>
			</td>
		</tr>
		<?php foreach ($materialKindArr as $materialKind => $materialTypeArr) { ?>
	    	<tr>
	    		<td colspan="2"><?php echo $materialKind ?></td>
	    			<?php foreach ($materialSpecificationArr as $specificationKey => $specificationVal) { ?>
	    				<?php if(isset($materialTypeArr[$specificationKey])){ ?>
							<?php foreach ($specificationVal as $key => $value) { ?>
								<?php if(isset($materialTypeArr[$specificationKey][$key])){ ?>
										<td><?php echo $materialTypeArr[$specificationKey][$key]; ?></td>
								<?php }else{ ?>
									<td>0</td>
								<?php } ?>
							<?php } ?>
	    				<?php }else{ ?>
	    				<?php foreach ($specificationVal as $key => $value) { ?>
							<td>0</td>
	    				<?php }} ?>
	    			<?php } ?>
	    	</tr>
    	<?php } } ?>
    	<?php } ?>
    </table>
    <?php echo LinkPager::widget(['pagination' => $pages]); ?>
    </body>
</div>

