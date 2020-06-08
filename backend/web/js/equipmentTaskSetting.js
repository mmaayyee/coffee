/**
 * Created by wangxl on 17/7/6.
 */
$(".addRefuelCycle").click(function(){
    var html = '<div class="form-group">';

    html += '<div class="form-group field-equipmenttasksetting-material_type">';
    html += '<label class="control-label" for="">物料分类</label>';
    html += '<select id="" class="form-control" name="EquipmentTaskSetting[material_type][]">';
    if(materialList){
        for(var i in materialList){
            if (i != "") {
                html += "<option value='"+i+"'>"+materialList[i]+"</option>";
            }
        }
    }
    html += '</select>';

    html += '<div class="help-block"></div>';
    html += '</div>';
    html += '<div class="form-inline">';
    html += '<div class="form-group field-equipmenttasksetting-refuel_cycle_days required">';
    html += '<label class="control-label" for="">换料天数</label>';
    html += '<input type="text" id="" class="form-control" name="EquipmentTaskSetting[refuel_cycle_days][]">';
    html += '<div class="help-block"></div>';
    html += '</div>';
    html += ' <input type="button" class="btn btn-danger del" value="删除" />';

    html += '</div>';
    html += '</div>';
    //判断物料分类的个数是否等于物料分类的选项个数
    var optionNum = $(this).parent().prev().find('select[name*=material_type] option').length;
    var selectNum = $("select[name*=material_type]").length;
    if (parseInt(selectNum) < (parseInt(optionNum)/parseInt(selectNum))) {
        $("#material-item").append(html);
    } else {
        $(this).attr("disabled",true);
    }
    //验证
    verifyFormInfo();
})


//修改时触发
if(refuelCycle){
    var html = '';
    $("#material-item").html('');
    for(var j in refuelCycle){
        html += '<div class="form-group">';

        html += '<div class="form-group field-equipmenttasksetting-material_type">';
        html += '<label class="control-label" for="">物料分类</label>';
        html += '<select id="" class="form-control" name="EquipmentTaskSetting[material_type][]">';
        if(materialList){
            for(var i in materialList){
                if (i != "") {
                    var selected = refuelCycle[j].material_type === i ? 'selected' : '';
                    html += '<option value="'+i+'" '+selected+'>'+materialList[i]+'</option>';
                }
            }
        }
        html += '</select>';

        html += '<div class="help-block"></div>';
        html += '</div>';
        html += '<div class="form-inline">';
        html += '<div class="form-group field-equipmenttasksetting-refuel_cycle_days required">';
        html += '<label class="control-label" for="">换料天数</label>';
        html += '<input type="text" id="" class="form-control" name="EquipmentTaskSetting[refuel_cycle_days][]" value="'+refuelCycle[j].refuel_cycle+'">';

        html += '<div class="help-block"></div>';
        html += '</div>';
        if(j > 0){
            html += ' <input type="button" class="btn btn-danger del" value="删除" />';
        }

        html += '</div>';
        html += '</div>';
    }
        $("#material-item").append(html);

    //验证
    verifyFormInfo();
}

function verifyFormInfo(){
    // 删除物料分类选项
    $(".del").click(function(){
        $(this).parent().parent().remove();
        $(".addRefuelCycle").attr("disabled", false);
    })

    // 物料数量验证操作
    $(".field-equipmenttasksetting-refuel_cycle_days .form-control").blur(function () {

        var obj = $(this).parent().children('input').val();
        var reg = /^[1-9][0-9]{0,2}$/;
        if (!reg.test(obj)) {
            $(this).next().html("换料天数的值必须在1~100之间");
            $(this).parent().addClass("has-error");
        } else {
            $(this).next().html("");
            $(this).parent().removeClass("has-error");
            $(this).parent().next().children('input').next().html('');
            $(this).parent().next().removeClass("has-error");
        }
    })

}

$('#equipmenttasksetting-equipment_type_id').change(function () {
    if ($(this).val() != null && $(this).val() != '') {
        if (!isNaN($(this).val())) {
            $(this).parent().removeClass('has-error');
            $(this).next('.help-block').remove();
            $('.field-equipmenttasksetting-organization_id').removeClass('has-error');
            $('.field-equipmenttasksetting-organization_id').children('.help-block').remove();
        }
    }
});
$('#equipmenttasksetting-organization_id').change(function () {
    if ($(this).val() != null && $(this).val() != '') {
        if (!isNaN($(this).val())) {
            $(this).parent().removeClass('has-error');
            $(this).next('.help-block').remove();
            $('.field-equipmenttasksetting-equipment_type_id').removeClass('has-error');
            $('.field-equipmenttasksetting-equipment_type_id').children('.help-block').remove();
        }
    }
});

$(".submit").click(function(){

    // 物料数量验证操作
    $(".field-equipmenttasksetting-refuel_cycle_days .form-control").each(function () {

        var obj = $(this).parent().children('input').val();
        var reg = /^[1-9][0-9]{0,2}$/;
        if (!reg.test(obj)) {
            $(this).next().html("换料天数的值必须在1~100之间。");
            $(this).parent().addClass("has-error");
        } else {
            $(this).next().html("");
            $(this).parent().removeClass("has-error");
            $(this).parent().next().children('input').next().html('');
            $(this).parent().next().removeClass("has-error");
        }
    });

    if($(".has-error .help-block").length > 0){
        if ($(".has-error .help-block").html().length > 0) {
            $(this).removeAttr('disabled');
            return false;
        }
    }
    materielIsRepeat();
    if  (!isRepeat) {
        $('form').submit();
    }
    return false;
})
//判断物料分类是否重复
var isRepeat = false;
function materielIsRepeat() {
    var materielArr = [];
    $("select[name*=material_type]").each(function() {
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