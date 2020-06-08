<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\ProductOfflineRecord;

/* @var $this yii\web\View */
/* @var $model backend\models\ProductOfflineRecordSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-offline-record-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="form-inline">
    <?= $form->field($model, 'build_id') ?>

    <?= $form->field($model, 'equip_code') ?>

    <?= $form->field($model, 'operator') ?>
    
    <?php  echo $form->field($model, 'product_name') ?>

    <?php  echo $form->field($model, 'type')->dropDownList(ProductOfflineRecord::$shelvesType) ?>
    <?= $form->field($model, 'start_time')->widget(\yii\jui\DatePicker::classname(), [
            //'language' => 'ru',
            'dateFormat' => 'yyyy-MM-dd',
        ])->textInput(); ?>
        <?= $form->field($model, 'end_time')->widget(\yii\jui\DatePicker::classname(), [
            //'language' => 'ru',
            'dateFormat' => 'yyyy-MM-dd',
        ])->textInput(); ?>
    <div class="form-group">
        <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
    </div>
    
    </div>

    <?php ActiveForm::end(); ?>

</div>
