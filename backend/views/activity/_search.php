<?php

use yii\helpers\Html;
use backend\models\Activity;
use yii\widgets\ActiveForm;
use common\models\ActivityApi;

/* @var $this yii\web\View */
/* @var $model backend\models\ActivitySearch */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="activity-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="form-inline form-group">
        
    <?= $form->field($model, 'activity_name') ?>

    <?= $form->field($model, 'status')->dropDownList(Activity::statusListSearch()) ?>

    <?= $form->field($model, 'activity_type_id')->dropDownList(ActivityApi::getActivityTypeList(2, 1)) ?>
    
    <?=$form->field($model, 'createFrom')->widget(\janisto\timepicker\TimePicker::className(), [
    //'language' => 'fi',
    'mode'          => 'datetime',
    'clientOptions' => [
            'dateFormat' => 'yy-mm-dd',
            'timeFormat' => 'HH:mm',
        ],
    ]); ?>
    <?=$form->field($model, 'createTo')->widget(\janisto\timepicker\TimePicker::className(), [
    //'language' => 'fi',
    'mode'          => 'datetime',
    'clientOptions' => [
            'dateFormat' => 'yy-mm-dd',
            'timeFormat' => 'HH:mm',
            'hour'       => 23,
            'minute'     => 59,
        ],
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
    </div>

    </div>
    <?php ActiveForm::end(); ?>

</div>
