<?php

use common\models\Building;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\AutoComplete;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\BuildingSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    .btn-primary {
        width: 100px;
    }
    .btn-success {
        margin-bottom: 0px;
    }
</style>
<div class="building-search">

    <?php $form = ActiveForm::begin(['action' => ['index'], 'method' => 'get', 'id' => 'buildingForm']);?>
    <div class="form-inline">
        <?=$form->field($model, 'name')->widget(AutoComplete::classname(), ['clientOptions' => ['source' => Building::getDeliveryBuildNameList()], 'options' => ['class' => 'form-control']])?>
        <?=$form->field($model, 'build_number')?>
        <?=$form->field($model, 'bd_maintenance_user')?>
        <?=$form->field($model, 'org_id')->widget(Select2::classname(), [
    'data'          => $orgIdNameList,
    'theme'         => 'bootstrap',
    'options'       => ['placeholder' => '请选择所属机构'],
    'pluginOptions' => ['allowClear' => true, 'width' => '200px']])?>
        <?=$form->field($model, 'build_status')->dropDownList(Building::$build_status)?>
        <?=$form->field($model, 'build_type')->dropDownList(\backend\models\BuildType::getBuildType());?>
        <?=$form->field($model, 'building_level')->dropDownList(Building::getBuildLevelArr())?>
        <?=$form->field($model, 'is_share')->dropDownList(Building::getShareArr())?>
        <?=$form->field($model, 'is_delivery')->dropDownList(Building::getShareArr())?>
        <?=$form->field($model, 'sign_org_id')->widget(Select2::classname(), [
    'data'          => $orgIdNameList,
    'theme'         => 'bootstrap',
    'options'       => ['placeholder' => '请选择合同签约公司'],
    'pluginOptions' => ['allowClear' => true, 'width' => '200px']])?>
        <?=$form->field($model, 'source_org_id')->widget(Select2::classname(), [
    'data'          => $orgIdNameList,
    'theme'         => 'bootstrap',
    'options'       => ['placeholder' => '请选择客户来源'],
    'pluginOptions' => ['allowClear' => true, 'width' => '200px']])?>
        <?=$form->field($model, 'business_type')->dropDownList(['' => '请选择'] + $model::$businessTypeList)?>
        <div class="form-group">
            <?=Html::Button(' 搜 索 ', ['class' => 'btn btn-primary', 'id' => 'search'])?>
            <?php if (Yii::$app->user->can('点位导出')): ?>
                <?=Html::Button('导出', ['class' => 'btn btn-success', 'id' => 'export'])?>
             <?php endif;?>
        </div>
        <?php ActiveForm::end();?>
    </div>
</div>

<?php ob_start();?>
 $(function(){
        $("#search").click(function(){
            $("#buildingForm").attr("action","<?php echo Url::to(['building/index']); ?>");
            $("#buildingForm").submit();
        });
        $("#export").click(function(){
            $("#buildingForm").attr("action","<?php echo Url::to(['building/export']); ?>");
            $("#buildingForm").submit();
        });
    });
<?php $this->registerJs(ob_get_clean());?>

