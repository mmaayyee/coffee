<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ActivityCombinPackageDelivery */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="activity-combin-package-delivery-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'activity_id')->textInput() ?>

    <?= $form->field($model, 'order_id')->textInput() ?>

    <?= $form->field($model, 'address_id')->textInput() ?>

    <?= $form->field($model, 'distributio_type')->textInput() ?>

    <?= $form->field($model, 'distribution_user_id')->textInput() ?>

    <?= $form->field($model, 'distribution_user_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'courier_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_delivery')->textInput() ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
