<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Api;

/* @var $this yii\web\View */
/* @var $model app\models\ClearEquipSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="clear-equip-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="form-inline">

    <?= $form->field($model, 'equip_type_id')->dropDownList(Api::getEquipTypeList()) ?>

    <?= $form->field($model, 'code')->dropDownList(Api::getClearTypeList(1)) ?>

    <?= $form->field($model, 'remark') ?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
    </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
