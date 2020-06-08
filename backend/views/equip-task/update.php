<?php

use yii\helpers\Html;
use common\models\EquipTask;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipTask */

$this->title = '更新任务: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '任务列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '编辑';
if($model->task_type == EquipTask::EXTRA_TASK){
    $_form = 'extra_task_form';
}elseif($model->task_type == EquipTask::LIGHTBOX_ACCEPTANCE_TASK){
    $_form = 'light_box_acceptance_form';
}else{
    $_form = '_form';
}
?>
<div class="equip-task-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render($_form, [
        'model' => $model,
    ]) ?>

</div>
