<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\LaxinActivityConfig */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="laxin-activity-config-form">

    <?php $form = ActiveForm::begin();?>


    <?=$form->field($model, 'no_register_content')->textarea(['maxlength' => 100])->hint('最大只能输入100个字符')?>

    <?=$form->field($model, 'activity_description')->textarea(['maxlength' => 100])->hint('最大只能输入100个字符')?>

    <?=$form->field($model, 'new_user_content')->textarea(['maxlength' => 100])->hint('最大只能输入100个字符')?>

    <?=$form->field($model, 'old_user_content')->textarea(['maxlength' => 100])->hint('最大只能输入100个字符')?>

    <?=$form->field($model, 'rebate_node')->dropDownList(['2' => '取杯'], ['onChange' => 'changeBox($(this))'])?>

    <?=$form->field($model, 'is_repeate')->dropDownList(['1' => '否', '2' => '是'])?>


    <?php //echo $form->field($model, 'new_coupon_groupid')->widget(Select2::className(), ['data' => $couponGroupIdNameList, 'options' => ['multiple' => false, 'placeholder' => '请选择套餐'], 'pluginOptions' => ['allowClear' => true]])?>

    <?=$form->field($model, 'old_coupon_groupid')->widget(Select2::className(), ['data' => $couponGroupIdNameList,
    'options'                                                                               => ['multiple' => false, 'placeholder' => '请选择套餐'],
    'pluginOptions'                                                                         => [
        'allowClear' => true,
    ]])?>

    <?=$form->field($model, 'share_coupon_groupid')->widget(Select2::className(), ['data' => $couponGroupIdNameList,
    'options'                                                                                 => ['multiple' => false, 'placeholder' => '请选择套餐'],
    'pluginOptions'                                                                           => [
        'allowClear' => true,
    ]])?>

    <?php //$form->field($model, 'new_beans_number')->textInput(['maxlength' => true])?>

    <?=$form->field($model, 'old_beans_number')->textInput(['maxlength' => true])?>

    <?=$form->field($model, 'share_beans_number')->textInput(['maxlength' => true])?>

    <?=$form->field($model, 'share_beans_percentage')->textInput(['maxlength' => true])?>

    <?=$form->field($model, 'start_time')->widget(\yii\jui\DatePicker::classname(), ['dateFormat' => 'yyyy-MM-dd'])->textInput(['readonly' => true]);?>

    <?=$form->field($model, 'end_time')->widget(\yii\jui\DatePicker::classname(), ['dateFormat' => 'yyyy-MM-dd'])->textInput(['readonly' => true]);?>

    <?=$form->field($model, 'create_time')->textInput(['maxlength' => true, 'readonly' => true])?>

    <div class="form-group">
        <?=$form->field($model, 'backgroud_img')->fileInput(['id' => 'up_image'])?>
        <div class="form-group" id="imagediv">
            <img id="imageShow" width="100" src="<?php echo $model->backgroud_img . '?v=' . time(); ?>"/>
        </div>
    </div>
    <div class="form-group">
        <?=$form->field($model, 'cover_img')->fileInput(['id' => 'up_img'])?>
        <div class="form-group" id="imgdiv">
            <img id="imgShow" width="100" src="<?php echo $model->cover_img . '?v=' . time(); ?>"/>
        </div>
    </div>

    <div class="form-group">
        <?=Html::submitButton('更新', ['class' => 'btn btn-success'])?>
    </div>

    <?php ActiveForm::end();?>

</div>

<script>
    window.onload=function () {
        $("#laxinactivityconfig-rebate_node").trigger("change");
    }
    function changeBox()
    {
        $(".field-laxinactivityconfig-is_repeate").hide();
        if ($("#laxinactivityconfig-rebate_node").val() == 2) {
            $(".field-laxinactivityconfig-is_repeate").show();
        }
    }
</script>
