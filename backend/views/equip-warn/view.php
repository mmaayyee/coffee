<?php

use backend\models\EquipWarn;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipWarn */

$this->title                   = $model->id;
$this->params['breadcrumbs'][] = ['label' => '异常报警列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-warn-view">

    <h1><?=Html::encode($this->title)?></h1>

    <p>
        <?=Yii::$app->user->can('编辑异常报警设置') ? Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) : ''?>
        <?=Yii::$app->user->can('删除异常报警设置') ? Html::a('删除', ['delete', 'id' => $model->id], [
    'class' => 'btn btn-danger',
    'data'  => [
        'confirm' => '确定要删除吗?',
        'method'  => 'post',
    ],
]) : ''?>
    </p>

    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        [
            'attribute' => 'warn_content',
            'value'     => isset(EquipWarn::$warnContent[$model->warn_content]) ? EquipWarn::$warnContent[$model->warn_content] : '',
        ],
        [
            'attribute' => 'userid',
            'value'     => EquipWarn::getPositionNameStr($model->userid),
        ],
        [
            'attribute' => 'notice_type',
            'value'     => EquipWarn::reportType($model->notice_type),
        ],
        [
            'attribute' => 'report_num',
            'value'     => $model->report_num . '级',
        ],
        [
            'attribute' => 'continuous_number',
            'value'     => $model->continuous_number . '次',
        ],
        [
            'attribute' => 'interval_time',
            'value'     => $model->interval_time . '个小时',
        ],
        [
            'attribute' => 'is_report',
            'value'     => $model->is_report == 1 ? '是' : '否',
        ],
        'report_setting',
        [
            'attribute' => 'create_time',
            'value'     => $model->create_time ? date('Y-m-d H:i:s', $model->create_time) : '',
        ],
    ],
])?>

</div>
