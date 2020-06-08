<?php

use backend\models\Activity;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\ActivitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '自组合套餐活动管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activity-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Html::a('添加', ['create'], ['class' => 'btn btn-success'])?>
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
                return $model->start_time ? date("Y-m-d H:i", (int) $model->start_time) : "";
            },
        ],
        [
            'label'  => '活动结束时间',
            'format' => 'text',
            'value'  => function ($model) {
                return $model->end_time ? date("Y-m-d H:i", (int) $model->end_time) : "";
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
            'label'  => '活动类型',
            'format' => 'text',
            'value'  => function ($model) {
                return empty($model->activity_type) ? '' : \backend\models\ActivityCombinPackageAssoc::$activityType[$model->activity_type];
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
            'template' => '{view} {update} {delivery}',
            'buttons'  => [
                // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                'view'     => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('自组合套餐活动查看') ? '' : Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url);
                },
                'update'   => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('自组合套餐活动编辑') ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                },
                'delivery' => function ($url, $model, $key) {
                    return !\Yii::$app->user->can('自组合用户发货管理') ? '' : Html::a('<span class="">发货管理</span>', Url::to(['activity-combin-package-delivery/index', 'ActivityCombinPackageDeliverySearch[activity_id]' => $model->activity_id]));
                },
            ],
        ],
    ],
]);?>
</div>
