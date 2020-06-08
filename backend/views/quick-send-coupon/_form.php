<?php

use backend\models\QuickSendCoupon;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->registerJsFile("/js/laypage.min.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/laytpl.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile("/js/quick-send-coupon.js?v=1.8", ["depends" => [\yii\web\JqueryAsset::className()]]);

/* @var $this yii\web\View */
/* @var $model common\models\QuickSendCoupon */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quick-send-coupon-form">

    <?php $form = ActiveForm::begin();?>
	<div>
    <?=$form->field($model, 'phone')->textInput(['maxlength' => 11])?><?=Html::Button('添加', ['class' => 'btn btn-primary sendPhoneAdd'])?>
    <?php if ($model->isNewRecord) {?>
                <?=$form->field($model, 'send_phone_list')->dropDownList([], ['multiple' => 'true'])?>
            <?php } else {?>
                <?=$form->field($model, 'send_phone_list')->dropDownList($model->send_phone_list, ['multiple' => 'true'])?>
            <?php }?>
    <?=$form->field($model, 'send_phone')->hiddenInput()->label(false)?>
	</div>

    <?=$form->field($model, 'coupon_sort')->dropDownList(QuickSendCoupon::getCouponeFieldName(0))?>

	<div id="package">
		<?=$form->field($model, 'coupon_package_id')->widget(Select2::className(), [
    'data'          => $couponGroupList,
    'options'       => ['placeholder' => '请选择'],
    'pluginOptions' => [
        'allowClear' => true,
    ]])?>

	</div>

	<div id="singleProduct">
		<?=$form->field($model, 'coupon_type')->dropDownList(QuickSendCoupon::getNewCouponType(1))?>
		<?=$form->field($model, 'is_product')->dropDownList(QuickSendCoupon::getCouponType())?>
    	<?=$form->field($model, 'coupon_id')->widget(Select2::className(), [
    'data'          => $couponList,
    'options'       => ['placeholder' => '请选择'],
    'pluginOptions' => [
        'allowClear' => true,
    ]])?>
	</div>
  <?=$form->field($model, 'coupon_number')->textInput()?>
  <?=$form->field($model, 'consume_id')->textInput(['readOnly' => 'readOnly'])?>
  <?=$form->field($model, 'order_code')->textInput(['readOnly' => 'readOnly'])?>
  <?=$form->field($model, 'caller_number')->textInput(['maxlength' => 11])?>
  <?=$form->field($model, 'coupon_remarks')->textarea(['rows' => 3])?>
    <div class="form-group">
        <?=Html::submitButton('发送', ['class' => 'btn btn-success'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
<div class="modal fade error-message" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">提示</h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->