<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use common\models\Building;
use common\models\Equipments;
use backend\models\Organization;
use backend\models\Manager;
use backend\models\ScmMaterial;


/* @var $this yii\web\View */
/* @var $model backend\models\DistributionTaskEquipSettingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="distribution-task-equip-setting-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-group form-inline">
        <?= $form->field($model, 'build_id')->widget(AutoComplete::classname(), ['clientOptions' => ['source' => Building::getDeliveryBuildNameList(['build_status' => Building::SERVED])], 'options' => ['class' => 'form-control']]) ?>

        <?php $equipModel = new Equipments(); ?>
        <?= $form->field($model, 'equip_type_id')->dropDownList($equipModel->getEquipTypeArray()) ?>

        <?php $organization = Organization::getBranchArray(); ?>

        <?php if (Manager::getManagerBranchID() == 1) {
            $organization[1] = '全国';
        } ?>

        <?= $form->field($model, 'org_id')->dropDownList($organization) ?>
        <?= $form->field($model, 'material_id')->dropDownList(ScmMaterial::getScmMaterialList(true));?>
        <? /*= $form->field($model, 'cleaning_cycle') */ ?>

        <?php // echo $form->field($model, 'refuel_cycle') ?>

        <?php // echo $form->field($model, 'day_num') ?>

        <div class="form-group">
            <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
