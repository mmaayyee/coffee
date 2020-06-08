<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\Manager */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="manager-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'role')->dropDownList(\backend\models\AuthItem::getRoleArray($model)) ?>
    <?php if($model->getScenario() != "create"):?>
        <?= $form->field($model, 'username')->textInput(['readonly'=>true]) ?>
    <?php else:?>
        <?= $form->field($model, 'username')->textInput() ?>
    <?php endif;?>
    <?= $form->field($model, 'password')->passwordInput() ?>
    
    <?= $form->field($model, 'repassword')->passwordInput() ?>

    <?=$form->field($model, 'userid')->widget(Select2::classname(), ['data' => \common\models\WxMember::getMemberNameList(), 'options' => ['placeholder' => '请选择成员名'], 'pluginOptions' => ['width' => '100%', 'allowClear' => true]])?>

    <?= $form->field($model, 'realname')->textInput() ?>
    
    <?= $form->field($model, 'mobile')->textInput() ?>
    
    <?= $form->field($model, 'email')->textInput() ?>
    
    <?= $form->field($model, 'status')->dropDownList($model->getStatusArray()) ?>

    <div class="form-group">
        <?= Html::submitButton('确定', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php
$wxMemberUrl  =   Url::to(["manager/get-name-mob"]);
$this->registerJs('
    $("#manager-userid").change(function(){
        var manager_userid = $("#manager-userid").val();
        $.post(
        "'.$wxMemberUrl.'",
        {userid:manager_userid},
            function(data){
                if(data){
                    $("#manager-realname").val(data.realname);
                    $("#manager-mobile").val(data.mobile);
                    $("#manager-email").val(data.email);
                }
            },
            "json"
        );
    })
');
?>