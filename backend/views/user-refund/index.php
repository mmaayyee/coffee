<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserRefundSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '退款记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-refund-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>


    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '手机号',
            'value' => function ($model) {return $model->fundMobile;},
        ],
        [
            'attribute' => '退款金额',
            'format'    => 'text',
            'value'     => function ($model) {return $model->refundPrice;},
        ],
        [
            'attribute' => '退回咖豆',
            'format'    => 'text',
            'value'     => function ($model) {return $model->refundBeansNum;},
        ],
        [
            'attribute' => '退款原因',
            'format'    => 'text',
            'value'     => function ($model) {return $model->refundMsg;},
        ],
        [
            'attribute' => '退款类型',
            'format'    => 'text',
            'value'     => function ($model) {return $model->refund_type;},
        ],
        [
            'attribute' => '退款状态',
            'format'    => 'text',
            'value'     => function ($model) {return $model->refund_status;},
        ],
        [
            'attribute' => '申请时间',
            'format'    => 'text',
            'value'     => function ($model) {return $model->refundCreatedTime;},
        ],
        [
            'label'  => '退款订单ID',
            'format' => 'raw',
            'value'  => function ($model) {return "<a href='" . Url::to(['order-info/view', 'id' => $model->order_id]) . "'>" . $model->order_id . "</a>";},
        ],
//            [
        //                'class'    => 'yii\grid\ActionColumn',
        //                'template' => '{view} {update} {delete}',
        //                'buttons'  => [
        //                    'update' => function ($url, $model, $key) {
        //                        return (!\Yii::$app->user->can('编辑退款申请') || $model->refund_status) ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
        //                    },
        //                ],
        //            ],
    ],
]);?>

</div>
