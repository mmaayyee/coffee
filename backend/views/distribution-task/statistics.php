<?php

use backend\models\DistributionTask;
use common\models\WxMember;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\DistributionTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '运维任务统计管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-task-statistics">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_statistics_search', ['model' => $searchModel]); ?>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'assign_userid',
            'value'     => function ($model) {
                $userList = DistributionTask::getUserNameList();
                if(!empty($userList[$model->assign_userid])){
                    return $userList[$model->assign_userid];
                }else{
                    return '暂无';
                }
            },
        ],
        [
            'attribute' => 'count',
            'value'     => function ($model) {
                $buildNumber = DistributionTask::getUserBuildNum();
                if (!empty($buildNumber[$model->assign_userid])) {
                    return $buildNumber[$model->assign_userid];
                } else {
                    return '暂无';
                }
            },
        ],
        [
            'attribute' => 'is_finish',
            'format'    => 'text',
            'value'     => function ($model) {
                return $model->is_finish;
            },
        ],
        [
            'attribute' => 'no_finish',
            'value'     => function ($model) {
                return $model->no_finish;
            },
        ],

        [
            'attribute' => 'date',
            'value'     => function ($model) {
                return $model->date;
            },
        ],
    ],
]);?>

</div>
