<?php

use common\models\WxMember;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

// use yii\jui\DatePicker;
/* @var $this yii\web\View */
/* @var $model backend\models\EquipDelivery */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-delivery-form">

    <?php $form = ActiveForm::begin();?>
    <?=$form->field($model, 'build_id')->widget(Select2::classname(), [
    'data'          => $model->isNewRecord ? $model->getBuildNameArray() : $model->getBuildNameArray(0),
    'options'       => [
        'placeholder' => '请选择楼宇',
        'data-url'    => Yii::$app->request->baseUrl . '/equip-task/ajax-get-build',
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
    'pluginEvents'  => [
        "change" => "function() {
            var positions = " . json_encode(WxMember::$position) . "
            var buildId = $('#equipdelivery-build_id').val();
            $.get(
                '/equip-delivery/get-sender',
                {'buildId': buildId},
                function(data){
                    var td = '';
                    $('.add-send tr').empty();
                    for (var i in data) {
                        td += '<tr><td>'+positions[data[i][\"position\"]]+'：</td><td>'+data[i]['name']+'</td></tr>';
                    }
                    $('.add-send').append(td);
                },
                'json'
            );
        }",
    ]])?>
    <?=$form->field($model, 'original_build_id')->hiddenInput(['value' => $model->build_id])->label(false)?>
    <?=$form->field($model, 'equip_type_id')->dropDownList($model->getEquipTypeModelArray())?>
    <?php if ($model->isNewRecord) {
    ?>
        <?=$form->field($model, 'delivery_time')->widget(
        DatePicker::className(), [
            'dateFormat'    => 'yyyy-MM-dd',
            'clientOptions' => [
                'minDate' => date('Y-m-d', strtotime('+2 days')),
            ],
        ])->textinput();?>
    <?php } else {
    ?>
            <?=$form->field($model, 'delivery_time')->widget(
        DatePicker::className(), [
            'dateFormat'    => 'yyyy-MM-dd',
            'clientOptions' => [
                'minDate' => date('Y-m-d', strtotime('+2 days')),
            ],
        ])->textinput();?>
    <?php }?>

    <?=$form->field($model, 'voice_type')->dropDownList(\backend\models\EquipDelivery::getVoiceTypeArr())?>

    <?=$form->field($model, 'is_ammeter')->dropDownList($model->getIsNeedArr())?>

    <?=$form->field($model, 'is_lightbox')->dropDownList($model::getLightBoxArr())?>

    <?=$form->field($model, 'special_require')->textarea(['maxlength' => 255, 'rows' => 6])?>
    <?php if ($model->isNewRecord) {?>
        <div class="form-group add-send">
            <h4>发送信息人：</h4>
        </div>
    <?php }?>

    <div class="form-group">
        <?=Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>
    <?php ActiveForm::end();?>

</div>




