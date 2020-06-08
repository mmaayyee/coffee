<?php

use backend\models\Manager;
use backend\models\Organization;
use common\models\Building;
use common\models\Equipments;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\MaterialSafeValueSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<script  src='/js/jquery-1.8.3.min.js'></script>
<script>
    $(document).ready(function() {
        var selectedVal = $('#materialsafevaluesearch-org_id :selected').val();
        selectedVal > 0 ? $('#org_type_id').show() : '';
        $('#materialsafevaluesearch-org_id').on('change',function(){
            if(this.value > 0){
                $('#org_type_id').show();
            }else{
                $('#org_type_id').hide();
            };
        })
    })
</script>

<div class="material-safe-value-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-group form-inline">
        <?=$form->field($model, 'build_id')->widget(\kartik\select2\Select2::classname(), [
    'data'          => Building::getBusinessBuildByOrgId(),
    'theme'         => 'bootstrap',
    'options'       => [
        'placeholder' => '请选择楼宇',
        'data-url'    => Yii::$app->request->baseUrl . '/material-safe-value/ajax-get-equipment',
    ],
    'pluginOptions' => [
        'allowClear' => true,
        'width'      => '200px',
    ],
])?>
        <?=$form->field($model, 'equip_code')?>

        <?php $organization = Organization::getBranchArray(0);?>

        <?php if (Manager::getManagerBranchID() == 1) {$organization[1] = '全国';}?>

        <?=$form->field($model, 'org_id')->dropDownList($organization)?>

        <?=$form->field($model, 'org_type', ['options' => ['id' => 'org_type_id', 'class' => 'form-group', 'style' => 'display:none']])->dropDownList(Equipments::$orgType)?>

        <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
