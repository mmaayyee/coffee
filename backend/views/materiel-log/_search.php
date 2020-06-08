<?php

use backend\models\MaterielLog;
use janisto\timepicker\TimePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MaterielLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="materiel-log-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-group  form-inline">

    <?=$form->field($model, 'operaction_type')->dropDownList(MaterielLog::$operactionTypeList)?>

    <?=$form->field($model, 'activity_type')?>


    <?=$form->field($model, 'equipment_code')->textInput();?>

    <?=$form->field($model, 'build_name')->textInput();?>
    <?=$form->field($model, 'consume_id')->textInput();?>

    <?=$form->field($model, 'product_id')->widget(\kartik\select2\Select2::classname(), [
    'data'          => $productIDNameList,
    'options'       => ['placeholder' => '请产品名称', 'id' => 'org-name'],
    'pluginOptions' => ['allowClear' => true, 'width' => '200px']])->label('产品名称')?>

    <?=$form->field($model, 'startTime')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ]])?>


    <?=$form->field($model, 'endTime')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ]])?>

    <div class="form-group">
        <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
    </div>
    </div>
    <?php ActiveForm::end();?>

</div>
