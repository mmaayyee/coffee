<?php

use backend\models\Manager;
use backend\models\Organization;
use backend\models\ScmWarehouse;
use common\models\Building;
use common\models\EquipExtra;
use common\models\Equipments;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\EquipmentsSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<script  src='/js/jquery-1.8.3.min.js'></script>
<script>
    $(document).ready(function() {
        var selectedVal = $('#equipmentssearch-org_id :selected').val();
        selectedVal > 0 ? $('#org_type_id').show() : '';
        $('#equipmentssearch-org_id').on('change',function(){
            if(this.value > 0){
                $('#org_type_id').show();
            }else{
                $('#org_type_id').hide();
            };
        })
    })
</script>
<div class="equipments-search">
    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-group form-inline">
        <?=$form->field($model, 'equip_code')?>
        <?=$form->field($model, 'factory_code')?>
        <?=$form->field($model, 'card_number')?>
        <?=$form->field($model, 'factory_equip_model')?>

        <?=$form->field($model, 'build_id')->widget(AutoComplete::classname(), ['clientOptions' => ['source' => Building::getBusinessOperation(1)], 'options' => ['class' => 'form-control']])?>

        <?=$form->field($model, 'warehouse_id')->widget(Select2::classname(), ['data' => ScmWarehouse::getWarehouseIdNameArr(['use' => ScmWarehouse::EQUIP_USE]), 'options' => ['placeholder' => '请选择分库'], 'pluginOptions' => ['width' => 'auto', 'allowClear' => true]])?>

        <?=$form->field($model, 'pro_group_id')->widget(Select2::classname(), ['data' => $model->proGroupList(), 'options' => ['placeholder' => '请选择产品组'], 'pluginOptions' => ['width' => 'auto', 'allowClear' => true]])?>

        <?=$form->field($model, 'equip_type_id')->dropDownList($model->getEquipTypeArray())?>

        <?php $organization = Organization::getBranchArray();?>

        <?php if (Manager::getManagerBranchID() == 1) {$organization[1] = '全国';}?>

        <?=$form->field($model, 'org_id')->widget(Select2::classname(), [
    'data'          => $organization,
    'theme'         => 'bootstrap',
    'options'       => ['placeholder' => '请选择分公司'],
    'pluginOptions' => ['allowClear' => true, 'width' => '200px']])?>

        <?=$form->field($model, 'org_type', ['options' => ['id' => 'org_type_id', 'class' => 'form-group', 'style' => 'display:none']])->dropDownList(Equipments::$orgType)->label(false)?>

        <?=$form->field($model, 'equipment_status')->dropDownList($model->equipStatusArray())?>

        <?=$form->field($model, 'operation_status')->dropDownList($model->operationStatusArray())?>
        <?=$form->field($model, 'is_lock')->dropDownList(Equipments::$lock)?>

        <?=$form->field($model, 'equip_extra_id')->dropDownList(EquipExtra::getEquipExtraSelect())?>
        <?=$form->field($model, 'organization_type')->dropDownList(Organization::$organizationType)?>
        <div class="form-group">
            <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>
    <?php ActiveForm::end();?>

</div>
