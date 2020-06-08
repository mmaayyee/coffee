<?php

use backend\models\DeliveryOrder;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\View;
$this->registerJsFile('/assets/f0269b11/jquery.min.js', ['position' => View::POS_HEAD]);
/* @var $this yii\web\View */
/* @var $model common\models\DeliveryOrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<style>
    #export{
        float: right;
    }
</style>

<div class="delivery-order-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'id'     => 'orderForm',
]);?>
    <div class="form-group  form-inline">
        <?=$form->field($model, 'receiver')?>

        <?=$form->field($model, 'nickname')?>

        <?=$form->field($model, 'phone')?>

        <?=$form->field($model, 'delivery_order_code')->label('配送订单编号')?>

        <?=$form->field($model, 'sequence_number')->label('外卖订单')?>

        <?=$form->field($model, 'address')?>

        <?=$form->field($model, 'delivery_person_id')->dropDownList($model->person, ['prompt' => '请选择'])->label('配送员')?>

        <?=$form->field($model, 'build_id')->dropDownList($model->building_list, ['prompt' => '全部点位'])->label('点位')?>

        <div style='display: inline-block'>
            <?=$form->field($model, 'diachronic')->input('text',['style'=>'width:80px'])?>
            <label for='loginform-username' style='line-height:2.5em;'>分钟</label>

        </div>

        <?=$form->field($model, 'start_time')->widget(\janisto\timepicker\TimePicker::className(), [
    //'language' => 'fi',
    'mode'          => 'datetime',
    'clientOptions' => [
        'dateFormat' => 'yy-mm-dd',
        'timeFormat' => 'HH:mm:ss',
        'showSecond' => true,
    ],
])->label('开始时间');
?>

<?=$form->field($model, 'end_time')->widget(\janisto\timepicker\TimePicker::className(), [
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
])->label('结束时间');
?>

        <div class="form-group">
            <?=Html::Button('搜索', ['class' => 'btn btn-primary','id' => 'search'])?>
            <?php if (Yii::$app->user->can('导出外卖订单')): ?>&nbsp;
                <?=Html::Button('导出', ['class' => 'btn btn-success', 'id' => 'export'])?>
            <?php endif;?>
        </div>



    </div>
    <?=$form->field($model, 'delivery_order_status')->checkboxList(DeliveryOrder::deliveryOrderStatus(),array('separator'=>'   ','template'=>'{input}'))->label('')?>

    <?php ActiveForm::end();?>

</div>
<script>
    $(function(){
        $("#export").click(function(){
            $("#orderForm").attr("action","<?php echo Yii::$app->params['fcoffeeUrl'].'delivery-api/export-delivery-order.html'; ?>");
            $("#orderForm").submit();
        });
        $("#search").click(function(){
            $("#orderForm").attr("action","<?php echo Url::to(['delivery-order/index']); ?>");
            $("#orderForm").submit();
        });
    });

</script>