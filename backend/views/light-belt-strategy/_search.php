<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\LightBeltStrategy;

/* @var $this yii\web\View */
/* @var $model backend\models\LightBeltStrategySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="light-belt-strategy-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="form-inline form-group">
    
    <?= $form->field($model, 'strategy_name') ?>
    
    <?= $form->field($model, 'light_belt_type')->dropDownList(LightBeltStrategy::$lightBeltTypeArr)->label("灯带控制类型") ?>

    <?= $form->field($model, 'light_status')->dropDownList(LightBeltStrategy::$lightStatusArr); ?>

    <div class="form-group">
        <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
    </div>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>
