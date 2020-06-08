<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

$url             = empty($userId) ? 'build-consume-list' : 'user-consume-list';
$data            = Yii::$app->request->get();
$nickname        = !empty($data['nickname']) ? $data['nickname'] : '';
$register_mobile = !empty($data['register_mobile']) ? $data['register_mobile'] : '';
?>
<script>
	<!--确认退款-->
function confirmRefund(id){
 var url = "<?php echo Yii::$app->params['fcoffeeUrl']; ?>";
	var isconfirm = confirm("确认要退咖啡吗");
	if(isconfirm){
		$.ajax({
			url:url +'erpapi/customer-service/refund-coffee.html?comsume_id=' +id,
			data:'',
			dataType: 'json',
			type:'get',
			success:function(data){
				if(data.msg == 'success'){
					alert("退还咖啡成功")
				}else{
					alert("退还咖啡失败")
				}

			},
			error:function(){
				alert("退款失败")
			}
		})
	}
}
</script>


<div class="well">
<div class="coffee-language-search">
    <div class="form-group  form-inline">
        <?php $form = ActiveForm::begin([
    'action' => [$url],
    'method' => 'get',
]);?>
    <?=$form->field($model, 'user_id')->hiddenInput(['value' => $userId])->label(false);?>
    <?=$form->field($model, 'build_id')->hiddenInput(['value' => $buildID])->label(false);?>
    <?=$form->field($model, 'order_id')->textInput(['size' => 18])->label('订单编号')->textInput(['value' => $orderID])?>
    <?=$form->field($model, 'createdFrom')->widget(\janisto\timepicker\TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ],
])->textInput(['value' => $createdFrom]);
?>
    <?=$form->field($model, 'createdTo')->widget(\janisto\timepicker\TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'hour'       => 23,
        'minute'     => 59,
        'second'     => 59,
        'showSecond' => true,
    ],
])->textInput(['value' => $createdTo]);
?>
        <div class="form-group">
            <?=Html::submitButton('检索', ['class' => 'btn btn-success'])?>
        </div>
        <?php ActiveForm::end();?>
    </div>
</div>
</div>

	<span>共计 <?php echo $pager->totalCount; ?> 条数据</span>
	<table class="table table-bordered table-striped" style="width:100%">
		<thead>
			<th>序号</th>
			<th>消费ID</th>
			<th>订单编号</th>
			<th>原始订单编号</th>
			<th>电话号码</th>
			<th>制作时间</th>
			<th>单品名称</th>
			<th>制作糖量</th>
			<th>领取方式</th>
			<th>付款金额</th>
			<th>兑换券名称</th>
			<th>点位名称</th>
			<th>结果</th>
			<th>处理状态</th>
			<th>操作</th>
		</thead>
		<tbody>
			<?php $base = (Yii::$app->request->get('page', 1) - 1) * $pager->pageSize;?>
			<?php foreach ($consumeList as $index => $consume): ?>
				<?php $nickname = empty($consume['nickname']) ? $nickname : $consume['nickname'];
$register_mobile    = empty($consume['username']) ? $register_mobile : $consume['username'];?>
				<tr>
					<td><?php echo $base + $index + 1; ?></td>
					<td><a target="_blank" href="/index.php/user-consume/view?id=<?=$consume['user_consume_id']?>#/detail"><?=$consume['user_consume_id']?></a></td>
					<td><a target="_blank" href="/index.php/order-info/view?id=<?=$consume['order_id']?>#/detail"><?=$consume['order_code']?></a></td>
					<td><a target="_blank" href="/index.php/order-info/view?id=<?=$consume['order_source_id']?>#/detail"><?=$consume['order_source_code']?></a></td>
					<td><?php echo $register_mobile; ?></td>
					<td><?php echo $consume['fetch_time']; ?></td>
					<td><?php echo $consume['product_name']; ?></td>
					<td><?php echo $consume['user_consume_sugar']; ?></td>
					<td><?php echo $consume['consume_type']; ?></td>
					<td><?php echo $consume['actual_fee']; ?></td>
					<td><?php echo $consume['exchange_coupon_name']; ?></td>
					<td><?php echo $consume['building_name']; ?></td>
					<td><?php echo $consume['make_result']; ?></td>
					<td><?php echo $consume['detail_status']; ?></td>
                    <td>
                        <a target="_blank" class="glyphicon glyphicon-pencil" href="/index.php/service/complaint/add-complaint?user_consume_id=<?=$consume['user_consume_id'] . '&order_code=' . $consume['order_code'] . '&build_id=' . $consume['build_id'] . '&user_id=' . $consume['user_id'] . '&org_id=' . $consume['org_id'] . '&nickname=' . $nickname . '&register_mobile=' . $register_mobile . '&pay_type=' . $consume['pay_type'] . '&pay_at=' . $consume['pay_at'];?>"></a>
                        <a target="_blank" href="/index.php/quick-send-coupon/create?consume_id=<?=$consume['user_consume_id'] . '&order_code=' . $consume['order_code'] . '&phone=' . $register_mobile;?>">&nbsp;发券</a>
                        <?php if (!empty($consume['refund_coffee'])): ?>
                        <a   class="glyphicon glyphicon-share-alt"  href="javascript:void(0)" onclick="confirmRefund(<?php echo $consume['user_consume_id']; ?>)"></a>
                    <?php endif?>
                    </td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>

	<?=LinkPager::widget(['pagination' => $pager]);?>

