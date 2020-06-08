<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\WxMember;
/* @var $this yii\web\View */
/* @var $model backend\models\DistributionTaskSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="distribution-task-search">

    <?php $form = ActiveForm::begin([
        'action' => ['repair-task-record'],
        'method' => 'get',
    ]); ?>

    <div class="form-group form-inline">
        <?= $form->field($model, 'assign_userid')->dropDownList(WxMember::getDistributionUserArr(3)) ?>
        <?= $form->field($model, 'equip_id')->hiddenInput()->label(false) ?>
        <div class="form-group">
            <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
