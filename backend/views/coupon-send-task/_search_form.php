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
$this->registerJsFile('@web/js/sendCoupon.js?v=1.3', ['depends' => [JqueryAsset::className()]]);
?>
<script type="text/javascript">
    var couponGroups = <?php echo !$model->couponGroups ? Json::encode([]) : Json::encode($model->couponGroups); ?>;
    var consume = <?php echo !$model->where_string ? Json::encode([]) : Json::encode($model->where_string['consume']); ?>;
    var editBuildingList = <?php echo !$model->buildingList ? Json::encode([]) : $model->buildingList; ?>;
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
    <div class="form-inline block-inline">
        <?=$form->field($model, 'user_type')->dropDownList($model->userType, ['onChange' => 'changeUserType($(this))'])?>
        <?=$form->field($model, 'city')->dropDownList($model->cities)?>
        <?=$form->field($model, 'scenes')->dropDownList($model->scenesList)?>
    </div>
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
    ]])->label('优惠券有效期');?>
            <?=$form->field($model, 'coupon_end_time')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'options'       => ['readonly' => 'readonly'],
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm',
        'showSecond' => true,
        'hour'       => 23,
        'minute'     => 59,
        'second'     => 59,
    ]])->label('&nbsp;至&nbsp;');?>

        </div>
    </div>
    <?=$this->render('_time.php', ['model' => $model]);?>
    <div class="form-inline searchDate">
            <?=$form->field($model, 'searchStartTime')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'options'       => ['readonly' => 'readonly'],
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm',
        'showSecond' => true,
    ]])->label('起止时间');?>
            <?=$form->field($model, 'searchEndTime')->widget(TimePicker::className(), [
    'mode'          => 'datetime',
    'options'       => ['readonly' => 'readonly'],
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm',
        'showSecond' => true,
        'hour'       => 23,
        'minute'     => 59,
        'second'     => 59,
    ]])->label('&nbsp;至&nbsp;');?>
    </div>
    <?=$form->field($model, 'product')->checkBoxList($model->productList)?>
    <?=$form->field($model, 'search_build_type')->dropDownList($model->searchBuildType, ['onChange' => 'addBuildingType($(this))'])?>
    <div class="search-building">
        <?=$this->render('_building.php', ['model' => $model->where_string]);?>
    </div>
    <div class="block-file">
        <div class="form-group add-building-file">
            <label>上传楼宇</label>
            <div class="hint-block">（<span id="autoreqmark" style="color:#FF9966">*</span>导入文件必须是TXT格式的，且每个楼宇独占一行）</div>
            <?=Html::fileInput('CouponSendTask[verifyFile]', '', ['id' => 'add-file', 'onclick' => 'uploadFileClick(this)', 'onChange' => "uploadFile(this,1)", 'check-type' => 'fileFormat', 'fileFormat-message' => '文件上传格式不正确'])?>
            <input type="hidden" name="addBuildingFile" value=""/>
        </div>
        <div class="form-group verify-result"></div>
    </div>
    <div class="block-file">
        <span class="file-title">屏蔽文件</span>
            <div class="form-group">
                <label>上传手机号</label>
                <div class="hint-block">（<span id="autoreqmark">*</span>导入文件必须是TXT格式的，且手机号独占一行）</div>
                <?=Html::fileInput('CouponSendTask[verifyFile]', '', ['id' => 'sheild-file', 'onclick' => 'uploadFileClick(this)', 'onChange' => "uploadFile(this,0)", 'check-type' => 'fileFormat', 'fileFormat-message' => '文件上传格式不正确'])?>
                <input type="hidden" name="sheildMobileFile" value=""/>
            </div>
            <div class="form-group verify-result"></div>
    </div>

    <?=$form->field($model, 'send_type')->hiddenInput()->label(false)?>
    <?=$form->field($model, 'id')->hiddenInput()->label(false)?>
    <div class="form-group">
        <?=Html::submitButton('保存', ['class' => 'btn btn-success'])?>
    </div>
    <?php ActiveForm::end();?>
</div>