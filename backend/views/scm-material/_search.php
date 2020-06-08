<?php

use backend\models\ScmMaterialType;
use backend\models\ScmSupplier;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\ScmMaterial;
/* @var $this yii\web\View */
/* @var $model backend\models\ScmmaterialSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="scm-material-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>

    <div class="form-group form-inline">

    <?=$form->field($model, 'supplier_id')->dropDownList(ScmSupplier::getSupplierArray(['type' => ScmSupplier::MATERIAL]))?>

    <?=$form->field($model, 'name')?>

    <?=$form->field($model, 'material_type')->dropDownList(ScmMaterialType::getIdNameArr())?>

    <?=$form->field($model, 'is_operation')->dropDownList(ScmMaterial::getOperation())?>
    <div class="form-group">
        <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
    </div>

    </div>

    <?php ActiveForm::end();?>

</div>
