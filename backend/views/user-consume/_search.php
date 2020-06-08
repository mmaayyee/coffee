<?php
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
$this->registerJs('
    $("#search").click(function(){
        $("#orderForm").attr("action","' . Url::to(['user-consume/index']) . '");
        $("#orderForm").submit();
    });
    $("#export").click(function(){
        $("#orderForm").attr("action","' . Url::to(['user-consume/export']) . '");
        $("#orderForm").submit();
    });
    $(document).keypress(function (e) {
        if (e.keyCode == 13)
            $("#search").click();
    })
');
?>
<style>
    .btn-primary {
        width: 100px;
    }
    .btn-success {
        margin-bottom: 0px;
    }
</style>
<div>
<nav class="navbar navbar-default" role="navigation">
    <br/>
<div class="order-goods-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'id'     => 'orderForm']);?>
    <div class="form-group  form-inline">

        <div class="form-group">
            <label>所属分公司</label>
            <div class="select2-search">
            <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'orgId',
    'data'          => \backend\models\Organization::getOrganizationList(),
    'options'       => ['placeholder' => '分公司'],
    'pluginOptions' => ['allowClear' => true]]); ?>
            </div>
        </div>
        <?=$form->field($model, 'orgType')->dropDownList(\common\models\Equipments::$orgType)?>
        <?=$form->field($model, 'product_id')->dropDownList($model->getAllProductName())?>
        <?=$form->field($model, 'couponName')->dropDownList($model->getExchangeCouponIDName())?>
        <?=$form->field($model, 'isFee')->dropDownList($model->getFeetypeArray())?>
        <?=$form->field($model, 'is_refund')->dropDownList($model->isRefundList)?>
        <?=$form->field($model, 'order_id')->textInput(['size' => 8])?>
        <?=$form->field($model, 'user_consume_id')->textInput(['size' => 8])?>
         <?=$form->field($model, 'userMobile')->textInput(['size' => 15])?>
        <?=$form->field($model, 'building')->widget(\yii\jui\AutoComplete::classname(), ['clientOptions' => ['source' => $buildingArray]])->textInput();?>
        <?=$form->field($model, 'build_number')?>
        <?=$form->field($model, 'createdFrom')->widget(\janisto\timepicker\TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ]])?>
        <?=$form->field($model, 'createdTo')->widget(\janisto\timepicker\TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'hour'       => 23,
        'minute'     => 59,
        'second'     => 59,
        'showSecond' => true,
    ]])?>
    <?=$form->field($model, 'refundFrom')->widget(\janisto\timepicker\TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ]])?>
        <?=$form->field($model, 'refundTo')->widget(\janisto\timepicker\TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'hour'       => 23,
        'minute'     => 59,
        'second'     => 59,
        'showSecond' => true,
    ]])?>
        <div class="form-group">
            <?=Html::Button('检索', ['class' => 'btn btn-primary', 'id' => 'search'])?>
            <?php if (Yii::$app->user->can('消费记录列表导出')): ?>
            <?=Html::Button('导出', ['class' => 'btn btn-success', 'id' => 'export'])?>
              <?php endif;?>
        </div>

    </div>
    <?php ActiveForm::end();?>
</div>
</div>