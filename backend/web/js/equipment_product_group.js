//初始化存放勾选的单品数组
var groupCoffeNameArr = {};
//初始化设备标签数组
var equipmentLabelArr = [];
var groupCoffeeListTpl = $("#groupCoffeeListTpl").html();
var groupCoffeeList2Tpl = $("#groupCoffeeList2Tpl").html();
// var equipmentLabelTpl = $("#equipmentLabelTpl").html();
var tagNum = 0;
var ProductNmu = null;
var selectData='';
$(function(){
    //隐藏楼宇功能和设备标签功能
    $(".field-equipmentproductgroup-group_status, .add-building-file, .search-building, .equipment-label").hide();
    $(".select2-search").validation();

    if ($("#equipmentproductgroup-pro_group_stock_info_id").val()) {
        changeStockInfo($("#equipmentproductgroup-pro_group_stock_info_id"));
    }
    $("form").on("beforeSubmit", function(){
        BuildingDate($("#equipmentproductgroup-build_type_upload"));
        if ($(".select2-search").valid() == false || validForm()== false || $(".has-error").length > 0) {
            $("form").find(".btn-success").removeAttr("disabled");
            jumpError();
            return false;
        } else {
            uploadFile();
        }
    }).on('submit', function (e) {
            e.preventDefault();
    });

});
/**
 * 选择不同选择产品组料仓信息ajax请求不同产品
 * @author  wxz
 * @version 2017-09-12
 * @param   object   obj js对象
 */

function changeStockInfo(obj){
    var stockInfoId   = $(obj).val();
    var proGroupId    = $('.groupId').val();
    var csrf = $("#csrf").val();
    groupCoffeNameArr = {};
        $.ajax({
         type: "POST",
         url: "/equipment-product-group/search-product",
         data: {'proGroupStockId':stockInfoId, 'proGroupId': proGroupId, '_csrf':csrf},
         dataType: "json",
         async:false,
         success: function(data){
              if (!isEmptyAttr(data)) {
                $(".product-list3").hide();
                //区分首次加载产品是否有臻选咖啡,1为臻选,0为普通
                if(data[0]){
                    laytpl(groupCoffeeListTpl).render(data[0], function(html){
                        $(".product-list").html(html).show();
                    });
                    upImg($(".product-list"), 0);
                    if(data[1]){
                        laytpl(groupCoffeeList2Tpl).render(data[1], function(html){
                            $(".product-list2").html(html).hide();

                        });
                        upImg($(".product-list2"), 1);
                    }

                    $("#cfNormal").prop("checked",true);
                }else if(data[1]){
                        laytpl(groupCoffeeList2Tpl).render(data[1], function(html){
                            $(".product-list2").html(html).show();
                            $("#cfZselect").css("checked",true);
                        });
                    upImg($(".product-list2"), 1);
                }else{
                    $(".product-list3").css({"height":"9rem","line-height":"9rem","text-align":"center"}).html("没有相关产品哦").show();
                }
                initProductViwe();
                $(obj).blur();

            }
         }
    });
}

function initProductViwe(){
    //延迟加载图片
    $("img.lazy").lazyload();
    $(".id-checkbox").each(function() {
        addCoffeNameArr(this);
    });
    $(".id-checkbox").on('click', function(){
        addCoffeNameArr(this);
    })
    $(":checkbox[data-check]").click(function () {
        var target = $(this).attr("data-check");
        $(this).parents("table").find(target).prop("checked", $(this).prop("checked"));
        $(this).parents("table").find(target).each(function(){
            addCoffeNameArr(this);
        });
    });
    if (!isEmptyAttr(groupCoffeNameArr)) {
        updateLabelProduct(equipLabelList);
    }
    $(".product-list").validation({reqmark:false});
    $(".product-list2").validation({reqmark:false});
    $(".is-choose-sugar input:checked").each(function(){
        isChooseSugar($(this));
    });
    tagNum = $(".label-group > .form-inline").length;

    $(".field-equipmentproductgroup-group_status, .add-building-file, .equipment-label").show();
    addBuildingType($("#equipmentproductgroup-build_type_upload"));
}
/*
 *图片上传
 */
function upImg(obj, type){
             //绑定图片上传事件

    var objArr = obj.find("table input[type=file]");
    if( type == 0){
        for(var i=0; i<objArr.length;i++) {
            if (obj.find("#cover_"+i).length>0) {
                new uploadPreview({UpBtn: "cover_"+i, DivShow: "imgdiv"+i, ImgShow: "imgShow_"+i});
            }
            if (obj.find("#covera_"+i).length>0) {
                new uploadPreview({UpBtn: "covera_"+i, DivShow: "imgdiva"+i, ImgShow: "imgShowa_"+i});
            }
            if (obj.find("#coverb_"+i).length>0) {
                new uploadPreview({UpBtn: "coverb_"+i, DivShow: "imgdivb"+i, ImgShow: "imgShowb_"+i});
            }
            (function(i){
                obj.find("#imgdiv"+i).click(function(){
                    $(this).prev().find("input[type='file']").click();
                });
                obj.find("#imgdiva"+i).click(function(){
                    $("#product_img_error"+i).hide();
                    $(this).prev().find("input[type='file']").click();
                });
                obj.find("#imgdivb"+i).click(function(){
                    $("#product_img_errors"+i).hide();
                    $(this).prev().find("input[type='file']").click();
                });
            })(i)

        }
    }else if(type == 1){
        for(var i=0; i<objArr.length;i++) {
            if (obj.find("#zcover_"+i).length>0) {
                new uploadPreview({UpBtn: "zcover_"+i, DivShow: "zimgdiv"+i, ImgShow: "zimgShow_"+i});
            }
            if (obj.find("#zcovera_"+i).length>0) {
                 new uploadPreview({UpBtn: "zcovera_"+i, DivShow: "zimgdiva"+i, ImgShow: "zimgShowa_"+i});
            }
            if (obj.find("#zcoverb_"+i).length>0) {
                 new uploadPreview({UpBtn: "zcoverb_"+i, DivShow: "zimgdivb"+i, ImgShow: "zimgShowb_"+i});
            }
            (function(i){
                obj.find("#zimgdiv"+i).click(function(){
                    $(this).prev().find("input[type='file']").click();
                });
                obj.find("#zimgdiva"+i).click(function(){
                    $("#product_imgs_error"+i).hide();
                    $(this).prev().find("input[type='file']").click();
                });
                obj.find("#zimgdivb"+i).click(function(){
                    $("#product_imgs_errors"+i).hide();
                    $(this).prev().find("input[type='file']").click();
                });
            })(i)
        }
    }
}
//是否选糖
function isChooseSugar(obj) {
     if (obj.val() == 1) {
        obj.parent().parent().parent().find(".choose-sugar").show();
        obj.parent().parent().parent().find(".choose-sugar input[type=text]").attr("check-type", "required plus");
     } else {
        obj.parent().parent().parent().find(".choose-sugar").hide();
        obj.parent().parent().parent().find(".choose-sugar input[type=text]").removeAttr("check-type");
        obj.parent().parent().parent().find(".form-group").removeClass("has-error");
        obj.parent().parent().parent().find(".form-group #valierr").remove();
     }
}

//根据单品的勾选状态添加一个数组groupCoffeNameArr
function addCoffeNameArr(obj){
    var key =  $(obj).parent().parent().data("key");
    if ($(obj).is(":checked")) {
        var  inputElement = $(obj).parent().parent().find("input[type='text']");
        if (inputElement.attr("data-check") != "") {
            $.each(inputElement, function(){
                $(this).attr("check-type", $(this).attr("data-check"));
            });
        }
        groupCoffeNameArr[key] = $(obj).parent().parent().find("input.groupCoffeName").val();
    } else{
        delete groupCoffeNameArr[key];
    }
    if ($(".label-group").html()){
        equipmentLabelArr = [];
        updateLabelProduct(equipmentLabelArr);
    }

};

//判断关联数组的是否为空
function isEmptyAttr(obj){
    for(var key in obj){
        return false;
    };
    return true;
};
//添加设备标签
function addTag(){
    if (isEmptyAttr(groupCoffeNameArr)) {
        $("#tsModal").modal();
        $("#tsModal .title").text("请选择单品");
    } else{
        if($(".label-group > .form-inline").length < 7){
            tagNum++;
            var tagData = {"groupCoffeNameArr":groupCoffeNameArr, "tagNum":tagNum };
        } else {
            $("#tsModal").modal();
            $("#tsModal .title").text("设备标签最多添加7个");
        }
    }
}
//根据勾选的单品更新设备标签的产品
function updateLabelProduct(equipmentLabels){
    $(".label-group .form-inline").each(function(){
        saveLabelData($(this).find("button"));
    });
    var tagDate = {"groupCoffeNameArr":groupCoffeNameArr, "tagNum":tagNum, "equipmentLabelArr":equipmentLabels};

    $(".label-group > .form-inline").each(function(){
        $(this).find("#productNum").text('('+$(this).find("input:checked").length+')');
    });
    $(".label-group").validation({reqmark:false});
}
//获取设备标签中的产品数量
function getProductNum(obj) {
    var productNum = $(obj).parent().parent().find(".product-checkbox:checked").length;
    $(obj).parent().parent().parent().find("#productNum").text("("+productNum+")");
}
//删除设备标签
function delTag(obj){
    removeByValue(equipmentLabelArr,$(obj).parent().parent().attr("id"));
    $(obj).parent().parent().remove();
}
//修改设备标签
function reviseTag(obj){
    $(obj).parent().parent().find("div.checkbox").show();
    $(obj).attr({"class":"btn btn-warning btn-sm", "onclick": "keepTag(this)"}).text("保存");
}
//根据数组对象中的属性值删除对象
function removeByValue(arr,value) {//数组，属性，属性值
    for(var j=0;j<arr.length;j++){
        if(arr[j].label_id == value){
            arr.splice(j,1);
            break;
        }
    }
}
//根据数组对象中的属性值修改对象
function keepByValue(arr,value) {//数组，属性，属性值
    for(var j=0;j<arr.length;j++){
        if(arr[j].label_id == value){
            arr.splice(j,1);
            break;
        }
    }
}
function saveLabelData(obj, labelProductIdList) {
    var labelId = obj.parent().parent().attr("id");
    var labelName = obj.parent().parent().find("#labelName").val();
    var checkProductNum = obj.parent().parent().find("#productNum").text();
    var labelSort = obj.parent().parent().find("#labelSort").val();
    var labelProductIdList = [];
    obj. parent().parent().find("input:checkbox:checked").each(function() {
        labelProductIdList.push(Number($(this).val()));
    });
    var equipmentLabel = {"label_id":labelId ,"label_name":labelName, "checkProductNum":checkProductNum, "sort":labelSort, "labelProductIdList":labelProductIdList};
    equipmentLabelArr.push(equipmentLabel);
}

/**
 * 添加产品组时ajax请求楼宇
 * @author  wxz
 * @version 2017-10-11
 * @param   object   page:页数   pageSize:条数
 */
var getBuildingData = function(page, pageSize){
    var groupStockInfo = $("#equipmentproductgroup-pro_group_stock_info_id").val(); // 获取产品组料仓信息ID；
    var buildingName = $("input[name='buildingName']").val();
    var buildingType = $("select[name='buildingType']").val();
    var orgRange = $("select[name='orgRange']").val();
    var csrf = $("#csrf").val();
    var branch = $("select[name='branch']").val();
    var buildTypeUpload = $("#equipmentproductgroup-build_type_upload").val();
    var resultData ;
    if (!groupStockInfo || buildTypeUpload == 1) {
        return false;
    }
    $.ajax({
         type: "POST",
         url: "/equipment-product-group/search-build",
         data: { "_csrf":csrf, 'pro_group_stock_info_id': groupStockInfo, "name":buildingName,"build_type":buildingType,"org_id":branch,'orgRange':orgRange,"page":page,"pageSize":pageSize},
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
 * 选择分公司的搜索条件显示范围搜索
 */
function changeBranch(obj) {
    if ($(obj).val() != ''){
        $("#org_range").show();
    }
}

/**
 * 添加产品组搜索楼宇时获取所有楼宇数据
 * @author  wxz
 * @version 2017-10-20
 * @param   object
 */
var getAllBuildingData = function(){
    var allBuildingData ;
    var buildingName = $("input[name='buildingName']").val();
    var buildingType = $("select[name='buildingType']").val();
    var branch = $("select[name='branch']").val();
    var orgRange = $("select[name='orgRange']").val();
    var csrf = $("#csrf").val();
    var groupStockInfoId = $("#equipmentproductgroup-pro_group_stock_info_id").val(); // 获取产品组料仓信息ID；
    $.ajax({
        type: "post",
        url: "/equipment-product-group/get-all-building-in-product",
        data: { "_csrf": csrf, "orgRange": orgRange, 'pro_group_stock_info_id': groupStockInfoId, "name":buildingName,"build_type":buildingType,"org_id":branch},
        dataType: "json",
        async: false,
        success: function(data){
            if(data != []){
                allBuildingData =data;
            }
        },
    });
    return allBuildingData;
}

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
    // 多条进行json传输、数组
    var formData = $("form").serializeObject();
    var uploadFlag = true;
    var k=0;
    var j=0;
    $("input[type='checkbox']").each(function (i,obj) {
        if($(this).prop("checked")){
            var groupItem = $(this).parent().parent();
            var itemId = groupItem.data('key');
            var coverInput = groupItem.find('input[name="groupCoffeeList['+itemId+'][group_coffee_new_cover]"]');
            var flowchartInput = groupItem.find('input[name="groupCoffeeList['+itemId+'][group_coffee_flowchart]"]');
            var coverInputValue = coverInput.val();
            var flowchartInputValue = flowchartInput.val();
            var coverImg = coverInput.prev().find('img').attr('src');
            var flowchartImg = flowchartInput.prev().find('img').attr('src');
            if (coverInputValue.length==0){
                if(coverImg.indexOf("http") == -1 && coverImg.indexOf("https") == -1){//
                    coverInput.next().show();
                    coverInput.next().html('请上传对应饮品封面图1');
                    uploadFlag = false;
                    $("form").find(".btn-success").removeAttr("disabled");
                }
            }
            if (flowchartInputValue.length==0){
                 if(flowchartImg.indexOf("http") == -1 && flowchartImg.indexOf("https") == -1){//
                    flowchartInput.next().show();
                    flowchartInput.next().html('请上传对应饮品流程图1');
                    uploadFlag = false;
                    $("form").find(".btn-success").removeAttr("disabled");
                }
            }
        }
    });
    if(!uploadFlag) return false;
    formData=new FormData($('#w0')[0]);
    console.log(isCopy);
    if (isCopy==1) {
        formData.delete('EquipmentProductGroup[product_group_id]');
    }
    $.ajax({
        url: url+"equip-product-group-api/equip-product-group-create.html?"+verifyPassword,
        secureuri: false,
        dataType: 'json',
        type: 'post',
        // fileElementId: fileIdList,
        data: formData,
        processData: false,
        contentType: false,
        success : function(data) {
            if(data.ret == 1){
                saveLog()
                setTimeout(function () {
                    window.location.href="/equipment-product-group/index";
                }, 1500);
            } else {
                $("form").find(".btn-success").removeAttr("disabled");
            }
            if (data.repeatGroupName) {
                $(".submit-error").html("分组名称重复，请刷新重试");
            }
        },
        error : function(data) {
            $("form").find(".btn-success").removeAttr("disabled");
            $(".submit-error").html('服务器上传失败。');
        }
    });
}

/**
 * 新增bootstrap3-validation.js 验证方法
 * */
$.extend($.fn.validation.defaults.validRules.push(
    {
        name: 'plus',
        validate: function(value) {
            return (!/^[+]{0,1}(\d+)$|^[+]{0,1}(\d+\.\d+)$/.test(value));
        },
        defaultMsg: '请输入正数。'
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
//根据选择楼宇类型提交处理楼宇提交数据
function BuildingDate(obj)
{
    if (obj.val() == 2) {
        $(".add-building-file").find("input").val("");
        $("input[name='buildingIdArr[]']").prop('disabled',false);
        $(".add-building-file .form-group").removeClass("has-error");
        $(".add-building-file").find("#valierr").remove();
    }else if(obj.val() == 1){
        $("input[name='buildingIdArr[]']").prop('disabled',true);
    }else{
        $("input[name='buildingIdArr[]']").prop('disabled',true);
        $(".add-building-file .form-group").removeClass("has-error");
        $(".add-building-file").find("#valierr").remove();
    }
}
function validForm() {
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
            })
            inputElement.parent().removeClass("has-error");
            inputElement.parent().find("#valierr").remove();
        })
        if ($(".product-list").valid() == false||$(".product-list2").valid() == false) {
            return false;
        }
    }
}
//跳转到bootstrap3-validation验证出错的元素位置
function  jumpError() {
    if ($(".has-error").length > 0 ){
        var top = $(".has-error").offset().top - 50;
         scrollTo(0, top);
       }
}
function labelSortDistinct(obj) {
    var labelSortAttr = [];
    var labelSortValue = $(obj).parent().parent().siblings().find("#labelSort");
    $.each(labelSortValue, function(){
        if ($(this).val()) {
            labelSortAttr.push($(this).val());
        };
    });
    if (labelSortAttr.length > 0){
        if ($.inArray($(obj).val(), labelSortAttr) != -1) {
            $("#tsModal #myModalLabel").text("提示框");
            $("#tsModal .title").html($(obj).val()+'已存在');
            $("#tsModal").modal();
            $(obj).val("");
        }
    }
}
function saveLog(){
    var type = 0;
    if($(".groupId").val() != ''){
        type = 1;
    }
    $.ajax({
            type : "get",
            url : '/equipment-product-group/save-log',
            data : {'type':type,'groupName':$('#equipmentproductgroup-group_name').val()},
            success : function(data){

            }
    });
}
/*******单品、臻选咖啡进行切换***
*************zhq******/
pdtCheck();
function pdtCheck(){
        $(".pdtType input").on("click",function(){
            productListValid();
            if ($(".product-list").valid() == false || $(".product-list2").valid() == false) {
                jumpError();
                return false;
            }
            var _this=$(this);
            var $type_id=_this.attr("pid");
            if($type_id==0){
                if($(".product-list").html()==""){
                    $(".product-list2").hide();
                    $(".product-list3").css({"height":"9rem","line-height":"9rem","text-align":"center"}).html("没有相关产品哦").show();
                }else{
                    $(".product-list3,.product-list2").hide();
                    $(".product-list").show();
                }

            }else{

                if($(".product-list2").html()==""){

                    $(".product-list").hide();
                    $(".product-list3").css({"height":"9rem","line-height":"9rem","text-align":"center"}).html("没有相关产品哦").show();
                }else{
                    $(".product-list3,.product-list").hide();
                    $(".product-list2").show();

                }

            }




        })
}
//移除当前未勾选的咖啡列表的验证属性
function productListValid(){
    $(".id-checkbox:not(:checked)").each(function(){
        var  inputElement = $(this).parent().parent().find("input[type='text']");
        $.each(inputElement, function(){
            $(this).attr("data-check", $(this).attr("check-type"));
            $(this).removeAttr("check-type");
        })
        inputElement.parent().removeClass("has-error");
        inputElement.parent().find("#valierr").remove();
    })
}
