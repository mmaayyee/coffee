<?php

use backend\models\EquipRfidCard;
use backend\models\EquipRfidCardRecord;
use common\models\WxMember;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\EquipRfidCardRecordSearch */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile("@web/js/equip-rfid-card-record.js", ["depends" => [JqueryAsset::className()]]);
?>

<div class="equip-rfid-card-record-search">

    <?php $form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'id'     => 'equipRfidCardRecordForm',
]);?>


    <div class="form-group form-inline">
        <div class="form-group">
            <label>RFID门禁卡号</label>
            <div class="select2-search">
            <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'rfid_card_code',
    'data'          => EquipRfidCard::getRfidCardCodeArr(),
    'options'       => ['placeholder' => 'RFID门禁卡号'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>
        </div>

        <div class="form-group">
            <label>开门人员</label>
            <div class="select2-search">
            <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'open_people',
    'data'          => WxMember::getMemberNameInfoArr(),
    'options'       => ['placeholder' => '开门人员'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>
        </div>
        <?=$form->field($model, 'equip_code')->textInput()?>
        <div class="form-group">
            <label>楼宇名称</label>
            <div class="select2-search">
            <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'build_id',
    'data'          => \common\models\Building::getPreDeliveryBuildList(),
    'options'       => ['placeholder' => '楼宇名称'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>
        </div>
        <div class="form-group">
            <label>所属分公司</label>
            <div class="select2-search">
            <?php echo Select2::widget([
    'model'         => $model,
    'attribute'     => 'orgId',
    'data'          => \backend\models\Organization::getOrganizationList(),
    'options'       => ['placeholder' => '分公司'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]); ?>
            </div>
        </div>
        <?=$form->field($model, 'orgType')->dropDownList(\common\models\Equipments::$orgType)?>
        <?=$form->field($model, 'open_type')->dropDownList(EquipRfidCardRecord::$openType)?>
        <?=$form->field($model, 'startTime')->widget(\yii\jui\DatePicker::classname(), [
    'dateFormat' => 'yyyy-MM-dd',
])->textInput();?>

        <?=$form->field($model, 'endTime')->widget(\yii\jui\DatePicker::classname(), [
    'dateFormat' => 'yyyy-MM-dd',
])->textInput();?>

        <div class="form-group">
            <?=Html::Button('检索', ['class' => 'btn btn-primary', 'id' => 'search'])?>
                        <?php if (Yii::$app->user->can('导出门禁卡开门记录')) {?>
        <?=Html::Button('导出', ['class' => 'btn btn-primary', 'id' => 'export'])?>
    <?php }?>
        </div>
    </div>
    <?php ActiveForm::end();?>

</div>
