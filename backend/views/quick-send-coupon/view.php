<?php

use backend\models\QuickSendCoupon;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Building */

$this->title                   = $model->id;
$this->params['breadcrumbs'][] = ['label' => '快速发券管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<script type="text/javascript">
 var a = "<?=$model->send_phone?>";
</script>
<div class="building-view">

    <h1><?=Html::encode($this->title);?></h1>
    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        'id',
        [
            'label' => '类型',
            'value' => QuickSendCoupon::getCouponeFieldName(0, $model->coupon_sort),
        ],
        [
            'label'  => '用户账号',
            'format' => 'raw',
            'value'  => $model->send_phone,
        ],
        'coupon_number',
        [
            'label'  => '优惠券名称',
            'format' => 'raw',
            'value'  => $model->content,
        ],
        'coupon_remarks',
        [
            'label' => '发劵时间',
            'value' => date('Y-m-d H:i', $model->create_time),
        ],
        [
            'label' => '消费记录ID',
            'value' => $model->consume_id,
        ],
        [
            'label' => '订单编号',
            'value' => $model->order_code,
        ],
        [
            'label' => '来电号码',
            'value' => $model->caller_number,
        ],
    ],
]);?>

</div>
