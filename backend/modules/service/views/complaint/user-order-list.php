<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
$data            = Yii::$app->request->get();
$nickname        = !empty($data['nickname']) ? $data['nickname'] : '';
$register_mobile = !empty($data['register_mobile']) ? $data['register_mobile'] : '';
?>

<div class="well">
    <div class="coffee-language-search">
        <div class="form-group  form-inline">
            <?php $form = ActiveForm::begin([
    'action' => ['user-order-list'],
    'method' => 'get',
]);?>
            <?=$form->field($model, 'user_id')->hiddenInput(['value' => $userId])->label(false);?>
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
    <th>订单编号</th>
    <th>创建时间</th>
    <th>支付方式</th>
    <th>付款金额</th>
    <th>优惠券名称</th>
    <th>订单杯数</th>
    <th>已消费杯数</th>
    <th>订单状态</th>
    <th>订单来源</th>
    <th>操作</th>
    </thead>
    <tbody>
    <?php $base = (Yii::$app->request->get('page', 1) - 1) * $pager->pageSize;?>
    <?php foreach ($orderList as $index => $order): ?>
        <tr>
            <td><?php echo $base + $index + 1; ?></td>
            <td><a target="_blank" href="/index.php/order-info/view?id=<?=$order['order_id']?>#/detail"><?=$order['order_code']?></a></td>
            <td><?php echo $order['created_at']; ?></td>
            <td><?php echo $order['pay_type_name']; ?></td>
            <td><?php echo $order['actual_fee']; ?></td>
            <td><?php echo $order['coupon_names']; ?></td>
            <td><?php echo $order['order_cups']; ?></td>
            <td><?php echo $order['consumed_number']; ?></td>
            <td><?php echo $order['order_status_name']; ?></td>
            <td><?php echo $order['source_type_name']; ?></td>
            <td><a target="_blank" href="/index.php/service/complaint/add-complaint?order_code=<?=$order['order_code']?>&user_id=<?=$order['user_id']?>&nickname=<?=$nickname?>&register_mobile=<?=$register_mobile?>&pay_type=<?=$order['pay_type']?>&pay_at=<?=$order['pay_at']?>"><span class="glyphicon glyphicon-pencil"></span></a> </td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

<?=LinkPager::widget(['pagination' => $pager]);?>

