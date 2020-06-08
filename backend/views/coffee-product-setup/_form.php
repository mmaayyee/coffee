<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CoffeeProductSetup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="coffee-product-setup-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'product_id')->textInput() ?>

    <?= $form->field($model, 'equip_type_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_number')->textInput() ?>

    <?= $form->field($model, 'water')->textInput() ?>

    <?= $form->field($model, 'delay')->textInput() ?>

    <?= $form->field($model, 'volume')->textInput() ?>

    <?= $form->field($model, 'stir')->textInput() ?>

    <?= $form->field($model, 'stock_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'blanking')->textInput() ?>

    <?= $form->field($model, 'mixing')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
