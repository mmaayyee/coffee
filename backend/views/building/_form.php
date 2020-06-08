<?php

use backend\models\BuildType;
use backend\models\LightBeltProgram;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\Building */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile('http://map.qq.com/api/js?v=2.exp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/jquery.cxselect.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/build.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

?>
<div class="building-form">

    <?php $form = ActiveForm::begin(['action' => [$submitAction]]);?>

    <?=$form->field($model, 'name')->textInput(['maxlength' => 30])?>

    <?=$form->field($model, 'build_type')->dropDownList(BuildType::getBuildType());?>

    <?=$form->field($model, 'org_id')->widget(Select2::classname(), [
    'data'          => $orgIdNameList,
    'theme'         => 'bootstrap',
    'options'       => ['placeholder' => '请选择所属机构'],
    'pluginOptions' => ['allowClear' => true]])?>

    <div id="city_china">
    <?=$form->field($model, 'province')->dropDownList([], ['class' => 'province form-control', 'data-value' => $model->province])?>

    <?=$form->field($model, 'city')->dropDownList([], ['class' => 'city form-control', 'data-value' => $model->city])?>

    <?=$form->field($model, 'area')->dropDownList([], ['class' => 'area form-control', 'data-value' => $model->area])?>
    </div>
    <?=$form->field($model, 'address')->textInput(['maxlength' => 100])?>

    <div id="allmap" style="width:100%;height:200px;"></div>

    <?=$form->field($model, 'longitude')->textInput(['id' => 'lng']);?>

    <?=$form->field($model, 'latitude')->textInput(['id' => 'lat']);?>

    <?=$form->field($model, 'contact_name')->textInput(['maxlength' => 10])?>

    <?=$form->field($model, 'contact_tel')->textInput(['maxlength' => 20])?>

    <?=$form->field($model, 'people_num')->textInput(['maxlength' => 6])?>

    <?=$form->field($model, 'bd_maintenance_user')->textInput(['maxlength' => 50, 'value' => $bdMaintenanceUser])?>

    <?=$form->field($model, 'first_free_strategy')->widget(Select2::classname(), [
    'data'          => $couponGroupList,
    'theme'         => 'bootstrap',
    'options'       => ['placeholder' => '请选择首杯免费策略'],
    'pluginOptions' => ['allowClear' => true]])?>

    <?=$form->field($model, 'strategy_change_date')->widget(\yii\jui\DatePicker::classname(), ['dateFormat' => 'yyyy-MM-dd'])->textInput();?>

    <?=$form->field($model, 'first_backup_strategy')->widget(Select2::classname(), [
    'data'          => $couponGroupList,
    'theme'         => 'bootstrap',
    'options'       => ['placeholder' => '请选择首杯备份策略'],
    'pluginOptions' => ['allowClear' => true]])?>

    <?=$form->field($model, 'is_share')->radioList($model->getShareArr(2))?>

    <?php isset($model->is_delivery) ? $model->is_delivery : $model->is_delivery = 2;?>
   <?=$form->field($model, 'is_delivery')->radioList(['1' => '是', '2' => '否'])?>
    <div class="form-group building-program_id">
        <label>请选择灯带方案</label>
        <?php
echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'program_id',
    'data'          => LightBeltProgram::getProgramNameList(),
    'options'       => [
        'placeholder' => '请选择灯带方案',
        // "multiple"  => true,
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]);
?>
        <div class="help-block"></div>
    </div>
    <?=$form->field($model, 'building_level')->dropDownList($buildLevelArr)?>
    <?=$form->field($model, 'sign_org_id')->widget(Select2::classname(), [
    'data'          => $orgIdNameList,
    'theme'         => 'bootstrap',
    'options'       => ['placeholder' => '请选择合同签约公司'],
    'pluginOptions' => ['allowClear' => true]])?>
    <?=$form->field($model, 'source_org_id')->widget(Select2::classname(), [
    'data'          => $orgIdNameList,
    'theme'         => 'bootstrap',
    'options'       => ['placeholder' => '请选择客户来源'],
    'pluginOptions' => ['allowClear' => true]])?>
    <?=$form->field($model, 'business_type')->dropDownList(['' => '请选择'] + $model::$businessTypeList)?>
    <div class="form-group">
        <?=Html::Button($model->isNewRecord ? '创建' : '确定', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>


    <?php ActiveForm::end();?>

</div>
<?php
$this->registerJs('
    $(".btn").click(function(){
      $("#w0").submit();
    })
')?>
