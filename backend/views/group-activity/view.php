<?php

use backend\models\GroupActivity;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\GroupActivity */

$this->title                   = $model->main_title;
$this->params['breadcrumbs'][] = ['label' => '拼团活动列表展示', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-activity-view">

    <h1><?=Html::encode($this->title)?></h1>

    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        [
            'label' => 'ID',
            'value' => $model->group_id,
        ],
        [
            'label' => '标题',
            'value' => $model->main_title,
        ],
        [
            'label' => '副标题',
            'value' => $model->subhead,
        ],
        [
            'label' => '活动开始时间',
            'value' => $model->begin_time,
        ],
        [
            'label' => '活动结束时间',
            'value' => $model->end_time,
        ],
        [
            'label' => '状态',
            'value' => function ($model) {
                return GroupActivity::dropDown("status", $model->status);
            },
        ],
        [
            'label' => '活动类型',
            'value' => function ($model) {
                return GroupActivity::dropDown("type", $model->type);
            },
        ],
        [
            'label' => '开团时长',
            'value' => $model->duration,
        ],
        [
            'label' => '价格梯度',
            'value' => $model->price_ladder,
        ],
        [
            'label' => '最多成团数',
            'value' => $model->drink_num,
        ],
        [
            'label' => '饮品梯度',
            'value' => $model->drink_ladder,
        ],
        [
            "label"  => "商品图片",
            "format" => [
                "image",
                [
                    "width"  => "84",
                    "height" => "84",
                ],
            ],
            'value'  => $model->activity_img,
        ],
        [
            'label' => '活动排序',
            'value' => $model->group_sort,
        ],
        [
            'label' => '成团剩余数',
            'value' => $model->residue_num,
        ],
    ],
])?>
    <table id="w0" class="table table-striped table-bordered detail-view">

        <tr><th>详情图片</th><td>
                <?php
foreach ($model->activity_details_img as $key => $value) {
    echo '<img src="' . $value . '" width="84" height="84" alt="">  ';
}
?>
            </td></tr>
    </table>
    <center>
        <a href="index">返回</a>
    </center>
</div>
