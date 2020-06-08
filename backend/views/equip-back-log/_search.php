<?php

use janisto\timepicker\TimePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CoffeeLabelSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="coffee-label-search">

    <?php $form = ActiveForm::begin(['method' => 'get', 'action' => '/equip-back-log/index']);?>
    <div class="form-group  form-inline">
    <?=$form->field($model, 'org_name')->widget(AutoComplete::classname(), ['clientOptions' => ['source' => array_values($orgIdNameList)], 'options' => ['class' => 'form-control']])->label('联营方名称')?>
    <?=$form->field($model, 'equip_code')->label('设备编号')?>
     <?=$form->field($model, 'build_name')->widget(AutoComplete::classname(), ['clientOptions' => ['source' => array_values($buildIdNameList)], 'options' => ['class' => 'form-control']])->label('点位名称')?>
     <?=$form->field($model, 'operation_id')->widget(Select2::classname(), [
    'data'          => $model->operaLogIdNameList,
    'theme'         => 'bootstrap',
    'options'       => ['placeholder' => '请选择操作', 'id' => 'operation_id'],
    'pluginOptions' => ['allowClear' => true, 'width' => '200px']])->label('操作')?>
    <?=$form->field($model, 'is_consume_material')->dropDownList(['' => '请选择', '1' => '是', '0' => '否'])?>
    <?=$form->field($model, 'startTime')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ]])->label('开始时间')?>
        <?=$form->field($model, 'endTime')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'hour'       => 23,
        'minute'     => 59,
        'second'     => 59,
        'showSecond' => true,
    ]])->label('结束时间')?>
    <div class="form-group">
        <?=Html::submitButton('搜索', ['class' => 'btn btn-primary', 'name' => 'export', 'value' => 0])?>
        <?=!Yii::$app->user->can('导出工厂模式操作日志') ? '' : Html::submitButton('导出', ['class' => 'btn btn-primary', 'name' => 'export', 'value' => 1])?>
    </div>
    </div>
    <?php ActiveForm::end();?>

</div>
