<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model backend\models\SpeechControlSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="speech-control-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

   <div class="form-group form-inline">
   		<div class="form-group">
   			<?= $form->field($model, 'speech_control_title')->textInput(); ?>
   		</div>
        <div class="form-group">
            <label>状态</label>
            <div class="select2-search">
                <?php
                echo Select2::widget([
                    'model' => $model,
                    'attribute' => 'status',
                    'data' => [1=>'待审核',2=>'待上线',3=>'审核失败',4=>'上线',5=>'下线'],
                    'options' => ['multiple' => false, 'placeholder' => '请选择状态'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>
        </div>
        <div class="form-group  form-inline">
            <?= $form->field($model, 'start_time')->widget(\yii\jui\DatePicker::classname(), [
                'dateFormat' => 'yyyy-MM-dd',
            ])->textInput(); ?>
            <?= $form->field($model, 'end_time')->widget(\yii\jui\DatePicker::classname(), [
                'dateFormat' => 'yyyy-MM-dd'
            ])->textInput(); ?>
        <div class="form-group">
            <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
