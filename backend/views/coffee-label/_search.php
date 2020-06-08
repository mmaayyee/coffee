<?php

use backend\models\CoffeeLabel;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CoffeeLabelSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="coffee-label-search">

    <?php $form = ActiveForm::begin([
    'method' => 'get',
]);?>
    <div class="form-group  form-inline">
    <?=$form->field($model, 'label_name')?>

    <?=$form->field($model, 'online_status')->dropDownList(CoffeeLabel::getOnlineList())->label('状态')?>

    <?=$form->field($model, 'access_status')->dropDownList(CoffeeLabel::getAccessList())->label('分类')?>

    <?=$form->field($model, 'product_name')?>
    <?php // echo $form->field($model, 'online_status') ?>

    <?php // echo $form->field($model, 'access_status') ?>

    <?php // echo $form->field($model, 'sort') ?>

    <?php // echo $form->field($model, 'status') ?>

    <div class="form-group">
        <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
    </div>
    </div>
    <?php ActiveForm::end();?>

</div>
