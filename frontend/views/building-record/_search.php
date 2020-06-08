<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\BuildingRecordSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="building-record-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'creator_id') ?>

    <?= $form->field($model, 'creator_name') ?>

    <?= $form->field($model, 'org_id') ?>

    <?= $form->field($model, 'building_name') ?>

    <?php // echo $form->field($model, 'build_type_id') ?>

    <?php // echo $form->field($model, 'building_status') ?>

    <?php // echo $form->field($model, 'province') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'area') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'floor') ?>

    <?php // echo $form->field($model, 'business_circle') ?>

    <?php // echo $form->field($model, 'build_longitude') ?>

    <?php // echo $form->field($model, 'build_latitude') ?>

    <?php // echo $form->field($model, 'contact_name') ?>

    <?php // echo $form->field($model, 'contact_tel') ?>

    <?php // echo $form->field($model, 'build_public_info') ?>

    <?php // echo $form->field($model, 'build_special_info') ?>

    <?php // echo $form->field($model, 'build_appear_pic') ?>

    <?php // echo $form->field($model, 'build_hall_pic') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
