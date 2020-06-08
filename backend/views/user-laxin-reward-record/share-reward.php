<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserLaxinRewardRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '分享奖励列表';
?>
<div class="user-laxin-reward-record-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '分享者',
            'value' => function ($model) {
                return $model->share_userid;
            },
        ],
        [
            'label' => '奖励日期',
            'value' => function ($model) {
                return empty($model->reward_time) ? '' : date('Y-m-d H:i:s', $model->reward_time);
            },
        ],
        [
            'label' => '奖励',
            'value' => function ($model) {
                return $model->group_name;
            },
        ],
        [
            'label' => '咖豆数量',
            'value' => function ($model) {
                return $model->beans_number;
            },
        ],
        [
            'label' => '绑定者',
            'value' => function ($model) {
                return $model->laxin_userid;
            },
        ],
    ],
]);?>
</div>
