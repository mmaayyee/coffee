<?php

use common\helpers\Tools;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CustomerServiceComplaintSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '客诉列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-service-complaint-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', [
    'model'           => $searchModel,
    'buildingList'    => $buildingList,
    'solution'        => $solution,
    'questionList'    => $questionList,
    'advisoryList'    => $advisoryList,
    'orgList'         => $orgList,
    'processStatus'   => $processStatus,
    'orgBuildingList' => $orgBuildingList,
]); ?>

    <p>
        <?=Html::a('新建客诉', ['/service/complaint/add-complaint'], ['class' => 'btn btn-success', 'target' => '_blank'])?>
    </p>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        [
            'class'  => 'yii\grid\SerialColumn',
            'header' => '序号',
        ],
        [
            'attribute' => '客诉编号',
            'format'    => 'text',
            'value'     => function ($model) {return isset($model->complaint_code) ? $model->complaint_code : '';},
        ],
        [
            'attribute' => '来电号码',
            'format'    => 'text',
            'value'     => function ($model) {return isset($model->callin_mobile) ? $model->callin_mobile : '';},
        ],
        [
            'attribute' => '订单编号',
            'format'    => 'raw',
            'value'     => function ($model) {
                return $model->getOrderCode();
            },
        ],
        [
            'attribute' => '工号',
            'format'    => 'text',
            'value'     => function ($model) {return isset($model->manager_name) ? $model->manager_name : '';},
        ],
        [
            'attribute' => '所在城市',
            'format'    => 'text',
            'value'     => function ($model) {return isset($model->org_city) ? $model->org_city : '';},
        ],
        [
            'attribute' => '点位名称',
            'format'    => 'text',
            'value'     => function ($model) {return isset($model->building_name) ? $model->building_name : '';},
        ],
        [
            'attribute' => '设备类型',
            'format'    => 'text',
            'value'     => function ($model) {return isset($model->equipment_type) ? $model->equipment_type : '';},
        ],
        [
            'attribute' => '咨询类型',
            'format'    => 'text',
            'value'     => function ($model) {return isset($model->advisory_type_id) ? $model->advisory_type_id : '';},
        ],
        [
            'attribute' => '问题类型',
            'format'    => 'text',
            'value'     => function ($model) {return isset($model->question_type_id) ? $model->question_type_id : '';},
        ],
        [
            'attribute' => '问题描述',
            'format'    => 'text',
            'value'     => function ($model) {return isset($model->question_describe) ? $model->question_describe : '';},
        ],
        [
            'attribute' => '需退款金额（元）',
            'value'     => function ($model) {
                return $model->order_refund_price;
            },
        ],
        [
            'attribute' => '进度',
            'format'    => 'text',
            'value'     => function ($model) {return isset($model->process_status) ? $model->process_status : '';},
        ],
        [
            'attribute' => '处理时间',
            'format'    => 'text',
            'value'     => function ($model) {
                return $model->complete_time <= 0 ? '' : Tools::time2string($model->complete_time - strtotime($model->add_time));
            },
        ],
        [
            'attribute' => '客户区分',
            'format'    => 'text',
            'value'     => function ($model) {
                return $model::$customerTypeList[$model->customer_type] ?? '';
            },
        ],
        [
            'attribute' => '创建时间',
            'format'    => 'text',
            'value'     => function ($model) {return isset($model->add_time) ? $model->add_time : '';},
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'header'   => '操作',
            'template' => '{view} {update} {del}',
            'buttons'  => [
                'view'   => function ($url, $model, $key) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, ['title' => '查看', 'target' => '_blank']);
                },
                'update' => function ($url, $model, $key) {
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', 'add-complaint?complain_id=' . $model->complaint_id, ['title' => '编辑', 'target' => '_blank']);
                },

            ],
        ],
    ],
]);?>
</div>
