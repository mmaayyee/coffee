<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\LaxinActivityConfig */

$this->title = '拉新活动设置';
?>
<div class="laxin-activity-config-view">

    <h1><?=Html::encode($this->title)?></h1>

    <p>
        <?=!Yii::$app->user->can('编辑拉新活动') ? '' : Html::a('编辑', ['update'], ['class' => 'btn btn-primary'])?>
    </p>

    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        'no_register_content',
        'activity_description',
        'new_user_content',
        'old_user_content',
        'rebate_node',
        'is_repeate',
        // 'new_coupon_groupid',
        'old_coupon_groupid',
        'share_coupon_groupid',
        // 'new_beans_number',
        'old_beans_number',
        'share_beans_number',
        'share_beans_percentage',
        'start_time',
        'end_time',
        'create_time',
        [
            "label"  => "背景图",
            "format" => [
                "image",
                [
                    "width" => "84",
                ],
            ],
            'value'  => $model->backgroud_img . '?v=' . time(),
        ],
        [
            "label"  => "遮罩图",
            "format" => [
                "image",
                [
                    "width" => "84",
                ],
            ],
            'value'  => $model->cover_img . '?v=' . time(),
        ],
    ],
])?>

</div>
