// 获取入库原因（修改时用到）
var updateReason = $("#scmstock-reason").val();
// 如果入库原因为配送员归还则显示配送员选项否则隐藏
if(updateReason == 2){
    $(".field-scmstock-distribution_clerk_id").show();
    $(".field-scmstock-material_gram").show();
}else{
    $(".field-scmstock-distribution_clerk_id").hide();
    $(".field-scmstock-material_gram").hide();
}
// 入库原因更改操作
$("#scmstock-reason").change(function(){
    var clerkVal = $("#scmstock-reason").val();
    if(clerkVal == "2"){
        $(".field-scmstock-distribution_clerk_id").show();
        $(".field-scmstock-material_gram").show();
    }else{
        $(".field-scmstock-distribution_clerk_id").hide();
        $(".field-scmstock-material_gram").hide();
    }
});


$(".addmaterial").click(function(){
    var updateReason = $("#scmstock-reason").val();
    // 添加物料选项
    var html = "";
    html += '<div class="form-group"><div class="form-group field-scmstock-material_id required"><label class="control-label" for="scmstock-material_id">物料</label> <select class="form-control" name="ScmStock[material_id][]"><option value="">请选择</option>';
    if (materialList){
        for (var i in materialList) {
            if (i != "") {
                html += "<option value=\'"+i+"\'>"+materialList[i]+"</option>";
            }
        }
    }
    html += '</select> <div class="help-block"></div> </div> <div class="form-inline"><div class="form-group field-scmstock-material_num"><label class="control-label" for="scmstock-material_num">物料数量</label> <input type="text" class="form-control" name="ScmStock[material_num][]" maxlength="5" value=""  /><div class="help-block"></div></div>';
    if(updateReason == 2) {
        html += '<div class="form-group field-scmstock-material_gram "><label class="control-label" for="scmstock-material_num">散料重量(克)</label> <input type="text" class="form-control" name="ScmStock[material_gram][]" maxlength="5" value=""  /><div class="help-block"></div></div>';
    }
    html += '<input type="button" class="btn btn-danger del" value="删除" /></div></div>';
    $("#material-item").append(html);
    $("[name='ScmStock[material_id][]']").select2();
    //调用验证
    verifyFormInfo();

});

$().ready(function(){

    verifyFormInfo();

    // 提交验证
    $("#save").click(function(){
        $(this).attr("disabled",true);
        var verifyRes = true;
        if ($("#scmstock-reason").val() == 2 && !$('#scmstock-distribution_clerk_id').val()) {
            $('#scmstock-distribution_clerk_id').parent().addClass("has-error");
            $('#scmstock-distribution_clerk_id').next().html("配送员不可为空");
            verifyRes = false;
        }
        $(".field-scmstock-material_num .form-control").each(function(){
            if($(this).parent().next().children('input').val() == '') {
                var obj = $(this).val();
                reg = /^[1-9][0-9]{0,4}$/;
                if (!reg.test(obj)) {
                    $(this).parent().find('.help-block').html("物料数量的值必须为0~99999");
                    $(this).parent().addClass("has-error");
                    verifyRes = false;
                } else {
                    $(this).parent().find('.help-block').html("");
                    $(this).parent().removeClass("has-error");
                    $(this).parent().next().children('input').next().html('');
                    $(this).parent().next().removeClass("has-error");
                }
            }
        })
        // 散料重量验证操作
        $(".field-scmstock-material_gram .form-control").each(function(){
            if($(this).parent().prev().children('input').val() == '') {
                var obj = $(this).val();
                reg = /^[1-9][0-9]{0,4}$/;
                if (!reg.test(obj)) {
                    $(this).parent().find('.help-block').html("散料重量的值必须为0~99999");
                    $(this).parent().addClass("has-error");
                    verifyRes = false;
                } else {
                    $(this).parent().find('.help-block').html("");
                    $(this).parent().removeClass("has-error");
                    $(this).parent().prev().children('input').next().html('');
                    $(this).parent().prev().removeClass("has-error");
                }
            }
        })
        $(".field-scmstock-material_id .form-control").each(function(){
             if(!$(this).val()){
                $(this).parent().find('.help-block').html('物料不能为空');
                $(this).parent().addClass("has-error");
                verifyRes = false;
            }else{
                 $(this).parent().find('.help-block').html("");
                 $(this).parent().removeClass("has-error");
            }
        })
        materielIsRepeat();

        if (!verifyRes) {
            $(this).removeAttr("disabled");
            return false;
        }

        if(!isRepeat && verifyRes){
            $("form").submit();
        }
        return false;
    })
})

//修改入库单
if (stock) {
    var html = '';
    var updateReason = $("#scmstock-reason").val();
    $("#material-item").html('');
    for (var j in stock) {
        if (j != "") {
            html += '<div class="form-group"><div class="form-group field-scmstock-material_id required"><label class="control-label" for="scmstock-material_id">物料</label> <select class="form-control" name="ScmStock[material_id][]"><option value="">请选择</option>';
            if (materialList) {
                for (var i in materialList) {
                    if (i != "") {
                        selected = stock[j].material_id == i ? 'selected' : '';
                        html += "<option value=\'" + i + "\' " + selected + ">" + materialList[i] + "</option>";
                    }
                }
            }

            html += '</select> <div class="help-block"></div> </div> <div class="form-inline"><div class="form-group field-scmstock-material_num"><label class="control-label" for="scmstock-material_num">物料数量</label> <input type="text" class="form-control" name="ScmStock[material_num][]" value="' + stock[j].material_num + '"  maxlength="5" /><div class="help-block"></div></div>';
            if (updateReason == 2 && materialList[stock[j].material_id].indexOf('克') > 0) {
                html += '<div class="form-group field-scmstock-material_gram "><label class="control-label" for="scmstock-material_num">散料重量(克)</label> <input type="text" class="form-control" name="ScmStock[material_gram][]" value="' + stock[j].material_gram + '"  maxlength="5"/><div class="help-block"></div></div>';
            }
            if(j != 0){
                html += '<input type="button" class="btn btn-danger del" value="删除" />';
            }
            html += '</div></div>';
        }
    }

    $("#material-item").append(html);

    //调用验证
    verifyFormInfo();
}

function verifyFormInfo(){
    // 删除物料选项
    $(".del").click(function(){
        $(this).parent().parent().remove();
    })

    // 物料数量验证操作
    $(".field-scmstock-material_num .form-control").blur(function(){
        var obj = $(this).val();
        var reg = /^[1-9][0-9]{0,4}$/;
        if($(this).parent().next().children('input').val() == '') {
            if (!reg.test(obj)) {
                $(this).parent().find('.help-block').html("物料数量的值必须为0~99999");
                $(this).parent().addClass("has-error");
            } else {
                $(this).parent().find('.help-block').html("");
                $(this).parent().removeClass("has-error");
            }
        }else{
            if(obj.length > 0 && !reg.test(obj)){
                $(this).parent().find('.help-block').html("物料数量的值必须为0~99999");
                $(this).parent().addClass("has-error");
            }else{
                $(this).parent().find('.help-block').html("");
                $(this).parent().removeClass("has-error");
            }
        }
    })

    // 散料重量验证操作
    $(".field-scmstock-material_gram .form-control").blur(function(){
        var reg = /^[1-9][0-9]{0,4}$/;
        var obj = $(this).val();
        if($(this).parent().prev().children('input').val() == '') {
            if (!reg.test(obj)) {
                $(this).parent().find('.help-block').html("散料重量的值必须为0~99999");
                $(this).parent().addClass("has-error");
            } else {
                $(this).parent().find('.help-block').html("");
                $(this).parent().removeClass("has-error");
            }
        }else{
            if(obj.length > 0 && !reg.test(obj)){
                $(this).parent().find('.help-block').html("散料重量的值必须为0~99999");
                $(this).parent().addClass("has-error");
            }else{
                $(this).parent().find('.help-block').html("");
                $(this).parent().removeClass("has-error");
            }
        }
    })

    $(".field-scmstock-material_id .form-control").change(function() {
        if ($(this).find("option:selected").text().indexOf('克') > 0 && $("#scmstock-reason").val() === '2') {
            $(this).parent().next().children('.field-scmstock-material_gram').show();
            $(this).parent().next().children('.field-scmstock-material_gram').find('input').removeAttr('disabled');
        } else {
            $(this).parent().next().children('.field-scmstock-material_gram').hide();
            $(this).parent().next().children('.field-scmstock-material_gram').find('input').attr('disabled',true);
        }
    });

    // 验证物料是否为空
    $(".field-scmstock-material_id .form-control").blur(function(){
        if(!$(this).val()){
            $(this).parent().find('.help-block').html("物料不能为空");
            $(this).parent().addClass("has-error");
            verifyRes = false;
        }else{
            $(this).parent().find('.help-block').html("");
            $(this).parent().removeClass("has-error");
        }
    })
}
//判断物料是否重复
var isRepeat = false;
function materielIsRepeat() {
    var materielArr = [];
    $("select[name*=material_id]").each(function() {
        materielArr.push($(this).find("option:selected").text());
    });
    materielArr = materielArr.sort();
    for(var i=0;i<materielArr.length;i++){
        if (materielArr[i] == materielArr[i+1]){
            $('#myModal').modal();
            $('#myModal .title').text(materielArr[i]+"物料重复");
            isRepeat = true;
            return false;
        }
    }
    isRepeat = false;
}