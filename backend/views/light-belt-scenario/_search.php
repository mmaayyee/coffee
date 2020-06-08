<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\LightBeltScenario;


/* @var $this yii\web\View */
/* @var $model backend\models\LightBeltScenarioSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="light-belt-scenario-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-group form-inline">
    <?= $form->field($model, 'scenario_name') ?>

    <?= $form->field($model, 'equip_scenario_name')->dropDownList(LightBeltScenario::$equipScenarioNameArr) ?>

    <?= $form->field($model, 'strategy_name')->label("策略名称") ?>

    <?= $form->field($model, 'product_group_name')->label("饮品组名称") ?>
    
    <div class="form-group">
        <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
    </div>

    </div>
    <?php ActiveForm::end(); ?>

</div>
