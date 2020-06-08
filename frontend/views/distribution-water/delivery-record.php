<?php 
use yii\helpers\Url;
$this->title = '水单配送记录';
?>

<?php if (!$distributeWaterArr) {	?>
	<div style="margin: 20% 0;text-align: center;">
		<div class="glyphicon glyphicon-exclamation-sign text-primary" style="font-size:10rem;margin-bottom: 8%;"></div>
		<p style="font-size: 1.4rem">暂无任务</p>	
	</div>
<?php }else{ ?>	
	<div>
		水单配送记录（<?php echo $waterCount; ?>）
	</div>
	<div>
		<table class="table table-bordered"> 
		<?php foreach ($distributeWaterArr as $distributeWate) { ?>
			<tr>
				<td>
					<?php echo \common\models\Building::getBuildingDetail('name', ['id'=> $distributeWate['build_id']])['name'] ?>
				</td>
				<td>
					<?php echo floatval($distributeWate['need_water']); ?>桶
				</td>
			</tr>
			<tr>
				<td>
					完成配送时间
				</td>
				<td>
					<?php echo date("Y年m月d日 H时i分", $distributeWate['upload_time']) ?>
				</td>
			</tr>
		<?php } ?>
		</table>
	</div>
<?php } ?>