<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
$this->registerJsFile('/assets/f0269b11/jquery.min.js', ['position' => View::POS_HEAD]);
/* @var $this yii\web\View */
/* @var $model app\models\OrderGoodsSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    .btn-primary {
        width: 100px;
    }
    .btn-success {
        margin-bottom: 0px;
    }
</style>
<div class="order-goods-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'id'     => 'orderForm',
]);?>
    <div class="form-group  form-inline">

    <?=$form->field($model, 'order_id')->textInput(['size' => 8])?>

    <!--<?=$form->field($model, 'userName')->textInput(['size' => 10])?>-->
    <?=$form->field($model, 'userMobile')->textInput(['size' => 15])?>

    <?=$form->field($model, 'source_type')->dropDownList($model->getSourceTypeArray(), ['id' => 'sourceType'])?>

    <?=$form->field($model, 'source_id')->dropDownList(array(), ['id' => 'sourceID'])?>
    <?php echo $form->field($model, 'source_status')->dropDownList($model->getStatusArray()) ?>

    <?=$form->field($model, 'createdFrom')->widget(\janisto\timepicker\TimePicker::className(), [
    //'language' => 'fi',
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ],
]);
?>
    <?=$form->field($model, 'createdTo')->widget(\janisto\timepicker\TimePicker::className(), [
    //'language' => 'fi',
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
        <?=Html::Button('检索', ['class' => 'btn btn-primary', 'id' => 'search'])?>
         <?php if (Yii::$app->user->can('订单商品导出')): ?>
        <?=Html::Button('导出', ['class' => 'btn btn-success', 'id' => 'export'])?>
        <?php endif;?>
    </div>
</div>
    <?php ActiveForm::end();?>

</div>
<div style="display:none" id="productContent">
    <?php foreach ($productList as $key => $name): ?>
    <option value="<?=$key?>" <?=$sourceType == 0 && $key == $sourceID ? "selected" : '';?>><?=$name?></option>
    <?php endforeach;?>
</div>
<div style="display:none" id="groupContent">
    <?php foreach ($groupList as $key => $name): ?>
    <option value="<?=$key?>" <?=$sourceType == 1 && $key == $sourceID ? "selected" : '';?>><?=$name?></option>
    <?php endforeach;?>
</div>
<div style="display:none" id="productActiveContent">
    <?php foreach ($productActiveList as $key => $name): ?>
    <option value="<?=$key?>" <?=$sourceType == 3 && $key == $sourceID ? "selected" : '';?>><?=$name?></option>
    <?php endforeach;?>
</div>
<div style="display:none" id="groupActiveContent">
    <?php foreach ($groupActiveList as $key => $name): ?>
    <option value="<?=$key?>" <?=$sourceType == 4 && $key == $sourceID ? "selected" : '';?>><?=$name?></option>
    <?php endforeach;?>
</div>
<script>
    function setSource(){
        var source  = $("#sourceType").val();

        if(source == 0 ){
            $("#sourceID").html($('#productContent').html());
        }else if(source == 1 ){
            $("#sourceID").html($('#groupContent').html());
        }else if(source == 3){
            $("#sourceID").html($('#productActiveContent').html());
        }else if(source == 4){
            $("#sourceID").html($('#groupActiveContent').html());
        }else{
            $("#sourceID").html('<option value="">请选择</option>');
        }
    }
    $(function(){
        $("#search").click(function(){
            $("#orderForm").attr("action","<?php echo Url::to(['order-goods/index']); ?>");
            $("#orderForm").submit();
        });
        $("#export").click(function(){
            $("#orderForm").attr("action","<?php echo Url::to(['order-goods/export']); ?>");
            $("#orderForm").submit();
        });
        $("#sourceType").change(function(){
            setSource();
        });
        setSource();
    });
</script>