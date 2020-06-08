<?php

use backend\models\CouponSendTask;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;
$this->registerJsFile("@web/js/jquery-1.9.1.min.js");
$this->registerJsFile('@web/js/bootstrap3-validation.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<?php if ($model->check_status == CouponSendTask::CHECK_NOT): ?>
 <?php $form = ActiveForm::begin(['action' => '/coupon-send-task/check-status', 'method' => 'post']);?>
    <?=$form->field($model, 'refuse_reason')->textArea(['maxlength' => 100])?>
    <?=$form->field($model, 'check_status')->dropDownList($model->checkStatus, ['onChange' => 'changeCouponsendtaskCheckStatus(this)'])?>
    <?=$form->field($model, 'id')->hiddenInput()->label(false)?>
    <div class="form-group">
        <?=Html::submitButton('保存', ['class' => 'btn btn-success'])?>
    </div>
    <?php ActiveForm::end();?>
<?php endif?>
<script type="text/javascript">
/**
 * 选择不同的审核状态判断批注是否必填
 * @author  wxz
 * @version 2017-09-01
 * @param   object   obj js对象
 */
function changeCouponsendtaskCheckStatus(obj){
    if ($(obj).val()== 2) {
        $("#couponsendtask-refuse_reason").attr("check-type", "required");
    } else {
        $("#couponsendtask-refuse_reason").removeAttr("check-type");
        $("#couponsendtask-refuse_reason").parent().removeClass("has-error");
    }
}
window.onload = function(){
    $("#w1").validation();
    $("#w1").on("beforeSubmit", function(){
        if ($("#w1").valid() == false) {
            $("#w1").find(".btn-success").removeAttr("disabled");
             return false;
        }
    });
}

</script>