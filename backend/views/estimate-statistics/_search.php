<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use backend\models\Organization;
/* @var $this yii\web\View */
/* @var $model backend\models\EstimateStatisticsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="estimate-statistics-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-group form-inline">
        <div class="form-group">
            <label>分公司</label>
            <div class="select2-search">
                <?php
                echo Select2::widget([
                    'model' => $model,
                    'attribute' => 'org_id',
                    'data' => Organization::getOrganizationList(),
                    'options' => ['multiple' => false, 'placeholder' => '请选择分公司'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);
                ?>
            </div>
        </div>
        <div class="form-group  form-inline">
            <?= $form->field($model, 'startTime')->widget(\yii\jui\DatePicker::classname(), [
                'dateFormat' => 'yyyy-MM-dd',
            ])->textInput(); ?>
            <?= $form->field($model, 'endTime')->widget(\yii\jui\DatePicker::classname(), [
                'dateFormat' => 'yyyy-MM-dd'
            ])->textInput(); ?>
            <div class="form-group">
                <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>

</div>
