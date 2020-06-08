<?php

use common\models\Building;
use common\models\WxMember;
use yii\helpers\Html;
use backend\models\Manager;
use yii\jui\AutoComplete;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipTaskSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<script  src='/js/jquery-1.8.3.min.js'></script>
<script>
    $(document).ready(function() {
        var selectedVal = $('#equiptasksearch-org_id :selected').val();
        selectedVal > 0 ? $('#org_type_id').show() : '';
        $('#equiptasksearch-org_id').on('change',function(e){
            if(this.value > 0){
                $('#org_type_id').show();
            }else{
                $('#org_type_id').hide();
            };
        })
    })
</script>
<div class="equip-task-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>

    <div class="form-group form-inline">
    <?php if (!$model->type) {
    ?>


    <?=$form->field($model, 'build_id')->widget(AutoComplete::classname(), ['clientOptions' => ['source' => Building::getDeliveryBuildNameList(['>', 'build_status', Building::PRE_DELIVERY])], 'options' => ['class' => 'form-control']])?>


        <?=$form->field($model, 'task_type')->dropDownList(\common\models\EquipTask::$task_type);?>

        <?php //if (Manager::getManagerBranchID() == 1) {?>
            <?=$form->field($model, 'org_id')->dropDownList(\backend\models\Organization::getBranchArray(0))->label('分公司')?>
        <?php //}?>

        <?=$form->field($model, 'org_type', ['options' => ['id' => 'org_type_id','class' => 'form-group','style'=>'display:none']])->dropDownList(\common\models\Equipments::$orgType)?>

        <?=$form->field($model, 'create_user')->textInput();?>

    <?php } else {
    ?>

        <?=$form->field($model, 'assign_userid')->dropDownList(WxMember::equipDistributionIdNameArr());?>

        <?=$form->field($model, 'start_time')->label('开始时间')->widget(\yii\jui\DatePicker::classname(), [
        'dateFormat' => 'yyyy-MM-dd',
    ])->textInput();?>

        <?=$form->field($model, 'end_time')->label('结束时间')->widget(\yii\jui\DatePicker::classname(), [
        'dateFormat' => 'yyyy-MM-dd',
    ])->textInput();?>

        <?=Html::hiddenInput('EquipTaskSearch[build_id]', $model->build_id);?>
        <?=Html::hiddenInput('EquipTaskSearch[equip_id]', $model->equip_id);?>
        <?=Html::hiddenInput('EquipTaskSearch[type]', $model->type);?>

    <?php }?>

    <div class="form-group">
        <?=Html::submitButton('搜索', ['class' => 'btn btn-primary']);?>
    </div>
    </div>
    <?php ActiveForm::end();?>

</div>
