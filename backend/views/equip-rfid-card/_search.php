<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use common\models\WxMember;
use backend\models\Organization;
use backend\models\EquipRfidCard;

/* @var $this yii\web\View */
/* @var $model backend\models\EquipRfidCardSearch */
/* @var $form yii\widgets\ActiveForm */
$getBranchArray = Organization::getBranchArray();
unset($getBranchArray['']);
?>

<div class="equip-rfid-card-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-group form-inline">
        <?= $form->field($model, 'rfid_card_code')->label('RFID门禁卡号') ?>
        <div class="form-group">
            <label>指定人员</label>
            <div class="select2-search">
            <?php echo Select2::widget([
                'model'         => $model,
                'attribute'     => 'member_id',
                'data'          => WxMember::getMemberNameInfoArr(),
                'options'       => ['placeholder' => '指定人员'],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]); ?>
            </div>
        </div>
        <?= $form->field($model, 'org_id')->dropDownList($getBranchArray) ?>

        <?= $form->field($model, 'area_type')->dropDownList(EquipRfidCard::$areaType ) ?>
        <?= $form->field($model, 'rfid_state')->dropDownList(EquipRfidCard::$rfidState) ?>

        <div class="form-group">
            <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
