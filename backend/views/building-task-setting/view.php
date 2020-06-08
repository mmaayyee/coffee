<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Building;
use backend\models\BuildingTaskSetting;
/* @var $this yii\web\View */
/* @var $model backend\models\BuildingTaskSetting */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '楼宇日常任务管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="building-task-setting-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Yii::$app->user->can('编辑楼宇日常任务') ? Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) : ""; ?>
        <?= Yii::$app->user->can('删除楼宇日常任务') ? Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确定删除该项吗?',
                'method' => 'post',
            ],
        ]) : "";?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'building_id',
                'value' => Building::getField('name',['id' => $model->building_id])
            ],
            'cleaning_cycle',
            [
                'attribute' => 'refuel_cycle',
                'value' => BuildingTaskSetting::getRuelCycle($model->refuel_cycle)
            ],
            'day_num',
            [
                'attribute' => 'error_value',
                'value' => $model->error_value.'g'
            ],
        ],
    ]) ?>

</div>
