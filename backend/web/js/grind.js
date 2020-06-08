var buildingList = [];
var buildingArr = [];
// 添加楼宇模板
var addtpl = document.getElementById("building_add_template").innerHTML;
// 删除楼宇模板
var deltpl = document.getElementById("building_del_template").innerHTML;

$(function(){
    // 初始化编辑页面
    updateFun();
    // 初始化楼宇搜索数据
    getBuildingData(1);
});
// 初始化编辑页面
function updateFun(){
    // 如果是添加则不做任何操作
    if(parseInt(isNewRecord) != 0) {
        return '';
    }
    $('#grind-grind_type').attr('disabled','disabled');
    // 如果范围类型为楼宇
    if(parseInt(updateGrindType) == 2){
        $('.search-org').hide()
        $('.search-building').show()
        $(".addPreview tbody").html("");
        // 初始化编辑页面以保存的楼宇信息
        if(searchUpdateBuild.length > 0){
            initAddPreview(searchUpdateBuild)
        }
    }
}
/**
 * 初始化编辑页已保存的楼宇信息
 * @param  array buildingDate 以保存的楼宇信息
 */
function initAddPreview(buildingDate){
    laytpl(deltpl).render(buildingDate, function(html){
        $(".addPreview tbody").html(html);
    });
    if($(".addPreview tbody").find("tr").length>0){
        $(".addPreview tbody button").each(function(){
            var val=$(this).prev().val();
            $(".searchResult input[value="+val+"]").next().prop("disabled", true);
        })
    }
    $(".addPreview tbody").find("button").removeClass("add").addClass("delete").attr("onClick","deleteOne(this)")
    $(".addPreview .allDelete").removeAttr("disabled");
    $(".addPreview tbody").find(".glyphicon").removeClass("glyphicon-plus").addClass("glyphicon-minus");
}
//搜索
function buildingSearch(){
    getBuildingData(1);
    $("#addAll").removeAttr("disabled");
}
/**
 * 根据搜索条件获取楼宇数据
 * @param  {int} page     分页数
 */
function getBuildingData(page){
    var pageSize = 5;
    var buildingName  = $("input[name='buildingName']").val();
    var buildingType  = $("select[name='buildingType']").val();
    var equipmentCode = $("input[name='equipmentCode']").val();
    var csrf = $("#csrf").val();
    $.ajax({
         type: "POST",
         url: "/grind/search-build",
         data: {"name":buildingName, 'equipmentCode':equipmentCode,"build_type":buildingType,"page":page, "pageSize":pageSize, '_csrf':csrf},
         dataType: "json",
         async: false,
         success: function(data){
            getSearchResults(page,data,pageSize);
         }
    });
}
//将获取的楼宇信息展示出来
function getSearchResults(page,data,pageSize){
    if (data && data.totalCount >0) {
        $(".block-a .no-data").hide();
        $(".block-a .searchResult").show();
        $(".searchResult tbody").html("");
        laytpl(addtpl).render(data.buildArr,function(html){
            $(".searchResult tbody").html(html);
        });
        initButStatus();
        paging(data.totalCount, page, pageSize);
        $(".searchResult .SortId").each(function(index, value) {
            var serialNumber = (parseInt(page) - 1) * parseInt(pageSize) + parseInt(index) + 1;
            $(this).attr("data-text", serialNumber);
        })
    } else {
        $(".block-a .searchResult").hide();
        $(".block-a .no-data").show();
    }
}

/**
 * 修改范围类型
 */
function grindTypeChange(obj)
{
    var grindType = $(obj).val()
    if(grindType == 0){
        $('.search-org').hide()
        $('.search-building').hide()
    }else if(grindType == 1){
        $('.search-org').show()
        $('.search-building').hide()
    }else{
        $('.search-org').hide()
        $('.search-building').show()
    }
}
// 获取所有要添加的楼宇ID列表
function getBuilding(){
    $('.overflow').find('input').each(function(k,v){
        buildingList[k] = $(this).val()
    })
    return buildingList
}
/**
 * 全部添加时根据查询条件获取所有楼宇
 * @author  wxz
 * @version 2017-10-20
 * @param   object
 */
function addAllBuilding(obj) {
    var allBuildingData ;
    var buildingName = $("input[name='buildingName']").val();
    var buildingType = $("select[name='buildingType']").val();
    var equipmentCode = $("input[name='equipmentCode']").val();
    var csrf = $("#csrf").val();
    $.ajax({
        type: "post",
        url: "/grind/get-all-building-in-product-source-grind",
        data: { "name":buildingName,'equipmentCode':equipmentCode,"build_type":buildingType,"_csrf": csrf},
        dataType: "json",
        async: false,
        success: function(data){
            if(data != []){
                $(obj).attr("disabled",true);
                showAddList(data);
            }
        },
    });
    return allBuildingData;
}
/**
 * 展示全部添加的楼宇数据
 * @return {[type]} [description]
 */
function showAddList(data) {
    $(".addPreview tbody").html("");
    buildingArr = buildingArr.concat(data);
    buildingArr = uniqueArray(buildingArr, "id");
    initAddPreview(buildingArr);
    $(".searchResult table").find("button").attr("disabled",true);
}


/**
 * 删除单个楼宇
 */
function deleteOne(obj){
    $(obj).parent().parent().remove();
    buttonStatus(obj);
    $("#addAll").removeAttr("disabled");
}
//删除单个楼宇时初始化添加和删除按钮
function buttonStatus(obj){
    if($(".addPreview tbody").find("tr").length>0){
        var val=$(obj).prev().val();
        $(".searchResult input[value="+val+"]").next().removeAttr("disabled");
        if($(".searchResult .add").length==$(".searchResult .add:disabled").length){
            $("#batchAdd").attr("disabled",true);
        }else{
            $("#batchAdd").attr("disabled",false);
        }
    }else{
        $(".searchResult table").find("button").removeAttr("disabled");
        $(".addPreview").find("button").attr("disabled",true);
    }
}


//楼宇搜索结果分页
function paging(counts,page,pageSize){
    var pagecount= counts % pageSize == 0 ? counts / pageSize:counts/pageSize+1;
    var laypages = laypage({
        cont: $(".searchResult .pages"),
        pages: pagecount, //通过后台拿到的总页数
        curr: page,
        hash: true,
        first: false,
        last: false, //将尾页显示为总页数。若不显示，设置false即可
        prev: '&laquo;', //若不显示，设置false即可
        next: '&raquo;', //若不显示，设置false即
        jump: function(obj,first){
            if(!first){
                getBuildingData(obj.curr);
                window.location.hash = "#searchResult";
            }
        }
    })
}

/**
 * 单个添加楼宇
 * @param {[type]} obj [description]
 */
function addBuilding(obj){
    $('#buidlingVerify').hide()
    var buildingItem = $(obj).parent().parent("tr").html();
    var html="<tr>"+buildingItem+"</tr>";
    $(".addPreview table").find("tbody").append(html);
    $(obj).attr("disabled",true);
    $(".addPreview tbody").find("button").removeClass("add").addClass("delete").attr("onClick","deleteOne(this)");
    $(".addPreview tbody").find(".glyphicon").removeClass("glyphicon-plus").addClass("glyphicon-minus");
    $(".addPreview tbody").find("input[type=hidden]").prop("disabled",false).attr("name","buildingIdArr[]");
    $(".allDelete,.addPreview .btn-success").removeAttr("disabled");
    if($(".searchResult .add").length==$(".searchResult .add:disabled").length){
        $("#batchAdd").attr("disabled",true);
    }else{
        $("#batchAdd").removeAttr("disabled");
    }
}
/**
 * 
 * @return {[type]} [description]
 */
function initButStatus(){
    $(".addPreview .delete").each(function(){
        var val=$(this).prev().val();
        if($(".searchResult input[value="+val+"]")){
            $(".searchResult input[value="+val+"]").next().attr("disabled",true);
        }
    });
    if($(".searchResult .add").length == $(".searchResult .add:disabled").length){
            $("#batchAdd").attr("disabled",true);
    }else{
        $("#batchAdd").removeAttr("disabled");
    }
}

//批量添加
function batchAddBuilding(obj){
    $('#buidlingVerify').hide()
    var html = null;
    $(".searchResult tbody tr").each(function(){
        if ($(this).find("button").attr("disabled")!="disabled") {
            var tr = "<tr>"+$(this).html()+"</tr>";
            html += tr;
        }
    });
    $(".addPreview table").find("tbody").append(html);
    $(obj).parents(".searchResult table").find("button").attr("disabled",true);
    $(".addPreview tbody").find("button").removeClass("add").addClass("delete");
    $(".addPreview tbody").find(".glyphicon").removeClass("glyphicon-plus").addClass("glyphicon-minus");
    $(".addPreview tbody").find("input[type=hidden]").prop("disabled",false).attr("name","buildingIdArr[]");
    $(".allDelete,.addPreview .btn-success").removeAttr("disabled");

}
//批量删除
function allDelete(obj){
    $(obj).parents(".addPreview").find("tbody").html("");
    $(".searchResult table").find("button").removeAttr("disabled");
    $(obj).attr("disabled",true);
    $(".addPreview .btn-success").attr("disabled",true);
    $("#addAll").removeAttr("disabled");
    buildingList = [];
    buildingArr = [];
};
// 表单提交
function formSubmit(){
    var grindType = $('#grind-grind_type').val()
    if(grindType == 2){
        buildingList = getBuilding()
        if(buildingList.length <= 0 ){
            $('#buidlingVerify').show()
            return false;
        }
        $('#grind-buildinglist').val(buildingList);
    }
    $("form").submit();
}