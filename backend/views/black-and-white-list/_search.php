<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserTagSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-tag-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>

    <div class="form-inline">
        <?=$form->field($model, 'username')?>

        <?=$form->field($model, 'buildname')?>

        <?=$form->field($model, 'market_type')->dropDownList($model->marketType)?>
        <div class="form-group">
            <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>

    <?php ActiveForm::end();?>

</div>
