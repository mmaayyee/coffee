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
        <div class="form-group">
            <?=Html::a('统计', 'javascript:;', ['class' => 'btn btn-primary', 'id' => 'statistics-btn'])?>
            (统计筛选结果)
        </div>
    </div>
    <?php ActiveForm::end();?>
</div>
<div id="statistics-wrap"></div>
<?php ob_start();?>
$('#statistics-btn').click(function(){
	$.get('statistics', $(this).parents().find('form').serialize(),function(data){
		var table = ['<table class="table">',
						'<th>发送用户总数</th><th>使用用户总数</th><th>券发送总数</th><th>券使用总数</th>',
						'<tr>',
							'<td>', data.user_num, '</td>',
							'<td>', data.user_coupon_total_num, '</td>',
							'<td>', data.coupon_num, '</td>',
							'<td>', data.user_total_num, '</td>',
						'</tr>',
					 '</table>'];
		 $('#statistics-wrap').html(table.join(''));
	}, 'json');
});
<?php $this->registerJs(ob_get_clean(), $this::POS_END);?>