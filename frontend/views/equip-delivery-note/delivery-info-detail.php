<?php
use backend\models\ScmEquipType;
use yii\helpers\Url;
$this->title = "投放单通知";
?>
<style type="text/css">
	.wrap > .container {
    	padding: 8px;
	}
	 table{
		border-left:1px solid #ddd;
		border-right:1px solid #ddd;
		border-bottom:1px solid #ddd ;
	}
	tbody > tr > td{
		border-left:1px solid #ddd;
		word-break:break-word;
	}
</style>
<div>
	<table class="table table-striped">
		<tr>
			<td>
				楼宇名称：
			</td>
			<td>
				<?php echo isset($deliveryInfo->build->name) ? $deliveryInfo->build->name : ''; ?>
			</td>
		</tr>
		<tr>
			<td>
				楼宇人数：
			</td>
			<td>
				<?php echo isset($deliveryInfo->build->people_num) ? $deliveryInfo->build->people_num : 0; ?> 人
			</td>
		</tr>
		<tr>
			<td>
				发起时间：
			</td>
			<td>
				<?php echo $deliveryInfo['create_time'] ? date("Y年m月d日 H时i分", $deliveryInfo['create_time']) : ''; ?>
			</td>
		</tr>
		<tr>
			<td>
				详细地址：
			</td>
			<td>
				<?php echo isset($deliveryInfo->build) ? $deliveryInfo->build->province . $deliveryInfo->build->city . $deliveryInfo->build->area . $deliveryInfo->build->address : ''; ?>
			</td>
		</tr>
		<tr>
			<td>
				销售责任人：
			</td>
			<td>
				<?php echo $deliveryInfo['sales_person'] ?>
			</td>
		</tr>
		<tr>
			<td>
				投放设备数量：
			</td>
			<td>
				<?php echo $deliveryInfo['delivery_number'] ? $deliveryInfo['delivery_number'] : '0'; ?> 台
			</td>
		</tr>
		<tr>
			<td>
				设备类型：
			</td>
			<td>
			<?php echo ScmEquipType::getEquipTypeDetail('*', ['id' => $deliveryInfo['equip_type_id']])['model']; ?>
			</td>
		</tr>
		<tr>
			<td>
				需要投放时间：
			</td>
			<td>
				<?php echo date("Y年m月d日", $deliveryInfo['delivery_time']) ?>
			</td>
		</tr>

		<tr>
			<td>
				设备声音：
			</td>
			<td>
				<?php echo $deliveryInfo['voice_type'] ?>
			</td>
		</tr>
		<tr>
			<td>
				需要电表：
			</td>
			<td>
				<?php echo !empty($deliveryInfo['is_ammeter']) ? '是' : '否' ?>
			</td>
		</tr>
		<tr>
			<td>
				需要灯箱外包：
			</td>
			<td>
				<?php $lightList = \backend\models\EquipDelivery::getLightBoxArr();?>
				<?php echo isset($lightList[$deliveryInfo['is_lightbox']]) ? $lightList[$deliveryInfo['is_lightbox']] : '';?>
			</td>
		</tr>
		<tr>
			<td>
				有无特殊要求：
			</td>
			<td>
				<?php echo !empty($deliveryInfo['special_require']) ? $deliveryInfo['special_require'] : '暂无' ?>
			</td>
		</tr>
	</table>
	<?php if ($readFeedback) {?>
		<div class="form-group">
			<label>反馈内容：</label>
			<?php echo $readFeedback; ?>
		</div>
	<?php } else {?>
		<form action="<?php echo Url::to(['equip-delivery-note/update-delivery-read']) ?>" class="form-inline" role="form">
			<div class="form-group">
		      <textarea name="read_feedback" class="form-control" rows="3"></textarea>
		      <input type="hidden" name="deliveryId" value="<?php echo $deliveryInfo['Id']; ?>">
		   	</div>
			<div class="form-group">
		      <input class="btn btn-block btn-success" type="submit" value="确认并反馈">
		   	</div>
		</form>
	<?php }?>
</div>