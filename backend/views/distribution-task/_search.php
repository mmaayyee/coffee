<?php

use backend\models\DistributionTask;
use common\models\Building;
use common\models\WxMember;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\DistributionTaskSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="distribution-task-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>

    <div class="form-group form-inline">
        <div class="form-group">
            <label>请选择楼宇</label>
            <div class="select2-search" >
            <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'build_id',
    'data'          => Building::getDeliveryBuildList([Building::SERVED, Building::TRAFFICKING_IN]),
    'options'       => ['placeholder' => '请选择楼宇'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>
        </div>
        <div class="form-group">
            <label>请选择任务类别</label>
            <div class="select2-search" >
            <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'task_type',
    'data'          => DistributionTask::getTaskTypeList(),
    'options'       => ['multiple' => false, 'placeholder' => '请选择任务类别'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>
        </div>
        <div class="form-group">
            <label>请选择任务状态</label>
            <div class="select2-search" >
                <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'is_sue',
    'data'          => DistributionTask::getTaskStatus(),
    'options'       => ['multiple' => false, 'placeholder' => '请选择任务状态'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>
        </div>
        <div class="form-group">
            <label>请选择运维人员</label>
            <div class="select2-search" >
                <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'assign_userid',
    'data'          => WxMember::getDistributionUserArr(3),
    'options'       => ['multiple' => false, 'placeholder' => '请选择运维人员'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>
        </div>
        <div class="form-group">
            <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>
    <?php ActiveForm::end();?>

</div>
