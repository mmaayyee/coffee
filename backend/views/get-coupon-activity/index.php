<?php

use backend\models\Activity;
use yii\grid\GridView;
use yii\helpers\Html;
$this->title                   = '领券活动';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activity-index">
    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?=\Yii::$app->user->can('添加领券活动') ? Html::a('添加领券活动', ['create'], ['class' => 'btn btn-success']) : ''?>
    </p>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label'  => '活动名称',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->activity_name;
            },
        ],
        [
            'label'  => '活动开始时间',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->start_time ? date("Y-m-d H:i", $model->start_time) : "";
            },
        ],
        [
            'label'  => '活动结束时间',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->end_time ? date("Y-m-d H:i", $model->end_time) : "";
            },
        ],
        [
            'label'  => '活动地址',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->activity_url ? $model->activity_url : "";
            },
        ],

        [
            'label'  => '活动状态',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->status ? Activity::getStatus($model) : "";
            },
        ],
        [
            'label'  => '创建时间',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->created_at ? date("Y-m-d H:i", $model->created_at) : "";
            },
        ],

        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{view} {update}',
            'buttons'  => [
                'view'   => function ($url, $model, $key) {
                    return \Yii::$app->user->can('查看领券活动') ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url) : '';
                },
                'update' => function ($url, $model, $key) {
                    return \Yii::$app->user->can('编辑领券活动') && Activity::getIsDisplayUpdate($model) ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url) : '';
                },
            ],
        ],
    ],
]);?>
</div>
