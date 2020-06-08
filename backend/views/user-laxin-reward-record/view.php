<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\UserLaxinRewardRecord */

$this->title = $model->laxin_reward_record_id;
$this->params['breadcrumbs'][] = ['label' => 'User Laxin Reward Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-laxin-reward-record-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->laxin_reward_record_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->laxin_reward_record_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'laxin_reward_record_id',
            'share_userid',
            'laxin_userid',
            'beans_number',
            'coupon_group_id',
            'coupon_number',
            'reward_time',
        ],
    ]) ?>

</div>
