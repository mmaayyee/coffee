<?php

use common\helpers\Tools;
use yii\grid\GridView;
use yii\helpers\Html;

$this->title                   = '楼宇列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="point-postion-index">
    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
       <?=!Yii::$app->user->can("点位助手创建") ? "" : Html::a('添加', ['create'], ['class' => 'btn btn-success'])?>
    </p>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '楼宇名称',
            'value' => function ($model) {
                return $model->point_name;
            },
        ],
        [
            'label' => '状态',
            'value' => function ($model) {
                return $model::$pointStatusList[$model->point_status] ?? '';
            },
        ],
        [
            'label' => '销量星级',
            'value' => function ($model) {
                return $model->getStarLevel();
            },
        ],
        [
            'label' => '渠道',
            'value' => function ($model) use ($pointTypeList) {
                return $pointTypeList[$model->point_type_id] ?? '';
            },
        ],
        [
            'label' => '日人流量',
            'value' => function ($model) {
                return $model->day_peoples . '万';
            },
        ],
        [
            'label' => '合作方式',
            'value' => function ($model) {
                return $model::$cooperationTypeList[$model->cooperation_type] ?? '';
            },
        ],
        [
            'label' => '付款周期',
            'value' => function ($model) {
                return $model::$payCycleList[$model->pay_cycle] ?? '';
            },
        ],
        [
            'label' => '创建时间',
            'value' => function ($model) {
                return Tools::getDateByTime($model->create_time);
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{view} {update}',
            'buttons'  => [
                'view'   => function ($url, $model, $key) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', '/point-position/view?id=' . $model->point_id);
                },

                'update' => function ($url, $model, $key) {
                    return !Yii::$app->user->can("点位助手修改") ? "" : Html::a('<span class="glyphicon glyphicon-pencil"></span>', '/point-position/update?id=' . $model->point_id);
                },
            ],
        ],
    ],
]);?>

</div>
