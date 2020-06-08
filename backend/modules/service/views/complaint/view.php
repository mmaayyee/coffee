<?php

use backend\models\OrderInfo;
use backend\modules\service\models\Complaint;
use common\helpers\Tools;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$order                         = new OrderInfo();
$this->params['breadcrumbs'][] = ['label' => '客诉详情  ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-service-complaint-view">
    <p>
        <?=Html::a('修改', ['/service/complaint/add-complaint', 'complain_id' => $model->complaint_id], ['class' => 'btn btn-primary'])?>
    </p>
    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        [
            'attribute' => '订单编号',
            'format'    => 'raw',
            'value'     => function ($model) {
                if (!empty($model->order_code)) {
                    $codeArr   = explode(',', $model->order_code);
                    $orderCode = '';
                    foreach ($codeArr as $code) {
                        if (!empty($code)) {
                            $orderCode .= "<a href='" . Url::to(['/order-info/view', 'id' => 0, 'order_code' => $code]) . "'>" . $code . "</a>,";
                        }
                    }
                    return trim($orderCode, ',');
                } else {
                    return '';
                }
            },
        ],
        [
            'attribute' => '消费记录ID',
            'format'    => 'raw',
            'value'     => function ($model) {
                if (!empty($model->user_consume_id)) {
                    return "<a href='" . Url::to(['/user-consume/view', 'id' => $model->user_consume_id]) . "'>" . $model->user_consume_id . "</a>";
                } else {
                    return '';
                }
            },
        ],
        [
            'attribute' => '机构名称',
            'value'     => $model->org_id,
        ],
        [
            'attribute' => '所在城市',
            'value'     => $model->org_city,
        ],
        [
            'attribute' => '点位名称',
            'value'     => $model->building_name,
        ],
        [
            'attribute' => '设备分类',
            'value'     => $model->equipment_type,
        ],
        [
            'attribute' => '设备日志',
            'value'     => $model->equipment_last_log,
        ],
        [
            'attribute' => '咨询类型',
            'value'     => $model->advisory_type_id,
        ],
        [
            'attribute' => '问题类型',
            'value'     => $model->question_type_id,
        ],
        [
            'attribute' => '问题描述',
            'value'     => $model->question_describe,
        ],
        [
            'attribute' => '客户名称',
            'value'     => $model->customer_name,
        ],
        [
            'attribute' => '来电号码',
            'value'     => $model->callin_mobile,
        ],
        [
            'attribute' => '注册号码',
            'value'     => empty($model->register_mobile) ? '' : $model->register_mobile,
        ],

        [
            'attribute' => '微信号',
            'value'     => $model->nikename,
        ],
        [
            'attribute' => '支付方式',
            'value'     => $model->pay_type == -1 ? '' : $order->getPaytype($model->pay_type),
        ], [
            'attribute' => '购买品种',
            'value'     => $model->buy_type,
        ], [
            'attribute' => '购买时间',
            'value'     => $model->buy_time,
        ],
        [
            'attribute' => '协商解决方案',
            'value'     => $model->solution_id,
        ],
        [
            'attribute' => '已退咖啡品种',
            'value'     => $model->retired_coffee_type,
        ], [
            'attribute' => '需退款金额（元）',
            'value'     => empty($model->order_refund_price) ? '' : $model->order_refund_price,
        ], [
            'attribute' => '最迟退款日期',
            'value'     => $model->latest_refund_time,
        ], [
            'attribute' => '实际退款日期',
            'value'     => $model->real_refund_time,
        ], [
            'attribute' => '进度',
            'value'     => $model->process_status,
        ],
        [
            'attribute' => '处理时间',
            'format'    => 'text',
            'value'     => $model->complete_time <= 0 ? '' : Tools::time2string($model->complete_time - strtotime($model->add_time)),
        ],
        [
            'attribute' => '客户区分',
            'format'    => 'text',
            'value'     => Complaint::$customerTypeList[$model->customer_type] ?? '',
        ],
        [
            'attribute' => '退款是否消费',
            'value'     => $model->is_consumption,
        ],
        [
            'attribute' => '工号',
            'value'     => $model->manager_name,
        ],
        [
            'attribute' => '创建时间',
            'value'     => $model->add_time,
        ],
    ],
])?>

</div>
