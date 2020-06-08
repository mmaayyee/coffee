<?php

use backend\models\Organization;
use common\models\Api;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\MaterielMonthSearch */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile('@web/js/jquery-1.9.1.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/bootstrap-datepicker/bootstrap-datepicker.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/bootstrap-datepicker/bootstrap-datepicker.zh-CN.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('@web/bootstrap-datepicker/bootstrap-datepicker3.min.css');
$this->registerJs('
    $("#materielmonthsearch-starttime").datepicker({
         format: "yyyy-mm",
         weekStart: 1,
         language: "zh-CN",
         autoclose:true,
         startView: 2,
         maxViewMode:2,
         minViewMode:1,
         forceParse: false
    })
')
?>

<div class="materiel-month-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>

    <div class="form-group  form-inline">
    <?=$form->field($model, 'build_name')?>
    <?=$form->field($model, 'orgId')->dropDownList(Organization::getOrgIdNameArr())?>
    <?=$form->field($model, 'build_type')->dropDownList(Api::getBuildTypeList())?>
    <?=$form->field($model, 'equip_type_id')->dropDownList(Api::getEquipTypeList())?>
    <?=$form->field($model, 'material_type_id')->dropDownList(Api::getMaterialTypeList())?>
    <?=$form->field($model, 'startTime')->textInput();?>

    <div class="form-group">
        <?=Html::submitButton('搜索', ['class' => 'btn btn-primary'])?>
    </div>
    </div>
    <?php ActiveForm::end();?>

</div>
