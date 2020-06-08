<?php

use janisto\timepicker\TimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\SpecialSchedulSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="special-schedul-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-inline">
    <?=$form->field($model, 'special_schedul_name')?>
    <?=$form->field($model, 'start_time')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm',
        'showSecond' => true,
    ]])->label('开始时间');?>

        <?=$form->field($model, 'end_time')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm',
        'showSecond' => true,
        'hour'       => 23,
        'minute'     => 59,
        'second'     => 59,
    ]])->label('结束时间');?>
    <?=$form->field($model, 'state')->dropDownList($model->stateList)?>
    <?=$form->field($model, 'is_coupons')->dropDownList($model->isCoupon)?>
    <?=$form->field($model, 'user_type')->dropDownList($model->userType)?>
    <?=$form->field($model, 'build_name')->textInput()?>

    <div class="form-group">
        <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
    </div>
    </div>
    <?php ActiveForm::end();?>

</div>
