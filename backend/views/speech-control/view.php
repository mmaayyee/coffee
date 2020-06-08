<?php

use backend\models\SpeechControl;
use yii\helpers\Html;
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $model backend\models\SpeechControl */

$this->title                   = '查看语音控制';
$this->params['breadcrumbs'][] = ['label' => '查看语音控制', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="speech-control-view">

    <h1><?=Html::encode($this->title)?></h1>

    <?=DetailView::widget([
    'model'      => json_decode($model, false),
    'attributes' => [
        [
            'attribute' => '标题',
            'value'     => function ($model) {
                return $model->speechControlInfo->speech_control_title;
            },

        ],
        [
            'attribute' => '上线时间',
            'value'     => function ($model) {
                return date('Y-m-d H:i:s', $model->speechControlInfo->start_time);
            },
        ],
        [
            'attribute' => '结束时间',
            'value'     => function ($model) {
                return date('Y-m-d H:i:s', $model->speechControlInfo->end_time);
            },

        ],
        [
            'attribute' => '状态',
            'value'     => function ($model) {
                $status = '';
                if ($model->speechControlInfo->status == SpeechControl::NO_CONFIRM) {
                    $status = '待审核';
                } elseif ($model->speechControlInfo->status == SpeechControl::NO_ONLINE) {
                    $status = '待上线';
                } elseif ($model->speechControlInfo->status == SpeechControl::IS_REFUSE) {
                    $status = '已拒绝';
                } elseif ($model->speechControlInfo->status == SpeechControl::IS_ONLINE) {
                    $status = '上线';
                } elseif ($model->speechControlInfo->status == SpeechControl::IS_DOWNLINE) {
                    $status = '下线';
                }
                return $status;
            },

        ],
        [
            'attribute' => '场景',
            'format'    => 'html',
            'value'     => function ($model) {
                $sceneName = '';
                foreach ($model->sceneList as $scene) {
                    $sceneName .= $scene->scene_name . '<br/>';
                }
                return $sceneName;
            },

        ],
        [
            'attribute' => '饮品列表',
            'format'    => 'html',
            'value'     => function ($model) {

                $productList = '<table class="table table-striped table-bordered detail-view"><tr><td>上线普通</td><td>下线普通</td><td>甄选单品</td></tr><tr>';
                foreach ($model->productList as $productArray) {
                    $productList .= '<td>';
                    foreach ($productArray as $product) {
                        $productList .= $product->product_name . '<br/>';
                    }
                    $productList .= '</td>';
                }
                $productList . '<tr/><table/>';
                return $productList;
            },

        ],
        [
            'attribute' => '语音内容',
            'value'     => function ($model) {
                return $model->speechControlInfo->speech_control_content;
            },

        ],

        [
            'attribute' => '楼宇列表',
            'format'    => 'html',
            'value'     => function ($model) {
                $buildName = '';
                foreach ($model->buildList as $build) {
                    $buildName .= $build->build_name . '<br/>';
                }
                return $buildName;
            },

        ],
        [
            'attribute' => '添加时间',
            'value'     => function ($model) {
                return date('Y-m-d H:i:s', $model->speechControlInfo->create_time);
            },

        ],
        [
            'attribute' => '审核时间',
            'value'     => function ($model) {
                return $model->speechControlInfo->examine_time > 0 ? date('Y-m-d H:i:s', $model->speechControlInfo->examine_time) : '暂无';
            },

        ],
    ],
])?>

</div>
