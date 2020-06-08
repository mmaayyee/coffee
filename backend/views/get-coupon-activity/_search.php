<?php

use backend\models\Activity;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ActivitySearch */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="activity-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-inline form-group">

    <?=$form->field($model, 'activity_name')?>

    <?=$form->field($model, 'status')->dropDownList(Activity::activityStatusList())?>

    <?=$form->field($model, 'createFrom')->widget(\janisto\timepicker\TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm',
    ],
])->label('活动创建时间');?> __
    <?=$form->field($model, 'createTo')->widget(\janisto\timepicker\TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm',
        'hour'       => 23,
        'minute'     => 59,
    ],
])->label(false);?>
    <div class="form-group">
        <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
    </div>
    </div>
    <?php ActiveForm::end();?>

</div>
