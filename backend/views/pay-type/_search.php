<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PayTypeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pay-type-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>

    <?=$form->field($model, 'pay_type_name')?>

    <?=$form->field($model, 'is_open')->dropDownList($model->isOpenList)?>

    <?php echo $form->field($model, 'is_support_discount')->dropDownList($model->isDiscountList) ?>

    <div class="form-group">
        <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        <?=Html::resetButton('重置', ['class' => 'btn btn-default'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
