<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\OrderInfoSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div>
    <nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
        <ul class="nav navbar-nav navbar-left">
             <?php if (Yii::$app->user->can('订单支付信息汇总查看')): ?>
              <li><a href="/order-info/paymentinfo" id="view_paymentinfo"><span class="glyphicon glyphicon-list-alt"></span> 支付信息汇总查看</a></li>
            <?php endif;?>

        </ul>
    </div>

    <div class="order-info-search">

<?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-group  form-inline">

    <?=$form->field($model, 'user_id')->textInput(['size' => 10]);?>

    <?=$form->field($model, 'order_code')->textInput();?>

    <?=$form->field($model, 'user_mobile')->textInput(['size' => 12]);?>

    <?=$form->field($model, 'pay_type')->dropDownList($model->getPayTypeArray());?>

    <?=$form->field($model, 'order_status')->dropDownList($model->getStatusArray());?>

    <?=$form->field($model, 'order_type')->dropDownList($model->getOrderTypeArray());?>

    <?=$form->field($model, 'source_type')->dropDownList($model::$orderSourceType);?>

    <div class="form-group">
        <label >优惠券名称</label>
        <div class="select2-search">
            <?php echo \kartik\select2\Select2::widget([
    'model'         => $model,
    'attribute'     => 'coupon_name',
    'data'          => $couponIdNameList,
    'options'       => ['multiple' => false, 'placeholder' => '请选择优惠券'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]);
?>
    </div>
    <?=$form->field($model, 'payFrom')->widget(\janisto\timepicker\TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ],
]);
?>
<?=$form->field($model, 'payTo')->widget(\janisto\timepicker\TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'hour'       => 23,
        'minute'     => 59,
        'second'     => 59,
        'showSecond' => true,
    ],
]);
?>
    <?=$form->field($model, 'createdFrom')->widget(\janisto\timepicker\TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ],
]);
?>

    <?=$form->field($model, 'createdTo')->widget(\janisto\timepicker\TimePicker::className(), [
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'hour'       => 23,
        'minute'     => 59,
        'second'     => 59,
        'showSecond' => true,
    ],
]);
?>
    <div class="form-group">
        <?=Html::submitButton('检索', ['class' => 'btn btn-primary']);?>
    </div>
    <?php ActiveForm::end();?>
</div>
</div>
</div>

<script type="text/javascript">
   window.onload=function(){
         $("#view_orderinfo").on("click",function(){
                var checkList = $("input[name=\'selection[]\']:checked");
                if(checkList.length !== 1){
                    alert("请选中一项进行操作");
                    return false;
                }
                // window.location.replace("' . Url::toRoute('order-info/view') . '" + "?order_id=" + checkList.attr("value"));
                window.location.href="/order-info/view?id="+ checkList.attr("value");
        });
    }

</script>

