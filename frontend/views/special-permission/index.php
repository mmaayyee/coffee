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
?>
<div class="equip-rfid-card-index">
	 <?php $form = ActiveForm::begin([
        'action' => ['special-permission/create'],
        'method' => 'get',
    ]); ?>
    
    <?= $form->field($model, 'equipId')->textInput(['maxLength'=>30])->label("请输入设备编号"); ?>
    
    <?= $form->field($model, 'verificateCode')->textInput(['maxLength'=>6])->label("请输入验证码"); ?>
    
    <?php if(isset($error) && $error){ ?>
    <div class="form-inline form-group">
        <label style="color: red;"><?php echo $error; ?></label>
    </div>
    <?php } ?>
    <input type="hidden" id="text_sign" name="EquipRfidCard[text_sign]" value="">
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

$this->registerJs('
    $(".generate").click(function(){
        $("#text_sign").val("");
    })
    
    // 测试环境使用，上线后无用
    $(".generate_text").click(function(){
        $("#text_sign").val("1");
    })

');
?>