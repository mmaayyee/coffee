<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\WxMember;
use backend\models\DistributionUser;

/* @var $this yii\web\View */
/* @var $model backend\models\DistributionUser */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile('@web/js/distribut_user.js',['depends' => [\yii\web\JqueryAsset::className()]]);

?>

<div class="distribution-user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_status')->dropDownList(DistributionUser::$user_status,['onChange' => 'changeBox($(this))']) ?>
    <?=$form->field($model, 'start_time')->widget(\yii\jui\DatePicker::classname(), [
        'dateFormat' => 'yyyy-MM-dd',
    ])->textInput(['readonly'=> true,'disabled'=>true])?>

    <?=$form->field($model, 'end_time')->widget(\yii\jui\DatePicker::classname(), [
        'dateFormat' => 'yyyy-MM-dd',
    ])->textInput(['readonly'=> true,'disabled'=>true])?>
    <?= $form->field($model, 'is_leader')->dropDownList(DistributionUser::$is_leader) ?>

    <?= $form->field($model, 'leader_id')->dropDownList(DistributionUser::orgLeaderArr()) ?>


    <div class="form-group">
        <?= Html::submitButton('确定', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    window.onload=function () {
        $("#distributionuser-user_status").trigger("change");
    }
    function changeBox()
    {
        $(".field-distributionuser-start_time").hide();
        $(".field-distributionuser-end_time").hide();
        if ($("#distributionuser-user_status").val() == 3) {
            $(".field-distributionuser-start_time").show();
            $("#distributionuser-start_time").attr('disabled',false);
            $(".field-distributionuser-end_time").show();
            $("#distributionuser-end_time").attr('disabled',false);
        }
    }
</script>