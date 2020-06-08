<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Organization;

/* @var $this yii\web\View */
/* @var $model app\models\GrindSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="grind-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index-building'],
        'method' => 'get',
    ]); ?>
    <div class="form-group form-inline">
        <?= $form->field($model, 'equipmentCode') ?>

        <?= $form->field($model, 'buildName') ?>

        <?= $form->field($model, 'org_id')->dropDownList(Organization::getOrgIdNameArr()) ?>

        <?= $form->field($model, 'grind_id')->hiddenInput()->label(false)?>

        <div class="form-group">
            <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        </div>
    
    </div>
    <?php ActiveForm::end(); ?>

</div>
