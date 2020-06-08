<?php
/* @var $this yii\web\View */
/* @var $model common\models\DeliveryOrder */
$this->title                   = '';
$this->params['breadcrumbs'][] = $this->title;
?>
<body>
     <style>
     .delivery-box {
        margin: 10px auto 0 auto;
        padding: 8px;
        width: 150px;
        height: 150px;
        border: 1px solid #aaa;
        color: #666;
     }
     .delivery-box-title {
        font-weight: 600;
        font-size: 24px;
     }
     .delivery-box-txt {
        font-size: 14px;
     }
     .delivery-box-num {
        font-weight: 600;
        font-size: 32px;
     }
    </style>
    <div style="padding:30px;">
        <div class="row">
            <div class="col-md-2">
                <div class="delivery-box">
                    <p class="delivery-box-title">今日订单</p>
                    <p class="delivery-box-txt">今日下单数总数</p>
                    <p class="delivery-box-num" id="todayOrderTotal"></p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="delivery-box">
                    <p class="delivery-box-title">未接单</p>
                    <p class="delivery-box-txt">未被接单订单数</p>
                    <p class="delivery-box-num" id="notAcceptOrderNum"></p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="delivery-box">
                    <p class="delivery-box-title">已接单</p>
                    <p class="delivery-box-txt">已被接单订单数</p>
                    <p class="delivery-box-num" id="acceptedOrderNum"></p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="delivery-box">
                    <p class="delivery-box-title">已完成</p>
                    <p class="delivery-box-txt">已经完成订单数</p>
                    <p class="delivery-box-num" id="doneOrderNum"></p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="delivery-box">
                    <p class="delivery-box-title">失败订单</p>
                    <p class="delivery-box-txt">被关闭的订单数</p>
                    <p class="delivery-box-num" id="closedOrderNum"></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">&nbsp;</div>
            <div class="col-md-2">
                <div class="delivery-box">
                    <p class="delivery-box-title">配送中</p>
                    <p class="delivery-box-txt">正在配送的订单数</p>
                    <p class="delivery-box-num" id="deliveryOrderNum"></p>
                </div>
            </div>
        </div>
    </div>
    <script src="http://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript">
        $(function(){
            var todayOrderTotal = <?php echo $orderCount['order_total'] ?>; //今日下单数总数
            var notAcceptOrderNum = <?php echo $orderCount['wait_order_total'] ?>; //未接单
            var acceptedOrderNum = <?php echo $orderCount['get_order_total'] ?>; //已接单
            var doneOrderNum = <?php echo $orderCount['complate_order_total'] ?>; //已完成
            var closedOrderNum = <?php echo $orderCount['cancel_order_total'] ?>;// 失败订单
            var deliveryOrderNum = <?php echo $orderCount['delivery_order_total'] ?>;//配送中
            $("#todayOrderTotal").text(todayOrderTotal);
            $("#notAcceptOrderNum").text(notAcceptOrderNum);
            $("#acceptedOrderNum").text(acceptedOrderNum);
            $("#doneOrderNum").text(doneOrderNum);
            $("#closedOrderNum").text(closedOrderNum);
            $("#deliveryOrderNum").text(deliveryOrderNum);
        })

    </script>
</body>