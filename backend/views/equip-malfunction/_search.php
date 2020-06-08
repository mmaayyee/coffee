<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipMalfunctionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-malfunction-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-group form-inline">

    <?= $form->field($model, 'content') ?>

    <div class="form-group">
        <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
    </div>

    </div>

    <?php ActiveForm::end(); ?>

</div>
