<?php
use backend\models\Manager;
use backend\models\DiscountHolicy;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Api;
$this->registerJsFile('@web/js/jquery.cxselect.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/sale-register-code.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

?>
<div class="discount-building-assoc-form">

    <?php $form = ActiveForm::begin();?>
    
    <!--=$form->field($model, 'sale_id')->dropDownList(Api::getSaleIdNameList());?>

    =$form->field($model, 'build_id')->dropDownList(Api::getBuildIdNameList());?>-->

    <div class="form-group" id="img-border" style="display:none;">
        <img src="#" id="img-value" >
        <?=Html::a('下载', ['upload'], ['class' => 'btn btn-success','id' => 'upload-img'])?>
    </div>
    <div id="qrcode-error" style="color:#A94442;margin-bottom:3%; display: none;" ></div>
    <div class="form-group" >
        <?=Html::Button('生成',[ "class" => 'btn btn-primary','id' => "two-img"])?>
    </div>
    <?php ActiveForm::end();?>

</div>