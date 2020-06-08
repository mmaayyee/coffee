<?php

use common\models\Building;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\BuildType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="build-type-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'id')->hiddenInput()->label(false)?>
    <?=$form->field($model, 'city_name')->dropdownList($cities)?>
    <?=$form->field($model, 'coupon_group_id')->dropdownList(Building::getCouponGroupList())->hint('“无”代表该城市不支持使用优惠券')?>

    <div class="form-group">
        <?=Html::submitButton('保存', ['class' => 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
