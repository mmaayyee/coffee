<?php 
use yii\helpers\Url;
$this->title = '水单列表';
?>

<?php if (!$distributeWaterArr) {	?>
	<div style="margin: 20% 0;text-align: center;">
		<div class="glyphicon glyphicon-exclamation-sign text-primary" style="font-size:10rem;margin-bottom: 8%;"></div>
		<p style="font-size: 1.4rem">暂无任务</p>	
	</div>
<?php }else{ ?>
<div class="panel panel-default">
   <div class="panel-heading">
      <h3 class="panel-title" style="text-align:center;margin: 0 auto;">
		水单
      <span class="badge"><?php echo $distributionWaterCount; ?></span>
      </h3>
   </div>
   <div class="panel-body">
   	<?php foreach ($distributeWaterArr as $distributeWate) { ?>
   		<div class="row" >
	        <div class="col-xs-4">
	        	<a href="<?php echo Url::to(['distribution-water/detail', 'id'=>$distributeWate['Id']]) ?>"><?php echo \common\models\Building::getBuildingDetail('name', ['id'=> $distributeWate['build_id']])['name'] ?></a>
   			</div>
   			<div class="col-xs-8">
            	<div class="row">
               		<div class="col-xs-12" >
               			<?php echo floatval($distributeWate['need_water']); ?>桶
               		</div>
               		<div class="col-xs-12" >
						接收信息时间：<?php echo date("Y年m月d日 H时i分", $distributeWate['order_time']) ?>
               		</div>
                </div>
            </div>
        </div>
        <hr/>
    <?php } ?>    	
   	</div>
</div>	    
<?php } ?>