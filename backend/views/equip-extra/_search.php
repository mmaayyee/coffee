<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\EquipExtra;
/* @var $this yii\web\View */
/* @var $model common\models\EquipExtraSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-extra-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-inline">

        <?= $form->field($model, 'extra_name') ?>

        <?= $form->field($model, 'is_del')->dropDownList(EquipExtra::$status) ?>

        <div class="form-group">
            <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        </div>

    </div>
    <?php ActiveForm::end(); ?>

</div>
