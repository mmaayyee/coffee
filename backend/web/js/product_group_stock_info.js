var num = 0;
var stockInfoData = null;
var stockList = null;
if($("#productgroupstockinfo-equip_type_id").val())
{
    getStockDetail();
}
//添加数据
// var upDate = null;
//修改数据
// var upDate = {"id":12,"product_group_stock_name":"产品组料仓物料信息名称","equip_type_id":"2","equip_type_name":"12盎司设备","stockList":[{"stock_code":"G","stock_volume_bound":1000,"materiel_id":3,"blanking_rate":1.2,"warning_value":800,"bottom_value":100},{"stock_code":"1","stock_volume_bound":1000,"materiel_id":5,"blanking_rate":2.4,"warning_value":800,"bottom_value":100}]}
function getStockInfo() {
    var equipTypeID = $("#productgroupstockinfo-equip_type_id").val();
    $.ajax({
        type: "GET",
        url: "/product-group-stock-info/get-equip-type-stock-info",
        async: false,
        data: {'equipTypeID': equipTypeID},
        dataType: "json",
        success: function(data){
           if(data){
               stockInfoData = data;
               if (upDate && upDate.equip_type_id == equipTypeID) {
                   stockList = upDate.stockList;
                   num = upDate.stockList.length;
               } else {
                   stockList = null;
               }
            }
        }
    });
}
var addStockInfo = function() {
    num++;
    var materialStockNum = $(".material-stock").length;
    if (stockInfoData && materialStockNum < stockInfoData.equipTypeStockList.length) {
        var dataList = {"data" : stockInfoData, "num" : num};
        var addStockInfoTpl = $("#addStockInfoTpl").html();
        laytpl(addStockInfoTpl).render(dataList, function(html) {
            $("#stockInfo table tbody").append(html);
        });
        $("#stockInfo").validation();
        $("input[name*=top_value]").change(function() {
            $(this).parent().parent().find("input[name*=bottom_value]").trigger("blur");
        });
    } else {
       $("#tsModal").modal();
       $("#tsModal .title").text("料仓添加的数量已达上限~");
    }
}
var delStockInfo = function(obj) {
    $(obj).parent().parent().remove();
}
var stockCodechangeStatus = 0
//rule验证，判断料仓号是否相同
var stockCodeChange = function() {
    var changeStatus = 0
    stockCodechangeStatus = 0
    $('.stock_code_change').each(function(key,val){
        changeStatus = 0;
        $('.stock_code_change').each(function(k,v){
            if($(v).val() == $(val).val() && $(v).parent().parent().index() != $(val).parent().parent().index()){
                changeStatus = 1;
            }
        })
        if(changeStatus == 1){
            if($(val).parent().hasClass('has-error')){
            }else{
                $(val).parent().addClass('has-error')
                str = "<span class=help-block id=valierr >料仓已存在。</span>"
                $(val).after(str)
            }
            stockCodechangeStatus = 1
        }else{
            $(val).parent().removeClass('has-error')
            $(val).next("span").remove()
        }
    })
}
var sugarUniqueStatus = 0
//rule验证 汤料不可多选
var sugarUnique = function(obj) {
    // var changeStatus = 0
    // sugarUniqueStatus = 0
    // $('.materialTypeSugarUnique').each(function(key,val){
    //     if($(val).val() == 8){
    //         changeStatus = 0;
    //         $('.materialTypeSugarUnique').each(function(k,v){
    //             if($(v).val() == 8 && $(v).parent().parent().index() != $(val).parent().parent().index()){
    //                 changeStatus = 1;
    //             }
    //         })
    //         if(changeStatus == 1){
    //             if($(val).parent().hasClass('has-error')){
    //             }else{
    //                 $(val).parent().addClass('has-error')
    //                 str = "<span class=help-block id=valierr >糖料不能多选。</span>"
    //                 $(val).after(str)
    //             }
    //             sugarUniqueStatus = 1
    //         }else{
    //             $(val).parent().removeClass('has-error')
    //             $(val).next("span").remove()
    //         }
    //     }
    // })
    // if($(obj).val() != 8){
    //     $(obj).parent().removeClass('has-error')
    //     $(obj).next().remove()
    // }
}

$(function() {
    $("#productgroupstockinfo-equip_type_id").change(function() {
        getStockDetail();
    });
    $(".btn-success").on("click", function() {
        stockCodeChange()
        // sugarUnique()
        if ($("#w0").valid() == false || stockCodechangeStatus == 1) {
            return false;
        } else {
            $("#w0").submit();
        }
    });
    $.extend($.fn.validation.defaults.validRules.push(
        {name: 'plus', validate: function(value) {return (!/^[+]{0,1}(\d+)$|^[+]{0,1}(\d+\.\d+)$/.test(value));}, defaultMsg: '请输入正数。'},
        {name: 'int', validate: function(value) {return (!/^(|0|[1-9]\d*)$/.test(value));}, defaultMsg: '请输入整数。'},
        {name: 'compare', validate: function(value,err) {
            if( parseFloat($(this).parent().parent().find("input[name*=bottom_value]").val()) > parseFloat($(this).parent().parent().find("input[name*=top_value]").val())){
                    return true;
            }
        }, defaultMsg: '容量下限值不能大于容量上限值。' }
    ));
});
function getStockDetail()
{
    num = 0;
    getStockInfo();
    if (stockInfoData && stockInfoData.equipTypeStockList.length != 0) {
        var dataList = {"data" : stockInfoData, "stockList" : stockList, "num" : num};
        var stockInfoTpl = $("#stockInfoTpl").html();
        laytpl(stockInfoTpl).render(dataList, function(html) {
              $("#stockInfo").html(html);
        });
        $("#stockInfo").validation();
        $("input[name*=top_value]").change(function() {
            $(this).parent().parent().find("input[name*=bottom_value]").trigger("blur");
        });
    } else {
        $("#stockInfo").html("");
    }
}