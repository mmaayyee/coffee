<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ScmUserSurplusMaterialSureRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '剩余物料修改记录';
$this->params['breadcrumbs'][] = ['label' => '配送员管理', 'url' => ['/distribution-user/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scm-user-surplus-material-sure-record-index">
    <h1><?=Html::encode($this->title)?></h1>
     <p>
        <?=isset($searchModel->author) && $searchModel->author ? Html::a('返回上一页', '/distribution-user/view?id=' . $searchModel->author, ['class' => 'btn btn-primary']) : ''?>
    </p>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '申请人',
            'value' => function ($model) {
                return isset($model->user) ? $model->user->name : '';
            },
        ],
        [
            'label' => '物料名称',
            'value' => function ($model) {
                return isset($model->material) ? $model->material->name : '';
            },
        ],
        [
            'label' => '物料规格',
            'value' => function ($model) {
                return isset($model->material) ? $model->material->weight . $model->material->materialType->spec_unit : '';
            },
        ],
        [
            'label' => '添加还是减少',
            'value' => function ($model) {
                return $model::$addReduce[$model->add_reduce];
            },
        ],
        [
            'label' => '物料数量',
            'value' => function ($model) {
                return isset($model->material) ? $model->material_num . $model->material->materialType->unit : '';
            },
        ],
        [
            'label' => '申请日期',
            'value' => function ($model) {
                return $model->date;
            },
        ],
        [
            'label' => '申请原因',
            'value' => function ($model) {
                return $model->reason;
            },
        ],
        [
            'label' => '审核状态',
            'value' => function ($model) {
                return $model::$sure[$model->is_sure];
            },
        ],
        [
            'label' => '确认日期',
            'value' => function ($model) {
                return $model->sure_time ? date('Y-m-d', $model->sure_time) : '';
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'header' => '操作',
            'template' => '{sure}{sure_no}',
            'buttons'  => [
                'sure'    => function ($url, $model) {
                    return (Yii::$app->user->can('剩余物料修改确认') && $model->is_sure == 1) ? Html::a('通过', 'update?id=' . $model->id . '&is_sure=2', ['class' => 'btn btn-primary']) . ' ' : '';
                },
                'sure_no' => function ($url, $model) {
                    return (Yii::$app->user->can('剩余物料修改确认') && $model->is_sure == 1) ? Html::a('不通过', 'update?id=' . $model->id . '&is_sure=3', ['class' => 'btn btn-primary']) : '';
                },
            ],
        ],
    ],
]);?>

    <?=GridView::widget([
        'dataProvider' => $gramDataProvider,
        'columns'      => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => '申请人',
                'value' => function ($model) {
                    return isset($model->user) ? $model->user->name : '';
                },
            ],
            [
                'label' => '物料类型',
                'value' => function ($model) {
                    return isset($model->material_type_id) ? $model->materialType->material_type_name : '';
                },
            ],
            [
                'label' => '供应商',
                'value' => function ($model) {
                    return isset($model->supplier_id) ? $model->supplier->name : '';
                },
            ],
            [
                'label' => '添加还是减少',
                'value' => function ($model) {
                    return $model::$addReduce[$model->add_reduce];
                },
            ],
            [
                'label' => '物料重量',
                'value' => function ($model) {
                    return isset($model->material_gram) ? $model->material_gram . $model->materialType->weight_unit : '';
                },
            ],
            [
                'label' => '申请日期',
                'value' => function ($model) {
                    return $model->date;
                },
            ],
            [
                'label' => '申请原因',
                'value' => function ($model) {
                    return $model->reason;
                },
            ],
            [
                'label' => '审核状态',
                'value' => function ($model) {
                    return $model::$sure[$model->is_sure];
                },
            ],
            [
                'label' => '确认日期',
                'value' => function ($model) {
                    return $model->sure_time ? date('Y-m-d', $model->sure_time) : '';
                },
            ],
            [
                'class'    => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{sure}{sure_no}',
                'buttons'  => [
                    'sure'    => function ($url, $model) {
                        return (Yii::$app->user->can('剩余物料修改确认') && $model->is_sure == 1) ? Html::a('通过', 'update-gram?id=' . $model->id . '&is_sure=2', ['class' => 'btn btn-primary']) . ' ' : '';
                    },
                    'sure_no' => function ($url, $model) {
                        return (Yii::$app->user->can('剩余物料修改确认') && $model->is_sure == 1) ? Html::a('不通过', 'update-gram?id=' . $model->id . '&is_sure=3', ['class' => 'btn btn-primary']) : '';
                    },
                ],
            ],
        ],
    ]);?>

</div>
