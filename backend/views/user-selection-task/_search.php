<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\datepicker\DatePicker;
use backend\models\UserSelectionTask;

/* @var $this yii\web\View */
/* @var $model backend\models\ExchangeCouponLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-selection-task-search">

    <?php $form = ActiveForm::begin([
        // 'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-group  form-inline">
    <?= $form->field($model, 'selection_task_name') ?>

    <?= $form->field($model, 'selection_task_status')->dropDownList(UserSelectionTask::getTaskStatusList())->label('执行状态')?>

    <?= $form->field($model, 'selection_task_result')->dropDownList(UserSelectionTask::getTaskResultList())->label('执行结果') ?>
    
    <?= $form->field($model, 'start_time')->widget(\janisto\timepicker\TimePicker::className(), [
          //'language' => 'fi',
          'mode' => 'datetime',
          'clientOptions' => [
              'dateFormat' => 'yy-mm-dd',
              'timeFormat' => 'HH:mm:ss',
              'showSecond' => true,
          ]
      ])->label('开始时间');
    ?>
    
    <?= $form->field($model, 'end_time')->widget(\janisto\timepicker\TimePicker::className(), [
          //'language' => 'fi',
          'mode' => 'datetime',
          'clientOptions' => [
              'dateFormat' => 'yy-mm-dd',
              'timeFormat' => 'HH:mm:ss',
              'hour'   => 23,
              'minute' => 59,
              'second' => 59,
              'showSecond' => true,
          ],
      ])->label('结束时间');
    ?>
    
    <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
    
    </div>
    <?php ActiveForm::end(); ?>
    
</div>
