<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\ScmEquipType;
use backend\models\Organization;
use backend\models\BuildingTaskSetting;
/* @var $this yii\web\View */
/* @var $model backend\models\EquipmentTaskSetting */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '公司设备类型日常任务管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipment-task-setting-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Yii::$app->user->can('编辑公司设备类型日常任务') ? Html::a('编辑', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) : ''; ?>
        <?= Yii::$app->user->can('删除公司设备类型日常任务') ? Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确定要删除该项吗?',
                'method' => 'post',
            ],
        ]) : ""; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'equipment_type_id',
                'value' => ScmEquipType::getModel($model->equipment_type_id)
            ],
            [
                'attribute' => 'organization_id',
                'value' => Organization::getOrgNameByID($model->organization_id)
            ],
            [
                'attribute' => 'cleaning_cycle',
                'value' => $model->cleaning_cycle.'天'
            ],
            [
                'attribute' => 'day_num',
                'value' => $model->day_num.'天'
            ],
            [
                'attribute' => 'refuel_cycle',
                'value' => BuildingTaskSetting::getRuelCycle($model->refuel_cycle)
            ],
            [
                'attribute' => 'error_value',
                'value' => $model->error_value.'g'
            ],
        ],
    ]) ?>

</div>
