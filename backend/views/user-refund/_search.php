<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserRefundSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-refund-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-group  form-inline">
        <?=$form->field($model, 'fundMobile')->textInput(['size' => 15])?>
        <?=$form->field($model, 'order_id')?>

        <?=$form->field($model, 'createdFrom')->widget(\janisto\timepicker\TimePicker::className(), [
    //'language' => 'fi',
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ],
]);
?>
        <?=$form->field($model, 'createdTo')->widget(\janisto\timepicker\TimePicker::className(), [
    //'language' => 'fi',
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'hour'       => 23,
        'minute'     => 59,
        'second'     => 59,
        'showSecond' => true,
    ],
]);
?>
        <?=$form->field($model, 'refund_type')->dropDownList($model->getRefundTypeArray())?>
        <?=$form->field($model, 'refund_status')->dropDownList($model->getStatusArray())?>

        <div class="form-group">
            <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>
    <?php ActiveForm::end();?>

</div>
