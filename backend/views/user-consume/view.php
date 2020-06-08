<?php
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\OrderGoods */
$this->params['breadcrumbs'][] = ['label' => '消费记录', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-goods-view">

    <h1><?=Html::encode($this->title)?></h1>
    <h2>消费信息</h2>
    <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>消费ID</th>
                    <th>用户手机</th>
                    <th>用户类别</th>
                    <th>订单号码</th>
                    <th>领取方式</th>
                    <th>消费时间</th>
                </tr>
            </thead>
            <tr>
                <td><?=$consumptionInformation['user_consume_id']?></td>
                <td><?=$consumptionInformation['userMobile']?></td>
                <td><?=$consumptionInformation['userType']?></td>
                <td><a href="<?=Url::to(['order-info/view', 'id' => $consumptionInformation['order_id']])?>"><?=$consumptionInformation['order_id']?></a></td>
                <td><?=$consumptionInformation['payment']?></td>
                <td><?=$consumptionInformation['fetch_time']?></td>
            </tr>
            <tbody>
            </tbody>
        </table>
</div>
<hr>
     <h2>商品信息</h2>
     <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>商品名称</th>
                    <th>商品类别</th>
                    <th>商品原价</th>
                    <th>支付方式</th>
                    <th>支付价格</th>
                    <th>实付金额</th>
                    <th>咖豆数量</th>
                    <th>咖豆抵用金额</th>
                    <th>优惠券价值</th>
                    <th>配送费</th>
                    <th>优惠券名称</th>
                </tr>
            </thead>
            <tr>
                <td><?=$consumptionInformation['product_name']?></td>
                <td><?=$consumptionInformation['product_type']?></td>
                <td><?=$consumptionInformation['source_price']?></td>
                <td><?=$consumptionInformation['pay_type']?></td>
                <td><?=$consumptionInformation['actual_fee']?></td>
                <td><?=$consumptionInformation['real_price']?></td>
                <td><?=$consumptionInformation['beans_num']?></td>
                <td><?=$consumptionInformation['beans_amount']?></td>
                <td><?=$consumptionInformation['coupon_real_value']?></td>
                <td><?=$consumptionInformation['delivery_cost']?></td>
                <td><?=$consumptionInformation['couponName']?></td>
            </tr>
            <tbody>
            </tbody>
        </table>
        <hr>
        <h2>楼宇信息</h2>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>点位编号</th>
                    <th>点位名称</th>
                    <th>一级机构</th>
                    <th>二级机构</th>
                    <th>渠道类型</th>
                    <th>设备类型</th>
                    <th>运营模式</th>
                    <th>设备编号</th>
                </tr>
            </thead>
            <tr>
                <td><?=$consumptionInformation['build_number']?></td>
                <td><?=$consumptionInformation['building']?></td>
                <td><?=$consumptionInformation['equipment_one']?></td>
                <td><?=$consumptionInformation['equipment_two']?></td>
                <td><?=$consumptionInformation['build_type']?></td>
                <td><?=$consumptionInformation['equipment_name']?></td>
                <td><?=$consumptionInformation['equipment_static']?></td>
                <td><?=$consumptionInformation['equipment_code']?></td>
            </tr>
            <tbody>
            </tbody>
        </table>
</div>
