<?php

use backend\models\Manager;
use backend\models\Organization;
use common\models\WxMember;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$organization = Organization::getBranchArray();
unset($organization[1]);
$org_id = Manager::getManagerBranchID();
/* @var $this yii\web\View */
/* @var $model backend\models\DistributionWaterSearch */
/* @var $form yii\widgets\ActiveForm */

$this->registerJsFile('@web/js/jquery-1.9.1.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/bootstrap-datepicker/bootstrap-datepicker.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/bootstrap-datepicker/bootstrap-datepicker.zh-CN.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('@web/bootstrap-datepicker/bootstrap-datepicker3.min.css');
$this->registerJs('
    $("#distributionfiller-start_time").datepicker({
        format: "yyyy-mm-dd",
        language: "zh-CN",
        autoclose:true,
    }).on("changeDate",function(){
        var startDate = $(this).val();
        var endDate = $("#distributionfiller-end_time").val();
        if (endDate && endDate < startDate) {
            $("#distributionfiller-end_time").val("");
        }
        $("#distributionfiller-end_time").datepicker("setStartDate", $(this).val());

    })
    $("#distributionfiller-end_time").datepicker({
        format: "yyyy-mm-dd",
        language: "zh-CN",
        autoclose:true,
    })
')
?>

<div class="distribution-task-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>

    <div class="form-inline form-group">
        <?=$org_id == 1 ? $form->field($model, 'orgId')->dropDownList($organization) : '';?>
        <div class="form-group">
            <label>请选择配送员</label>
            <div class="select2-search" >
            <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'add_material_author',
    'data'          => WxMember::getDistributionUserArr(3, $model->orgId),
    'options'       => ['placeholder' => '请选择配送员'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>
        </div>

        <?=$form->field($model, 'start_time')->textInput();?>

        <?=$form->field($model, 'end_time')->textInput();?>



        <div class="form-group">
            <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
        </div>
    </div>

    <?php ActiveForm::end();?>

</div>
