<?php

use janisto\timepicker\TimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\CouponSendTask;

/* @var $this yii\web\View */
/* @var $model backend\models\CouponSendTaskSearch */
/* @var $form yii\widgets\ActiveForm */
$taskStatusList = CouponSendTask::$taskStatus;
$selectList[''] = '请选择';
$list = $selectList+ $taskStatusList;
?>

<div class="coupon-send-task-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-inline">
        <?=$form->field($model, 'task_name')?>
        <?=$form->field($model, 'check_status')->dropDownList($list)?>
        <?=$form->field($model, 'startTime')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ]])->label('发始时间');?> 至 <?=$form->field($model, 'endTime')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'hour'       => 23,
        'minute'     => 59,
        'second'     => 59,
        'showSecond' => true,
    ]])->label(false);?>
        <div class="form-group">
            <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>
    <?php ActiveForm::end();?>

</div>
