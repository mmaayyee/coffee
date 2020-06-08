<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ManagerLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="manager-log-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-group  form-inline">

    <?=$form->field($model, 'realname')?>

    <?=$form->field($model, 'module_name')?>

    <?=$form->field($model, 'createdFrom')->widget(\yii\jui\DatePicker::classname(), ['dateFormat' => 'yyyy-MM-dd'])->textInput(['class' => 'form-control'])?>
    <?=$form->field($model, 'createdTo')->widget(\yii\jui\DatePicker::classname(), ['dateFormat' => 'yyyy-MM-dd'])->textInput(['class' => 'form-control'])?>

    <div class="form-group">
        <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
    </div>
    </div>
    <?php ActiveForm::end();?>

</div>
