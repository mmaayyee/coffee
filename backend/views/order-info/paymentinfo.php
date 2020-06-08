<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
// echo $orderInfoID;
//die;
// $this->title                   = $model->order_id;
$this->params['breadcrumbs'][] = ['label' => '订单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('/assets/f0269b11/jquery.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>
<?php $form = ActiveForm::begin([
    'action' => ['paymentinfo'],
    'method' => 'get',
    'id'     => 'orderForm',
]);?>

<div class="form-group  form-inline">
    <?=$form->field($model, 'createdFrom')->widget(\janisto\timepicker\TimePicker::className(), [
    //'language' => 'fi',
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ],
]);
?>
    <?=$form->field($model, 'createdTo')->widget(\janisto\timepicker\TimePicker::className(), [
    //'language' => 'fi',
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'hour'       => 23,
        'minute'     => 59,
        'second'     => 59,
        'showSecond' => true,
    ],
]);
?>
    <?=$form->field($model, 'pay_type')->dropDownList($model->getPayTypeArray());?>
    <div class="form-group">
         <?php if (Yii::$app->user->can('订单支付信息汇总查看')): ?>
             <?=Html::Button('检索', ['class' => 'btn btn-primary', 'id' => 'search'])?>
            <?php endif;?>
             <?php if (Yii::$app->user->can('订单支付信息汇总导出')): ?>
               <?=Html::Button('导出', ['class' => 'btn btn-primary', 'id' => 'export'])?>
            <?php endif;?>
    </div>
    <?php ActiveForm::end();?>
</div>

<table class="table table-striped table-bordered">
    <tr>
        <td>订单原价总金额</td>
        <td><?=$paymentinfoList['sourcePrice'];?></td>
    </tr>
    <tr>
        <td>订单总价总金额</td>
        <td><?=$paymentinfoList['totalFee'];?></td>
    </tr>
    <tr>
        <td>用户优惠总金额</td>
        <td><?=$paymentinfoList['UserDiscountFee'];?></td>
    </tr>
    <tr>
        <td>公司优惠总金额</td>
        <td><?=$paymentinfoList['sourcePriceDiscount'];?></td>
    </tr>
    <tr>
        <td>实际支付总金额（含咖豆、优惠券）</td>
        <td><?=$paymentinfoList['realPrice'];?></td>
    </tr>
    <tr>
        <td>购买总杯数</td>
        <td><?=$paymentinfoList['totalCups'];?></td>
    </tr>
    <tr>
        <td>付款总金额</td>
        <td><?=$paymentinfoList['actualFee'];?></td>
    </tr>
    <tr>
        <td>咖豆使用总数</td>
        <td><?=$paymentinfoList['beansNum'];?></td>
    </tr><tr>
        <td>咖豆抵用总金额</td>
        <td><?=$paymentinfoList['beansAmount'];?></td>
    </tr><tr>
        <td>咖豆实际价值</td>
        <td><?=$paymentinfoList['beansRealAmount'];?></td>
    </tr><tr>
        <td>退款总额</td>
        <td><?=$paymentinfoList['userRefundPrice'];?></td>
    </tr><tr>
        <td>优惠券优惠总额</td>
        <td><?=$paymentinfoList['couponRealValue'];?></td>
    </tr><tr>
        <td>活动优惠总额</td>
        <td><?=$paymentinfoList['orderActivityPrice'];?></td>
    </tr>
</table>
<script>
    $(function(){
        $("#search").click(function(){
            $("#orderForm").attr("action","<?php echo Url::to(['order-info/paymentinfo']); ?>");
            $("#orderForm").submit();
        });
        $("#export").click(function(){
            $("#orderForm").attr("action","<?php echo Url::to(['order-info/paymentinfo-export']); ?>");
            $("#orderForm").submit();
        });
    });
    //点击回车键进行搜索
    $(function(){
        $(document).keypress(function (e) {
            if (e.keyCode == 13)
                $("#search").click();
        })
    });
</script>