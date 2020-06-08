<?php

use backend\models\GroupBeginTeam;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\GroupBeginTeamSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '客服搜索';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-begin-team-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>


    <?=GridView::widget([
    'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
    'columns'      => [
        [
            'class'         => 'yii\grid\SerialColumn',
            'headerOptions' => ['width' => '40'],
        ], //不需要显示前面的导航
        [
            'attribute'     => 'main_title',
            'label'         => '团名称',
            'headerOptions' => ['width' => '200'],
        ],
        [
            'attribute'     => 'begin_datatime',
            'label'         => '开团时间',
            'headerOptions' => ['width' => '150'],
        ],
        [
            'attribute'     => 'end_datatime',
            'label'         => '结束时间',
            'headerOptions' => ['width' => '150'],
        ],
        [
            'attribute'     => 'group_booking_status',
            'label'         => '状态',
            'value'         => function ($model) {
                return GroupBeginTeam::dropDown("group_booking_status", $model->group_booking_status);
            },
            "filter"        => GroupBeginTeam::dropDown("group_booking_status"),
            'headerOptions' => ['width' => '100'],
        ],
        [
            'attribute'     => 'team',
            'label'         => '团队管理',
            'format'        => 'html',
            'value'         => function ($model) {
                if (empty($model->team)) {
                    return '暂无用户支付';
                }
                $show = '';
                foreach ($model->team as $team) {
                    $isboos   = $team['is_boos'] == 1 ? '团长' : '团员';
                    $nickname = !empty($team['nickname']) ? $team['nickname'] : '暂无';
                    $show .= $isboos . '昵称:' . $nickname . '&nbsp;' . $isboos . '手机号' . $team['member_mobile'] . '<br/>';
                }
                return $show;
            },
            'headerOptions' => ['width' => '200'],
        ],
        ['class'        => 'yii\grid\ActionColumn', 'header' => '操作', 'template' => '{view}',
            'buttons'       => [
                'view' => function ($url, $model) {
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', "view?id={$model->begin_team_id}", ['title' => '查看']);
                },
            ],
            'headerOptions' => ['width' => '20'],
        ],

    ],
    'emptyText'    => '没有筛选到任何内容哦',
]);?>
</div>
