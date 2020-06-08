<?php

use yii\helpers\Html;
use backend\models\Activity;
use yii\widgets\ActiveForm;
use common\models\ActivityApi;
use backend\models\ActivityCombinPackageAssoc;

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

    <?= $form->field($model, 'status')->dropDownList(ActivityCombinPackageAssoc::$statusList) ?>

    <?= $form->field($model, 'is_refund')->dropDownList(ActivityCombinPackageAssoc::$isRefund) ?>

    <?= $form->field($model, 'activity_type')->dropDownList(ActivityCombinPackageAssoc::$activityType, ['prompt' => '请选择'])->label('活动类型') ?>

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
