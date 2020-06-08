<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title                   = '消费记录';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('/js/jquery-1.9.1.min.js', ['position' => View::POS_END]);
$this->registerJsFile('/js/echarts.min.js', ['position' => View::POS_END]);
$this->registerJsFile('/user-consume-compare/compare.js', ['position' => View::POS_END]);
?>
<style>
    .charts {
      width: 1060px;
      height: 500px;
    }
    .charts-pie {
      width: 950px;
      height: 750px;
      margin-top: 5px;
    }
    .chart-txt {
        margin: 0;
        padding: 0;
        height:25px;
        line-height:25px;
        text-align:center;
    }
</style>
<script type="text/javascript">
    var rootCoffeeStieUrl = '<?php echo Yii::$app->params['fcoffeeUrl']; ?>';
</script>
<div class="order-goods-index">
    <h1><?=Html::encode($this->title)?></h1>
    <p class="chart-txt" style="width: 1060px;">&nbsp;&nbsp;<strong>对比数据:</strong> <input type="radio" name="compareId" value="1"> 昨日数据 <input type="radio" name="compareId" value="2"> 上周同期 <input type="radio" name="compareId" value="3"> 上月同期 <input type="radio" name="compareId" value="4"> 去年同期 <input type="radio" name="compareId" value="0"> 取消对比</p>
    <p class="chart-txt" style="width: 1060px;">&nbsp;&nbsp;<strong>本日 </strong>杯均价:<span id="todayCupAverage"></span> </p>
    <p class="chart-txt" style="display: none;width: 1060px;" id="comparePart">&nbsp;&nbsp;<strong><sapn id="compareName"></sapn> </strong>杯均价:<span id="compareCupAverage"></span></p>
    <div id="consumeChart" class="charts"></div>
    <p class="chart-txt" style="width: 950px;">&nbsp;&nbsp;<strong>选择日期: <input type="text" id="datepicker" value="" readonly style="width:90px;height:25px;"/>&nbsp;&nbsp;&nbsp;&nbsp; </strong> <strong>时间:</strong><span id="pieTime"></span> <strong>总销量:</strong><span id="pieTotal"></span>杯</p>
    <div id="pieChart" class="charts-pie"></div>
    <?php echo $this->render('_search', ['model' => $searchModel, 'buildingArray' => $buildingArray]); ?>
    <div>
    <span>实付金额(含咖豆价值、优惠券价值)汇总: <?php echo $realPrice; ?></span>
    <span>实付金额(含咖豆价值、优惠券价值、配送费)汇总: <?php echo $consumeAmount; ?></span>
</div>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        [
            'class'  => 'yii\grid\SerialColumn',
            'header' => '序号',
        ],
        [
            'label'  => '订单号',
            'format' => 'raw',
            'value'  => function ($model) {return "<a href='" . Url::to(['order-info/view', 'id' => $model->order_id]) . "'>" . $model->order_id . "</a>";},
        ],
        [
            'label'  => '用户ID',
            'format' => 'text',
            'value'  => function ($model) {return $model->user_id;},
        ],
        [
            'label'  => '消费ID',
            'format' => 'text',
            'value'  => function ($model) {return $model->user_consume_id;},
        ],
        [
            'label'  => '用户号码',
            'format' => 'text',
            'value'  => function ($model) {return isset($model->userMobile) ? $model->userMobile : "";},
        ],
        [
            'label'  => '单品名称',
            'format' => 'text',
            'value'  => function ($model) {return isset($model->product_id) ? $model->product_id : "";},
        ],
        [
            'label'  => '原价价格',
            'format' => 'text',
            'value'  => function ($model) {return isset($model->source_price) ? $model->source_price : "";},
        ],
        [
            'label'  => '实付金额',
            'format' => 'text',
            'value'  => function ($model) {return isset($model->actual_fee) ? $model->actual_fee : '';},
        ],
        [
            'label'  => '优惠券价值',
            'format' => 'text',
            'value'  => function ($model) {return isset($model->coupon_value) ? $model->coupon_value : '';},
        ],
        [
            'label'  => '配送费',
            'format' => 'text',
            'value'  => function ($model) {return isset($model->delivery_cost) ? $model->delivery_cost : '';},
        ],
        [
            'label'  => '点位名称',
            'format' => 'text',
            'value'  => function ($model) {return !isset($model->building) ? '' : $model->building;},
        ],
        [
            'label'  => '点位编号',
            'format' => 'text',
            'value'  => function ($model) {return !isset($model->build_number) ? '' : $model->build_number;},
        ],
        [
            'label'  => '制作糖量',
            'format' => 'text',
            'value'  => function ($model) {return !isset($model->user_consume_sugar) ? '' : $model->user_consume_sugar;},
        ],
        [
            'label'  => '制作时间',
            'format' => 'text',
            'value'  => function ($model) {return !isset($model->fetch_time) ? '' : $model->fetch_time;},
        ],
        [
            'label'  => '领取方式',
            'format' => 'text',
            'value'  => function ($model) {return isset($model->consume_type) ? $model->consume_type : '';},
        ],
        [
            'label' => '兑换券名称',
            'value' => function ($model) {return $model->couponName;},
        ],
        [
            'label' => '状态',
            'value' => function ($model) {return $model->is_refund;},
        ],
        [
            'label' => '退还时间',
            'value' => function ($model) {return $model->refund_time;},
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'header'   => '操作',
            'template' => '{view} {refund}',
            'buttons'  => [
                'view'   => function ($url, $model) {
                    return !\Yii::$app->user->can('消费记录详情查看') ? '' : Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                        Url::to(['/user-consume/view?id=' . $model->user_consume_id]));
                },
                'refund' => function ($url, $model) {
                    $options = [
                        'onclick' => 'return confirm("确定更新退款状态吗？");',
                    ];
                    return !\Yii::$app->user->can('消费记录更新退还状态') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>',
                        Url::to(['/user-consume/refund?id=' . $model->user_consume_id]), $options);
                },
            ],
        ],

    ],
]);?>

</div>
