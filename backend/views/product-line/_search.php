<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\ProductOfflineRecord;
use backend\models\Manager;
use kartik\select2\Select2;
use common\models\Building;
use common\models\Equipments;
/* @var $this yii\web\View */
/* @var $model backend\models\AdvertSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="product-offline-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="form-group  form-inline ">
        <div id="_radio" >
            <input type="radio" name="equip_code_radio" id="build_id_radio" value="1" checked>
            <label for="build_id_radio">楼宇名称</label>

            <input type="radio" name="equip_code_radio" id="equip_code_radio" value="2">
            <label for="equip_code_radio">设备编号</label>
        </div>


        <?= $form->field($model, 'build_id')->widget(Select2::classname(),['data'=>Building::getEquipCodeAndBuildNameArr(['build_status'=>3]),'theme'=>'bootstrap','options' => ['placeholder' => '楼宇名称', 'id'=>'build-id'],
        'pluginOptions' => [
        'allowClear' => true, 'width'=>'180px'
        ],])->label(false); ?>

        <?= $form->field($model, 'equip_code')->textInput()->label(false); ?>

        <?= $form->field($model, 'lock_from')->dropDownList(ProductOfflineRecord::$lockFrom)->label('下架来源');  ?>
        <?= $form->field($model, 'start_time')->widget(\yii\jui\DatePicker::classname(), [
            'dateFormat' => 'yyyy-MM-dd',
        ])->textInput(); ?>
        <?= $form->field($model, 'end_time')->widget(\yii\jui\DatePicker::classname(), [
            'dateFormat' => 'yyyy-MM-dd',
        ])->textInput(); ?>

        <div class="form-group">
            <?= Html::submitButton('检索', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs('
    var equipCode = "'.$model->equip_code.'";
    var buildId   = "'.$model->build_id.'";
    // console.log("设备编号："+equipCode);
    // console.log("楼宇名称："+buildId);
    if(equipCode){
        $("#build_id_radio").removeAttr("checked");
        $("#equip_code_radio").prop("checked",true);
    }
    var radioVal = $("input[type=radio]:checked").val();
    if(radioVal == 1){
        $(".field-productofflinerecord-build_id").show();
        $("#productofflinerecord-equip_code").hide();
    }else{
        $(".field-productofflinerecord-build_id").hide();
        $("#productofflinerecord-equip_code").show();
    }
    // $("#select2-build-id-container").remove();

    $("#_radio").click(function(){
        var radioVal = $("input[type=radio]:checked").val();
        if(radioVal == 1){
            $(".field-productofflinerecord-build_id").show();
            $("#productofflinerecord-equip_code").hide();
            // $("#build-id").val("");
        }else{
            $(".field-productofflinerecord-build_id").hide();
            $("#productofflinerecord-equip_code").show();
            // $("#productofflinerecord-equip_code").val("");
        }
    })

')

?>