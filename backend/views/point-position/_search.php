<?php

use backend\models\BuildType;
use janisto\timepicker\TimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="coupon-send-task-search">

    <?php $form = ActiveForm::begin(['method' => 'get', 'action' => '/point-position/index']);?>
    <div class="form-inline">
        <?=$form->field($model, 'point_name')?>
        <?=$form->field($model, 'point_status')->dropDownList(['' => '请选择'] + $model::$pointStatusList)?>
        <?=$form->field($model, 'point_type_id')->dropDownList(BuildType::getBuildType())?>
        <?=$form->field($model, 'cooperation_type')->dropDownList(['' => '请选择'] + $model::$cooperationTypeList)?>
        <?=$form->field($model, 'pay_cycle')->dropDownList(['' => '请选择'] + $model::$payCycleList)?>
        <?=$form->field($model, 'startTime')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ]])->label('创建时间');?> 至 <?=$form->field($model, 'endTime')->widget(TimePicker::className(), [
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
