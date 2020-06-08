<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Manager;
use backend\models\Organization;


/* @var $this yii\web\View */
/* @var $model backend\models\EquipmentTaskSettingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipment-task-setting-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="form-inline">
        <?php $delivery = new \backend\models\EquipDelivery(); ?>
        <?= $form->field($model, 'equipment_type_id')->dropDownList($delivery->getEquipTypeModelArray()) ?>

        <?php if (Manager::getManagerBranchID() == 1) { ?>
            <?= $form->field($model, 'organization_id')->dropDownList(Organization::getOrgIdNameArr(['>', 'org_id', 1])) ?><?php } ?>

        <div class="form-group">
            <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
