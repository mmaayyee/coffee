<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipTask */

$this->title                   = $model->id;
$this->params['breadcrumbs'][] = ['label' => '任务列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-task-view">

    <h1><?=Html::encode($this->title)?></h1>
    <?php if (!$model->recive_time && $model->process_result < 2) {?>
    <p>
        <?=Html::a('更新任务', ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])?>
    </p>
    <?php }?>
    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        [
            'attribute' => 'build_id',
            'value'     => \common\models\Building::getBuildingDetail('name', array('id' => $model->build_id))['name'],
        ],
        [
            'attribute' => 'content',
            'format'    => 'html',
            'value'     => \common\models\EquipTask::getMalfunctionContent($model->content, $model->task_type),
        ],
        [
            'attribute' => 'assign_userid',
            'value'     => $model->assignMemberName ? $model->assignMemberName->name : '',
        ],
        [
            'attribute' => 'task_type',
            'value'     => \common\models\EquipTask::$task_type[$model->task_type],
        ],
        'remark',
    ],
])?>

</div>
