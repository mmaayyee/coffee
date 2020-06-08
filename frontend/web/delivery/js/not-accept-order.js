// var apiData = {"status":"success","data":[{"take_out_code":"W1808091643171309","actual_fee":"23.80","receiver":"王冰清","phone":"18888888888","area":"新市区","address":"新疆","created_at":"1533804197"},{"take_out_code":"W1808091645249591","actual_fee":"23.80","receiver":"王冰清","phone":"18888888888","area":"新市区","address":"新疆","created_at":"1533804324"},{"take_out_code":"W1808091646034787","actual_fee":"23.80","receiver":"王冰清","phone":"18888888888","area":"新市区","address":"新疆","created_at":"1533804362"}]}

$(function(){
    console.log("popUpWinAcceptOrder..",popUpWinAcceptOrder)
    initNav(0);
    getNoAcceptOrder();
    $("#refreshData").on("click",function(){
        refreshOrderList();
    });
    //--------弹出窗--------------
    popUpTime(popUpWinAcceptOrder, acceptOrderConfirm, acceptOrderCancel,setDeliveryTime);
    // $("#"+popUpWinAcceptOrder).show();
})
var popUpWinAcceptOrder = "acceptOrderWin";
var currentOrderid = "";
var acceptOrderBtnActiveFlag = true;
var delayTime = 1000;
var orderData;
var deliveryTime = "";
var countDownTime = 180; //单位是秒
var startTime = new Date().getTime();
var countDownInterval = window.setInterval(function(){
    var nowTime = new Date().getTime();
    if((nowTime-startTime)>countDownTime*1000){
        refreshOrderList();
    }
},500);
function refreshOrderList(){
    console.log("刷新");
    startTime = new Date().getTime();
    $("#orderList").html("<p>数据加载中</p>");
    getNoAcceptOrder();
}
function getNoAcceptOrder(){
    $.ajax({
        type: "POST",
        url:"/delivery/not-accept-order",
        dataType:"json",
        success:function(apiData){
            var orderListTpl = $("#orderListTpl").html();
            // 以下为测试代码
            // var apiData = {"status":"success","data":{"data_list":[{"delivery_order_id":"52","delivery_order_code":"W1810221714534055","delivery_order_status":2,"delivery_cost":"0.00","actual_fee":0,"order_cups":2,"receiver":"高永立","phone":"13501167215","area":"朝阳区来广营北京国生汽车销售服务有限公司","address":"哈哈","longitude":"116.453903","latitude":"40.021626","create_time":1540199693,"accept_time":1540199693,"reason_name":null,"product_name":["鲜萃香草奶茶","拿铁咖啡","卡布奇诺"]},{"delivery_order_id":"52","delivery_order_code":"W1810221714534055","delivery_order_status":2,"delivery_cost":"0.00","actual_fee":0,"order_cups":2,"receiver":"高永立","phone":"13501167215","area":"朝阳区来广营北京国生汽车销售服务有限公司","address":"哈哈","longitude":"116.453903","latitude":"40.021626","create_time":1540199693,"accept_time":1540199693,"reason_name":null,"product_name":["巧克力","香草拿铁咖啡","冰拿铁"]}],"list_count":{"wait_list_count":"11","accept_list_count":"0","complete_list_count":"0"}}}
            console.log("apiData:",apiData);
            // var apiData = JSON.parse(apiData);
            if(apiData.status=="success"){
                orderData = apiData.data.data_list;
                console.log("orderData:",orderData);
                setCount(apiData.data);
                if(orderData.length>0){
                    orderData.forEach(function(item){
                        var addressSplit = item.area.split(" ");
                        item.userAddress = (addressSplit.length>1?" "+addressSplit[1]:"")+addressSplit[0]+item.address;
                    });
                    laytpl(orderListTpl).render(orderData, function(html){
                        $("#orderList").html(html);
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
        }
    })
}
function setDeliveryTime(time) {
    deliveryTime = time;
    console.log("deliveryTime..",deliveryTime);
}
function acceptOrderConfirm(){
    if(deliveryTime=="") {
        $("#"+popUpWinAcceptOrder).find(".pop-tip2").html("请选择估计送达时间!");
        return false;
    }
    if(!acceptOrderBtnActiveFlag) return false;
    acceptOrderBtnActiveFlag = false;
    console.log("提交预估时间");
    $("#"+popUpWinAcceptOrder).find(".pop-tip2").html("提交中...");

    $.ajax({
        type: "POST",
        url:"/delivery/save-expect-time",
        data:{"delivery_order_id":currentOrderid,"minute":deliveryTime},
        dataType:"json",
        success:function(apiData){
            console.log("apiData:",apiData)
            // var apiData = JSON.parse(apiData);
            if(apiData.status=="success"){
                confirmOrder();
            } else {
                $("#"+popUpWinAcceptOrder).find(".pop-tip2").html(apiData.msg);
                refreshOrderList();
                // alert(apiData.msg);
                window.setTimeout(function(){
                    acceptOrderBtnActiveFlag = true;
                    $("#"+popUpWinAcceptOrder).hide();
                    $("#"+popUpWinAcceptOrder).find(".pop-tip2").html("");
                },preRefreshTime);
            }
        },
        error:function(XMLHttpRequest,textStatus){
            console.log("fail:",textStatus);
            $("#"+popUpWinAcceptOrder).find(".pop-tip2").html("提交失败");
            acceptOrderBtnActiveFlag = true;
        }
    })
}
function confirmOrder(){
    $.ajax({
        type: "POST",
        url:"/delivery/receiv-order",
        data:{"delivery_order_id":currentOrderid},
        dataType:"json",
        success:function(apiData){
            console.log("apiData:",apiData)
            // var apiData = JSON.parse(apiData);
            if(apiData.status=="success"){
                $("#"+popUpWinAcceptOrder).find(".pop-tip2").html("提交成功!");
                refreshOrderList();
                window.setTimeout(function(){
                    acceptOrderBtnActiveFlag = true;
                    $("#"+popUpWinAcceptOrder).hide();
                    $("#"+popUpWinAcceptOrder).find(".pop-tip2").html("");
                },delayTime);
            } else {
                $("#"+popUpWinAcceptOrder).find(".pop-tip2").html(apiData.msg);
                refreshOrderList();
                // alert(apiData.msg);
                window.setTimeout(function(){
                    acceptOrderBtnActiveFlag = true;
                    $("#"+popUpWinAcceptOrder).hide();
                    $("#"+popUpWinAcceptOrder).find(".pop-tip2").html("");
                },preRefreshTime);
            }
        },
        error:function(XMLHttpRequest,textStatus){
            console.log("fail:",textStatus);
            acceptOrderBtnActiveFlag = true;
            $("#"+popUpWinAcceptOrder).find(".pop-tip2").html(textStatus);
        }
    })
}
function acceptOrderCancel(){
    if(!acceptOrderBtnActiveFlag) return false;
    console.log("取消了")
    $("#"+popUpWinAcceptOrder).find(".pop-tip2").html("");
    $("#"+popUpWinAcceptOrder).hide();
}

function acceptOrder(orderId){
    console.log("订单编号:",orderId);
    currentOrderid = orderId;
    deliveryTime = "";
    $("#"+popUpWinAcceptOrder).find(".pop-tip2").html("");
    $(".time-box").removeClass("time-box-selected");
    $(".time-box").addClass("time-box-default");
    $("#"+popUpWinAcceptOrder).show();
}


