<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use janisto\timepicker\TimePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\ServiceCountSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="service-count-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-inline container-fluid">
        <?=$form->field($model, 'begin_time')->widget(TimePicker::className(), [
        'mode'          => 'datetime',
        'clientOptions' => [
            'dateFormat' => 'yy-mm-dd',
            'timeFormat' => 'HH:mm:ss',
            'showSecond' => true,
        ],
    ])->label('开始日期')?>
    <?=$form->field($model, 'end_time')->widget(TimePicker::className(), [
        'mode'          => 'datetime',
        'clientOptions' => [
            'dateFormat' => 'yy-mm-dd',
            'timeFormat' => 'HH:mm:ss',
            'showSecond' => true,
        ],
    ])->label('截止日期')?>
        <?= $form->field($model,'category')->dropDownList($category)?>

        <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
    </div>

    </div>

    <?php ActiveForm::end(); ?>



</div>
