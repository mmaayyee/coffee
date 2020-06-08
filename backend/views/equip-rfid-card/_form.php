<?php

use backend\models\EquipRfidCard;
use backend\models\EquipRfidCardAssoc;
use backend\models\Organization;
use common\models\Equipments;
use common\models\WxMember;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\models\EquipRfidCard */
/* @var $form yii\widgets\ActiveForm */
$getBranchArray = Organization::getBranchArray();
unset($getBranchArray['1']);
unset($getBranchArray['']);
$rfidStart = EquipRfidCard::$rfidState;
unset($rfidStart['']);
?>
<style type="text/css">
	.chooes{
		display: inline-block;
		width:30%;
		margin-top: 95px;
		vertical-align: top;
	}
	.update-equip-code{
		width:68%;
		display: inline-block;
	}
	.add,#equiprfidcard-ownedequipcode,#equiprfidcard-offequipcode{
		width: 100%;
		display:block;
	}
	.add button{
		display:inline-block;
		width:60px;
		height: 30px;
		vertical-align: top;
	}
	.add button:first-child{
		margin:50px 5% ;
	}
	.add button:last-child{
		margin:50px 1% ;
	}
	.form-group .field-equiprfidcard-ownedequipcode,.form-group.field-equiprfidcard-offequipcode{
		width:60%;
		display: inline-block;
	}
    .col-rg-100 label{
        width: 20%;
    }
</style>
<div class="equip-rfid-card-form">

    <?php $form = ActiveForm::begin();?>

    <?php if ($model->isNewRecord) {?>
        <?=$form->field($model, 'rfid_card_code')->textInput(['maxLength' => 6])?>
    <?php } else {?>
        <?=$form->field($model, 'rfid_card_code')->textInput(['disabled' => 'disabled'])?>
    <?php }?>
    <?=$form->field($model, 'rfid_card_pass')->passwordInput(['maxLength' => 6])?>

    <?=$form->field($model, 'repassword')->passwordInput(['maxLength' => 6])?>

    <?=$form->field($model, 'area_type')->dropDownList(EquipRfidCard::$areaType)?>

    <?=$form->field($model, 'org_id')->checkboxList($getBranchArray, ['class' => 'col-rg-100'])?>


    <?php if ($model->isNewRecord) {
    ?>
    <div class="form-group equip-id">
        <div class="chooes">
            <label>请选择设备所在楼宇</label>
            <?php
echo Select2::widget([
        'model'         => $model,
        'attribute'     => 'equipId',
        'data'          => [],
        'options'       => [
            'placeholder' => '请选择楼宇',
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]);
    ?>
        </div>
        <div class="update-equip-code">
            <div class="add">
                <button type="button" class="glyphicon glyphicon-arrow-right update-own-equip-code"></button>
                <?=$form->field($model, 'ownedEquipCode')->dropDownList([], ['multiple' => 'true'])->label('负责楼宇')?>
                <button type="button" class="own-clear">清空</button>
            </div>
            <div class="add">
                <button type="button" class="glyphicon glyphicon-arrow-right update-off-equip-code"></button>
                <?=$form->field($model, 'offEquipCode')->dropDownList([], ['multiple' => 'true'])->label('绑定楼宇(离线可开门)')?>
                <button type="button" class="off-clear">清空</button>
            </div>
        </div>
    </div>
<?php } else {
    ?>
    <div class="form-group equip-id update-equip-id form-group form_equ form-inline ">
        <div class="chooes">
	        <label>请选择设备所在楼宇</label>
	        <?php
if ($model->area_type == 1 || $model->area_type == 4) {
        $orgId = '';
    } else {
        $orgId = $model->org_id;
    }
    echo Select2::widget([
        'model'         => $model,
        'attribute'     => 'equipId',
        'data'          => Equipments::getEquipArr($orgId),
        'options'       => [
            'placeholder' => '请选择楼宇',
            // "multiple"  => true,
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]);
    ?>
        </div>
        <div class="update-equip-code">
        	<div class="add">
	            <button type="button" class="glyphicon glyphicon-arrow-right update-own-equip-code"></button>
	            <?=$form->field($model, 'ownedEquipCode')->dropDownList(EquipRfidCardAssoc::getEquipCodeArrByCode($model->rfid_card_code), ['multiple' => 'true'])->label('负责楼宇')?>
	            <button type="button" class="own-clear">清空</button>
	        </div>
        	<div class="add">
	            <button type="button" class="glyphicon glyphicon-arrow-right update-off-equip-code"></button>
	            <?=$form->field($model, 'offEquipCode')->dropDownList(EquipRfidCardAssoc::getEquipCodeArrByCodeOff($model->rfid_card_code), ['multiple' => 'true'])->label('绑定楼宇(离线可开门)')?>
	            <button type="button" class="off-clear">清空</button>
	        </div>
        </div>
    </div>
    <?php }?>

    <div class="form-group">
        <label>指定人员</label>
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

    <?=$form->field($model, 'rfid_state')->dropDownList($rfidStart)?>
    <?=$form->field($model, 'is_bluetooth')->radioList(EquipRfidCard::$isBluetooth)?>

    <div class="form-group">
        <?=Html::submitButton($model->isNewRecord ? '添加' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>

    <?php ActiveForm::end();?>

</div>

<?php
$rfidEquipUrl   = Url::to(["equip-rfid-card/get-equip-id"]);
$ownedEquipCode = json_encode($model->ownedEquipCode);
$offEquipCode   = json_encode($model->offEquipCode);
$this->registerJs('
    var ownedEquipCode  =   \'' . $ownedEquipCode . '\';
    var offEquipCode    =   \'' . $offEquipCode . '\';

    var orgId = $("input:checkbox:checked").map(function(index,elem) {
        return $(elem).val();
    }).get().join(",");
    var updateOwnEquipCode = $("#equiprfidcard-ownedequipcode").val();
    var updateOffEquipCode = $("#equiprfidcard-offequipcode").val();

    // 判断修改还是添加
    var areaTypeUpdate =  $("#equiprfidcard-area_type").val();
    if(areaTypeUpdate){
        if(areaTypeUpdate == 1){
            $(".field-equiprfidcard-org_id").hide();
            $(".equip-id").hide();
        }

        if(areaTypeUpdate == 2){
            $(".field-equiprfidcard-org_id").show();
            $(".equip-id").hide();
        }

        if (areaTypeUpdate == 3) {
            // 分公司
            orgIdChange();
        }

        if(areaTypeUpdate == 4){
            $(".field-equiprfidcard-org_id").hide();
        }
    }else{
        // 默认隐藏分公司
        $(".field-equiprfidcard-org_id").hide();
        $(".equip-id").hide();

    }


    // 可开门设备 （多台）
    $(".update-own-equip-code").click(function(){
        checkAddAndRepeat("#equiprfidcard-ownedequipcode", ".field-equiprfidcard-ownedequipcode");
    });

    // 可离线开门设备 （多台）
    $(".update-off-equip-code").click(function(){
        checkAddAndRepeat("#equiprfidcard-offequipcode", ".field-equiprfidcard-offequipcode");
    });


    // 判断设备的添加 和 重复问题
    function checkAddAndRepeat(idStr, classStr)
    {
        var equiprfidcardEquipid    =   $("#equiprfidcard-equipid").val();
        if(!equiprfidcardEquipid) {
            $(classStr).addClass("has-error");
             $(classStr).find(".help-block").html("设备所在的楼宇不能为空。");
        }
        var equiprfidcardBuildName  =   $(".select2-selection__rendered").attr("title");
        if(!equiprfidcardEquipid){
            return false;
        }

        //检测是否有值 添加入多选框的 (可开门设备)
        var equiprfidcardOwnedequipcode    =   $(idStr).val();
        $(classStr).removeClass("has-error");
        $(classStr).removeClass("has-success");
        if(equiprfidcardOwnedequipcode){
            if(!checkedRepeat(equiprfidcardEquipid, idStr)){
                $(classStr).addClass("has-error");
                $(classStr).find(".help-block").html("设备不可重复。");
                return false;
            }else{
                $(classStr).removeClass("has-error");
                $(classStr).find(".help-block").html("");
            }
        } else {
            $(classStr).find(".help-block").html("");
        }
        // 追加进入多选框中
        $(idStr).append(\'<option class="deloption" selected="selected" value=\'+equiprfidcardEquipid+\'>\'+equiprfidcardBuildName+\'</option>\');
        $("#equiprfidcard-equipid").select2("val", "");

        // 双击进行删除
        if(idStr == "#equiprfidcard-ownedequipcode"){
            dbLclickDelete(idStr);
        }else{
            dbLclickDeleteOff();
        }

    }

    // 双击进行删除
    dbLclickDelete("#equiprfidcard-ownedequipcode");
    dbLclickDeleteOff();

    // 鼠标离开时，全部选中框内数据
    $("#equiprfidcard-ownedequipcode").blur(function(){
        $("#equiprfidcard-ownedequipcode option").map(function(){
            $(this).attr("selected","selected");
        })
    });
    $("#equiprfidcard-offequipcode").blur(function(){
        $("#equiprfidcard-offequipcode option").map(function(){
            $(this).attr("selected","selected");
        })
    })


    // 双击进行删除（不但删除负责楼宇，也需要删除离线绑定楼宇）
    function dbLclickDelete(str){
        $(str).find("option").dblclick(function(){
            var equiprfidcardEquipid = $(this).val();
            $(".field-equiprfidcard-offequipcode").find("option").each(function(){
                var ownedEquipCode = $(this).val();
                if (ownedEquipCode == equiprfidcardEquipid) {
                    $("#equiprfidcard-offequipcode option[value="+ownedEquipCode+"]").remove();
                }
            });
            $(this).remove();
        });
    }

    // 双击进行删除（离线）
    function dbLclickDeleteOff(){
        $("#equiprfidcard-offequipcode").find("option").dblclick(function(){
            $(this).remove();
        });
    }


    //***********************设备重复检测*********************
    function checkedRepeat(equiprfidcardEquipid, classStr){
        var ownedEquipCodeStr = true;
        $(classStr).find("option").each(function(){
            var ownedEquipCode = $(this).val();
            if (ownedEquipCode == equiprfidcardEquipid) {
                ownedEquipCodeStr = false;
            }

        });
        return ownedEquipCodeStr;
    }

    // 点击清空所有值(可开们设备)
    $(".own-clear").click(function(){
        $("#equiprfidcard-ownedequipcode").empty();
        return false;
    })

    $(".off-clear").click(function(){
        $("#equiprfidcard-offequipcode").empty();
        return false;
    })


    // 区域类型change 选择
    $("#equiprfidcard-area_type").change(function(){
        var areaType    =   $("#equiprfidcard-area_type").val();
        if(areaType==""){
            $(".field-equiprfidcard-org_id").hide();
            $(".equip-id").hide();
            // $("#equiprfidcard-equipid").find("option").empty();

            $("#equiprfidcard-ownedequipcode").empty();
            $("#equiprfidcard-offequipcode").empty();

            $("#equiprfidcard-org_id").find("option").remove();

        }
        // 判断选择的区域类型
        if(areaType==1){
            $(".field-equiprfidcard-org_id").hide();
        }
        if(areaType==2){ //分公司全部设备
            $(".field-equiprfidcard-org_id").show();
            $(".equip-id").hide();
            $("#equiprfidcard-org_id").unbind("change"); // 解绑
        }else{
            $(".equip-id").hide();
        }
        // 全国部分设备
        if(areaType == 4){
            // 清除报错
            $(".equip-id").removeClass("has-error");
            $(".equip-id").find(".help-block").html("");

            // 清空equipId 的值
            $("#equiprfidcard-equipid").select2("val", "");

            $(".equip-id").show();
            $(".field-equiprfidcard-org_id").hide();
            $("#equiprfidcard-ownedequipcode").empty();
            $("#equiprfidcard-offequipcode").empty();
            $(".select2-selection__rendered").find("li.select2-selection__choice").remove();
            // 全国所有的设备  ajax传输 选择的区域类型 获取分公司的数组，组合option放入
            getEquip("", "#equiprfidcard-equipid");
        }
        // 分公司部分设备
        if(areaType==3){ // 分公司部分设备，显示分公司 及 楼宇
            // 清除报错
            $(".equip-id").removeClass("has-error");
            $(".equip-id").find(".help-block").html("");

            $(".field-equiprfidcard-org_id").show();
            $("#equiprfidcard-equipid").val("");
            // 分公司
            orgIdChange();
            var equipOrgId  =   $("#equiprfidcard-org_id").val();
            if(equipOrgId!=0){
                $("#equiprfidcard-org_id").trigger("change");
            }

        }
    })

    // var appendSign = true;
    // 点击按钮提交判断
    $(".btn").click(function(){

        // 提交时，请选择设备所在楼宇 和 负责人所属楼宇(离线可开门) 加入selected
        $("#equiprfidcard-ownedequipcode option").map(function(){
            $(this).attr("selected","selected");
        })

        $("#equiprfidcard-offequipcode option").map(function(){
            $(this).attr("selected","selected");
        })

        // 如果显示，则不可为空
        var isHideOrg = $(".field-equiprfidcard-org_id").is(":hidden"); // 判断是否隐藏
        // 获取分公司中选中的分公司
        var orgId = $("input:checkbox:checked").map(function(index,elem) {
            return $(elem).val();
        }).get().join(",");

        var ownedEquipCode  =   $("#equiprfidcard-ownedequipcode").val();
        var offEquipCode    =   $("#equiprfidcard-offequipcode").val();

        var isHideEquipId   =   $(".equip-id").is(":hidden"); // 判断是否隐藏设备楼宇
        var isHideOrgId     =   $(".field-equiprfidcard-org_id").is(":hidden"); // 判断是否隐藏设备

        if(!isHideOrg && orgId==""){
            $(".field-equiprfidcard-org_id").removeClass("has-success");
            $(".field-equiprfidcard-org_id").addClass("has-error");
            $(".field-equiprfidcard-org_id").find(".help-block").html("分公司不可为空。");
            return false;
        }

        areaType    =   $("#equiprfidcard-area_type").val();
        if (areaType == 4 || areaType == 3) {
            var ownedequipcode_length = $("#equiprfidcard-ownedequipcode").find("option:selected:selected").map(function(index, elem){
                return $(elem).val();
            }).get().join(",").length;
            if (ownedequipcode_length == 0) {
                $(".field-equiprfidcard-ownedequipcode").addClass("has-error");
                $(".field-equiprfidcard-ownedequipcode").find(".help-block").html("负责楼宇不能为空。");
                return false;
            }
        }

        //if 楼宇隐藏 则清空option
        if(isHideEquipId){
            // $("#equiprfidcard-equipid").find("option").empty();
            $("#equiprfidcard-ownedequipcode").empty();
            $("#equiprfidcard-offequipcode").empty();
            $(".select2-selection__rendered").find("li.select2-selection__choice").remove();
        }
        // 提交时，如果没有填写确认密码，但是填写了密码，则进行检测
        var password = $("#equiprfidcard-rfid_card_pass").val();
        if(password){
            var repassword = $("#equiprfidcard-repassword").val();
            if(!repassword){
                $(".field-equiprfidcard-repassword").addClass("has-error");
                $(".field-equiprfidcard-repassword").find(".help-block").html("确认密码不可为空！");
                return false;
            }else{
                $(".field-equiprfidcard-repassword").removeClass("has-error");
                $(".field-equiprfidcard-repassword").find(".help-block").html("");
            }
        }

    })

    // 处理分公司change事件
    function orgIdChange()
    {
        $("#equiprfidcard-org_id").change(function(){
            $(".select2-selection__rendered").find("li.select2-selection__choice").remove();
            var orgId = $("input:checkbox:checked").map(function(index,elem) {
                return $(elem).val();
            }).get().join(",");
            $(".equip-id").show();
            // ajax传输 选择的区域类型 获取分公司的数组，组合option放入
            getEquip(orgId, "#equiprfidcard-equipid");
        });
    }

    //获取设备数组，组合option放入
    function getEquip(orgId, element)
    {
        $.get("' . $rfidEquipUrl . '", {orgId:orgId},
            function(data){
                var option = "<option value=\'\'>请选择</option>";
                for (var i in data) {
                    option += "<option value="+i+">"+data[i]+"</option>";
                }
                $(element).html(option);
            },
            "json"
        )
    }

');

?>