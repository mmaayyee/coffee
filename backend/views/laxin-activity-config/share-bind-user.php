<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\LaxinActivityConfig */

$this->title = '分享者绑定用户列表';
$this->params['breadcrumbs'][] = ['label' => '分享者绑定用户列表', 'url' => ['share-bind-user']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="laxin-activity-config-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('编辑', ['update'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('查看', ['view'], ['class' => 'btn btn-primary']) ?>
    </p>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'share_userid',
            'laxin_userid',
            'beans_number',
            'coupon_group_id',
            'coupon_number',
            'reward_time',

        ],
    ]) ?>

</div>
