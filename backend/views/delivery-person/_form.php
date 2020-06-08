<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DeliveryPerson */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="delivery-person-form">

    <?php $form = ActiveForm::begin();?>

    <?=$form->field($model, 'person_name')->widget(Select2::classname(), ['data' => \common\models\WxMember::getDeliveryNameList([], $model->person_name), 'options' => ['placeholder' => '请选择成员名'], 'pluginOptions' => ['width' => '100%', 'allowClear' => true]])?>
    <?=$form->field($model, 'wx_number')->textInput(['maxlength' => true])?>
    <?=$form->field($model, 'mobile')->textInput(['maxlength' => true])?>

    <div class="form-group">
        <?=Html::submitButton('保存', ['class' => 'btn btn-success'])?>
    </div>

    <?php ActiveForm::end();?>

</div>
<?php
$deliveryPerson = Url::to(["delivery-person/get-name-mob"]);
$this->registerJs('
    $("#deliveryperson-person_name").change(function(){
        var name = $("#deliveryperson-person_name").val();
        $.post(
        "' . $deliveryPerson . '",
        {name:name},
            function(data){
                if(data){
                    $("#deliveryperson-wx_number").val(data.userid);
                    $("#deliveryperson-mobile").val(data.mobile);
                }
            },
            "json"
        );
    })
');
?>
