<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Manager;
use kartik\select2\Select2;
use backend\models\Organization;
use \backend\models\ScmMaterial;

$this->registerJsFile('@web/js/dayTask.js?v=1.9', ['depends' => [\yii\web\JqueryAsset::className()]]);

/* @var $this yii\web\View */
/* @var $model backend\models\DistributionTaskEquipSetting */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="distribution-task-equip-setting-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'build_id')->widget(Select2::className(), [
        'data' => \common\models\Building::getOperationBuildList(),
        'options' => ['placeholder' => '请选择楼宇'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]) ?>
    <?php if ($model->build_id == 0): ?>
        <?php $equipModel = new \backend\models\EquipDelivery(); ?>
        <?= $form->field($model, 'equip_type_id')->dropDownList($equipModel->getEquipTypeModelArray()) ?>
        <?php if (Manager::getManagerBranchID() == 1) { ?>
            <?= $form->field($model, 'org_id')->dropDownList(Organization::getOrgIdNameArr(['>', 'org_id', 1])) ?>
        <?php } ?>
    <?php endif; ?>
    <?php if ($flag === 'distribution') { ?>
        <?= $form->field($model, 'day_num')->textInput() ?>
    <?php } elseif ($flag === 'change') { ?>
        <?php $materialArr = ScmMaterial::getScmMaterialList(true);?>
        <?= $form->field($model, 'material_id')->dropDownList($materialArr)?>
        <?= $form->field($model, 'refuel_cycle')->textInput() ?>
    <?php } elseif ($flag === 'clear') { ?>
        <?= $form->field($model, 'cleaning_cycle')->textInput() ?>
    <?php } ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '添加' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
