<?php
/**
 * Created by PhpStorm.
 * User: wangxl
 * Date: 17/12/19
 * Time: 上午10:18
 */
use backend\models\ShopOrder;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\widgets\DetailView;
$this->title                   = '订单详情';
$this->params['breadcrumbs'][] = ['label' => '订单列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile("@web/js/jquery-1.9.1.min.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("@web/js/bootstrap3-validation.js", ["depends" => [JqueryAsset::className()]]);

?>
<div class="building-view">

    <h1><?=Html::encode($this->title);?></h1>

    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        [
            'attribute' => 'user_id',
            'value'     => $orderInfo['user_id'],
        ],
        [
            'label' => '收货人',
            'value' => $orderInfo['receiver'],
        ],
        [
            'label' => '收货手机',
            'value' => $orderInfo['phone'],
        ],
        [
            'label' => '收货地址',
            'value' => $orderInfo['address'],
        ],
        [
            'attribute' => 'order_code',
            'value'     => $orderInfo['order_code'],
        ],

        [
            'attribute' => 'order_status',
            'value'     => $model->getOrderStatus($orderInfo['order_status']),
        ],
        [
            'attribute' => 'create_time',
            'value'     => $orderInfo['create_time'] > 0 ? date('Y-m-d H:i:s', $orderInfo['create_time']) : '',
        ],
        [
            'attribute' => 'send_time',
            'value'     => $orderInfo['send_time'] > 0 ? date('Y-m-d H:i:s', $orderInfo['send_time']) : '',
        ],

        [
            'attribute' => 'receive_time',
            'value'     => $orderInfo['receive_time'] > 0 ? date('Y-m-d H:i:s', $orderInfo['receive_time']) : '',
        ],
        [
            'attribute' => '订单总额',
            'value'     => $orderInfo['total_fee'],
        ],
        [
            'attribute' => '商品总额',
            'value'     => bcsub($orderInfo['total_fee'], $orderInfo['express_money'], 2),
        ],
        [
            'attribute' => '实付金额',
            'value'     => $orderInfo['actual_fee'],
        ],
        [
            'attribute' => '快递金额',
            'value'     => $orderInfo['express_money'],
        ],
        [
            'attribute' => '是否开过发票',
            'value'     => $orderInfo['invoice'] ? '已申请过发票' : '发票未申请',
        ],
    ],
]);?>

    <div class="bs-example" data-example-id="bordered-table">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>商品名称</th>
                <th>规格</th>
                <th>单价</th>
                <th>数量</th>
            </tr>
            </thead>
            <tbody>
            <?php $goodsList = $model->getOrderGoodsDetail($orderId);?>
            <?php if ($goodsList): ?>
                <?php foreach ($goodsList as $key => $goods): ?>
                    <tr>
                        <th scope="row"><?php echo $goods['goods_name']; ?></th>
                        <td><?php echo $goods['goods_attribute']; ?></td>
                        <td><?php echo $goods['goods_price']; ?></td>
                        <td><?php echo $goods['goods_num']; ?></td>
                    </tr>
                <?php endforeach;?>
            <?php endif;?>
            </tbody>
        </table>
    </div>

        <!--待发货和存在物流信息的显示-->
    <?php if ($orderInfo['order_status'] == ShopOrder::WAIT_EXPRESS || isset($expressInfo['LogisticCode']) && $expressCompany): ?>
    <div class="list-group">
        <a href="#" class="list-group-item active">
            物流信息
        </a>
        <?php if ($orderInfo['order_status'] == ShopOrder::WAIT_EXPRESS && Yii::$app->user->can('发货')): ?>
        <li class="list-group-item form-inline">
            <div class="form-group">
                <label class="sr-only" for="express_code">订单发货</label>
                <div class="input-group">
                    <div class="input-group-addon">快递公司</div>
                    <select class="form-control" name="express">
                        <?php foreach ($company as $code => $name): ?>
                        <option value="<?=$code?>"><?=$name;?></option>
                        <?php endforeach;?>
                    </select>
                    <div class="input-group-addon">快递单号</div>
                    <input type="text" class="form-control" id="express_code" placeholder="快递单号">
                </div>
            </div>
            <button id="add_express" class="btn btn-primary">发货</button>
        </li>
        <?php endif;?>
        <?php if (isset($expressInfo['LogisticCode']) && $expressCompany): ?>
            <li class="list-group-item form-inline">
                <div class="form-group">
                    <label class="sr-only" for="express_code">订单发货</label>
                    <div class="input-group">
                        <div class="input-group-addon">快递公司</div>
                        <select class="form-control" name="express">
                            <?php foreach ($company as $code => $name): ?>
                                <option value="<?=$code?>"><?=$name;?></option>
                            <?php endforeach;?>
                        </select>
                        <div class="input-group-addon">快递单号</div>
                        <input type="text" class="form-control" id="express_code" placeholder="快递单号">
                    </div>
                </div>
                <button id="add_express" class="btn btn-primary">修改新的快递信息</button>
            </li>
            <li class="list-group-item">快递单号：<?php echo $expressInfo['LogisticCode']; ?></li>
            <li class="list-group-item">快递公司：<?php echo $expressCompany; ?></li>
            <?php foreach ($expressInfo['Traces'] as $trace): ?>
                <li class="list-group-item"><?=$trace['AcceptTime'] . ' ' . $trace['AcceptStation'];?></li>
            <?php endforeach;?>
        <?php endif;?>
    </div>
    <?php endif;?>
</div>
<!--申请退款-->

    <?php if ($refundInfo): ?>
    <div class="list-group">
        <a href="#" class="list-group-item active"> 退款信息 </a>
        <li class="list-group-item"><?=$refundInfo;?></li>
    </div>
    <?php endif;?>
    <!--无退款信息,并且待发货或者已完成状态才可以申请退款-->
    <?php if (($orderInfo['order_status'] == ShopOrder::WAIT_EXPRESS || $orderInfo['order_status'] == ShopOrder::FINISHED || $orderInfo['order_status'] == ShopOrder::WAIT_RECEIVE) && Yii::$app->user->can('订单退款')): ?>
		        <button id="refund-btn" type="button" class="btn btn-primary" data-toggle="modal" data-target="#refundModal" data-order-id="<?php echo $orderId; ?>" data-order-status="<?php echo $orderInfo['order_status']; ?>">申请退款</button>
		    <?php endif;?>
    <div class="modal fade" id="refundModal" tabindex="-1" role="dialog" aria-labelledby="refundModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="refundModalLabel">申请退款</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="refundReason" class="control-label">退款原因:</label>
                            <input type="text" class="form-control" id="refundReason" maxlength="50" check-type="required">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-primary" id="submitId" onclick="replayRefund();">提交</button>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">

    function replayRefund(){
        $("#refundModal form").validation();
        if($("#refundModal form").valid() == false) {
            return false;
        }
        var orderId = $("#refund-btn").data("order-id");
        var orderStatus = $("#refund-btn").data("order-status");
        var refundReason = $("#refundReason").val();
        $.ajax({
            "type":"post",
            "url":"/shop-order/refund-order",
            "data":{"refundReason":refundReason,"orderId":orderId,"orderStatus":orderStatus},
            "dataType":"json",
            "success":function(data){
                if(data.result){
                    window.location.reload();
                }else{
                    alert("申请退款失败");
                }
            }
        });
        $("#refundModal").modal("hide")
    }

</script>
<?php
$this->registerJS('
$("#express_code").on("blur",function(){
   var reg = /^[0-9A-Za-z]{5,}$/;
   var expressCode = $(this).val();
   if(reg.test(expressCode)){
      $("#add_express").on("click",function(data){
         var expressCode = $("#express_code").val();
         var company = $("[name=\'express\'] :checked").val();
         var orderId = ' . $orderId . ';
         $.ajax({
           "type":"post",
           "url":"' . Url::toRoute('shop-order/update-order-express') . '",
           "data":{"orderExpress":{"orderId":orderId,"expressCode":expressCode,"company":company}},
           "dataType":"json",
           "success":function(data){
              if(data.result){
                 window.location.reload();
              }else{
                 alert("发货失败");
              }
           }
         })
      });
   }else{
    alert("请输入合法得快递单号");
   }
});
   $("#add_express").on("click",function(data){
        var data =  $("#express_code").val();
        if (data == \'\'){
            alert(\'请输入快递单号\');
        }
    });
     $("#refundReason").on("keypress",function (event) {
        if (event.keyCode == "13"){
            return false;
        }
    });
');?>
