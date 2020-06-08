<?php

use common\models\Api;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\LightBeltProductGroupSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="light-belt-product-group-search">

    <?php $form = ActiveForm::begin([
    'action' => ['light-belt-program/build-program-view'],
    'method' => 'get',
]);?>
    <div class="form-group form-inline">
        <?=$form->field($model, 'buildName')->label("楼宇名称")?>
        <?=$form->field($model, 'equipCode')->label("设备编号")?>
        <?=$form->field($model, 'equipType')->dropDownList(Api::getEquipTypeList())->label("设备类型")?>
        <?=$form->field($model, 'branch')->dropDownList(json_decode(Api::getOrgListByType(0), true))->label("分公司")?>
        <?=$form->field($model, 'agent')->dropDownList(json_decode(Api::getOrgListByType(1), true))->label("代理商")?>
        <?=$form->field($model, 'partner')->dropDownList(json_decode(Api::getOrgListByType(2), true))->label("合作商")?>
        <?=$form->field($model, 'program_name')->label("方案名称")?>
        <?=$form->field($model, 'scenario_name')->label("场景名称")?>
        <?=$form->field($model, 'strategy_name')->label("策略名称")?>
        <?=$form->field($model, 'product_group_name')->label("饮品组名称")?>
        <div class="form-group">
            <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>

    <?php ActiveForm::end();?>

</div>
