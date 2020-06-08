<?php

use backend\models\Organization;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ScmSupplier */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="scm-supplier-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'realname')->textInput(['maxlength' => 30])?>

    <?=$form->field($model, 'name')->textInput(['maxlength' => 6])?>

    <?=$form->field($model, 'supplier_code')->textInput(['maxlength' => 2])?>

    <?=$form->field($model, 'type')->dropDownList($model->supplyTypeArray())?>

    <?=$form->field($model, 'org_id')->checkboxList(Organization::getBranchArray(2))?>

    <?=$form->field($model, 'username')->textInput(['maxlength' => 8])?>

    <?=$form->field($model, 'tel')->textInput(['maxlength' => 11])?>

    <?=$form->field($model, 'email')->textInput(['maxlength' => 30])?>

    <div class="form-group">
        <?=Html::submitButton('保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
<?php
$this->registerJs('
        $(function(){
            if($("#scmsupplier-type").val() == 2){
                $(".field-scmsupplier-org_id").show();
            }else{
                $(".field-scmsupplier-org_id").hide();
            }
        });
        $("#scmsupplier-type").change(function(){
            var scmsupplierType = $("#scmsupplier-type").val();
            if(scmsupplierType == 2){
                $(".field-scmsupplier-org_id").show();
            }else{
                $(".field-scmsupplier-org_id").hide();
            }
        });


    ');

?>


