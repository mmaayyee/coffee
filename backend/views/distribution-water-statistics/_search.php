<?php

use backend\models\Manager;
use backend\models\Organization;
use common\models\Building;
use kartik\select2\Select2;
use yii\helpers\Html;
/* @var $this yii\web\View */
use yii\widgets\ActiveForm;
/* @var $model backend\models\DistributionWaterSearch */
/* @var $form yii\widgets\ActiveForm */
$organization = Organization::getBranchArray();
$org_id       = Manager::getManagerBranchID();

// 获取当前的用户角色
$userId = Yii::$app->user->identity->id;

$this->registerJsFile('@web/js/jquery-1.9.1.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/bootstrap-datepicker/bootstrap-datepicker.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/bootstrap-datepicker/bootstrap-datepicker.zh-CN.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('@web/bootstrap-datepicker/bootstrap-datepicker3.min.css');
$this->registerJs('
    $("#distributionwater-completion_date").datepicker({
        format: "yyyy-mm",
        language: "zh-CN",
        autoclose:true,
        minViewMode:1
    })
')
?>
<?php $this->registerJs('
    $(".btn-primary").click(function(){
        var data = $("#distributionwater-completion_date").val();
        if(!data){
            $("#error").show();
            return false;
        }
        $("#error").hide();
    });
');?>
<style type="text/css">
    #error{
        display: none;
    }
</style>
<div class="distribution-water-search">

    <?php $form = ActiveForm::begin([
    'action' => ['search'],
    'method' => 'get',
]);?>

    <div class="form-inline form-group">
        <div class="form-group">
            <label>请选择楼宇</label>
            <div class="select2-search">
                <?php $buildList = Building::getPreDeliveryBuildList(1, '', 1);unset($buildList['']);
echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'build_id',
    'data'          => $buildList,
    'options'       => [
        'placeholder' => '请选择楼宇',
        "multiple"    => true,
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]);?>
            </div>
        </div>

        <?=$form->field($model, 'completion_date')->textInput();?>
        <?php if ($org_id == 1) {?>
            <?=$form->field($model, 'orgId')->dropDownList($organization);?>
        <?php }?>
        <div class="form-group">
            <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        </div>
        <div class="form-group" id="error" style="color:red;">检索日期不可为空</div>
    </div>

    <?php ActiveForm::end();?>

</div>
