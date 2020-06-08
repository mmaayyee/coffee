<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
$this->title = '剩余物料修改记录';
?>
<p>
    <?=$is_show ? Html::a('添加申请', ['create'], ['class' => 'btn btn-success']) : ''?>
</p>
<div class="scm-user-surplus-material-sure-record-index">
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '物料名称',
            'value' => function ($model) {
                return isset($model->material) ? $model->material->name : '';
            },
        ],
        [
            'label' => '物料规格',
            'value' => function ($model) {
                return isset($model->material) && $model->material->weight ? $model->material->weight . $model->material->materialType->spec_unit : '';
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
            'label' => '修改原因',
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
            'label' => '审核日期',
            'value' => function ($model) {
                return $model->sure_time ? date('Y-m-d', $model->sure_time) : '';
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update}',
            'buttons'  => [
                'update' => function ($url, $model) {
                    return $model->is_sure == 1 ? Html::a('修改', $url, ['class' => 'btn btn-primary']) : '';
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
                'label' => '修改原因',
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
                'label' => '审核日期',
                'value' => function ($model) {
                    return $model->sure_time ? date('Y-m-d', $model->sure_time) : '';
                },
            ],
            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{update}',
                'buttons'  => [
                    'update' => function ($url, $model) {
                        return $model->is_sure == 1 ? Html::a('修改', Url::to(['/distribution-user/update-gram','id' =>$model->id]), ['class' => 'btn btn-primary']) : '';
                    },
                ],
            ],
        ],
    ]);?>

</div>
