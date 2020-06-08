<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DeliveryPersonSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="delivery-person-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-group  form-inline">
        <?= $form->field($model, 'person_name') ?>

        <?= $form->field($model, 'wx_number') ?>

        <?= $form->field($model, 'mobile') ?>

        <div class="form-group">
            <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
