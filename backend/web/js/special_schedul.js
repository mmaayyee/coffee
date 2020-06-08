var specialSchedulProductListTpl = $("#specialSchedulProductListTpl").html();
// var specialSchedul2ProductListTpl = $("#specialSchedul2ProductListTpl").html();
var limitConditionNum = 0;
$(function(){
    isChangeLimitNumber('#restriction_type');
    addBuildingType($("#SpecialSchedul-add_build_type"));
    //选择时间插件
    $("#specialschedul-start_time").datetimepicker({
        minDate: new Date(),
        onClose: function(selectedDate) {
            $("#specialschedul-end_time").datepicker( "option", "minDate", selectedDate );
        }
    });
    $("#specialschedul-end_time").datetimepicker({
        onClose: function(selectedDate) {
            $("#specialschedul-start_time").datepicker( "option", "maxDate", selectedDate );
        }
    });
    $("#specialschedul-start_time").change(function(){
        $("#specialschedul-end_time").blur();
    });
    $(".field-specialschedul-start_time, .field-specialschedul-end_time").validation();

    if(specialSchedulData){
        $(".specialSchedul3").hide();

        //区分首次加载产品是否有臻选咖啡,1为臻选,0为普通
        if(specialSchedulProductList[0]){
            specialSchedulData.specialSchedulProductList = specialSchedulProductList[0];
            specialSchedulData.productType="";
            laytpl(specialSchedulProductListTpl).render(specialSchedulData, function(html){
                $(".specialSchedul").html(html);


            });
            upImg($(".specialSchedul"), 0);
            if(specialSchedulProductList[1]){
                specialSchedulData.specialSchedulProductList = specialSchedulProductList[1];
                specialSchedulData.productType="z";
                laytpl(specialSchedulProductListTpl).render(specialSchedulData, function(html){
                    $(".specialSchedul2").html(html).hide();

                });
                upImg($(".specialSchedul2"), 1);
            }

            $("#cfNormal").prop("checked",true);

        }else if(specialSchedulProductList[1]){
            specialSchedulData.specialSchedulProductList = specialSchedulProductList[1];
            specialSchedulData.productType="z";
            laytpl(specialSchedulProductListTpl).render(specialSchedulData, function(html){
                $(".specialSchedul2").html(html);
                $("#cfZselect").prop("checked",true);
            });
            upImg($(".specialSchedul2"), 1);
        }else{
            $(".specialSchedul3").css({"height":"9rem","line-height":"9rem","text-align":"center"}).html("没有相关产品哦").show();
        }

        initActivity();
        $(".product-list").validation();
         $(":checkbox[data-check]").click(function () {
            var target = $(this).attr("data-check");
            $(this).parents("table").find(target).prop("checked", $(this).prop("checked"));
            $(this).parents("table").find(target).each(function() {
                editActivity(this);
            });
        });
        //点击单品活动的多选框触发编辑活动的事件
        $(".id-checkbox").on('click', function(){
           editActivity(this);
        })
    }
    window.parent.onscroll = function(e){
        scrollModal();
    }
    function scrollModal(){
        if(self!=top){
            var scrollTop = window.parent.document.documentElement.scrollTop||window.parent.document.body.scrollTop+50;
            // console.log("scrollTop..",scrollTop);
            $(".modal-content").css({top: scrollTop+"px"});
        }
    }
    scrollModal();
});

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

function initActivity(){
    $(".id-checkbox").each(function() {
        editActivity(this);
    });
    $(".id-checkbox:not(:checked)").each(function(){
        var selet = $(this).parent().next(".set-up-activity").find("select");
        isChangeActivityType(selet);
        if (selet.is(":disabled")) {
            selet.next().find("input[type='text']").prop("disabled", true);
        }
    });
}
//当选择单品列表中某个单品被选中时,可编辑单品活动
function editActivity(obj) {
    if ($(obj).is(":checked")) {
        if ($(obj).parent().next().find("input[type='text']").attr("data-check")){
            $(obj).parent().next().find("input[type='text']").each(function(){
                $(this).attr("check-type", $(this).attr("data-check"));
            });
        }
        //表单验证
        $(".productList").validation();
        $(obj).parent().next().find("select,input").removeAttr("disabled");
        $(".updateProductList").find(".logChange").removeAttr("disabled");
    } else {
        $(obj).parent().next().find("select,input").prop("disabled", true);
    }
    if ($(".id-checkbox:checked").length < 1) {
        $(".updateProductList").find(".logChange").prop("disabled", true);
    }
}
//批量修改活动
function updateActivity(){
    var activityType = $("#batchUpdataModal select").val();
    var activityStrategy = [];
    $("#batchUpdataModal input[type='text']").each(function(){
        activityStrategy.push($(this).val());
    });
    console.log("activityType..",activityType);
    console.log("activityStrategy..",activityStrategy);
    $(".set-up-activity select:not(:disabled)").each(function(){
        var i = 0;
        $(this).find("option[value='"+activityType+"']").prop("selected", true);
        isChangeActivityType(this,activityStrategy.length>3?[1,2]:[1]);
        $(this).next().find("input[type='text']").each(function(){
            $(this).val(activityStrategy[i]);
            i++;
        });
    });
}
function ladderClick(obj,type,id,op){
    var activityStrategy = [];
    var parent = $(obj).parent();
    $(parent).find("input[type='text']").each(function(){
        activityStrategy.push($(this).val());
    });
    // console.log(activityStrategy)
    var activityStrategyTpl = $("#activityStrategyTpl").html();
    var activityData = {'activity':activity, 'activityType':type, "productID":id,formArr:op==0?[1,2]:[1]};
    laytpl(activityStrategyTpl).render(activityData, function(html){
        // console.log(html)
        $(parent).html(html);
        $(parent).find("input[type='text']").each(function(index,item){
            // console.log("this..",this,":",item)
            $(this).val(activityStrategy[index]);
        });
    });
}
//选择活动类型显示不同具体活动
function isChangeActivityType(obj,myFormArr){
    // obj = $(obj)
    var activityStrategyTpl = $("#activityStrategyTpl").html();
    var activityType = Number($(obj).val());
    var productID = $(obj).parent().parent().data("key");
    var activityData = {'activity':activity, 'activityType':activityType, "productID":productID,formArr:myFormArr?myFormArr:[1]};
    // console.log("activityData..",activityData);
    laytpl(activityStrategyTpl).render(activityData, function(html){
        $(obj).next().html(html);
    });

}
//根据是否勾选限制数量的状态判断是否添加限制条件
function isChangeLimitNumber(obj){
    limitConditionNum = 0;
    if ($(obj).is(":checked")) {
        addLimitCondition();
    } else {
        $(".limit-condition").html("");
    }
}
//添加限制条件
function addLimitCondition(obj){
    var limitConditionData = {'limitConditionNum':limitConditionNum++, 'restriction':restriction};
    var limitConditionTpl = $("#limitConditionTpl").html();
    var limitTypeNum = $(obj).parent().find("select option").length;
    var limitNumber = $(obj).parent().parent().find("select").length;
    //判断添加限制条件的个数不能大于限制类型的个数长度
    if (limitTypeNum > 0 && limitTypeNum <= limitNumber){
        $("#tsModal #myModalLabel").text("提示框");
        $("#tsModal .title").html("限制条件添加的个数不超"+limitTypeNum);
        $("#tsModal").modal();
    } else {
        laytpl(limitConditionTpl).render(limitConditionData, function(html){
            $(".limit-condition").append(html);
        });
        //限制条件表单验证
        $(".limit-condition").validation();
        if(obj!==undefined){
             //追加限值类型选项是获取默认值
            var restrictionArr = [];
            $(".restriction-list select").each(function(i,e){
                restrictionArr.push($(e).val());
            })
            var restrictionValueArr = {1:'每人',2:'每天',3:'总数'};
            if(restrictionArr.length > 1){
                $.each(restrictionValueArr,function(i,e){
                    if ($.inArray(i, restrictionArr) == -1) {
                       $(".limit-condition > div:last").find("select option[value='"+i+"']").prop("selected", true);
                       $(".limit-condition > div:last").find("select").attr('data-value', i);
                       return false;
                    }
                })
            }
        }
    }
}
//删除限制条件
function delLimitCondition(obj){
    $(obj).parent().remove();
}
/**
 * 选择限购条件
 * @author  zgw
 * @version 2017-04-10
 * @param   {[type]}   obj [description]
 * @return  {[type]}       [description]
 */
function restrictionChange(obj)
{
    var restrictionArr = [];
    var index = $(".restriction-list select").index(obj);
    $(".restriction-list select").each(function(i,e){
        if (index != i) {
            restrictionArr.push($(e).val());
        }
    })
    if ($.inArray(obj.val(), restrictionArr) != -1) {
        $("#tsModal #myModalLabel").text("提示框");
        $("#tsModal .title").html(obj.find("option:selected").text()+'已存在');
        $("#tsModal").modal();
        obj.val(obj.attr('data-value'));
    } else {
        obj.attr('data-value', obj.val());
    }
}

/**
 * 添加产品组时ajax请求楼宇
 * @author  wxz
 * @version 2017-10-11
 * @param   object   page:页数   pageSize:条数
 */
var getBuildingData = function(page, pageSize){
    var buildingName = $("input[name='buildingName']").val();
    var buildingType = $("select[name='buildingType']").val();
    var branch = $("select[name='branch']").val();
    var orgRange = $("select[name='orgRange']").val();
    var equipmentType = $("select[name='equipmentType']").val();
    var equipmentCode = $("input[name='equipmentCode']").val();
    var csrf = $("#csrf").val();
    var resultData ;
    $.ajax({
         type: "POST",
         url: "/special-schedul/search-build",
         data: {"name":buildingName, 'equipmentCode':equipmentCode,"build_type":buildingType, "org_id":branch,'orgRange':orgRange, "equipmentType":equipmentType, "specialSchedulId":8, "page":page, "pageSize":pageSize, '_csrf':csrf},
         dataType: "json",
         async: false,
         success: function(data){
            if (data) {
                resultData = data;
            }
         }
    });
    return resultData;
}

/**
 * 添加产品组搜索楼宇时获取所有楼宇数据
 * @author  wxz
 * @version 2017-10-20
 * @param   object
 */
var getAllBuildingData = function(){
    // $("#addAll").add();
    var allBuildingData ;
    var buildingName = $("input[name='buildingName']").val();
    var buildingType = $("select[name='buildingType']").val();
    var branch = $("select[name='branch']").val();
    var equipmentType = $("select[name='equipmentType']").val();
    var csrf = $("#csrf").val();
    $.ajax({
        type: "post",
        url: "/equipment-product-group/get-all-building-in-product",
        data: { "name":buildingName,"build_type":buildingType,"org_id":branch,"equipmentType":equipmentType,"_csrf": csrf},
        dataType: "json",
        async: false,
        success: function(data){
            if(data != []){
                allBuildingData = data;
            }
        },
    });
    return allBuildingData;
}

$.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if(o[this.name]){
              if($.isArray(o[this.name])){
                 o[this.name].push(this.value);
              }else{
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
    var buildValid = BuildingDate($("#SpecialSchedul-add_build_type"))
    if (validForm()== false || $(".has-error").length > 0 || buildValid == false) {
        $("form").find(".btn-success").removeAttr("disabled");
        jumpError();
        return false;
    } else {
        var fileIDArr = [];
        $(".id-checkbox:checked").each(function(){
            var fileID = $(this).parent().parent().find("input[type='file']").attr("id");
            fileIDArr.push(fileID);
        });
        // console.log("fileIDArr..",fileIDArr);
        var fileID = $("input[type='file']").attr("id");
        var formData = $("form").serializeObject();
        $.ajax({
            url: url+"equip-product-group-api/save-special-schedul.html?"+verifyPassword,
            secureuri: false,
            dataType: 'json',
            // fileElementId: fileIDArr,
            // data: formData,
            data: new FormData($('#w0')[0]),
            type: 'post',
            processData: false,
            contentType: false,
            success : function(data) {
                if (data.error == 0) {
                    saveLog()
                    setTimeout(function () {
                            window.location.href="/special-schedul/index";
                    }, 1500);
                }else if(data.error == 3){
                    var str='';
                        str="<p>"+data.msg+"</p>"
                        str+='<table class="table table-striped table-bordered .table-responsive" border=1 width=580 ><tr><th>设备端活动名称</th><th>产品</th></tr>';
                    $.each(data.data,function(key,val){
                        str+="<tr><td align='center' >"+val.specialName+"</td><td align='center'>"
                        $.each(val.productList,function(k,v){
                            str = str+v+"<br/>"
                        })
                        str+="</td></tr>";
                    })
                    str+="</table>"
                    $("#tsModal .title").html(str);
                    $('#tsModal').modal()
                    $("form").find(".btn-success").removeAttr("disabled");
                }else {
                    alert(data.msg);
                    $("form").find(".btn-success").removeAttr("disabled");
                }
            },
            error : function(data) {
                $("form").find(".btn-success").removeAttr("disabled");
            }
        });
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
        $(".search-building").show();
        $(".add-building-file").hide();
        $(".add-building-file").removeClass("has-error");
        $(".add-building-file").next(".verify-result").removeClass("has-error").hide().html('');
        $(".add-building-file").find("#valierr").remove();
    } else {
        $(".search-building").hide();
        $(".add-building-file").show();
        $(".block-file").validation();
    }
}
//根据选择楼宇类型提交处理楼宇提交数据
function BuildingDate(obj)
{
    if (obj.val() == 1) {
        $(".add-building-file").find("input").val("");
        $("input[name='buildingIdArr[]']").prop('disabled',false);
        $(".add-building-file").find("#valierr").remove();
        if($("input[name='buildingIdArr[]']").length < 1){
            $("#tsModal #myModalLabel").text("提示框");
            $("#tsModal .title").html('请添加点位');
            $("#tsModal").modal();
            return false;
        }
    } else {
        if ($(".block-file").valid() == false) { return false;}
        $("input[name='buildingIdArr[]']").prop('disabled',true);

    }
}
/**
 * ajax上传文件
 * @author  wxz
 * @param   object   obj js对象
 */
function uploadBuildFile(obj) {
    var objID = $(obj).attr("id");
    // var oldFilePath = $(obj).next().val();
    var oldFilePath = '';
    if (fileValid(obj) == false){
        return false;
    }
    var formData = new FormData();
    formData.append("CouponSendTask[verifyFile]",$(obj)[0].files[0]);
    formData.append("oldFilePath",oldFilePath);
    $.ajax({
        type:'post',
        url:url+"equip-product-group-api/verify-file.html?"+verifyPassword,
        data:formData,
        dataType:'json',
        processData : false, // 使数据不做处理
        contentType : false, // 不要设置Content-Type请求头
        success:function(data){
            $('input[id="'+objID+'"]').parent().next(".verify-result").show();
            if (data.noExistsList) {
                fileValidFalse($('input[id="'+objID+'"]'));
                $(".verify-result").addClass("has-error").html("<label>验证反馈：</label>"+data.noExistsList);
                $('input[id="'+objID+'"]').parent().find("input[type='hidden']").attr("value", "");
            } else {
                $(".verify-result").removeClass("has-error");
                $(".verify-result").html('<label>验证反馈：</label>文件验证成功');
            }
            if (data.filePath != '') {
                $('input[id="'+objID+'"]').parent().find("input[type='hidden']").attr("value", data.filePath);
            }
            if (data.filePath == '') {
                //fileValidFalse($('input[id="'+objID+'"]'));
                $(".verify-result").addClass("has-error").html('<label>验证反馈：</label>文件验证失败，请检查文件格式是否正确');
                $('input[id="'+objID+'"]').parent().find("input[type='hidden']").attr("value", "");
            }
        },
        error : function(data) {
            //fileValidFalse($('input[id="'+objID+'"]'));
            $('input[id="'+objID+'"]').parent().find("input[type='hidden']").attr("value", "");
            $(".verify-result").addClass("has-error").html('服务器上传失败。');
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
function uploadFileClick(obj)
{
    $(obj).val('');
    $(obj).parent().removeClass("has-success").removeClass("has-error");
    $(obj).parent().find("#valierr").remove();
    $(obj).parent().next(".verify-result").hide().html('');
}
function fileValidFalse(obj){
    obj.parent().removeClass("has-success").addClass("has-error");
    if(obj.parent().find(".help-block").length < 1){
        obj.after('<span class="help-block" id="valierr">文件上传失败，请重新上传。</span>');
    }else{
        obj.parent().find("#valierr").text("验证失败，请重新上传。");
    }
}
function validForm() {
    if ($(".datetime").valid() == false|| $(".limit-condition").valid() == false) {
        return false;
    }
    if ($(".id-checkbox:checked").length < 1){
        $("#tsModal #myModalLabel").text("提示框");
        $("#tsModal .title").html('请选择单品');
        $("#tsModal").modal();
        return false;
    } else {
        $(".id-checkbox:not(:checked)").each(function(){
            var  inputElement = $(this).parent().parent().find("input[type='text']");
            $.each(inputElement, function(){
                $(this).attr("data-check", $(this).attr("check-type"));
                $(this).removeAttr("check-type");
            });
            inputElement.parent().removeClass("has-error");
            inputElement.parent().find("#valierr").remove();
        })
        if ($(".productList").valid() == false) {
            return false;
        }
    }

}
$("form").on("beforeSubmit", function(){
        uploadFile();
}).on('submit', function (e) {
        e.preventDefault();
});
//跳转到bootstrap3-validation验证出错的元素位置
function  jumpError() {
    if ($(".has-error").length > 0 ){
        var top = $(".has-error").offset().top - 50;
         scrollTo(0, top);
    }
}
function saveLog(){
        var type = 0;
        if($('#specialschedul-id').val() != ''){
            type = 1;
        }
        $.ajax({
              type : "get",
              url : '/special-schedul/save-log',
              data : {'type':type,'specialSchedulName':$('#specialschedul-special_schedul_name').val()},
              success : function(data){
              }
        });
}


/*******单品、臻选咖啡进行切换***
*************zhq******/
pdtCheck();
function pdtCheck(){
        $(".cfType").on("click",function(){
            productListValid();
            if ($(".productList").valid() == false) {
                jumpError();
                return false;
            }
            var _this=$(this);
            var $type_id=_this.attr("pid");
            if($type_id==0){
                if($(".specialSchedul").html()==""){
                    $(".specialSchedul2").hide();
                    $(".specialSchedul3").css({"height":"9rem","line-height":"9rem","text-align":"center"}).html("没有相关产品哦").show();
                }else{
                    $(".specialSchedul3,.specialSchedul2").hide();
                    $(".specialSchedul").show();
                }

            }else{

                if($(".specialSchedul2").html()==""){

                    $(".specialSchedul").hide();
                    $(".specialSchedul3").css({"height":"9rem","line-height":"9rem","text-align":"center"}).html("没有相关产品哦").show();
                }else{
                    $(".specialSchedul3,.specialSchedul").hide();
                    $(".specialSchedul2").show();

                }
            }
        })
}

function upImg(obj, type){
             //绑定图片上传事件
    var objArr = obj.find("table input[type=file]");
    if( type == 0){
        for(var i=0; i<objArr.length;i++) {

            obj.find("#imgdiv"+i).click(function(){
                $(this).prev().find("input[type='file']").click();
            });
            new uploadPreview({UpBtn: "cover_"+i, DivShow: "imgdiv"+i, ImgShow: "imgShow_"+i});
        }
    }else if(type == 1){
        for(var i=0; i<objArr.length;i++) {
            obj.find("#zimgdiv"+i).click(function(){
                $(this).prev().find("input[type='file']").click();
            });
            new uploadPreview({UpBtn: "zcover_"+i, DivShow: "zimgdiv"+i, ImgShow: "zimgShow_"+i});
        }
    }

}
//移除当前未勾选的咖啡列表的验证属性
function productListValid(){
    $(".id-checkbox:not(:checked)").each(function(){
        var  inputElement = $(this).parent().parent().find("input[type='text']");
        $.each(inputElement, function(){
            $(this).attr("data-check", $(this).attr("check-type"));
            $(this).removeAttr("check-type");
        });
        inputElement.parent().removeClass("has-error");
        inputElement.parent().find("#valierr").remove();
    })
}
 //点击批量修改按钮显示弹框
$(".updateProductList .logChange").click(function() {
    var batchUpdataTpl = $("#batchUpdataTpl").html();
    laytpl(batchUpdataTpl).render(activity, function(html){
        $("#batchUpdataModal .title").html(html);
    });
    isChangeActivityType($("#batchUpdataModal select"));
    $("#batchUpdataModal").modal();
    $("#batchUpdataModal #btn_submit").on("click", function() {
        updateActivity();
    })
});
$('#batchUpdataModal').on('hidden.bs.modal', function (e) {
    $(".updateProductList .logChange").prop("checked", false);
});



