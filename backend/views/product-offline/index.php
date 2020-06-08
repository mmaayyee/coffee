<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use common\models\WxMember;
use backend\models\EquipRfidCard;
use yii\helpers\Url;
use kartik\select2\Select2;
use common\models\Equipments;
use common\models\Building;
use backend\models\ProductOfflineRecord;
use backend\models\Manager;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipRfidCardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '产品上下架管理';
$this->params['breadcrumbs'][] = $this->title;

$orgID = Manager::getManagerBranchID();
?>
<style>
    .field-productofflinerecord-type{
        display: none;
    }
</style>
<div class="equip-rfid-card-index">
	 <?php $form = ActiveForm::begin([
        'action' => ['product-offline/shelves-send'],
        'method' => 'post',
    ]); ?>
    <div class="form-group product-offline-build-id">
        <label>请选择楼宇</label>
        <?php
            echo Select2::widget([
                'model' => $model,
                'attribute' => 'build_id',
                'data' => Building::getAllBuildIdEquipCodeArr(['building.build_status'=>3, 'building.org_id'=>$orgID]),
                'options' => [
                    'placeholder' => '请选择楼宇',
                    // "multiple"  => true,
                ],
                // 'pluginOptions' => [
                //     'allowClear' => true,
                // ],
            ]);
        ?>
        <div class="help-block"></div>
    </div>

    <?= $form->field($model, 'type')->dropDownList(ProductOfflineRecord::$shelvesType)->label("请选择类型"); ?>
    <div class="form-group" id="checkAll">

    </div>

    <div class="form-group">
        <?= Html::button('提交', ['class' => 'btn btn-primary generate']) ?>
    </div>
    <input type="hidden" name="productName" id="productName" value="">
    <?php ActiveForm::end(); ?>

</div>

<?php

$wxMemberUrl   =   Url::to(["product-offline/get-product"]);
$this->registerJs('
    // 楼宇
    $("#productofflinerecord-build_id").change(function(){
        var buildId = $("#productofflinerecord-build_id").val();
        var type    = $("#productofflinerecord-type").val();
        if(buildId){
            $(".field-productofflinerecord-type").show();
        }
        if(type.length != 0){
            var buildId = $("#productofflinerecord-build_id").val();
            var type    = $("#productofflinerecord-type").val();
            getProduct(buildId, type);
        }else{
            $("#checkAll").html("");
        }
    })

    // 提交按钮
    $(".generate").click(function(){
        var productNameArr = [];
        var buildId = $("#productofflinerecord-build_id").val();
        if(!buildId){
            getError(".product-offline-build-id", "楼宇不可为空");
            return false;
        }else{
            var type = $("#productofflinerecord-type").val();
            if(!type){
                getError(".field-productofflinerecord-type", "类型不可为空");
                return false;
            }
            var isCheckbox = $("input[type=checkbox]").is(":checked");
            if(!isCheckbox){
                getError("#checkAll", "产品名称不可为空");
                return false;
            }
            var noProduct   =   $("#no_product").val();
            if(noProduct == 0){
                return false;
            }
        }

        $("#checkAll input:checked").each(function(){
            var productName = $("#checkAll label[for="+$(this).attr("id")+"]").text();
            productNameArr.push(productName);
        })
        $("#productName").val(productNameArr);
        $(this).attr("disabled","disabled");
        $("#w0").submit();
    })

    // 类型
    $("#productofflinerecord-type").change(function(){
        var buildId = $("#productofflinerecord-build_id").val();
        var type    = $("#productofflinerecord-type").val();
        if(type.length != 0){
            getProduct(buildId, type);
        }else{
            $("#checkAll").html("");
        }

    })

    // 提示信息
    function getError(attribute, message)
    {
        $(attribute).addClass("has-error");
        $(attribute).find(".help-block").html(message);
    }



    // ajax 获取产品数据
    function getProduct(buildId, type)
    {
        $.post(
            "'.$wxMemberUrl.'",
            {buildId: buildId, type: type },
                function(data){
                    console.log("显示："+data);
                    $("#checkAll").html(data);
                },
                "json"
            );
    }



');

