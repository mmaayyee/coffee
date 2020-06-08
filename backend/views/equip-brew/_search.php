<?php

use common\models\Building;
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipBrewSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-brew-search">

    <?php $form = ActiveForm::begin(['action' => ['index'], 'method' => 'get']);?>
    <div class="form-inline">
    <?=$form->field($model, 'equip_code')?>
    <?=$form->field($model, 'build_name')->widget(AutoComplete::classname(), ['clientOptions' => ['source' => Building::getDeliveryBuildNameList(['build_status' => Building::SERVED])], 'options' => ['class' => 'form-control']])?>
    <div class="form-group">
        <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
    </div>
    </div>
    <?php ActiveForm::end();?>

</div>
