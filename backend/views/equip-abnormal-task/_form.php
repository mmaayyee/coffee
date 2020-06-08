<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipAbnormalTask */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-abnormal-task-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'equip_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'build_id')->textInput() ?>

    <?= $form->field($model, 'org_id')->textInput() ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <?= $form->field($model, 'abnormal_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'task_status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
