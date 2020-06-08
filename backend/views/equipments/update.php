<?php

use backend\models\Organization;
use backend\models\ScmWarehouse;
use common\models\Equipments;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */

$this->title                   = '编辑设备信息管理: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '设备信息管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '编辑';
$this->registerJs("
    $('#equipments-org_id').change(function(){
        var orgId = $(this).val();
        $.get('get-warehouse', {orgId:orgId}, function(data) {
            console.log(data);
            var html;
            $.each(data, function(index, e){
                html += '<option value=\"'+index+'\">'+e+'</option>';
            })
            $('#equipments-warehouse_id').html(html);
        }, 'json')
    })
")
?>
<div class="equipments-form">

    <?php $form = ActiveForm::begin();?>
    <?php if ($model->build_id || $model->is_unbinding > Equipments::NOBINDING) {?>
        <?php if ($model->factory_code && !$model->errors) {?>
    	   <?=$form->field($model, 'factory_code')->textInput(['disabled' => "disabled"]);?>
        <?php } else {?>
            <?=$form->field($model, 'factory_code')->textInput();?>
        <?php }?>
        <?php if ($model->factory_equip_model && !$model->errors) {?>
           <?=$form->field($model, 'factory_equip_model')->textInput(['disabled' => "disabled"]);?>
        <?php } else {?>
            <?=$form->field($model, 'factory_equip_model')->textInput();?>
        <?php }?>
	<?php } else {?>
		<?=$form->field($model, 'factory_code')->textInput(['maxLength' => '50']);?>
		<?=$form->field($model, 'factory_equip_model')->textInput(['maxLength' => '20']);?>
	<?php }?>
    <?php if (!empty($model->build_id)): ?>
	   <?=$form->field($model, 'card_number')->textInput(['maxLength' => '50']);?>
    <?php else: ?>
        <?=$form->field($model, 'equip_type_id')->dropDownList($model->getEquipTypeArray())?>
    <?php endif;?>

    <?=$form->field($model, 'miscellaneou_remark')->textarea(['maxlength' => 500, 'rows' => 6]);?>
    <?php if (Yii::$app->user->identity->branch == 1 && ($model->operation_status == $model::PRE_SELIVERY || $model->operation_status == $model::SCRAPPED)): ?>

    <?=$form->field($model, 'org_id')->widget(Select2::classname(), [
    'data'          => Organization::getOrgIdNameArr('', 2),
    'theme'         => 'bootstrap',
    'options'       => ['placeholder' => '请选择所属机构'],
    'pluginOptions' => ['allowClear' => true]])?>
    <?=$form->field($model, 'warehouse_id')->dropDownList(ScmWarehouse::getOrgWarehouse($model->org_id));?>
    <?php endif?>
    <?php if ($model->build_id): ?>
        <?php $productGroup = $model->proGroupList();?>
        <?php echo $form->field($model, 'pro_group_id')->dropDownList($productGroup); ?>
    <?php endif;?>
    <?=$form->field($model, 'bluetooth_name')?>
    <div class="form-group">
        <?=Html::submitButton('修改', ['class' => 'btn btn-primary']);?>
    </div>

    <?php ActiveForm::end();?>

</div>
