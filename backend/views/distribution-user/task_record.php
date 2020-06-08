<?php

use backend\models\DistributionTask;
use backend\models\EquipMalfunction;
use backend\models\ScmMaterial;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\LinkPager;

$this->title                   = '任务记录';
$this->params['breadcrumbs'][] = ['label' => '运维人员', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distribution-user-view">

    <h1><?=Html::encode($this->title)?></h1>
    <form action="/distribution-user/task-record" method="get">
    <div class="form-group form-inline">
        <div class="form-group">
            <label>运维日期</label>
            <?php
echo DatePicker::widget([
    'name'       => 'startDate',
    'value'      => $startDate,
    'options'    => ['placeholder' => '开始查询日期', 'class' => 'form-control'],
    'dateFormat' => 'yyyy-MM-dd',
]) . ' 至 ' . DatePicker::widget([
    'name'       => 'endDate',
    'value'      => $endDate,
    'options'    => ['placeholder' => '结束查询日期', 'class' => 'form-control'],
    'dateFormat' => 'yyyy-MM-dd',
]);
?>
        </div>
        <div class="form-group">
            <?=Html::hiddenInput('author', $author)?>
            <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>
    </form>
    <p>
        <?=Html::a('返回上一页', '/distribution-user/view?id=' . $author, ['class' => 'btn btn-primary'])?>
    </p>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>处理时间</th>
                <th>楼宇名称</th>
                <th>任务类型</th>
                <th>运维内容</th>
                <th>故障现象</th>
                <th>处理结果</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($taskRecord as $taskObj) {?>
            <tr>
                <td><?php echo $taskObj->end_delivery_time ? date('Y-m-d H:i:s', $taskObj->end_delivery_time) : ''; ?></td>
                <td><?php echo isset($taskObj->build->name) ? $taskObj->build->name : ''; ?></td>
                <td><?php echo $taskObj->task_type ? DistributionTask::getTaskType($taskObj->task_type) : ''; ?></td>
                <td><?php echo $taskObj->task_type == DistributionTask::URGENT ? $taskObj->content : ScmMaterial::getTaskMaterial($taskObj->delivery_task); ?></td>
                <td><?php echo $taskObj->malfunction_task ? EquipMalfunction::getMalfunctionReasonName($taskObj->malfunction_task) : ''; ?></td>
                <td><?php echo $taskObj->result == 2 ? '失败' : '成功'; ?></td>
            </tr>
            <?php }?>
        </tbody>
    </table>
    <?php echo LinkPager::widget(['pagination' => $pages]); ?>

</div>
