$(function(){
    console.log("currentOrderid...",currentOrderid);
    currentOrderid = getQueryString("orderid");
    if(currentOrderid==null||currentOrderid=="") return false;
    orderContentTpl = $("#orderContentTpl").html();
    getMakingOrder();
    $("#completeOrder").on("click",function(){
        console.log("制作完成");
        $("#"+popUpWinCompleteOrder).show();
    });
    popUp(popUpWinCompleteOrder,"确定完成了？","确定完成","还没完成",completeConfirm,completeCancel);
    popUp(popUpWinConfirmOrder,"","强制完成","继续制作",confirmConfirm,confirmConfirmCancel);
    popUpTime("popUpChangeTime", changeTimeConfirm, changeTimeCancel,setDeliveryTime,2);
})
var popUpWinCompleteOrder = "completeOrderWin";
var currentOrderid = "test";
var popUpWinConfirmOrder = "confirmOrderWin";
var completeBtnActiveFlag = true;
var delayTime = 1000;
var data;
var orderData = [];
var orderContentTpl;
var refreshFlag = true;
function getMakingOrder(){
    /*
    var apiData = {"status":"success","data":{"id":"1","longitude":"116.453903","latitude":"40.021626","delivery_order_code":"W1808091643171309","take_out_order_id":"11","actual_fee":"23.80","delivery_status":"3","receiver":"王冰清","phone":"18888888888","area":"新市区","address":"新疆","created_at":"1533804197","prodctAccessList":[{"id":"19","product_id":"6","select_sugar":"1","product_name":"拿铁咖啡","coffee_static":"1","redeem_code":"9152848371"},{"id":"20","product_id":"7","select_sugar":"2","product_name":"摩卡","coffee_static":"1","redeem_code":"3152908922"},{"id":"21","product_id":"13","select_sugar":"2","product_name":"香草拿铁","coffee_static":"1","redeem_code":"3152908022"}]}}
    if(apiData.status=="success"){
                data = apiData.data;
                console.log("data:",data)
                if(data!={}){
                    orderData[0] = data;
                    var list = data.prodctAccessList;
                    if(list.length>0){
                        var sugarNameArr = ["无糖","半糖","全糖"];
                        var newList = list.map(function(item,index){
                            console.log("coffee_static..",item.coffee_static);
                            var newItem = {};
                            newItem.product_name = item.product_name;
                            newItem.product_sugar = sugarNameArr[Number(item.select_sugar)-1];
                            newItem.coffee_static = item.coffee_static;
                            newItem.redeem_code = Number(item.coffee_static)==2?item.redeem_code:"已完成";
                            return newItem;
                        })
                        data.prodctAccessList = newList;
                    }
                    laytpl(orderContentTpl).render(data, function(html){
                        $("#orderContent").html(html);
                    });
                    $(".complete-btn").show();
                    $(".refresh-btn").on("click",function(){
                        refreshOrder();
                    });
                } else {
                    $("#orderList").html("<p>无数据</p>");
                }
            } else {
                console.log(apiData.msg)
            }
            return false;
            */
    $.ajax({
        type: "POST",
        url:"/delivery/get-deli-detail",
        data: {"delivery_order_id":currentOrderid},
        dataType:"json",
        success:function(apiData){
            console.log("apiData:",apiData)
            refreshFlag = true;
            // var apiData = JSON.parse(apiData);
            if(apiData.status=="success"){
                data = apiData.data;
                // console.log("data:",data)
                if(JSON.stringify(data)!='{}'){
                    orderData[0] = data;
                    var list = data.prodctAccessList;
                    if(list.length>0){
                        var sugarNameArr = ["无糖","半糖","全糖"];
                        var newList = list.map(function(item,index){
                            // console.log("coffee_static..",item.coffee_static);
                            var newItem = {};
                            newItem.product_name = item.product_name;
                            newItem.product_sugar = sugarNameArr[Number(item.select_sugar)-1];
                            newItem.coffee_static = item.coffee_static;
                            newItem.redeem_code = Number(item.coffee_static)==2?item.redeem_code:"已完成";
                            return newItem;
                        })
                        data.prodctAccessList = newList;
                    }
                    laytpl(orderContentTpl).render(data, function(html){
                        $("#orderContent").html(html);
                        $("#orderRefreshing").hide();
                    });
                    $(".complete-btn").show();
                    $(".refresh-btn").on("click",function(){
                        refreshOrder();
                    });
                } else {
                    $("#orderList").html("<p>无数据</p>");
                }
            } else {
                console.log(apiData.msg)
            }
        },
        error:function(XMLHttpRequest,textStatus){
            console.log("fail:",textStatus)
            refreshFlag = true;
        }
    })
}

function refreshOrder(){
    console.log("刷新1");
    if(!refreshFlag) return false;
    refreshFlag = false;
    // $(".complete-btn").hide();
    data.prodctAccessList = [];
    laytpl(orderContentTpl).render(data, function(html){
        $("#orderContent").html(html);
        $("#orderRefreshing").show();
    });
    window.setTimeout(function(){
        getMakingOrder();
    },100);
}
//--------弹出窗--------------
function completeAction(popUpWinName){
    console.log(popUpWinName)
    if(!completeBtnActiveFlag) return false;
    completeBtnActiveFlag = false;
    console.log("确认完成了");
    $("#"+popUpWinName).find(".pop-tip").html("提交中...");
    // return false;
    $.ajax({
        type: "POST",
        url:"/delivery/make-complete-deli",
        data:{"delivery_order_id":currentOrderid},
        dataType:"json",
        success:function(apiData){
            console.log("apiData:",apiData)
            // var apiData = JSON.parse(apiData);
            if(apiData.status=="success"){
                $("#"+popUpWinName).find(".pop-tip").html("提交成功!");
                window.setTimeout(function(){
                    window.location.href = "/delivery/doing-order";
                },delayTime);
            } else {
                $("#"+popUpWinName).find(".pop-tip").html(apiData.msg);
                window.setTimeout(function(){
                    completeBtnActiveFlag = true;
                    $("#"+popUpWinName).hide();
                    $("#"+popUpWinName).find(".pop-tip").html("");
                },delayTime);
            }
        },
        error:function(XMLHttpRequest,textStatus){
            console.log("fail:",textStatus);
            $("#"+popUpWinName).find(".pop-tip").html(textStatus);
            window.setTimeout(function(){
                completeBtnActiveFlag = true;
                $("#"+popUpWinName).hide();
                $("#"+popUpWinName).find(".pop-tip").html("");
            },delayTime);
        }
    })
}
function completeConfirm(){
    if(!refreshFlag) return false;
    // 先强制刷新
    refreshOrder();
    var isRefreshInt = window.setInterval(function(){
        if(refreshFlag) {
            window.clearInterval(isRefreshInt);
            var resultNum = getResultData();
            console.log("resultNum..",resultNum);
            if(resultNum>0){
                completeBtnActiveFlag = true;
                $("#"+popUpWinCompleteOrder).hide();
                $("#"+popUpWinConfirmOrder).find(".pop-txt p").html("还有"+resultNum+"个没完成！");
                $("#"+popUpWinConfirmOrder).show();
            } else {
                completeAction(popUpWinCompleteOrder);
            }
        }
    },100);
}
function completeCancel(){
    if(!refreshFlag) return false;
    if(!completeBtnActiveFlag) return false;
    console.log("还没完成")
    $("#"+popUpWinCompleteOrder).hide();
}
function confirmConfirm(){
    completeAction(popUpWinConfirmOrder);
}
function confirmConfirmCancel(){
    if(!completeBtnActiveFlag) return false;
    console.log("取消")
    $("#"+popUpWinConfirmOrder).hide();
}
function getResultData(){
    var result = data.prodctAccessList.filter(function(item,index){
        return Number(item.coffee_static)==2;
    });
    return result.length;
}


var changTimeBtnActiveFlag = true;
var deliveryTime = "";

function changeTime(){
    deliveryTime = "";
    changTimeBtnActiveFlag = true;
    $("#popUpChangeTime").find(".pop-tip2").html("");
    $(".time-box").removeClass("time-box-selected");
    $(".time-box").addClass("time-box-default");
    $("#popUpChangeTime").show();
}
function setDeliveryTime(time) {
    deliveryTime = time;
    console.log("deliveryTime..",deliveryTime);
}
function changeTimeConfirm(){
    if(deliveryTime=="") {
        $("#popUpChangeTime").find(".pop-tip2").html("请选择估计送达时间!");
        return false;
    }
    if(!changTimeBtnActiveFlag) return false;
    changTimeBtnActiveFlag = false;
    console.log("修改送达时间")
    $("#popUpChangeTime").find(".pop-tip2").html("提交中...");

    $.ajax({
        type: "POST",
        url:"/delivery/save-expect-time",
        data:{"delivery_order_id":currentOrderid,"minute":deliveryTime},
        dataType:"json",
        success:function(apiData){
            console.log("apiData:",apiData)
            // var apiData = JSON.parse(apiData);
            if(apiData.status=="success"){
                $("#popUpChangeTime").find(".pop-tip2").html("提交成功");
                refreshOrder();
                setTimeout(function(){
                    $("#popUpChangeTime").hide();
                },1000)
            } else {
                $("#popUpChangeTime").find(".pop-tip2").html("提交失败");
                changTimeBtnActiveFlag = true;
            }
        },
        error:function(XMLHttpRequest,textStatus){
            console.log("fail:",textStatus);
            $("#popUpChangeTime").find(".pop-tip2").html("提交失败");
            changTimeBtnActiveFlag = true;
        }
    })
}
function changeTimeCancel(){
    if(!changTimeBtnActiveFlag) return false;
    $("#popUpChangeTime").hide();
}
