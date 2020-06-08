//文本编辑器
var ue = UE.getEditor("editor",{
    autoHeightEnabled: false,
    zIndex : 1,
    toolbars: [
        ['undo','redo','bold', 'indent','italic','underline', 'strikethrough','justifyleft', //居左对齐
        'justifyright', 'justifycenter', 'justifyjustify', 'forecolor', 'backcolor', 'touppercase', //字母大写
        'tolowercase', 'directionalityltr', 'directionalityrtl', 'rowspacingtop', 'rowspacingbottom', //段后距
        'subscript', 'fontborder', 'superscript', 'formatmatch', 'blockquote', //引用
        'horizontal', 'removeformat', 'time', 'date',
        'fontfamily', 'fontsize', 'paragraph', 'spechars', 'searchreplace', //查询替换
        ]
    ],
});
var currenteditor = null;
$(function(){
    $("#activity-activity_type_id").trigger("onchange");

    //选择时间插件
    $("#activity-start_time").datetimepicker({
        minDate: new Date(),
    });
    $("#activity-start_time").change(function(){
        $(this).trigger("blur");
        if($("#activity-end_time").val() != ""){
            $("#activity-end_time").trigger("change");
        }
    });
    $("#activity-end_time").change(function(){
        $(this).trigger("blur");
    });
    $(".field-activity-start_time, .field-activity-end_time").validation();

    $("form").on("beforeSubmit", function(){
            $("#activityDesc").val(ue.getContent());
            if ($("form,.grid-list, .award-setting").valid() == false ||usHasContents() == false || $(".has-error").length > 0) {
                $("form").find(".btn-success").removeAttr("disabled");
                jumpError();
                return false;
            } else {
                uploadFile();
            }
    }).on('submit', function (e) {
            e.preventDefault();
    })
    upImg($(".lottery-activity"));
    if($("#activity-activity_id").val()){
        $("#activity-background_music").removeAttr("check-type");
        $("#activity-background_music").change(function(){
            $(this).next().html("").hide();
            $(this).attr("check-type","required musicFormat");
        });
        ue.ready(function() {
            currenteditor = $("#activityDesc").val();
            ue.setContent(currenteditor);
        });
    }
    $(".lottery-activity, .frequency").validation({reqmark:false});
    $("#activity-awards_num").trigger("change");
    $("#activity-person_day_frequency").blur(function(){
        $("#activity-max_frequency").trigger("blur");
    });
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
 * 新增bootstrap3-validation.js 验证方法
 * */
$.extend($.fn.validation.defaults.validRules.push(
    {
        name: 'compareDate',
        validate: function(value,err) {
            var endDate = Date.parse(new Date( value )) / 1000;
            var startDate = Date.parse(new Date( $(this).parent().parent().prev().find("input[type='text']").val() )) / 1000;
            if(parseFloat(endDate) <= parseFloat(startDate)){
                return true;
            }
        },
        defaultMsg: '不能小于等于开始时间。'
    },
    
    {
        name: 'mostTimes',
        validate: function(value,err) {
            var personDayFrequency = $("#activity-person_day_frequency").val();
            var maxFrequency = $("#activity-max_frequency").val();
            if(personDayFrequency != 0 && maxFrequency != 0 && parseInt(maxFrequency) < parseInt(personDayFrequency)){
                return true;
            }
        },
        defaultMsg: '最多参与次数不能小于单天参与次数。'
    },
    {
        name: 'musicFormat',
        validate: function(value,err) {
            var musicFile = getMusicSize(this);
            if(musicFile && musicFile.status){
                $(this).attr("musicFormat-message",musicFile.err);
                return true;
            }
        }
    }
));


// 添加操作记录
function createActivityLog()
{
    var type =1;
    if($("#activity-activity_id").val()){
        type = 2;
    }
    $.ajax({
        type:'get',
        url:'/activity-combin-package-assoc/create-activity-log',
        data:{'type': type,'moduleType':5},
        success:function(data){

        },
    })
}

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
    $.each(formData,function(i,e){
        if (e.name == "Activity[activity_desc]") {
            formData[i].value=e.value.replace(/\"/g,"'");
        }
    })
    $.ajax({
        url: url+"activity-api/nine-lottery-create.html?"+verifyPassword,
        secureuri: false,
        dataType: 'json',
        type: 'post',
        data:  new FormData($('#w0')[0]),
        processData: false,
        contentType: false,
        success : function(data) {
            formString = '';
            if(data.ret == 1){
                createActivityLog();
                window.location.href="/activity/index";
            } else {
                $("form").find(".btn-success").removeAttr("disabled");
            }
            if (data.repeatActivityName) {
                $(".submit-error").html("活动名称不可重复");
            }
            if (data.repeatActivityName=="") {
                $(".submit-error").html("活动名称不可重复");
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

/*
 * 根据奖项数目显示奖项设置内容
 */
function prizesNumChange(obj) {
    var awardsNum = $(obj).val();
    var lotteryActivityID = $("#activity-activity_id").val();
    var csrf = $("#csrf").val();
    $.ajax({
        type:'POST',
        url:'/nine-lottery-activity/get-nine-activity-linkage-json',
        data:{'awards_num': awardsNum, 'activity_id': lotteryActivityID, '_csrf':csrf},
        success:function(data){
            data = JSON.parse(data);
            var awardListTpl = $("#awardListTpl").html();
            laytpl(awardListTpl).render(data, function(html){
                $(".award").html(html);

            });
            upImg($(".award"));
            if($("#activity-activity_id").val()){
                $(".imgdiv").each(function() {
                     $(this).parent().find("input[type='file']").removeAttr("check-type")
                });
            }
        $(".grid-list, .award-setting").validation({reqmark:false});
        },
        error:function(){

        }
    })

}

/*
 * 根据活动类型显示活动内容
 */
function activityTypeChange(obj) {
    if ($(obj).val() == 3) {
        $(".lottery-activity").show();
    }else{
        $(".lottery-activity").hide();
    }


}

function upImg(obj) {
    obj.find(".imgdiv").each(function() {
        var upBtn = $(this).prev("input[type='file']").attr("id");
        new uploadPreview({ UpBtn: upBtn, ImgShow: upBtn+"_img"})
    });
}

function prizesTypeChange(obj){
    if ($(obj).val() == 1) {
        $(obj).parent().next(".prizes-content").css("visibility", "visible");
        $(obj).parent().next(".prizes-content").find("select").attr("check-type", "required");
    }else{
        $(obj).parent().next(".prizes-content").css("visibility", "hidden");
        $(obj).parent().next(".prizes-content").find("select").removeAttr("check-type");
    }
}
//判断文本编辑是否有内容
function usHasContents(){
    if (ue.hasContents() == false) {
        $("#editor").parent().removeClass("has-success").addClass("has-error");
        $("#editor").next("#valierr").text("活动内容不能为空");
        return false;
    } else {
        $("#editor").parent().removeClass("has-error").addClass("has-success");
        $("#editor").next("#valierr").text("");
        return true;
    }
}
//验证音乐文件大小和格式
function getMusicSize(obj){
    var flieSize = {};
    console.log(obj);
    photoExt=obj.value.substr(obj.value.lastIndexOf(".")).toLowerCase();//获得文件后缀名
    if(photoExt!='.mp3'){
        flieSize.err = "请上传后缀名为.mp3的音乐!";
        flieSize.status = true;
        return flieSize;
    }
    var fileSize = 0;
    var isIE = /msie/i.test(navigator.userAgent) && !window.opera;
    if (isIE && !obj.files) {
         var filePath = obj.value;
         var fileSystem = new ActiveXObject("Scripting.FileSystemObject");
         var file = fileSystem.GetFile (filePath);
         fileSize = file.Size;
    }else {
         fileSize = obj.files[0].size;
    }
    fileSize=Math.round(fileSize/1024*100)/100; //单位为KB
    if(fileSize>=2048){
        flieSize.err = "音乐文件最大不超过为2MB，请重新上传!";
        flieSize.status = true;
        return flieSize;
    }

}