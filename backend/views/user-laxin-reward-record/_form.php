<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserLaxinRewardRecord */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-laxin-reward-record-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'share_userid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'laxin_userid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'beans_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'coupon_group_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'coupon_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reward_time')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
