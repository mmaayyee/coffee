<?php

use backend\models\Activity;
use common\models\ActivityApi;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\LotteryWinningRecordSearch */
/* @var $form yii\widgets\ActiveForm */
$activityNameList = ActivityApi::getActivityIdToName(2, 1);
?>

<div class="lottery-winning-record-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>


    <div class=" form-inline form-group">
    <?=$form->field($model, 'prizes_name')?>

    <?=$form->field($model, 'prizes_type')->dropDownList(Activity::prizesTypeList())?>

    <?php echo $form->field($model, 'user_id') ?>

    <?php echo $form->field($model, 'user_phone') ?>

    <?php echo $form->field($model, 'is_ship')->dropDownList(Activity::shipList()) ?>
    <div class="form-group">
        <label>抽奖活动名称</label>
        <div class="select2-search" >
        <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'activity_id',
    'data'          => $activityNameList ? $activityNameList : [],
    'options'       => ['multiple' => false, 'placeholder' => '请选择'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
        </div>
    </div>

    <?=$form->field($model, 'awards_name')?>

    <?php echo $form->field($model, 'is_winning')->dropDownList(['' => '请选择', '0' => '未中奖', '1' => '已中奖']) ?>

    <?php echo $form->field($model, 'activity_type_id')->dropDownList(ActivityApi::getActivityTypeList(2, 1)) ?>

    <?=$form->field($model, 'start_time')->widget(\janisto\timepicker\TimePicker::className(), [
    //'language' => 'fi',
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm',
    ],
]);?>
    <?=$form->field($model, 'end_time')->widget(\janisto\timepicker\TimePicker::className(), [
    //'language' => 'fi',
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm',
        'hour'       => 23,
        'minute'     => 59,
    ],
]);?>

    </div>

    <div class="form-group">
        <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
