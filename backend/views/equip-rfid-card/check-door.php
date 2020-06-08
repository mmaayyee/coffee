<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use backend\models\Organization;
use backend\models\EquipRfidCard;
use kartik\select2\Select2;
use common\models\Building;
use common\models\WxMember;
use yii\helpers\Url;
use common\models\Equipments;
use backend\models\EquipRfidCardAssoc;
/* @var $this yii\web\View */
/* @var $model backend\models\EquipRfidCard */
/* @var $form yii\widgets\ActiveForm */
$getBranchArray = Organization::getBranchArray();
unset($getBranchArray['1']);
$rfidStart = EquipRfidCard::$rfidState;
unset($rfidStart['']);
?>
<div class="equip-rfid-card-form">
<!-- passwordInput -->
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'rfid_card_code')->textInput() ?>

    <?= $form->field($model, 'rfid_card_pass')->textInput(['maxLength'=>6]) ?>
    
    <?= $form->field($model, 'equipId')->textInput()->label("设备编号") ?>
    
    <div class="form-group">
        <?= Html::submitButton( '检测', ['class' =>'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php if($retMessage){  ?>
<div>
    <?php echo $retMessage; ?>
</div>
<?php } ?>