<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserLaxinRewardRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '分享者绑定用户';
?>
<div class="user-laxin-reward-record-index">

    <h1><?=Html::encode($this->title)?></h1>
     <p>
        <?=!Yii::$app->user->can('查看拉新活动') ? '' : Html::a('查看拉新活动', ['/laxin-activity-config/view'], ['class' => 'btn btn-primary'])?>
    </p>
    <?php echo $this->render('_bind_search', ['model' => $searchModel]); ?>

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
            'label' => '绑定日期',
            'value' => function ($model) {
                return empty($model->reward_time) ? '' : date('Y-m-d H:i:s', $model->reward_time);
            },
        ],
        [
            'label' => '绑定者',
            'value' => function ($model) {
                return $model->laxin_userid;
            },
        ],
        [
            'label' => '是否注册',
            'value' => function ($model) {
//                return $model->is_register;
                return empty($model->is_register) ? '否' : '是';
            },
        ],
        [
            'label' => '注册日期',
            'value' => function ($model) {
                return empty($model->is_register) ? '' : date('Y-m-d H:i:s', $model->created_at);
            },
        ],
    ],
]);?>
</div>
