<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderInfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '订单管理';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('/js/jquery-1.9.1.min.js', ['position' => View::POS_END]);
$this->registerJsFile('/js/echarts.min.js', ['position' => View::POS_END]);
$this->registerJsFile('/order-info-compare/compare.js', ['position' => View::POS_END]);
?>
<style>
.grid-view th {
     white-space: normal;
}
.charts {
  width: 1060px;
  height: 500px;
}
.chart-txt {
   width: 1060px;
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
<div class="order-info-index">
    <h1><?=Html::encode($this->title);?></h1>
    <p class="chart-txt">&nbsp;&nbsp;<strong>对比数据:</strong> <input type="radio" name="compareId" value="1"> 昨日数据 <input type="radio" name="compareId" value="2"> 上周同期 <input type="radio" name="compareId" value="3"> 上月同期 <input type="radio" name="compareId" value="4"> 去年同期 <input type="radio" name="compareId" value="0"> 取消对比</p>
    <p class="chart-txt">&nbsp;&nbsp;<strong>本日 </strong>杯均价:<span id="todayCupAverage"></span> </p>
    <p class="chart-txt" style="display: none;" id="comparePart">&nbsp;&nbsp;<strong><sapn id="compareName"></sapn> </strong>杯均价:<span id="compareCupAverage"></span> </p>
    <div id="orderChart" class="charts"></div>
    <?php echo $this->render('_search', ['model' => $searchModel, 'couponIdNameList' => $couponIdNameList]); ?>
    <span>订单总数：<?php echo $count; ?></span> <span>实际支付总额：<?php echo $realPrice; ?> </span><span>购买总杯数：<?php echo $totalCups; ?> </span><span>每杯均价：<?php echo $averageCup; ?></span>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        [
            'class'  => 'yii\grid\SerialColumn',
            'header' => '序号',
        ],
        [
            'attribute' => '订单编号',
            'format'    => 'text',
            'value'     => function ($model) {return isset($model->order_code) ? $model->order_code : '';},
        ],
        [
            'label'  => '手机号',
            'format' => 'text',
            'value'  => function ($model) {
                return isset($model->user_mobile) ? $model->user_mobile : '';
            },
        ],
        [
            'attribute' => '支付方式',
            'format'    => 'text',
            'value'     => function ($model) {
                return $model->getPaytype($model->pay_type);
            },
        ],
        [
            'attribute' => '订单状态',
            'format'    => 'text',
            'value'     => function ($model) {
                return $model->getOrderStatus($model->order_status);
            },
        ],
        [
            'attribute' => '订单来源',
            'format'    => 'text',
            'value'     => function ($model) {
                return $model->getSourceType();
            },
        ],
        [
            'attribute' => '订单总价',
            'format'    => 'text',
            'value'     => function ($model) {
                return $model->total_fee;
            },
        ],
        [
            'attribute' => '付款金额',
            'format'    => 'text',
            'value'     => function ($model) {
                return $model->actual_fee;
            },
        ],
        [
            'label' => '实际支付(含咖豆与优惠券)',
            'value' => function ($model) {
                return sprintf("%.2f", $model->actual_fee + $model->beans_amount + $model->coupon_real_value);
            },
        ],
        [
            'label'  => '优惠汇总',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->discount_fee;
            },
        ],
        [
            'label' => '咖豆数量',
            'value' => function ($model) {
                return $model->beans_num;
            },
        ],
        [
            'attribute' => '咖豆实际价值',
            'value'     => function ($model) {
                return isset($model->beans_amount) ? floatval($model->beans_amount) : '';
            },
        ],
        [
            'label'  => '优惠券名称',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->coupon_name;
            },
        ],
        [
            'label'  => '优惠券价值',
            'format' => 'text',
            'value'  => function ($model) {
                return empty($model->coupon_real_value) ? '0.00' : $model->coupon_real_value;
            },
        ],
        [
            'label'  => '配送费',
            'format' => 'text',
            'value'  => function ($model) {
                return empty($model->delivery_cost) ? '0.00' : $model->delivery_cost;
            },
        ],
        [
            'label'  => '购买数量',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->order_cups;
            },
        ],
        [
            'attribute' => '创建时间',
            'format'    => 'text',
            'value'     => function ($model) {return date("Y-m-d H:i:s", $model->created_at);},
        ],
        [
            'attribute' => '支付时间',
            'format'    => 'text',
            'value'     => function ($model) {
                return $model->pay_at ? date("Y-m-d H:i:s", $model->pay_at) : '';
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'header'   => '查看',
            'template' => '{view}',
            'buttons'  => [
                'view' => function ($url, $model) {
                    return !\Yii::$app->user->can('订单管理详情查看') ? '' : Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                        Url::to(['/order-info/view?id=' . $model->order_id]));
                },

            ],
        ],
    ],
]);?>
</div>

