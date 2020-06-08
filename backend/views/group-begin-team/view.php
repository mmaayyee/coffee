<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\GroupBeginTeam;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model backend\models\GroupBeginTeam */

$this->title = $model->main_title;
$this->params['breadcrumbs'][] = ['label' => '客服搜索', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-begin-team-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label'=>'标题',
                'value'=>$model->main_title
            ],
            [
                'label'=>'活动副标题',
                'value'=>$model->subhead
            ],
            [
                'attribute' => 'group_booking_status',
                'label' => '状态',
                'value' => function($model) {
                    return GroupBeginTeam::dropDown("group_booking_status", $model->group_booking_status);
                },
                "filter" => GroupBeginTeam::dropDown("group_booking_status"),
            ],
            [
                'attribute' => 'group_booking_status',
                'label' => '类型',
                'value' => function($model) {
                    return GroupBeginTeam::dropDown("type", $model->type);
                },
                "filter" => GroupBeginTeam::dropDown("type"),
            ],
            [
                'label'=>'开始时间',
                'value'=>$model->begin_datatime
            ],
            [
                'label'=>'结束时间',
                'value'=>$model->end_datatime
            ],
            [
                'label'=>'拼团人数',
                'value'=>$model->group_booking_num
            ],
            [
                'label'=>'拼团价格',
                'value'=>$model->group_booking_price
            ],
            [
                'label'=>'饮品梯度',
                'value'=>$model->drink_ladder
            ],
        ],
    ]) ?>
    <table id="w0" class="table table-striped table-bordered detail-view">

        <tr>
            <td>昵称</td>
            <td>手机号</td>
            <td>参与时间</td>
        </tr>
        <?php
        if(isset($model->team)){
            foreach ($model->team as $key => $value) {
                $value['nickname'] = isset($value['nickname']) ? $value['nickname'] : '';
                $value['mobile']   = isset($value['mobile'])   ? $value['mobile'] : '';
                echo ' <tr>
                    <td>'.$value['nickname'].'</td>
                    <td>'.$value['mobile'].'</td>
                    <td>'.date('Y-m-d H:i:s',$value['member_time']).'</td>
                </tr>';
            }
        }

        ?>

    </table>
    <center>
        <a href="/group-begin-team/index">返回</a>
    </center>

</div>
