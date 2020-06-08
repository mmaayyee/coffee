<?php

use backend\models\GroupBeginTeam;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\GroupBeginTeamSearch */
/* @var $form yii\widgets\ActiveForm */

?>
<nav class="navbar navbar-default" role="navigation">
<div class="group-begin-team-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-group  form-inline">
    <?php echo $form->field($model, 'type')->dropDownList($model->dropDown('type')); ?>
    <?php echo $form->field($model, 'group_booking_status')->dropDownList($model->dropDown('group_booking_status')); ?>
        <label>活动名称</label>
        <div class="select2-search">
            <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'main_title',
    'data'          => GroupBeginTeam::getMainTitle(),
    'options'       => ['placeholder' => '请选择...'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
        </div>
    <br>
    <?=$form->field($model, 'begin_datatime')->widget(\janisto\timepicker\TimePicker::className(), [
//        'language' => 'fi',
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm',
    ],
]);?>
    <?=$form->field($model, 'end_datatime')->widget(\janisto\timepicker\TimePicker::className(), [
    //'language' => 'fi',
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm',
        'hour'       => 23,
        'minute'     => 59,
    ],
]);?>
    <br>
   <?=$form->field($model, 'nicknameHead')->textInput(['placeholder' => '请输入团长昵称'])->label('团长昵称')?>
    <?=$form->field($model, 'mobileHead')->textInput(['placeholder' => '请输入团长手机号'])->label('团长手机号')?>
    <br>
    <?=$form->field($model, 'nicknameMember')->textInput(['placeholder' => '请输入团员昵称'])->label('团员昵称')?>
    <?=$form->field($model, 'mobileMember')->textInput(['placeholder' => '请输入团员手机号'])->label('团员手机号')?>
    </div>

    <div class="form-group">
        <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
</nav>

