<?php
use backend\models\DeliveryOrder;
/* @var $this yii\web\View */
/* @var $model common\models\DeliveryOrder */
$this->title                   = $deliveryOrder['delivery_order_id'];
$this->params['breadcrumbs'][] = ['label' => '外卖订单', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<body>
     <style>
     .order-detail-title {
        font-weight: 600;
        font-size: 20px;
        color: #333;
     }
     .cancel-order-btn {
        margin: 20px auto;
        width: 80px;
        height: 50px;
        color: #fff;
        font-size: 15px;
        line-height: 50px;
        text-align: center;
        background: #bb1c07;
        cursor: pointer;
     }
     .cancel-order-off {
        margin: 20px auto;
        width: 80px;
        height: 50px;
        color: #fff;
        font-size: 15px;
        line-height: 50px;
        text-align: center;
        background: #888888;
     }
     .order-detail-icon {
        margin: 0 auto;
        width: 27px;
        height: 34px;
        background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABsAAAAiCAIAAAAhwzVnAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNS1jMDIxIDc5LjE1NTc3MiwgMjAxNC8wMS8xMy0xOTo0NDowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTQgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjIxQjY5QzJBQ0M2MjExRThCRDE5QkQzOTUyNzlBNEY2IiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjIxQjY5QzJCQ0M2MjExRThCRDE5QkQzOTUyNzlBNEY2Ij4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MjFCNjlDMjhDQzYyMTFFOEJEMTlCRDM5NTI3OUE0RjYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MjFCNjlDMjlDQzYyMTFFOEJEMTlCRDM5NTI3OUE0RjYiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz76mmQjAAABIUlEQVR42mL4Txzo6OolUiUTA7UB9U1kBLoTj/TPnz+vXLt+9cq1F69f8vPyKSsrmRgZCggIkGki0Lgdu3bLycrIy8lxc3MDuW/fvz918rSnhzsfHy9OI/GE8Zlz58+dP48m+Pr123kLF3378YOcmAF6Fug6NEERESEpKckXL16QEzNv3r8FehZTXFBA4NvX7+SYyM3JBQw7TPGvX76xsbGQYyIwZoFRgSb4/fv39x/eCwsJkWMiMKEAY/bNm3fIxm3fuUtLU0MIt4kE0uOnT5/XbtgAjAoBPr5v334AXQc0TkNdHV8SJ5hPgQnl3oMHjS1tt27ffvv2LUH1LARzFSc7u6K8PDsbu6qKysDk61ETR00cNXHUxFETR01EAIAAAwB8qENy/GEFsgAAAABJRU5ErkJggg==);
     }
    </style>
    <div style="padding:30px;">
        <div>
            <p class="order-detail-title">订单信息</p>
            <div class="row">
                <div class="col-md-4">
                    <p>用户支付时间：<?=isset($logList[2]['create_time']) ? date('Y-m-d H:i', $logList[2]['create_time']) : ''?></p>
                    <p>预计送达时间：<?=$deliveryOrder['expect_service_time'] ? date('Y-m-d H:i', $deliveryOrder['expect_service_time']) : '';?></p>
                    <p>订单编号：<?=$deliveryOrder['delivery_order_code']?></p>
                    <!-- <p>下单平台：</p>
                    <p>支付方式：</p> -->
                    <p>订单合计：<?=$orderInfo['total_fee']?>元</p>
                    <p>优惠金额：<?=$orderInfo['discount_fee']?>元</p>
                    <p>配送金额：<?=$deliveryOrder['delivery_cost']?>元</p>
                    <p>使用优惠：<?=$useCoupon?></p>
                    <p>使用配送优惠：<?=$useCostCoupon?></p>
                    <p>实际支付金额：<?=$orderInfo['actual_fee']?>元</p>
                    <p>支付咖豆数量：<?=$orderInfo['beans_num']?>个</p>
                </div>
                <div class="col-md-4">
                    <p>收件人：<?=$userAddress['receiver']?></p>
                    <p>收件人电话：<?=$userAddress['phone']?></p>
                    <p>配送地址：<?=$userAddress['province'] . $userAddress['city'] . $userAddress['area'] . $userAddress['address']?></p>
                    <p>配送点位：<?=$buildingName?></p>
                    <p>配送员：<?=$deliveryPerson['person_name']?></p>
                    <p>配送员电话：<?=$deliveryPerson['mobile']?></p>
                </div>
                <div class="col-md-3">
                    <div <?php
//|| $deliveryOrder['delivery_order_status'] == DeliveryOrder::ORDER_STATUS_WAIT_PICK
if (!\Yii::$app->user->can('取消外卖订单') || $deliveryOrder['delivery_order_status'] == DeliveryOrder::ORDER_STATUS_SHUT || $deliveryOrder['delivery_order_status'] == DeliveryOrder::ORDER_STATUS_COMP || $deliveryOrder['delivery_order_status'] == DeliveryOrder::ORDER_STATUS_WAIT_PAY) {
    echo 'class="cancel-order-off"';
} else {
    echo 'class="cancel-order-btn"' . 'onclick="showErrorWindow(' . $deliveryOrder['delivery_order_id'] . ')"';
}
?>" id="cancelOrderBtn">取消订单</div>
                    <p style="margin: 0 auto;text-align:center;font-size:12px;">取消订单后<br>实际支付金额将自动退还给顾客</p>
                </div>
            </div>
        </div>
        <div style="float:left;width: 60%;margin-top:20px;">
            <p class="order-detail-title">订单状态详情</p>
            <?php krsort($logList);?>
            <?php foreach ($logList as $k => $log): ?>

                    <div class="row">
                        <div class="col-md-2" style="text-align:right;width: 30.66666667%;"><?=date('Y-m-d H:i:s', $log['create_time'])?></div>
                        <div class="col-md-1" style="width: 16.66666667%;">
                            <div class="order-detail-icon"></div>
                        </div>
                        <div class="col-md-2" style="text-align:left;width: 30.66666667%;">
                            <?php
$statusName = DeliveryOrder::getDeliveryOrderStatus($log['action_type']);
if ($log['action_type'] == DeliveryOrder::ORDER_STATUS_SHUT) {
    $statusName .= ' (' . $failReason['reason_name'] . ')';
}
echo $statusName, '  ';
$model = new \backend\models\DeliveryOrderSearch();
switch ($log['action_type']) {
    case $model::ORDER_STATUS_WAIT_PAY:
        break;
    case $model::ORDER_STATUS_WAIT_PICK:
        echo '：' . $model->getRemainderTime(isset($logList['3'])
            ? $logList['3']['create_time']
            : (isset($logList['8'])
                ? $logList['8']['create_time']
                : (isset($logList['9'])
                    ? $logList['9']['create_time']
                    : time())),
            $logList['2']['create_time']);
        break;
    case $model::ORDER_STATUS_PICK:
        echo '：' . $model->getRemainderTime(isset($logList['4'])
            ? $logList['4']['create_time']
            : (isset($logList['8'])
                ? $logList['8']['create_time']
                : (isset($logList['9'])
                    ? $logList['9']['create_time']
                    : time())),
            $logList['3']['create_time']);
        break;
    case $model::ORDER_STATUS_MAKE:
        echo '：' . $model->getRemainderTime(isset($logList['5'])
            ? $logList['5']['create_time']
            : (isset($logList['8'])
                ? $logList['8']['create_time']
                : (isset($logList['9'])
                    ? $logList['9']['create_time']
                    : time())),
            $logList['4']['create_time']);
        break;
    case $model::ORDER_STATUS_DISTR:
        echo '：' . $model->getRemainderTime(isset($logList['8'])
            ? $logList['8']['create_time']
            : (isset($logList['9'])
                ? $logList['9']['create_time']
                : time()),
            $logList['5']['create_time']);
        break;
    case $model::ORDER_STATUS_COMP:
        break;
    case $model::ORDER_STATUS_SHUT:
        break;
}
?>

                        </div>
                    </div>
            <?php endforeach?>
        </div>
     <div style="float:right;width: 40%">
         <p class="order-detail-title">订单商品列表</p>
         <label style="text-align:left;width: 30.66666667%;">商品名</label>
         <label style="text-align:center;width: 16.66666667%;">单价</label>
         <label style="text-align:center;width: 16.66666667%;">数量</label>
         <?php
foreach ($productNameList as $product) {
    ?>
        <br>
        <label style="text-align:left;width: 30.66666667%;font-weight: inherit;"><?=$product['product_name']?>
        <?php if ($product['cf_product_type'] == 'drinks'): ?>
            <?=' · ' . DeliveryOrder::$select_sugar[$product['select_sugar']]?>
        <?php endif?>
        </label>
        <label style="text-align:center;width: 16.66666667%;font-weight: inherit;"><?=$product['product_price']?></label>
        <label style="text-align:center;width: 16.66666667%;font-weight: inherit;"><?=$product['cup_number']?></label>
         <?php
}
?>
     </div>
    </div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label">请选择取消原因</label><br>
                    <select name='fail_reason_id' class="form-control">
                         <?php
foreach ($reasonList as $reason) {
    echo '<option value="' . $reason['fail_id'] . '">' . $reason['reason_name'] . '</option>';
}

?>

                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="sendCancel()" class="btn btn-primary" data-dismiss="modal"><span aria-hidden="true"></span>确定</button>
            </div>
        </div>
    </div>
</div>
    <script src="http://libs.baidu.com/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript">
    window.parent.onscroll = function(e){
        scrollModal();
    }
    function scrollModal(){
        if(self!=top){
            var scrollTop = window.parent.document.documentElement.scrollTop||window.parent.document.body.scrollTop+50;
            // console.log("scrollTop..",scrollTop);
            $(".modal-content").css({top: scrollTop+"px"});
        }
    }
    scrollModal();
    function showErrorWindow(deliveryOrderId){
        //弹层
        $("#myModal").modal();
        //id传入
        $("#myModal").attr("delivery_order_id",deliveryOrderId);
    }
    function sendCancel(){
        var data = {};
        //订单id
        data.delivery_order_id = $("#myModal").attr("delivery_order_id");
        //获取取消原因
        data.fail_reason_id = $("select[name=fail_reason_id]").val();
        $.ajax({
            url:"/delivery-order/cancel",
            type:"post",
            dataType:"json",
            data:data,
            success:function(resData){
                if(resData["status"] == "success"){
                    location.reload();
                }else{
                    alert(resData['msg']);
                }
            },
            error:function(){
                alert('请求失败!');
            },
        });
    }
</script>
</body>
