<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\LightBeltProgramSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="light-belt-program-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="form-inline form-group">
        <?= $form->field($model, 'program_name')->label("方案名称") ?>
        
        <?= $form->field($model, 'scenario_name')->label("场景名称") ?>
        
        <?= $form->field($model, 'strategy_name')->label("策略名称") ?>
        
        <?= $form->field($model, 'product_group_name')->label("饮品组名称") ?>

        <?= $form->field($model, 'is_default')->checkbox(['1']) ?>
        
        <div class="form-group">
            <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    
</div>
