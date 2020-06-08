<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use common\models\WxMember;
use backend\models\EquipRfidCard;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipRfidCardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'RFID卡-修改密码';
?>
<div class="equip-rfid-card-index">
    <?php if(isset($successSign) && $successSign){ ?>
    <div style="margin: 20% 0;text-align: center;">
        <div class="glyphicon glyphicon-ok-sign " style="color:#57bb59; font-size:10rem;;margin-bottom: 8%;"></div>
        <p style="font-size: 1.4rem">密码修改成功，请退出此页！</p>
    </div>
    <?php }else{ ?>
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'reset-password-form','enableClientScript'=>true]); ?>
                <?= $form->field($model, 'currentPassword')->passwordInput() ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 'rePassword')->passwordInput() ?>
                <div class="form-group">
                    <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <?php } ?>
    
</div>
