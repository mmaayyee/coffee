<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PointEvaluationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="point-evaluation-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'point_name') ?>

    <?= $form->field($model, 'org_id') ?>

    <?= $form->field($model, 'point_applicant') ?>

    <?= $form->field($model, 'point_position') ?>

    <?php // echo $form->field($model, 'point_level') ?>

    <?php // echo $form->field($model, 'cooperate') ?>

    <?php // echo $form->field($model, 'point_status') ?>

    <?php // echo $form->field($model, 'build_type_id') ?>

    <?php // echo $form->field($model, 'build_record_id') ?>

    <?php // echo $form->field($model, 'point_basic_info') ?>

    <?php // echo $form->field($model, 'point_score_info') ?>

    <?php // echo $form->field($model, 'point_other_info') ?>

    <?php // echo $form->field($model, 'point_licence_pic') ?>

    <?php // echo $form->field($model, 'point_position_pic') ?>

    <?php // echo $form->field($model, 'point_company_pic') ?>

    <?php // echo $form->field($model, 'point_plan') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
