<?php

use yii\grid\GridView;
use yii\helpers\Html;
use backend\models\QuickSendCoupon;
use backend\models\CouponSendTask;

$this->title                   = '发券任务优惠券统计';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-send-task-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '任务名称',
            'value' => function ($model) {
                return $model->task_name;
            },
        ],
        [
            'label' => '发送用户总数',
            'value' => function ($model) {
                return $model->user_num;
            },
        ],
        [
            'label' => '使用用户总数',
            'value' => function ($model) {
                return $model->user_total_num ? $model->user_total_num : 0;
            },
        ],
        [
            'label' => '优惠券套餐名称',
            'value' => function ($model) {
                return $model->coupon_group_id ? '(套餐)'.$model->coupon_name : $model->coupon_name;
            },
        ],
        [
            'label' => '券发送总数',
            'value' => function ($model) {
                return $model->coupon_num ? $model->coupon_num : 0;
            },
        ],
        [
            'label' => '券使用总数',
            'value' => function ($model) {
                return $model->user_coupn_total_num ? $model->user_coupn_total_num : 0;
            },
        ],
        [
            'label' => '发券时间',
            'value' => function ($model) {
                return date("Y-m-d H:i", $model->create_time);
            },
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template'=>'{view} {expord}',
            'buttons' => [
                // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                'view' =>function ($url, $model, $key) {
                    return !\Yii::$app->user->can('发券任务统计查看') ?  '' : Html::a('<span class="glyphicon glyphicon-eye-open"></span>', '/coupon-send-task-total-statistics/view?id=' . $model->id);
                },
                'expord' => function ($url, $model, $key) {
                    // !\Yii::$app->user->can('发券任务统计查看') ?  '' : 
                    if(!\Yii::$app->user->can('发券任务统计导出') && ($model->check_status != 2)){
                        return '';
                    }else{
                        return Html::a('<span class="glyphicon glyphicon-log-out"></span>', '/coupon-send-task-total-statistics/expord?id=' . $model->id,['title'=>'excel导出']);
                    }
                },
            ],
        ],
    ],
]);?>

</div>
