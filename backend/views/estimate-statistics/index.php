<?php

use backend\models\EstimateStatistics;
use backend\models\OutStatistics;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\EstimateStatisticsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '运维预估单';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="estimate-statistics-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>


    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'org_id',
            'value'     => function ($model) use ($organization) {
                return $organization[$model->org_id] ?? '';
            },
        ],
        [
            'attribute' => 'material_info',
            'format'    => 'html',
            'value'     => function ($model) use ($scmMaterial) {
                $materialArray = !empty($model['material_info']) ? Json::decode($model['material_info']) : [];
                return OutStatistics::getMaterialDetail($materialArray, $scmMaterial);
            },
        ],

        [
            'attribute' => 'status',
            'value'     => function ($model) {
                return EstimateStatistics::$statusArray[$model->status];
            },
        ],
        [
            'attribute' => 'date',
            'label'     => '创建时间',
            'value'     => function ($model) {
                return date('Y-m-d', strtotime($model->date) - 60 * 60 * 24);
            },
        ],
        [
            'attribute' => 'send_date',
            'label'     => '发送时间',
            'value'     => function ($model) {
                return $model->send_date;
            },
        ],
        [
            'attribute' => 'distribution_date',
            'label'     => '完成配货时间',
            'value'     => function ($model) {
                return $model->distribution_date;
            },
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{update}{distribution}',
            'buttons'  => [
                'update'       => function ($url, $model) {
                    if ($model->status == EstimateStatistics::NO_SEND) {
                        return Yii::$app->user->can('修改预估单') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-pencil', 'title' => '修改']) : '';
                    }
                },
                'distribution' => function ($url, $model) {
                    if ($model->status == EstimateStatistics::NO_DISTRIBUTION) {
                        return Yii::$app->user->can('预估单配货') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-tags', 'title' => '配货']) : '';
                    }
                },
            ],
        ],
    ],
]);?>
</div>

