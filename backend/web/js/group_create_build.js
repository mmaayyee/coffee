/**
 * 选择添加楼宇方式
 * @author  zgw
 * @version 2017-09-07
 * @param   {[type]}   obj [description]
 */
function addBuildingType(obj)
{
    if (obj.val() == 2) {
        $(".search-building").show();
        $(".add-building-file").hide();
        $(".add-building-file").removeClass("has-error");
        $(".add-building-file").next(".verify-result").removeClass("has-error").hide().html('');
        $(".add-building-file").find("#valierr").remove();
        $("#searchResult").trigger("click");
    } else if(obj.val() == 1) {
        $(".search-building").hide();
        $(".add-building-file").show();
        $(".block-file").validation();
    }else {
        $(".search-building").hide();
        $(".add-building-file").hide();
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
function uploadTextFile(obj) {
    var objID = $(obj).attr("id");
    var oldFilePath = $(obj).next().val();
    if (fileValid(obj) == false){
        return false;
    }
    var formData = new FormData();
    formData.append("CouponSendTask[verifyFile]",$(obj)[0].files[0]);
    formData.append("oldFilePath",oldFilePath);
    $.ajax({
        type:'post',
        url: url+"equip-product-group-api/verify-file.html?"+verifyPassword,
        dataType: 'json',
        data: formData,
        processData : false, // 使数据不做处理
        contentType : false, // 不要设置Content-Type请求头
        success : function(data) {
            // $(".verify-result").show();
            $('input[id="'+objID+'"]').parent().next(".verify-result").show();
            if (data.noExistsList) {
                fileValidFalse($('input[id="'+objID+'"]'));
                $("#div_verify").addClass("has-error").html("<label>验证反馈：</label>"+data.noExistsList);
                $('input[id="'+objID+'"]').parent().find("input[type='hidden']").attr("value", "");
            } else {
                   $("#div_verify").removeClass("has-error").html('<label>验证反馈：</label>文件验证成功');
            }
            if (data.filePath != '') {
                   $('input[id="'+objID+'"]').parent().find("input[type='hidden']").attr("value", data.filePath);
            }
            if (data.filePath == '') {
                fileValidFalse($('input[id="'+objID+'"]'));
                $("#div_verify").addClass("has-error").html('<label>验证反馈：</label>文件验证失败，请检查文件格式是否正确');
                $('input[id="'+objID+'"]').parent().find("input[type='hidden']").attr("value", "");
            }
        },
        error : function(data) {
            fileValidFalse($('input[id="'+objID+'"]'));
            $("#div_verify").addClass("has-error").html('服务器上传失败。');
            $('input[id="'+objID+'"]').parent().find("input[type='hidden']").attr("value", "");
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
window.onload = function() {
    //页面加载完成后，如果有楼宇搜索修改数据，则隐藏修改删除按钮
    if($(".addPreview tbody tr")){
        var modifiedAttr = [];
        $(".addPreview table").find("button").each(function(){
            $(this).css("visibility", "hidden");
            modifiedAttr.push($(this).prev("input[type='hidden']").val());
        })
        $("#addAll").on("click", function(){
            $.each(modifiedAttr, function(index, value){
                $(".addPreview tbody").find("input[value="+value+"]").next("button").css("visibility", "hidden");
            });
        });
    }

}
