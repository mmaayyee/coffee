<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ScmUserSurplusMaterialSureRecordSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="scm-user-surplus-material-sure-record-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'author') ?>

    <?= $form->field($model, 'material_id') ?>

    <?= $form->field($model, 'add_reduce') ?>

    <?= $form->field($model, 'material_num') ?>

    <?php // echo $form->field($model, 'date') ?>

    <?php // echo $form->field($model, 'createTime') ?>

    <?php // echo $form->field($model, 'is_sure') ?>

    <?php // echo $form->field($model, 'sure_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
