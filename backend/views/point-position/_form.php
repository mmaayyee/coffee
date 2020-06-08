<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;
$this->registerJsFile('@web/js/cityselect.js', ['depends' => [JqueryAsset::className()]]);
$this->registerJs('
    var bl = \'<div class="row item" ><div class="col-xs-10 col-sm-5 form-group"><input type="text" class="form-control" name="PointPosition[name][]" value="" maxlength="30" onblur="verifyEmpty(this)"><div class="help-block empty-point">点位名称不能为空。</div></div><div class="col-xs-4 col-sm-2 form-group"><select class="form-control" name="PointPosition[status][]" onChange="starIsShow(this)"><option value="0">可投放</option><option value="1">已锁定</option><option value="2">已投放</option></select></div><div class="col-xs-4 col-sm-2 form-group"><select class="form-control" name="PointPosition[star][]"  style="display: none;"><option value="0">0星</option><option value="1">1星</option><option value="1.5">1.5星</option><option value="2">2星</option><option value="2.5">2.5星</option><option value="3">3星</option><option value="3.5">3.5星</option><option value="4">4星</option><option value="4.5">4.5星</option><option value="5">5星</option></select></div><div class="col-xs-4 col-sm-2"><button type="button" class="btn btn-primary btn-sm del-btn" onclick="delPoint(this)">删除点位</button></div></div>\';
    $(".add-btn").click(function(){
        $(".build-list").append(bl);
        $(".empty-point").hide();
    })
    $("#city_china").cxSelect({
        selects: ["province", "city", "area"],
    });
    $("#w0").on("beforeSubmit", function (e) {
        var istrue = 1;
        $(".build-list .row .form-control").each(function(e){
            if(!$(this).val()){
                $(this).parent().addClass("has-error");
                $(this).next().show();
                istrue=0;
            }
        })
        var namelist=[];
        $("input[name=\"PointPosition[name][]\"]").each(function(e){
                namelist[e]=$(this).val();
        })
        if(array_unique(namelist)){
            istrue=0;
        }
        if(istrue==1){
            uploadFile();
        }else{
            $("#saveBtn").removeAttr("disabled");
        }
    }).on("submit", function (e) {
        $(".build-list .row .form-control").each(function(e){
            if(!$(this).val()){
                $(this).parent().addClass("has-error");
                $(this).next().show();
            }
        })
        e.preventDefault();
    })
    $(".empty-point").hide();
')
?>
<div class="building-form">
    <?php $form = ActiveForm::begin();?>
    <?=$form->field($model, 'point_name')->textInput(['maxlength' => 30])?>
    <?=$form->field($model, 'point_id')->hiddenInput()->label(false)?>
    <?=$form->field($model, 'point_type_id')->dropDownList($pointTypeList);?>
    <div id="city_china">
        <?=$form->field($model, 'province')->dropDownList([], ['class' => 'province form-control', 'data-value' => $model->province])?>
        <?=$form->field($model, 'city')->dropDownList([], ['class' => 'city form-control', 'data-value' => $model->city])?>
        <?=$form->field($model, 'area')->dropDownList([], ['class' => 'area form-control', 'data-value' => $model->area])?>
    </div>
    <?=$form->field($model, 'address')->textInput(['maxlength' => 60])?>
    <?=$form->field($model, 'day_peoples')->textInput();?>
    <?=$form->field($model, 'cooperation_type')->dropDownList($model::$cooperationTypeList);?>
    <?=$form->field($model, 'pay_cycle')->dropDownList($model::$payCycleList);?>
    <?=$form->field($model, 'point_img')->fileInput();?>
    <p class="help-block">建议上传图片不要超过100KB</p>
    <?=$form->field($model, 'point_description')->textArea(['maxlength' => 100]);?>
    <p>包含点位</p>
    <div class="container build-list">
        <div class="row">
            <div class="col-xs-10 col-sm-5 form-group">点位名称</div>
            <div class="col-xs-4 col-sm-2 form-group">点位状态</div>
            <div class="col-xs-4 col-sm-2 form-group">销量星级</div>
            <div class="col-xs-4 col-sm-2 form-group">
                <button type="button" class="btn btn-primary btn-sm add-btn">添加点位</button>
            </div>
        </div>
        <?php if ($model->point_list): ?>
        <?php foreach (Json::decode($model->point_list) as $pointList): ?>
        <div class="row item" >
            <div class="col-xs-10 col-sm-5 form-group">
                <?=Html::textInput("PointPosition[name][]", $pointList[0], ["class" => "form-control", "onblur" => "verifyEmpty(this)", 'maxlength' => '30'])?>
                    <div class="help-block empty-point">点位名称不能为空。</div>
            </div>
            <div class="col-xs-4 col-sm-2 form-group">
                <?=Html::dropDownList("PointPosition[status][]", $pointList[1], $model::$pointStatusList, ["class" => "form-control", "onChange" => "starIsShow(this)"])?>
            </div>
            <?php if ($pointList[1] == 2): ?>
            <div class="col-xs-4 col-sm-2 form-group">
                <?=Html::dropDownList("PointPosition[star][]", $pointList[2] ?? '', $model::$starLevelList, ["class" => "form-control"])?>
            </div>
            <?php else: ?>
            <div class="col-xs-4 col-sm-2 form-group">
                <?=Html::dropDownList("PointPosition[star][]", $pointList[2] ?? '', $model::$starLevelList, ["class" => "form-control", "style" => "display: none"])?>
            </div>
            <?php endif?>

            <div class="col-xs-4 col-sm-2">
                <button type="button" class="btn btn-primary btn-sm del-btn" onclick="delPoint(this)">删除点位</button>
            </div>
        </div>
        <?php endforeach?>
        <?php else: ?>
            <div class="row item" >
                <div class="col-xs-10 col-sm-5 form-group">
                    <?=Html::textInput("PointPosition[name][]", "", ["class" => "form-control", "onblur" => "verifyEmpty(this)", 'maxlength' => '30'])?>
                    <div class="help-block empty-point">点位名称不能为空。</div>
                </div>
                <div class="col-xs-4 col-sm-2 form-group">
                    <?=Html::dropDownList("PointPosition[status][]", "", $model::$pointStatusList, ["class" => "form-control", "onChange" => "starIsShow(this)"])?>
                </div>
                <div class="col-xs-4 col-sm-2 form-group">
                    <?=Html::dropDownList("PointPosition[star][]", "", $model::$starLevelList, ["class" => "form-control", "style" => "display: none"])?>
                </div>
                <div class="col-xs-4 col-sm-2">
                    <button type="button" class="btn btn-primary btn-sm del-btn" onclick="delPoint(this)">删除点位</button>
                </div>
            </div>
        <?php endif?>
    </div>
    <div class="submit-error"></div>
    <div class="form-group">
        <?=Html::submitButton('保存', ['class' => 'btn btn-success', 'id' => 'saveBtn'])?>
    </div>
    <?php ActiveForm::end();?>
</div>
<script type="text/javascript">
    var url = "<?php echo Yii::$app->params['fcoffeeUrl'] ?>";
    var oldPointId = "<?php $model->point_id;?>";
    var erpUrl = "<?php echo Yii::$app->params['erpapi']; ?>"
    function delPoint(obj){
        if($(obj).parents('.item').siblings().length<2){
            alert('点位列表不能为空');
            return false;
        }
        if(confirm('确定要删除吗？')){
            $(obj).parents(".row").remove();
        }
    }
    function uploadFile() {
        $.ajax({
            url: url+"erpapi/point-position/save-point-position.html",
            dataType: 'json',
            type: 'post',
            data: new FormData($('#w0')[0]),
            processData: false,
            contentType: false,
            success : function(data) {
                if (data.error_code == 0) {
                    saveLog(data.data);
                } else if(data.error_code==2){
                    $(".field-pointposition-point_name").addClass("has-error").removeClass("has-success").find('.help-block').html(data.msg);
                    $("#w0").find(".btn-success").removeAttr("disabled");
                } else {
                    $(".submit-error").html(data.msg);
                    $("#w0").find(".btn-success").removeAttr("disabled");
                }
            },
            error : function(data) {
                $("#w0").find(".btn-success").removeAttr("disabled");
                $(".submit-error").html('服务器上传失败。');
            }
        });
    }
    function saveLog(pointId){
        var type = oldPointId ? '1':'0';
        $.ajax({
            url:erpUrl+"common/save-operate-log",
            dataType:'json',
            type:'post',
            data:{moduleName:"点位助手",operateType:type,operateContent:pointId},
            success:function(data){
                if(data == 1){
                    window.location.href="/point-position/index";
                } else {
                    $(".submit-error").html('日志添加失败');
                    $("#w0").find(".btn-success").removeAttr("disabled");
                }
            },
            error : function(data) {
                $("#w0").find(".btn-success").removeAttr("disabled");
                $(".submit-error").html('日志添加接口请求失败');
            }
        })
    }

    function verifyEmpty(obj){
        var name = $(obj).val();
        if(name){
            var namelist=[];
            $(obj).parents(".item").siblings().find('input[name="PointPosition[name][]"]').each(function(e){
                namelist[e]=$(this).val();
            });
            if($.inArray(name, namelist)>=0){
                $(obj).parent().addClass("has-error");
                $(obj).next().html('点位名称不能重复').show();
            } else {
                $(obj).parent().removeClass("has-error");
                $(obj).next().hide();
            }
        }else{
            $(obj).parent().addClass("has-error");
            $(obj).next().html('点位名称不能为空').show();
        }
    }

    function starIsShow(obj){
        var status = $(obj).val();
        if(status == 2){
            $(obj).parent().next().find("select").show();
        } else {
            $(obj).parent().next().find("select").hide();
        }
    }
    function array_unique(arr) {
       var hash = {};
       for(var i in arr) {
           if(hash[arr[i]]){
               return true;
           }
           hash[arr[i]] = true;
        }
       return false;
    }

</script>
<style type="text/css">
 .submit-error{
        color: #a94442;
    }
</style>
