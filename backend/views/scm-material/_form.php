<?php

use backend\models\ScmMaterial;
use backend\models\ScmMaterialType;
use backend\models\ScmSupplier;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\Scmmaterial */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="scm-material-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'supplier_id')->dropDownList(ScmSupplier::getSupplierArray(['type' => ScmSupplier::MATERIAL]))?>

    <?=$form->field($model, 'material_type')->dropDownList(ScmMaterialType::getIdNameArr(2))?>

    <?=$form->field($model, 'name')->textInput(['maxlength' => 30])?>

    <?=$form->field($model, 'weight')->textInput(['maxlength' => 5])?>

    <?=$form->field($model, 'is_operation')->dropDownList(ScmMaterial::getOperation())?>

    <div class="form-group">
        <?=Html::submitButton($model->isNewRecord ? '创建' : '更新', ['onclick' => 'return confirm("确认更新？")', 'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
