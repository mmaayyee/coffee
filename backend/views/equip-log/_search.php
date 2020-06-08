<?php

use backend\models\EquipLog;
use janisto\timepicker\TimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\EquipLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    #equiplogsearch-log_type, #equiplogsearch-equip_status {
        display: inline-block;
        vertical-align: middle;
    }
@media only screen and (min-width: 768px){
	label{
		font-weight:normal;
		margin-bottom: 0;
	}
	#w0 .field-equiplogsearch-log_type,.field-equiplogsearch-equip_status{
		padding-top:5px ;
		margin-right: 15px;
	}
	label.control-label{
		font-weight: bold;
	}
	.btn-primary{
		margin-left: 15px;
	}
}
</style>
<div class="equip-log-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-group form-inline">


        <?=$form->field($model, 'log_type')->radioList(EquipLog::$log_type)?>

        <?=$form->field($model, 'equip_status')->radioList(EquipLog::$equip_status)?>
        <?=$form->field($model, 'equip_code')->hiddenInput()->label(false)?>
        <?=$form->field($model, 'content')?>
        <?=$form->field($model, 'startTime')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm',
    ],
])->label('开始时间');?>
    <?=$form->field($model, 'endTime')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm',
        'hour'       => 23,
        'minute'     => 59,
    ],
])->label('结束时间');?>
        <div class="form-group">
            <?=Html::hiddenInput('equipId', Yii::$app->request->get('equipId'))?>
            <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>
    <?php ActiveForm::end();?>

</div>
