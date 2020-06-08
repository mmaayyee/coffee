<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ScmSupplierSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="scm-supplier-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-inline form-group">

        <?= $form->field($model, 'name') ?>

        <?= $form->field($model, 'username') ?>

        <?= $form->field($model, 'tel') ?>

        <?= $form->field($model, 'type')->dropDownList($model->supplyTypeArray()) ?>


        <div class="form-group">
            <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
        </div>

    </div>

    <?php ActiveForm::end(); ?>

</div>
