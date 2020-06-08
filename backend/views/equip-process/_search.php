<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipProcessSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-process-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    
    <div class="form-inline form-group">

        <?= $form->field($model, 'process_name') ?>

        <?= $form->field($model, 'process_english_name') ?>

        <?= $form->field($model, 'process_color') ?>
        <div class="form-group">
            <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
        </div>
    
    </div>
    
    <?php ActiveForm::end(); ?>

</div>
