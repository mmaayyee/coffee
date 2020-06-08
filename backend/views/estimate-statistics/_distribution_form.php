<?php

use backend\models\ScmMaterial;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\EstimateStatistics */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="estimate-statistics-form">

    <?php $form = ActiveForm::begin();?>

    <?php
$materialInfo = Json::decode($model->material_info);
$scmMaterial  = ScmMaterial::getScmMaterial();
$showMaterial = '';
foreach ($materialInfo as $materialTypeId => $material) {
    $name            = $scmMaterial[$materialTypeId]['material_type_name'] ?? '未知';
    $specUnit        = $scmMaterial[$materialTypeId]['spec_unit'] ?? '未知';
    $weightAndNumber = explode('|', $material);
    $weight          = $weightAndNumber[0];
    $packets         = $weightAndNumber[1];
    $showMaterial    = '<label class="control-label">' . $name . '-' . $weight . $specUnit . '</label>';
    echo $showMaterial . '<br/><input type="text" class="form-control" size="100" readonly="readonly" name="material_info[' . $materialTypeId . ']" value="' . $packets . '"><br/>';
}
?>

    <div class="form-group">
        <?=Html::submitButton('配货', ['class' => 'btn btn-success'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
