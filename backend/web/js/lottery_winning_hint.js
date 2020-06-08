$(function(){
    $("form").on("beforeSubmit", function(){
        if ($("form").valid() == false || $(".has-error").length > 0) {
           $("form").find(".btn-success").removeAttr("disabled");
          jumpError();
           return false;
        } else {
            uploadFile();
        }
    }).on('submit', function (e) {
            e.preventDefault();
    })
    upImg($("#w0"));
    if($("#lotterywinninghint-hint_id").val()){
        $(".imgdiv").each(function() {
             $(this).parent().find("input[type='file']").removeAttr("check-type")
        });
    }
    $("form").validation({reqmark:false});
});

$.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if(o[this.name]){ // 判断对象中是否已经存在 name，如果存在name
              if($.isArray(o[this.name])){
                o[this.name].push(this.value);
              }
              else{
                o[this.name]=[o[this.name],this.value];
              }
        }else{
            o[this.name] = this.value || '';
        }
    });
    return o;
};
/**
 * ajax上传文件
 * @author  wxz
 * @version 2017-09-04
 * @param   object   obj js对象
 */
function uploadFile() {
    var fileIdList = [];
    $("input[type=file]").each(function(){
        var fileID = $(this).attr("id");
        fileIdList.push(fileID);
    });
    // 多条进行json传输、数组
    var formData= '';
    var formString = '';
    var formData = $('form').serializeArray();
    $.ajaxFileUpload({
        url: url+"activity-api/lottery-winning-hint-create.html?"+verifyPassword,
        secureuri: false,
        dataType: 'json',
        fileElementId: fileIdList,
        data: formData,
        success : function(data) {
            formString = '';
            if(data.ret == 1){
                createActivityLog();
                window.location.href="/lottery-winning-hint/index";
            } else {
                $("form").find(".btn-success").removeAttr("disabled");
            }
            if(data.prompt){
                $(".submit-error").html("文件大小超过设置");
            }
        },
        error : function(data) {
            $("form").find(".btn-success").removeAttr("disabled");
            $(".submit-error").html('服务器上传失败。');
        }
    });
}

// 显示图片
function upImg(obj) {
    obj.find(".imgdiv").each(function() {
        var upBtn = $(this).prev("input[type='file']").attr("id");
        new uploadPreview({ UpBtn: upBtn, ImgShow: upBtn+"_img"});
    });
}
//跳转到bootstrap3-validation验证出错的元素位置
function  jumpError() {
    if ($(".has-error").length > 0 ){
        var top = $(".has-error").offset().top - 50;
        $("html, body").animate({
          scrollTop: top
        },
        {
            duration: 500,easing: "swing"
        });
    }
}
// 添加操作记录
function createActivityLog()
{
    var type =1;
    if($("#lotterywinninghint-hint_id").val()){
        type = 2;
    }
    $.ajax({
        type:'get',
        url:'/activity-combin-package-assoc/create-activity-log',
        data:{'type': type,'moduleType':6},
        success:function(data){

        },
    })
}
