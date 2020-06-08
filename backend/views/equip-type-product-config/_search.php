<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipTypeProductConfigSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-type-product-config-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'equip_type_id') ?>

    <?= $form->field($model, 'product_id') ?>

    <?= $form->field($model, 'cf_choose_sugar') ?>

    <?= $form->field($model, 'half_sugar') ?>

    <?php // echo $form->field($model, 'full_sugar') ?>

    <?php // echo $form->field($model, 'brew_up') ?>

    <?php // echo $form->field($model, 'brew_down') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
