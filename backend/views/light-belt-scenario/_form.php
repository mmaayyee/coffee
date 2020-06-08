<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\LightBeltScenario;
use backend\models\LightBeltProductGroup;
use common\models\Api;
use kartik\select2\Select2;
use backend\models\LightBeltStrategy;
?>

<div class="light-belt-scenario-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'scenario_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'equip_scenario_name')->dropDownList(LightBeltScenario::$equipScenarioNameArr) ?>

    <div class="form-group field-lightbeltscenario-product_group_id">
        <label>饮品组名称</label>
        <?php 
            echo Select2::widget([
                'model' => $model,
                'attribute' => 'product_group_id',
                'data' => LightBeltProductGroup::getProGroupList(),
                'options' => [
                    'placeholder' => '饮品组名称',
                    // "multiple"  => true,
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]);
        ?>
        <div class="help-block"></div>
    </div>

    <div class="form-group">
        <label>策略名称</label>
        <?php 
            echo Select2::widget([
                'model' => $model,
                'attribute' => 'strategy_id',
                'data' => LightBeltStrategy::getStrategyNameList(),
                'options' => [
                    'placeholder' => '策略名称',
                    // "multiple"  => true,
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]);
        ?>
        <div class="help-block"></div>
    </div>


    
    <?= $form->field($model, 'start_time')->textInput(['maxlength' => 2, 'placeholder' => '0-24 小时']) ?>
    
    <?= $form->field($model, 'end_time')->textInput(['maxlength' => 2, 'placeholder' => '0-24 小时']) ?>
    
    <div class="form-group">
        <?= Html::button($model->isNewRecord ? '添加' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>

<?php
$isNewRecord = $model->isNewRecord;

$this->registerJs('
    var scenarioName = $("#lightbeltscenario-equip_scenario_name").val();
    if (scenarioName) {
        if(scenarioName == "startMake" || scenarioName == "makeOver")
        {
            $(".field-lightbeltscenario-product_group_id").show();
        }else{
            $(".field-lightbeltscenario-product_group_id").hide();
        }
    }else {
        $(".field-lightbeltscenario-product_group_id").hide();
    }
    
    $("#lightbeltscenario-equip_scenario_name").change(function(){
        var scenarioName = $("#lightbeltscenario-equip_scenario_name").val();
        if(scenarioName == "startMake" || scenarioName == "makeOver")
        {
            $(".field-lightbeltscenario-product_group_id").show();
        }else{
            $(".field-lightbeltscenario-product_group_id").hide();
        }
    })
    // 提交时，进行验证
    $(".btn").click(function(){
        var isHide = $(".field-lightbeltscenario-product_group_id").is(":hidden");
        var proGroupId = $("#lightbeltscenario-product_group_id").val();
        var strategyId = $("#lightbeltscenario-strategy_id").val();
        if(!isHide){
            if(!proGroupId){
                $(".field-lightbeltscenario-product_group_id").addClass("has-error");
                $(".field-lightbeltscenario-product_group_id").find(".help-block").html("饮品组不可为空");
                return false;
            }
        }else{
            $("#lightbeltscenario-product_group_id").val("");
        }
        
        $(".btn").submit();
    })

        
    
');

?>