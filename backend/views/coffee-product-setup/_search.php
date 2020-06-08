<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CoffeeProductSetupSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="coffee-product-setup-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'setup_id') ?>

    <?= $form->field($model, 'product_id') ?>

    <?= $form->field($model, 'equip_type_id') ?>

    <?= $form->field($model, 'order_number') ?>

    <?= $form->field($model, 'water') ?>

    <?php // echo $form->field($model, 'delay') ?>

    <?php // echo $form->field($model, 'volume') ?>

    <?php // echo $form->field($model, 'stir') ?>

    <?php // echo $form->field($model, 'stock_code') ?>

    <?php // echo $form->field($model, 'blanking') ?>

    <?php // echo $form->field($model, 'mixing') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
