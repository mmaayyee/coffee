<?php

use frontend\assets\AppAsset;
use janisto\timepicker\TimePicker;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;
$this->registerCssFile("@web/css/add-condition.css?v=1.2", [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);
$this->registerJsFile("@web/js/jquery-1.9.1.min.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile('@web/js/bootstrap3-validation.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("@web/js/laypage.min.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("@web/js/laytpl.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile("@web/js/ajaxfileupload.js", ["depends" => [JqueryAsset::className()]]);
$this->registerJsFile('@web/js/sendCoupon.js?v=1.1', ['depends' => [JqueryAsset::className()]]);
?>
<script type="text/javascript">
    var couponGroups = <?php echo !$model->couponGroups ? Json::encode([]) : Json::encode($model->couponGroups); ?>;
    var editBuildingList = <?php echo !$model->buildingList ? Json::encode([]) : Json::encode($model->buildingList); ?>;
    var url = "<?php echo Yii::$app->params['fcoffeeUrl']; ?>";
    var verifyPassword = "<?php echo 'key=coffee08&secret=' . md5('50nGI1JW0OHfk8ahdaE8p5yQbm0U6Nwd'); ?>";
</script>
<div class="coupon-send-task-form">
    <?php $form = ActiveForm::begin();?>
    <div class="form-inline block-inline">
        <?=$form->field($model, 'task_name')->textInput(['maxlength' => 100])?>
        <?=$form->field($model, 'send_time')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'options'       => ['readonly' => 'readonly'],
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm',
        'showSecond' => true,
    ]]);?>

        <?=$form->field($model, 'task_type')->dropDownList($model->taskType, ['onChange' => 'changeSendType($(this))'])?>
    </div>
    <?=$form->field($model, 'export_reason')->textArea(['maxlength' => 100])?>
    <div class="coupon-expire">
        <?=$form->field($model, 'coupon_group_id')->dropDownList($model->groupList, ['onChange' => 'changeCouponGroup($(this))'])?>
        <div id="group-coupons"></div>
        <div class="form-inline">
        <?=$form->field($model, 'coupon_start_time')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'options'       => ['readonly' => 'readonly'],
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm',
        'showSecond' => true,
    ]])->label('优惠券有效期');?><?=$form->field($model, 'coupon_end_time')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'options'       => ['readonly' => 'readonly'],
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm',
        'showSecond' => true,
    ]])->label('&nbsp;至&nbsp;');?>
        </div>
    </div>
    <?=$form->field($model, 'import_user_type')->dropDownList($model->importUserType, ['onChange' => "changeImportUserType(this,0)"])?>
    <div class="block-file">
        <div class="form-group">
            <label>导入文件</label>
            <div class="hint-block">（<span id="autoreqmark">*</span>导入文件必须是TXT格式的，且每个楼宇或者手机号独占一行）</div>
            <?php if ($model->task_name): ?>
            <?=Html::fileInput('CouponSendTask[verifyFile]', '', ['id' => 'add-file', 'onclick' => 'uploadFileClick(this)', 'onChange' => "uploadFile(this,0)"])?>
            <?php else: ?>
            <?=Html::fileInput('CouponSendTask[verifyFile]', '', ['id' => 'add-file', 'onclick' => 'uploadFileClick(this)', 'onChange' => "uploadFile(this,0)", 'check-type' => 'required fileFormat', 'fileFormat-message' => '文件上传格式不正确'])?>
            <?php endif?>
            <input id="add-file-name" type="hidden" name="" value=""/>
        </div>
        <div class="form-group verify-result"></div>
    </div>
    <?=$form->field($model, 'sheild_user_type')->dropDownList($model->importUserType, ['disabled' => 'disabled'])?>
    <div class="block-file">
        <div class="form-group">
            <label>屏蔽文件</label>
            <div class="hint-block">（<span id="autoreqmark">*</span>导入文件必须是TXT格式的，且每个楼宇或者手机号独占一行）</div>
            <?=Html::fileInput('CouponSendTask[verifyFile]', '', ['id' => 'sheild-file', 'onclick' => 'uploadFileClick(this)', 'onChange' => "uploadFile(this,1)"])?>
            <input id="sheild-file-name" type="hidden" name="" value=""/>
        </div>
        <div class="form-group verify-result"></div>
    </div>
    <?=$form->field($model, 'send_type')->hiddenInput()->label(false)?>
    <?php if ($model->copy == 0): ?>
    <?=$form->field($model, 'id')->hiddenInput()->label(false)?>
    <?php endif?>
    <div class="form-group">
        <?=Html::submitButton('保存', ['class' => 'btn btn-success'])?>
    </div>
    <?php ActiveForm::end();?>

</div>

