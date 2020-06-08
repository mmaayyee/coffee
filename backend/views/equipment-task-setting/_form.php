<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Manager;
use backend\models\Organization;
use backend\models\ScmMaterialType;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipmentTaskSetting */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile('@web/js/equipmentTaskSetting.js?v=6.5', ['depends' => [\yii\web\JqueryAsset::className()]]);

?>
<script type="text/javascript">
//物料列表
var materialList = <?php echo json_encode(ScmMaterialType::getMaterialTypeStock()); ?>;

var refuelCycle = <?php echo isset($model->refuel_cycle) ? $model->refuel_cycle : 0;?>;

</script>

<div class="equipment-task-setting-form">
    <?php $form = ActiveForm::begin(); ?>
    <?php $delivery = new \backend\models\EquipDelivery();?>
    <?= $form->field($model, 'equipment_type_id')->dropDownList($delivery->getEquipTypeModelArray()) ?>

    <?php if (Manager::getManagerBranchID() == 1) {?>
        <?=$form->field($model, 'organization_id')->dropDownList(Organization::getOrgIdNameArr(['>', 'org_id', 1]))?>
    <?php }?>

    <?= $form->field($model, 'cleaning_cycle')->textInput() ?>

    <?= $form->field($model, 'day_num')->textInput() ?>

    <?= $form->field($model, 'error_value')->textInput() ?>

    <div id="material-item">
        <div class="form-group">

            <?=$form->field($model, 'material_type')->dropDownList(ScmMaterialType::getMaterialTypeStock(), ['name' => 'EquipmentTaskSetting[material_type][]', 'id' => ''])?>

            <div class="form-inline">
                <?=$form->field($model, 'refuel_cycle_days')->textInput(['name' => 'EquipmentTaskSetting[refuel_cycle_days][]'])?>
            </div>

        </div>
    </div>

    <div class="form-group">
        <?= Html::button($model->isNewRecord ? '添加' : '编辑', ['class' => $model->isNewRecord ? 'btn btn-success submit' : 'btn btn-primary submit']);?>

        <input type="button" class="btn btn-primary addRefuelCycle" value='增加换料选项'/>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<!--提示框-->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h5 id="myModalLabel">提示框</h5>
            </div>
            <div class="modal-body">
                <h5 class="form-group title text-center"></h5>
            </div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
            </div>
        </div>
    </div>
</div>