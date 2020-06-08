<?php
use yii\helpers\Url;
$this->title = "预投放通知";
?>
<?php if (!$deliveryArr) {?>
	<div style="margin: 20% 0;text-align: center;">
		<div class="glyphicon glyphicon-exclamation-sign text-primary" style="font-size:10rem;margin-bottom: 8%;"></div>
		<p style="font-size: 1.4rem">暂无数据</p>
	</div>
<?php } else {?>
	<div>
		<table class="table table-bordered">
		<?php foreach ($deliveryArr as $delivery) {?>
			<tr>
				<?php if ($delivery['read_time'] || $delivery['read_status'] == 1) {?>
					<td>
						<a href="<?php echo Url::to(['equip-delivery-note/pre-delivery-detail', 'id' => $delivery['delivery_id']]) ?>"><?php echo \common\models\Building::getBuildingDetail('name', ['id' => $delivery['deliver']['build_id']])['name'] ?></a>
					</td>
				<?php } else {?>
					<td>
						<a style="color:red;" href="<?php echo Url::to(['equip-delivery-note/pre-delivery-detail', 'id' => $delivery['delivery_id']]) ?>"><?php echo \common\models\Building::getBuildingDetail('name', ['id' => $delivery['deliver']['build_id']])['name'] ?></a>
					</td>
				<?php }?>
				<td>
					<?php echo date("Y年m月d日 H时i分", $delivery['deliver']['create_time']) ?>
				</td>
			</tr>
			<tr>
				<td>
					需要投放时间：
				</td>
				<td>
					<?php echo date("Y年m月d日", $delivery['deliver']['delivery_time']) ?>
				</td>
			</tr>
		<?php }?>
		</table>
	</div>
<?php }?>