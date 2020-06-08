<?php

use backend\models\QuickSendCoupon;
use janisto\timepicker\TimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\QuickSendCouponSearch */
/* @var $form yii\widgets\ActiveForm */
$this->registerJs('
    $("#export").click(function(){
        $("#w0").attr("action","export").submit();
    });
     $("#searchBtn").click(function(){
        $("#w0").attr("action","index").submit();
    });
')
?>

<div class="quick-send-coupon-search form-inline">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get']);?>

    <?=$form->field($model, 'coupon_sort')->dropDownList(QuickSendCoupon::getCouponeFieldName(1, ''))?>

    <?=$form->field($model, 'send_phone')?>
    <?=$form->field($model, 'caller_number')?>
    <?=$form->field($model, 'consume_id')?>
    <?=$form->field($model, 'order_code')?>
    <?=$form->field($model, 'startTime')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ]])->label('开始时间');?>

    <?=$form->field($model, 'endTime')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'hour'       => 23,
        'minute'     => 59,
        'second'     => 59,
        'showSecond' => true,
    ]])->label('结束时间');?>
    <div class="form-group">
        <?=Html::submitButton('搜索', ['class' => 'btn btn-primary', 'id' => 'searchBtn'])?>
        <?=Html::button('导出', ['class' => 'btn btn-primary', 'id' => 'export'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
