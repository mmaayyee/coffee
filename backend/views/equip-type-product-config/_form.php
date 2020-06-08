<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipTypeProductConfig */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-type-product-config-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'equip_type_id')->textInput() ?>

    <?= $form->field($model, 'product_id')->textInput() ?>

    <?= $form->field($model, 'cf_choose_sugar')->textInput() ?>

    <?= $form->field($model, 'half_sugar')->textInput() ?>

    <?= $form->field($model, 'full_sugar')->textInput() ?>

    <?= $form->field($model, 'brew_up')->textInput() ?>

    <?= $form->field($model, 'brew_down')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
