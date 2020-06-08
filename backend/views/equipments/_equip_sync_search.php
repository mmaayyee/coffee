<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\EquipmentsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipments-search">

    <?php $form = ActiveForm::begin([
        'action' => ['equip-sync'],
        'method' => 'get',
    ]); ?>
    <div class="form-group form-inline">
        <div class="form-group">
        <label>添加时间</label>
        <?php 
        echo \yii\jui\DatePicker::widget([
            'dateFormat' => 'yyyy-MM-dd',
            'model' => $model,
            'attribute' => 'start_time', 
            'options' => ['placeholder' => '开始查询日期', 'class'=>'form-control'],
        ]).' 至 ';
        echo \yii\jui\DatePicker::widget([
            'dateFormat' => 'yyyy-MM-dd',
            'model' => $model,
            'attribute' => 'end_time', 
            'options' => ['placeholder' => '结束查询日期', 'class'=>'form-control'],
        ]);
        ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
