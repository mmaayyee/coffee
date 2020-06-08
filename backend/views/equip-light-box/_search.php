<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipLightBoxSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-light-box-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="form-inline">
    <?= $form->field($model, 'light_box_name')->widget('yii\jui\AutoComplete', ['options' => ['class' => 'form-control', 'placeholder' => '请输入灯箱名称'], 'clientOptions' => ['source' => \backend\models\EquipLightBox::getLightBoxNameArr()] ]) ?>

    <div class="form-group">
        <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
    </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
