<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipDebugSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-debug-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="form-inline">
    <?= $form->field($model, 'debug_item')->widget('\yii\jui\AutoComplete',['options' => ['class' => 'form-control', 'placeholder' => '请填写设备调试项'], 'clientOptions' => ['source' => \backend\models\EquipDebug::getEquipDebugNameArr()]]) ?>

    <?= $form->field($model, 'equip_type_id')->dropDownList(\backend\models\ScmEquipType::getEquipTypeIDNameArr()) ?>
    <div class="form-group">
        <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
    </div>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>
