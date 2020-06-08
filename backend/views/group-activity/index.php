<?php

use backend\models\GroupActivity;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title                   = '拼团活动列表展示';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-activity-index">

    <h1><?=Html::encode($this->title)?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>



<?php echo GridView::widget([
    'dataProvider' => $dataProvider,
    // 'filterModel' => $searchModel,
    'columns'      => [
        [
            'class'         => 'yii\grid\SerialColumn',
            'headerOptions' => ['width' => '40'],
        ], //不需要显示前面的导航
        [
            'attribute'     => 'main_title',
            'label'         => '活动名称',
            'headerOptions' => ['width' => '200'],
        ],
        [
            'attribute'     => 'group_sort',
            'label'         => '活动排序',
            'value'         => function ($model) {
                return GroupActivity::getActivitySort($model);
            },
            'headerOptions' => ['width' => '50'],
        ],
        [
            'attribute'     => 'type',
            'label'         => '活动类型',
            'value'         => function ($model) {
                return GroupActivity::dropDown("type", $model->type);
            },
            "filter"        => GroupActivity::dropDown("type"),
            'headerOptions' => ['width' => '100'],
        ],
        [
            'attribute'     => 'status',
            'label'         => '活动状态',
            'value'         => function ($model) {
                return GroupActivity::getActivityStatus($model);
            },
            "filter"        => GroupActivity::dropDown("status"),
            'headerOptions' => ['width' => '100'],
        ],
        [
            'attribute'     => 'begin_time',
            'label'         => '活动开始时间',
            'value'         => function ($model) {
                return date("Y-m-d H:i:s", $model->begin_time);
            },
            'headerOptions' => ['width' => '100'],
        ],
        [
            'attribute'     => 'end_time',
            'label'         => '活动结束时间',
            'value'         => function ($model) {
                return date("Y-m-d H:i:s", $model->end_time);
            },
            'headerOptions' => ['width' => '100'],
        ],
        ['class'        => 'yii\grid\ActionColumn', 'header' => '操作', 'template' => '{view}{update}',
            'buttons'       => [
                'update' => function ($url, $model) {
                    return !\Yii::$app->user->can('拼团活动添加/编辑') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', "save?group_id={$model->group_id}", ['title' => '编辑']);
                },
                'view'   => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', "view?id={$model->group_id}", ['title' => '查看']);
                },
            ],
            'headerOptions' => ['width' => '20'],
        ],
    ],
    'emptyText'    => '没有筛选到任何内容哦',
]);
?>








</div>

