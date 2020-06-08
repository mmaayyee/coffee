<?php

use backend\models\Organization;
use common\models\WxMember;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

?>

<div class="equip-trafficking-suppliers-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'name')->textInput(['maxlength' => true])?>

    <?=$form->field($model, 'userid')->dropDownList(WxMember::getTraffickingSuppliers($model->userid))?>

    <?=$form->field($model, 'mobile')->textInput(['maxlength' => true])?>

    <?=$form->field($model, 'email')->textInput(['maxlength' => true])?>

    <?=$form->field($model, 'org_id')->checkboxList(Organization::getBranchArray(2))?>


    <div class="form-group">
        <?=Html::submitButton($model->isNewRecord ? '创建' : '确定', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
<?php
$wxMemberUrl  =   Url::to(["manager/get-name-mob"]);
$this->registerJs('
$("#equiptraffickingsuppliers-userid").change(function(){
    var manager_userid = $("#equiptraffickingsuppliers-userid").val();
    $.post(
    "' . $wxMemberUrl . '",
    {userid:manager_userid},
    function(data){
        $("#equiptraffickingsuppliers-mobile").val(data.mobile);
        $("#equiptraffickingsuppliers-email").val(data.email);
    },
    "json"
    );
})
');
?>