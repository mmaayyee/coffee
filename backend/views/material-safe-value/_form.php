<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Building;
$this->registerJsFile('@web/js/laytpl.js?v=1.0', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/bootstrap3-validation.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/safeValue.js?v=3.9', ['depends' => [\yii\web\JqueryAsset::className()]]);

/* @var $this yii\web\View */
/* @var $model backend\models\MaterialSafeValue */
/* @var $form yii\widgets\ActiveForm */
?>
<style type="text/css">
   .form-inline .form-group:nth-child(2) #autoreqmark{
        display: none;
    }
    .field-materialsafevalue-safe_value{
        margin-bottom:10px;
    }
</style>
<div class="material-safe-value-form">

    <?php $form = ActiveForm::begin(); ?>
    <?php echo isset($model->getErrors('safe_value')[0]) ? '<div class="help-block" style="color:#a94442">'.$model->getErrors('safe_value')[0].'</div>' : '';?>
    <?php if ($model->build_id) { ?>
        <?= $form->field($model, 'build_id')->dropDownList(Building::getPreDeliveryBuildList(), ['data-url' => Yii::$app->request->baseUrl . '/material-safe-value/ajax-get-equipment', 'disabled' => 'disabled']) ?><?php } else { ?>
        <?= $form->field($model, 'build_id')->widget(\kartik\select2\Select2::classname(), [
            'data' => Building::getOrganizationBuildList(),
            'options' => ['placeholder' => '请选择楼宇', 'data-url' => Yii::$app->request->baseUrl . '/material-safe-value/ajax-get-equipment'],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]) ?><?php } ?>

    <?= Html::hiddenInput('equipment_id', $model->equipment_id, ['id' => 'equipment_id']) ?>

    <div id="material_id"></div>

    <div class="form-group">
        <?=Html::button($model->isNewRecord ? '添加' : '编辑', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','onclick' => 'checkSubmit()']);?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script id="material_tpl" type="text/html">
    {{# console.log(d);}}
    {{# $.each(d.material_stock,function(key,val){ }}
    <div class="field-materialsafevalue-safe_value">
        <label class="control-label" for="materialsafevalue-safe_value">{{ val }} ( {{ d.material_type[key] }} / {{ d.unit[key] }} )</label>
        <div class="form-inline">
            <div class="form-group">
                <label class="control-label">预警值</label>
                {{# if(d.safe_value != undefined){ }}
                <input type="number" class="form-control" name="MaterialSafeValue[safe_value][{{key}}]" value="{{ d.safe_value[key] }}" check-type="required number int" maxlength="4" range="1~1000" data-message="请输入正整数">
                {{# } }}
                {{# if(d.safe_value == undefined){ }}
                <input type="number" class="form-control" name="MaterialSafeValue[safe_value][{{key}}]"  check-type="required number int" maxlength="4" range="1~1000" data-message="请输入正整数">
                {{# } }}
            </div>
            <div class="form-group">
                <label class="control-label">下限值</label>
                {{# if(d.bottom_value != undefined){ }}
                <input type="number" class="form-control" name="MaterialSafeValue[bottom_value][{{key}}]" value="{{ d.bottom_value[key] }}" check-type="number int" maxlength="4" range="0~1000" data-message="请输入正整数">
                {{# } }}
                {{# if(d.bottom_value == undefined){ }}
                <input type="number" class="form-control" name="MaterialSafeValue[bottom_value][{{key}}]" check-type="number int" maxlength="4" range="0~1000" data-message="请输入正整数">
                {{# } }}
            </div>
        </div>
    </div>
    {{# }) }}
</script>