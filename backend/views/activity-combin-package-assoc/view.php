<?php

use backend\models\Activity;
use backend\models\ActivityCombinPackageAssoc;
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $model backend\models\ActivityCombinPackageAssoc */

$this->title                   = '自组合套餐活动详情';
$this->params['breadcrumbs'][] = ['label' => '自组合套餐活动管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activity-combin-package-assoc-view">

    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        'activity_name',
        [
            'attribute' => 'activity_type',
            'value'     => function ($model){
                return empty($model->activity_type)? '' : ActivityCombinPackageAssoc::$activityType[$model->activity_type];
            }
        ],
        [
            'attribute' => 'free_delivery_cost',
            'value'     => function ($model){
                return $model->activity_type == 2 ? $model->free_delivery_cost.'元  用户商品金额大于或等于该数均可免配送费' : '';
            },
        ],
        'start_time',
        'end_time',
        [
            'attribute' => 'status',
            'value'     => $model->status ? Activity::getStatus($model, 1) : '',
        ],
        [
            'attribute' => 'is_refund',
            'value'     => ActivityCombinPackageAssoc::$isRefund[$model->is_refund],
        ],
        [
            'attribute' => 'not_part_city',
            'value'     => $model->not_part_city ? implode(',', $model->not_part_city) : '',
        ],

        [
            'attribute' => 'point_type',
            'value'     => $model->point_type ? ActivityCombinPackageAssoc::getPointType($model->point_type) : '',
        ],
        [
            'attribute' => 'activity_url',
            'value'     => $model->activity_url,
        ],
        [
            'attribute' => 'banner_photo_url',
            'format'    => 'html',
            'value'     => $model->banner_photo_url ? "<img src='" . $model->banner_photo_url . "'/>" : '',
        ],

        [
            'attribute' => '单品梯度',
            'format'    => 'html',
            'value'     => function ($model) {
                return $model->product_information_json ? ActivityCombinPackageAssoc::getProductInformationHtml($model->product_information_json) : ActivityCombinPackageAssoc::getFreeSingleHtml($model->free_single_json);
            },
        ],
        [
            'attribute' => '选择商品',
            'format'    => 'html',
            'value'     => $model->product_id_str ? $model->product_name : '',
        ],

        [
            'attribute' => 'created_at',
            'value'     => $model->created_at,
        ],
        [
            'attribute' => 'order_user_num',
            'value'     => $model->order_user_num ? $model->order_user_num : '暂未统计',
        ],
        [
            'attribute' => 'order_num',
            'value'     => $model->order_num ? $model->order_num : '暂未统计',
        ],
        [
            'attribute' => 'sales_volume',
            'value'     => $model->sales_volume ? $model->sales_volume : '暂未统计',
        ],
        [
            'attribute' => 'total_income',
            'value'     => $model->total_income ? $model->total_income : '暂未统计',
        ],
    ],
])?>

</div>
