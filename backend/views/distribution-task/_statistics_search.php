<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Building;
/* @var $this yii\web\View */
/* @var $model backend\models\DistributionTaskSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="distribution-task-statistics-search">

    <?php $form = ActiveForm::begin([
    'action' => ['statistics'],
    'method' => 'get',
]);?>

    <div class="form-group form-inline">
        <div class="form-group">
            <label>运维人员</label>
            <div class="select2-search" >
            <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'assign_userid',
    'data'          => \backend\models\DistributionTask::getUserNameList(),
    'options'       => ['placeholder' => '请选择运维人员'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>
        </div>
        <div class="form-group">
            <label>日期</label>
            <div class="select2-search" >
            <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'date',
    'data'          => \backend\models\DistributionTask::getDateList(),
    'options'       => ['multiple' => false, 'placeholder' => '请选择日期'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>

        <div class="form-group">
            <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>
    <?php ActiveForm::end();?>

</div>
