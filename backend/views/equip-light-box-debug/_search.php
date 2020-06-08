<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipLightBoxDebugSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-light-box-debug-search">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
    ]); ?>
    <div class="form-inline">
    <?= $form->field($model, 'debug_item')->widget('yii\jui\AutoComplete', ['options' => ['class' => 'form-control', 'placeholder' => '请填写调试项'], 'clientOptions' => ['source' => \backend\models\EquipLightBoxDebug::getLightBoxDebugNameArr($model->light_box_id)]]) ?>

    <div class="form-group">
        <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
    </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
