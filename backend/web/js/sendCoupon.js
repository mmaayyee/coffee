$(function(){
    changeCouponGroup($("#couponsendtask-coupon_group_id"));
    changeSendType($("#couponsendtask-task_type"));
    addBuildingType($("#couponsendtask-search_build_type"));
    if ($("#couponsendtask-import_user_type").length > 0) {
        changeImportUserType("#couponsendtask-import_user_type",0);
    }
    $("#couponsendtask-send_time").datetimepicker({
        minDate: new Date(),
        onClose: function( selectedDate ) {
            $("#couponsendtask-coupon_start_time").datepicker( "option", "minDate", selectedDate );
        }
    });
    $("#couponsendtask-coupon_start_time").datetimepicker({
        onClose: function( selectedDate ) {
            $("#couponsendtask-coupon_end_time").datepicker( "option", "minDate", selectedDate );
        }
    });
    $("#couponsendtask-coupon_end_time").datetimepicker({
        onClose: function(selectedDate) {
            $("#couponsendtask-coupon_start_time").datepicker( "option", "maxDate", selectedDate );
        }
    });
    $("#couponsendtask-searchstarttime").datetimepicker({
        onClose: function( selectedDate ) {
            $("#couponsendtask-searchendtime").datepicker( "option", "minDate", selectedDate );
        }
    });
    $("#couponsendtask-searchendtime").datetimepicker({
        onClose: function(selectedDate) {
            $("#couponsendtask-searchstarttime").datepicker( "option", "maxDate", selectedDate );
        }
    });
    $(".field-couponsendtask-coupon_end_time, .field-couponsendtask-coupon_start_time, .searchDate").validation();
    $("#couponsendtask-coupon_start_time").change(function(){
        $("#couponsendtask-coupon_end_time").trigger("blur");
    });
    $("#couponsendtask-searchstarttime").change(function(){
        $("#couponsendtask-searchendtime").trigger("blur");
    });
    //选择不同的场景显示不同的楼宇
    $("#couponsendtask-scenes").change(function(){
        if ($(this).val()!="" && $("#couponsendtask-search_build_type").val != ""){
            $(".search-building select[name='buildingType']").prop("disabled", true);
            $(".search-building select[name='buildingType']").find("option").removeAttr("selected");
            $(".search-building select[name='buildingType']").find("option[value='"+$("#couponsendtask-scenes").val()+"']").prop("selected", true);
            $("#searchResult").trigger("click");
        } else {
            $(".search-building select[name='buildingType']").prop("disabled", false);
        }
    });
})
/**
 * 新增bootstrap3-validation.js 验证方法
 * */
$.extend($.fn.validation.defaults.validRules.push(
    {
        name: 'compareDate',
        validate: function(value,err) {
            var endDate = Date.parse(new Date( value )) / 1000;
            var startDate = Date.parse(new Date( $(this).parent().parent().prev().find("input[type='text']").val() )) / 1000;
            if( parseFloat(endDate) <= parseFloat(startDate)){
                return true;
            }
        },
        defaultMsg: '不能小于等于开始时间。'
    },
    {
        name: 'verifyFile',
        validate: function(value,err) {
            if ($(this).prev().val() && value == "") {
                return true;
            }
        },
        defaultMsg: '验证失败，请重新上传文件。'
    },
    {
        name: 'fileFormat',
        validate: function(value,err) {
            var type = value.substr(value.lastIndexOf(".")).toLowerCase();
            if (type!=".txt" && value!= ""){
                return true;
            }
        },
        defaultMsg: '验证失败。'
    }
));
/**
 * 选择不同优惠券套餐显示不同的优惠券信息
 * @author  zgw
 * @version 2017-08-25
 * @param   object   obj js对象
 */
function changeCouponGroup(obj)
{
    var groupID = obj.val();
    var groupCoupons = couponGroups[groupID];
    if(groupCoupons){
        var html = ' <table class="table table-striped table-bordered"><tbody><tr><td>优惠券总数</td><td>'+groupCoupons.coupon_number+' 张</td></tr>';
            $.each(groupCoupons.coupons,function(i,e){
                html += '<tr><td>'+e.name+'</td><td>'+e.number+' 张</td></tr>';
            });
            html+='</tbody></table>';
        $("#group-coupons").html(html);
    }
}

/**
 * 选择不同的任务类型显示不同的内容
 * @author  zgw
 * @version 2017-08-26
 * @param   object   obj js对象
 */
function changeSendType(obj)
{
    if (obj.val() === '0') {
        $(".coupon-expire").show();
        $("#couponsendtask-coupon_start_time").attr("check-type","required ");
        $("#couponsendtask-coupon_end_time").attr("check-type","required compareDate");
        $(".field-couponsendtask-export_reason").hide();
        $("#couponsendtask-export_reason").val('');
    } else if (obj.val() === '1') {
        $(".coupon-expire").hide();
        $(".field-couponsendtask-coupon_start_time, .field-couponsendtask-coupon_end_time").removeClass("has-success");
        $(".field-couponsendtask-coupon_start_time, .field-couponsendtask-coupon_end_time").removeClass("has-error");
        $(".field-couponsendtask-coupon_start_time, .field-couponsendtask-coupon_end_time").find("#valierr").remove();
        $("#couponsendtask-coupon_start_time, #couponsendtask-coupon_end_time").removeAttr("check-type");
        $(".field-couponsendtask-export_reason").show();
    } else {
        $(".field-couponsendtask-coupon_start_time, .field-couponsendtask-coupon_end_time").removeClass("has-success");
        $(".field-couponsendtask-coupon_start_time, .field-couponsendtask-coupon_end_time").removeClass("has-error");
        $(".field-couponsendtask-coupon_start_time, .field-couponsendtask-coupon_end_time").find("#valierr").remove();
        $("#couponsendtask-coupon_start_time, #couponsendtask-coupon_end_time").removeAttr("check-type");
        $(".coupon-expire, .field-couponsendtask-export_reason").hide();
        $("#couponsendtask-export_reason").val('');
    }
}


/**
 * 选择添加楼宇方式
 * @author  zgw
 * @version 2017-09-07
 * @param   {[type]}   obj [description]
 */
function addBuildingType(obj)
{
    if (obj.val() == 1) {
        $("input[name='buildingIdArr[]']").prop('disabled',false);
        $(".search-building").show();
        $(".add-building-file").hide();
        $(".add-building-file").removeClass("has-error");
        $(".add-building-file").next(".verify-result").hide();
        $(".add-building-file").next(".verify-result").html('');
        $(".add-building-file").find(".formatErr,#valierr").remove();
        $(".add-building-file").find("input").val("");
        if ($("#couponsendtask-scenes").val() != "") {
            $(".search-building select[name='buildingType']").prop("disabled", true);
            $(".search-building select[name='buildingType']").find("option").removeAttr("selected");
            $(".search-building select[name='buildingType']").find("option[value='"+$("#couponsendtask-scenes").val()+"']").prop("selected", true);
            $("#searchResult").trigger("click");
        } else {
            $(".search-building select[name='buildingType']").prop("disabled", false);
        }
    } else {
        $(".search-building").hide();
        $(".add-building-file").show();
        $("input[name='buildingIdArr[]']").prop('disabled',true);
    }
}

/**
 * 选择不同的用户类型显示不同的内容
 * @author  wxz
 * @version 2017-09-01
 * @param   object   obj js对象
 */

function changeUserType(obj)
{
    var n = parseInt(obj.val());
    $(".searchDate").hide();
    $(".field-couponsendtask-searchendtime, .field-couponsendtask-searchstarttime").removeClass("has-success").removeClass("has-error");
    $(".field-couponsendtask-searchendtime, .field-couponsendtask-searchstarttime").find("#valierr").remove();
    if (n ===1) {
        $(".searchDate input").attr('value','');
        $("#scouponsendtask-searchstarttime, #couponsendtask-searchendtime").removeAttr("check-type");
        $(".groups").validation({reqmark:false});
        $(".field-couponsendtask-product, .condition, .condition .groups, .condition .groups:nth-child(1) .form-inline:nth-child(2)").show();
    } else if (n === 2) {
        $(".searchDate input").attr('value','');
        $("#scouponsendtask-searchstarttime, #couponsendtask-searchendtime").removeAttr("check-type");
        $(".groups:nth-child(1)").validation({reqmark:false});
        $(".condition .groups, .condition .groups:nth-child(1) .form-inline:nth-child(2)").hide();
        $(".condition, .condition .groups:nth-child(1), .field-couponsendtask-product").show();
    } else if (n === 0) {
        $(".field-couponsendtask-product, .condition, .condition .groups").hide();
        $(".searchDate").show().validation();
        $("#couponsendtask-searchendtime").attr("check-type","compareDate");
    } else {
        $(".searchDate input").attr('value','');
        $("#scouponsendtask-searchstarttime, #couponsendtask-searchendtime").removeAttr("check-type");
        $(".field-couponsendtask-product, .condition, .searchDate").hide();
    }
}

/**
 * 选择不同导入用户的方式显示不同上文件传的类型
 * @author  wxz
 * @version 2017-09-12
 * @param   object   obj js对象
 */
function changeImportUserType(obj)
{
    var n = parseInt($(obj).val());
    var getChangeFun = $("#add-file").attr("onchange");
    $("#add-file,#sheild-file,#add-file-name,#sheild-file-name").val("");
    $(obj).parent().next().find(".verify-result").text("").hide();
    switch(n)
    {
    case 1:
        $("#couponsendtask-sheild_user_type").val(0);
        $("#add-file").attr("onchange",getChangeFun.replace(/0/, "1"));
        $("#sheild-file").attr("onchange",getChangeFun.replace(/1/, "0"));
        $("#add-file-name").attr("name", 'addBuildingFile');
        $("#sheild-file-name").attr('name','sheildMobileFile');
      break;
    default:
        $("#couponsendtask-sheild_user_type").val(1);
        $("#add-file").attr("onchange",getChangeFun.replace(/1/, "0"));
        $("#sheild-file").attr("onchange",getChangeFun.replace(/0/, "1"));
        $("#add-file-name").attr("name", 'addMobileFile');
        $("#sheild-file-name").attr('name','sheildBuildingFile');
        break;
    }

}
function uploadFileClick(obj)
{
    $(obj).val('');
    $(obj).parent().removeClass("has-success").removeClass("has-error");
    $(obj).parent().find("#valierr").remove();
}
/**
 * ajax上传文件
 * @author  wxz
 * @version 2017-09-04
 * @param   object   obj js对象
 */
function uploadFile(obj, dataType) {
    var objID = $(obj).attr("id");
    var oldFilePath = $(obj).next().val();
    if (fileValid(obj) == false){
        return false;
    }
    $.ajaxFileUpload({
        url: url+"coupon-send-task-api/verify-file.html?"+verifyPassword,
        secureuri: false,
        dataType: 'json',
        fileElementId: objID,
        data: {'dataType': dataType,'oldFilePath': oldFilePath},
        success : function(data) {
            $('input[id="'+objID+'"]').parent().next(".verify-result").show();
            if (data.noExistsList) {
                fileValidFalse($('input[id="'+objID+'"]'));
                $('input[id="'+objID+'"]').parent().next(".verify-result").addClass("has-error").html("<label>验证反馈：</label>"+data.noExistsList);
                $('input[id="'+objID+'"]').parent().find("input[type='hidden']").attr("value", "");
            } else {
                $('input[id="'+objID+'"]').parent().next(".verify-result").removeClass("has-error");
                $('input[id="'+objID+'"]').parent().next(".verify-result").html('<label>验证反馈：</label>文件验证成功');
            }
            if (data.filePath != '') {
                $('input[id="'+objID+'"]').parent().find("input[type='hidden']").attr("value", data.filePath);
            }
            if (data.filePath == '') {
                fileValidFalse($('input[id="'+objID+'"]'));
                $('input[id="'+objID+'"]').parent().next(".verify-result").addClass("has-error").html('<label>验证反馈：</label>文件验证失败，请检查文件格式是否正确');
                $('input[id="'+objID+'"]').parent().find("input[type='hidden']").attr("value", "");
            }
        },
        error : function(data) {
            fileValidFalse($('input[id="'+objID+'"]'));
            $('input[id="'+objID+'"]').parent().find("input[type='hidden']").attr("value", "");
            $('input[id="'+objID+'"]').parent().next(".verify-result").addClass("has-error").html('服务器上传失败。');
        }
    });
}
function fileValid(obj){
    var fileValue = $(obj).val();
    var type = fileValue.substr(fileValue.lastIndexOf(".")).toLowerCase();
    if (type!=".txt" && fileValue) {
        $(obj).parent().removeClass("has-success").addClass("has-error");
        if($(obj).parent().find(".help-block").length < 1){
            $(obj).after('<span class="help-block" id="valierr">文件上传格式不正确</span>');
        }else{
            $(obj).parent().find("#valierr").text("文件上传格式不正确");
        }
        $(obj).parent().next(".verify-result").hide().html('');
        return false
    } else {
        return true;
    };
}
function fileValidFalse(obj){
    obj.parent().removeClass("has-success").addClass("has-error");
    if(obj.parent().find(".help-block").length < 1){
        obj.after('<span class="help-block" id="valierr">文件上传失败，请重新上传。</span>');
    }else{
        obj.parent().find("#valierr").text("验证失败，请重新上传。");
    }
}
window.onload = function(){
    changeUserType($("#couponsendtask-user_type"));
    $("form .block-file, .searchDate").validation({reqmark:false});
    $("form").on("beforeSubmit", function(){
        if ($("form").valid() == false) {
            $("form").find(".btn-success").removeAttr("disabled");
            return false;
        } else {
            if($(".verify-result.has-error:visible").length > 0 ){
                $(".verify-result.has-error").each(function() {
                    if ($(this).text() != ""){
                        fileValidFalse($(this).prev().find("input[type='file']"));
                    }
                });
                $("form").find(".btn-success").removeAttr("disabled");
                return false;
            }
        }
    });
}
