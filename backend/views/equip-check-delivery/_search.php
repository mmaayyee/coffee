<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipDeliverySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-delivery-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    
    <div class="form-group form-inline">
       
    </div>


    <div class="form-group">
        <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
