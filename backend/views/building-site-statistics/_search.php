<?php

use common\helpers\Tools;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;
$this->registerJsFile("@web/js/build-site-statistics.js", ["depends" => [JqueryAsset::className()]]);

/* @var $this yii\web\View */
/* @var $model backend\models\SpeechControlSearch */
/* @var $form yii\widgets\ActiveForm */

$searchArray       = !empty($searchData) ? Json::decode($searchData) : [];
$operationModeList = !empty($searchArray['operationModeList']) ? $searchArray['operationModeList'] : [];
$equipmentTypeList = !empty($searchArray['equipmentTypeList']) ? Tools::map($searchArray['equipmentTypeList'], 'equip_type_id', 'equip_type_name', null, null) : [];
$organizationList  = !empty($searchArray['organizationList']) ? Tools::map($searchArray['organizationList'], 'mechanism_id', 'mechanism_name', null, null) : [];
$buildList         = !empty($searchArray['buildList']) ? Tools::map($searchArray['buildList'], 'id', 'name', null, null) : [];
/**
 * 搜索条件默认值
 */
$operationMode      = '';
$equipmentType      = '';
$organizationId     = '';
$buildingName       = '';
$operationBeginDate = '';
$operationEndDate   = '';
$beginDate          = '';
$endDate            = '';
if (!empty($params['BuildingSiteStatisticsSearch'])) {
    $param              = $params['BuildingSiteStatisticsSearch'];
    $operationMode      = $param['operationMode'];
    $equipmentType      = $param['equipmentType'];
    $organizationId     = $param['organizationId'];
    $buildingName       = $param['buildingName'];
    $operationBeginDate = $param['operationBeginDate'];
    $operationEndDate   = $param['operationEndDate'];
    $beginDate          = $param['beginDate'];
    $endDate            = $param['endDate'];
}
?>

<div class="building-site-statistics-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'id'     => 'buildSiteForm',
]);?>

   <div class="form-group form-inline">
        <div class="form-group">
            <label>运营模式</label>
            <div class="select2-search">
                <?php
echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'operationMode',
    'data'          => $operationModeList,
    'options'       => ['multiple' => false, 'placeholder' => '请选择运营模式', 'value' => $operationMode],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]);
?>
            </div>
        </div>
        <div class="form-group">
            <label>设备类型</label>
            <div class="select2-search">
                <?php
echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'equipmentType',
    'data'          => $equipmentTypeList,
    'options'       => ['multiple' => false, 'placeholder' => '请选择状态', 'value' => $equipmentType],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]);
?>
            </div>
        </div>
        <div class="form-group">
            <label>所属公司</label>
            <div class="select2-search">
                <?php
echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'organizationId',
    'data'          => $organizationList,
    'options'       => ['multiple' => false, 'placeholder' => '请选择状态', 'value' => $organizationId],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]);
?>
            </div>
        </div>
        <div class="form-group">
            <label>点位名称</label>
            <div class="select2-search">
                <?php
echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'buildingName',
    'data'          => $buildList,
    'options'       => ['multiple' => false, 'placeholder' => '请选择点位名称', 'value' => $buildingName],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]);
?>
            </div>
        </div>
        <div class="form-group">
             <?=$form->field($model, 'operationBeginDate')->widget(\yii\jui\DatePicker::classname(), [
    'dateFormat' => 'yyyy-MM-dd',
])->textInput(['value' => $operationBeginDate]);?>
    </div>
    <div class="form-group">
             <?=$form->field($model, 'operationEndDate')->widget(\yii\jui\DatePicker::classname(), [
    'dateFormat' => 'yyyy-MM-dd',
])->textInput(['value' => $operationEndDate]);?>
    </div>
    <div class="form-group">
            <?=$form->field($model, 'beginDate')->widget(\yii\jui\DatePicker::classname(), [
    'dateFormat' => 'yyyy-MM-dd',
])->textInput(['value' => $beginDate]);?>
</div>
<div class="form-group">
            <?=$form->field($model, 'endDate')->widget(\yii\jui\DatePicker::classname(), [
    'dateFormat' => 'yyyy-MM-dd',
])->textInput(['value' => $endDate]);?>
    </div>
        <div class="form-group">
        <?=Html::Button('检索', ['class' => 'btn btn-primary', 'id' => 'search'])?>
        <?=Html::Button('导出', ['class' => 'btn btn-primary', 'id' => 'export'])?>
    </div>

    <?php ActiveForm::end();?>

</div>

