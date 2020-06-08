<?php

use backend\models\EquipAbnormalSendRecord;
use backend\models\EquipWarn;
use common\models\Building;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\EquipAbnormalSendRecordSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equip-abnormal-send-record-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]);?>
    <div class="form-group form-inline">
    <div class="form-group">
        <label>故障内容</label>
        <div class="select2-search">
        <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'abnormal_id',
    'data'          => EquipWarn::$warnContent,
    'options'       => ['placeholder' => '请选择故障内容'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
        </div>
    </div>


    <?=$form->field($model, 'build_id')->widget(AutoComplete::classname(), ['clientOptions' => ['source' => Building::getDeliveryBuildNameList(['>', 'build_status', Building::PRE_DELIVERY])], 'options' => ['class' => 'form-control']])?>

    <?=$form->field($model, 'is_process_success')->dropDownList(EquipAbnormalSendRecord::$processResult);?>

    <?=$form->field($model, 'equip_code')?>

    <div class="form-group">
        <?=Html::submitButton('检索', ['class' => 'btn btn-primary'])?>
    </div>
    </div>
    <?php ActiveForm::end();?>

</div>
