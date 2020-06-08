<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\WxMember;
use common\models\Building;
use backend\models\DistributionTask;

/* @var $this yii\web\View */
/* @var $model backend\models\DistributionTask */
$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '配送任务记录管理', 'url' => ['distribution-task-record/index','DistributionTaskSearch[equip_id]'=>$model->equip_id]];
?>
<style>
.table{
    text-align: center;
}
</style>
<div class="distribution-task-view">
    <h1><?= Html::encode($this->title) ?></h1>
	<p><a href="javascript:history.go(-1)" class="btn btn-success">返回上一页</a></p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'build_id',
                'value' => !empty($model->build_id) ? Building::getBuildingDetail("name", ['id'=>$model->build_id])['name'] : '',
            ],
            [
                'attribute' => 'assign_userid',
                'value' => !empty($model->assign_userid) ? WxMember::getMemberDetail("name", array('userid'=>$model->assign_userid))['name'] : '',
            ],
            [
                'attribute' => 'task_type',
                'value' => !empty($model->task_type) ? DistributionTask::$taskType[$model->task_type] : '暂无',
            ],

            [
                'attribute' => 'create_time',
                'value' => !empty($model->create_time) ? date('Y-m-d H:i:s', $model->create_time) : '暂无',
            ],
            [
                'attribute' => 'start_delivery_time',
                'value' => !empty($model->start_delivery_time) ? date('Y-m-d H:i:s', $model->start_delivery_time) : '暂无',
            ],
            [
                'attribute' => 'end_delivery_time',
                'value' => !empty($model->end_delivery_time) ? date('Y-m-d H:i:s', $model->end_delivery_time) : '暂无',
            ],
            [
            'attribute' => 'remark',
            'value' => $model->remark,
            'visible'=> $model->task_type==DistributionTask::URGENT ? 0 : 1
            ],
            [
                'attribute' => 'is_sue',
                'value' => !empty($model->is_sue) ? "是" : '否',
            ]
        ],
    ]) ?>

</div>
