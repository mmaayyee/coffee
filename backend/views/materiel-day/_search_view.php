<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\MaterielDaySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="materiel-day-search">

    <?php $form = ActiveForm::begin([
    'action' => ['view'],
    'method' => 'get',
]);?>
    <div class="form-group  form-inline">

    <?=$form->field($model, 'build_name')?>
    <?=$form->field($model, 'create_at')->hiddenInput()->label(false)?>
    <?=$form->field($model, 'material_type_id')->hiddenInput()->label(false)?>
    <?=$form->field($model, 'payment_state')->hiddenInput()->label(false)?>

    <div class="form-group">
        <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
    </div>
    </div>
    <?php ActiveForm::end();?>

</div>
