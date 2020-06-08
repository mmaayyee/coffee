<?php

use backend\models\DeliveryOrder;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\DeliveryOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title                   = '外卖订单';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs('
    window.parent.onscroll = function(e){
        scrollModal();
    }
    scrollModal();
')
?>
<div class="delivery-order-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

<?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label'  => '用户支付时间',
            'format' => 'text',
            'value'  => function ($model) {
                return isset($model->deliveryOrderLogs[2]['create_time']) ? date('Y-m-d H:i', $model->deliveryOrderLogs[2]['create_time']) : '';
            },
        ],
        [
            'label'  => '配送订单编号',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->delivery_order_code;
            },
        ],
        [
            'label'  => '系统订单',
            'format' => 'raw',
            'value'  => function ($model) {
                return isset($model->order_id) ? Html::a($model->order_id, ['/order-info/view?id=' . $model->order_id . '#/detail'], ['target' => '_blank', 'data' => ['pjax' => '0']]) : '';
            },
        ],
        [
            'label'  => '外卖订单',
            'format' => 'raw',
            'value'  => function ($model) {
                return isset($model->delivery_order_id) ? $model::getOrderSequenceNumber($model->delivery_order_id) : '';
            },
        ],
        [
            'label'  => '微信昵称',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->nickname;
            },
        ],
        [
            'label'  => '收件人',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->receiver;
            },
        ],
        [
            'label'  => '地址',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->address;
            },
        ],
        [
            'label'  => '分配点位',
            'format' => 'text',
            'value'  => function ($model) {
                return isset($model->building_list[$model->build_id]) ? $model->building_list[$model->build_id] : '点位已删除';
            },
        ],
        [
            'label'  => '电话',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->phone;
            },
        ],
        [
            'label'  => '订单状态',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->delivery_order_status_name;
            },
        ],
        [
            'label'  => '数量',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->product_num;
            },
        ],
        [
            'label'  => '配送员',
            'format' => 'text',
            'value'  => function ($model) {
                return empty($model->delivery_person_id) ? '未接单' : isset($model->person[$model->delivery_person_id]) ? $model->person[$model->delivery_person_id] : $model->delivery_person_id;
            },
        ],
        [
            'label'  => '阶段历时',
            'format' => 'text',
            'value'  => function ($model) {
                $create_time = end($model->deliveryOrderLogs);
                if ($create_time['action_type'] < 6) {
                    return $model->getRemainderTime(time(), $create_time['create_time']);
                }
                return '';
            },
        ],
        [
            'label'  => '全单历时',
            'format' => 'text',
            'value'  => function ($model) {
//                echo '<pre>';var_dump($model->deliveryOrderLogs);die;

                return isset($model->deliveryOrderLogs['2'])
                ? $model->getRemainderTime(isset($model->deliveryOrderLogs['8'])
                    ? $model->deliveryOrderLogs['8']['create_time']
                    : (isset($model->deliveryOrderLogs['9'])
                        ? $model->deliveryOrderLogs['9']['create_time']
                        : time()),
                    $model->deliveryOrderLogs['2']['create_time'])
                : '';
            },
        ],
        [
            'label'  => '预计送达',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->expect_service_time
                ? isset($model->deliveryOrderLogs['2'])
                ? $model->getRemainderTime($model->deliveryOrderLogs['2']['create_time'],
                    $model->expect_service_time)
                : ''
                : '';
            },
        ],
        [
            'label'  => '实际送达',
            'format' => 'text',
            'value'  => function ($model) {
                return isset($model->deliveryOrderLogs['8'])
                ? date('Y-m-d H:i', $model->deliveryOrderLogs['8']['create_time'])
                : '';
            },
        ],
        [
            'label'  => '内部备注',
            'format' => 'text',
            'value'  => function ($model) {
//                return empty($model->delivery_person_id) ? '未接单' : $model->person[$model->delivery_person_id];
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{cancel} {view} {switch}',
            'buttons'  => [
                'cancel' => function ($url, $model, $key) {
                    $options = [
                        'title'   => '取消订单',
                        'onclick' => "showErrorWindow('{$model->delivery_order_id}');return false;",
                    ];
                    //|| $model->delivery_order_status == DeliveryOrder::ORDER_STATUS_WAIT_PICK
                    return (!\Yii::$app->user->can('取消外卖订单') || $model->delivery_order_status == DeliveryOrder::ORDER_STATUS_SHUT || $model->delivery_order_status == DeliveryOrder::ORDER_STATUS_COMP || $model->delivery_order_status == DeliveryOrder::ORDER_STATUS_WAIT_PAY) ? '' : Html::a('<span class="glyphicon glyphicon-remove"></span>', $url, $options, []);
                },
                'view'   => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('查看外卖订单详情') ? '' : Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, ['title' => '查看', 'target' => '_blank', 'data' => ['pjax' => '0']]);
                },
                'switch' => function ($url, $model, $key) {
                    $options = [
                        'title'   => '切单',
                        'onclick' => "switchPerson('{$model->delivery_region_id}', this, '{$model->delivery_person_id}', '{$model->delivery_order_id}');return false;",
                    ];
                    $button = '';
                    if (in_array($model->delivery_order_status, [$model::ORDER_STATUS_PICK, $model::ORDER_STATUS_MAKE, $model::ORDER_STATUS_DISTR])) {
                        $button = Html::a('<span class="glyphicon glyphicon-transfer"></span>', 'javascript:node(0);', $options, []);
                    }
                    return !\Yii::$app->user->can('转移外卖订单') ? '' : $button;
                },
            ],
        ],
    ],
]);?>
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

<div class="modal fade" id="myPerson" tabindex="-1" role="dialog" aria-labelledby="mymyPersonLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h3 style="text-align: center">订单转移</h3>
                <div class="form-group">
                    <label class="control-label">原订单配送员</label><br>
                    <span id="personName"></span>
                </div>
                <div class="form-group">
                    <label class="control-label">转给配送员</label><br>
                    <select name='fail_reason_id' class="form-control" id="personList">
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="sendSwitch()" class="btn btn-primary" data-dismiss="modal"><span aria-hidden="true"></span>确定</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function scrollModal(){
        if(self!=top){
            var scrollTop = window.parent.document.documentElement.scrollTop||window.parent.document.body.scrollTop+50;
            // console.log("scrollTop..",scrollTop);
            $(".modal-content").css({top: scrollTop+"px"});
        }
    }
    function showErrorWindow(deliveryOrderId){
        //弹层
        $("#myModal").modal();
        //id传入
        $("#myModal").attr("delivery_order_id",deliveryOrderId);
    }

    function switchPerson(deliveryRegionId, _this, deliveryPersonId, deliveryOrderId){
        //弹层
        $("#myPerson").modal();
        $("#personName").text($(_this).parents('tr').find('td').eq(12).text());
        //获取配送员信息
        $.ajax({
            url:"/delivery-order/get-person-by-region",
            type:"post",
            dataType:"json",
            data:{
                delivery_region_id:deliveryRegionId
            },
            success:function(resData){
                if(resData.status == 'error'){
                    alert('请求失败!');
                }
                var str = '';
                $.each(resData.data, function (n,i) {
                    if(i.person_id != deliveryPersonId){
                        str += '<option value="' + i.person_id + '">' + i.person_name + '</option>';
                    }
                })
                $("#personList").html(str);
            },
            error:function() {
                alert('请求失败!');
            }
        });
        //id传入
        $("#myPerson").attr("delivery_order_id",deliveryOrderId);
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
                    location.reload();
                }
            },
            error:function(){
                alert('请求失败!');
            },
        });
    }

    function sendSwitch(){
        var data = {};
        //订单id
        data.delivery_order_id = $("#myPerson").attr("delivery_order_id");
        data.delivery_person_id = $("#personList").val();
        $.ajax({
            url:"/delivery-order/switch",
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
