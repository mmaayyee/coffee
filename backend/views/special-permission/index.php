<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use common\models\WxMember;
use backend\models\EquipRfidCard;
use yii\helpers\Url;
use kartik\select2\Select2;
use common\models\Equipments;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipRfidCardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'RFID卡管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-rfid-card-index">
	 <?php $form = ActiveForm::begin([
        'action' => ['special-permission/create'],
        'method' => 'get',
    ]); ?>
    <div class="form-group">
        <label>请选择门禁卡号</label>
        <?php 
            echo Select2::widget([
                'model' => $model,
                'attribute' => 'rfid_card_code',
                'data' => EquipRfidCard::getRfidCardArray(),
                'options' => [
                    'placeholder' => '请选择门禁卡号',
                    // "multiple"  => true,
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]);
        ?>
    </div>

    <div class="form-group equip-id">
        <label>请选择设备编号</label>
        <?php
            $equipIdArr = Equipments::getEquipArr("", 1);
            echo Select2::widget([
                'model' => $model,
                'attribute' => 'equipId',
                'data' => $equipIdArr,
                'options' => [
                    'placeholder' => '请选择设备编号',
                    // "multiple"  => true,
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]);
        ?>
        <div class="help-block"></div>
    </div>
    
    <input type="hidden" id="text_sign" name="EquipRfidCard[text_sign]" value="">
    <?= $form->field($model, 'verificateCode')->textInput()->label("请输入验证码"); ?>
    
    <?php if(isset($error) && $error){ ?>
    <div class="form-inline form-group">
        <label style="color: red;"><?php echo $error; ?></label>
    </div>
    <?php } ?>
    
    <?php if(isset($passStr) && !$error){ ?>
    <div class="form-inline form-group">
        <label>生成密码：</label>
        <?php echo $passStr; ?>
    </div>
    <?php } ?>
    <div class="form-group">
        <?= Html::submitButton('生成', ['class' => 'btn btn-primary generate']) ?>
        <a href="<?php echo Url::to(['special-permission/index']); ?>"><?= Html::button('清空', ['class' => 'btn btn-primary btn-clear']) ?></a>
        <?php if(Yii::$app->params['coffeeUrlSign'] == 1){ ?>
        <?= Html::submitButton('测试生成', ['class' => 'btn btn-primary generate_text']) ?>
        <?php } ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<?php

$rfidEquipUrl   =   Url::to(["special-permission/get-equip-id"]);
$this->registerJs('
    $(".generate").click(function(){
        var verificatCode    =  $("#equiprfidcard-verificatecode").val();
        var equipId          =  $("#equiprfidcard-equipid").val();
        if(!verificatCode){
            $(".field-equiprfidcard-verificatecode").addClass("has-error");
            $(".field-equiprfidcard-verificatecode").find(".help-block").html("验证码不可为空");
            return false;
        }
        
        if(!equipId){
            $(".equip-id").addClass("has-error");
            $(".equip-id").find(".help-block").html("设备编号不可都为空");
            return false;
        }
        $("#text_sign").val("");
    })
    
    // 测试环境使用，上线后无用
    $(".generate_text").click(function(){
        var verificatCode    =  $("#equiprfidcard-verificatecode").val();
        var equipId          =  $("#equiprfidcard-equipid").val();
        if(!verificatCode){
            $(".field-equiprfidcard-verificatecode").addClass("has-error");
            $(".field-equiprfidcard-verificatecode").find(".help-block").html("验证码不可为空");
            return false;
        }
        
        if(!equipId){
            $(".equip-id").addClass("has-error");
            $(".equip-id").find(".help-block").html("设备编号不可都为空");
            return false;
        }
        $("#text_sign").val("1");
    })


');

