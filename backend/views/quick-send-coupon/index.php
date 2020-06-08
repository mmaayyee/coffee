<?php

use backend\models\QuickSendCoupon;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel common\models\QuickSendCouponSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '快速发券';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quick-send-coupon-index ">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php if (Yii::$app->user->can('快速发券添加')) {?>
    <p>
        <?=Html::a('添加', ['create'], ['class' => 'btn btn-success'])?>
    </p>
    <?php }?>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label'  => '用户账号',
            'format' => 'raw',
            'value'  => function ($model) {return $model->send_phone;},
        ],
        [
            'label' => '类型',
            'value' => function ($model) {return QuickSendCoupon::getCouponeFieldName(0, $model->coupon_sort);},
        ],
        [
            'label' => '发劵时间',
            'value' => function ($model) {return date('Y-m-d H:i', $model->create_time);},
        ],
        [
            'label'  => '优惠券名称',
            'format' => 'raw',
            'value'  => function ($model) {return $model->content;},
        ],
        [
            'label' => '发劵数量',
            'value' => function ($model) {return $model->coupon_number;},
        ],
        [
            'label' => '消费记录ID',
            'value' => function ($model) {return $model->consume_id;},
        ],
        [
            'label' => '订单编号',
            'value' => function ($model) {return $model->order_code;},
        ],
        [
            'label' => '来电号码',
            'value' => function ($model) {return $model->caller_number;},
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{view}',
            'buttons'  => [
                'view' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', '/quick-send-coupon/view?id=' . $model->id);
                },
            ],
        ],
    ],
]);?>

</div>
