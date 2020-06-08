<?php
use backend\models\Manager;
use backend\models\SaleBuildingAssoc;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Api;
use kartik\select2\Select2;
$this->registerJsFile('@web/js/jquery.cxselect.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/sale-register-code.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

?>
<div class="sale-build-assoc-form">

    <?php $form = ActiveForm::begin();?>
    
    <div class="form-group form-inline">
            <div class="form-group form-inline"><label>姓名</label></div>
            <div class="form-group form-inline" style="width: 180px;">
            <?php 
                echo Select2::widget([
                    'model' => $model,
                    'attribute' => 'sale_id',
                    'data' => Api::getSaleIdNameList(),
                    'options' => [
                        'placeholder' => '姓名',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]);
            ?>
            </div>
        </div>
    <div class="form-group form-inline">
            <div class="form-group form-inline"><label>楼宇</label></div>
            <div class="form-group form-inline" style="width: 180px;">
            <?php 
                echo Select2::widget([
                    'model' => $model,
                    'attribute' => 'build_id',
                    'data' => Api::getBuildIdNameList(),
                    'options' => [
                        'placeholder' => '楼宇',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]);
            ?>
            </div>
        </div>

    <div class="form-group" id="img-border" style="display:none;">
        <img src="#" id="img-value" >
        <?=Html::a('下载', ['upload'], ['class' => 'btn btn-success','id' => 'upload-img'])?>
    </div>
    <div id="qrcode-error" style="color:#A94442;margin-bottom:3%; display: none;" ></div>
    <div id="qrcode-error" style="color:#A94442;margin-bottom:3%; display: none;" ></div>
    <div style="color:#A94442;display:none;" id="errorMsg">该人员已经绑定此楼宇</div>
    <div class="form-group" >
        <?=Html::Button('生成',[ "class" => 'btn btn-primary','id' => "two-img"])?>
    </div>
    <?php ActiveForm::end();?>

</div>