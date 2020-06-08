// var apiData = {"status":"success","data":[{"id":"1","take_out_code":"W1808091643171309","actual_fee":"23.80","delivery_status":"3","receiver":"王冰清","phone":"18888888888","area":"新市区","address":"新疆","created_at":"1533804197"},{"id":"1","take_out_code":"W1808091643171309","actual_fee":"23.80","delivery_status":"3","receiver":"王冰清","phone":"18888888888","area":"新市区","address":"新疆","created_at":"1533804197"},{"id":"1","take_out_code":"W1808091643171309","actual_fee":"23.80","delivery_status":"3","receiver":"王冰清","phone":"18888888888","area":"新市区","address":"新疆","created_at":"1533804197"}]}
// var delayTime = 1000;
$(function(){
    initNav(1);
    getDoingOrder();
    $("#refreshData").on("click",function(){
        refreshOrderList();
    });
    //--------弹出窗--------------
    popUp("popUpWinConfirm","确认送达吗？","确 认","取 消",confirmOrderId,confirmCancel);
    popUpTime("popUpChangeTime", changeTimeConfirm, changeTimeCancel,setDeliveryTime,2);
})
var orderData;
function getDoingOrder(){
    $.ajax({
        type: "POST",
        url:"/delivery/doing-order",
        dataType:"json",
        success:function(apiData){
            var orderListTpl = $("#orderListTpl").html();
            console.log("apiData:",apiData)
            // 以下为测试代码
            // var apiData = {"status":"success","data":{"data_list":[{"delivery_order_id":"52","delivery_order_code":"W1810221714534055","delivery_order_status":5,"delivery_cost":"0.00","actual_fee":0,"order_cups":2,"receiver":"高永立","phone":"13501167215","area":"朝阳区来广营北京国生汽车销售服务有限公司","address":"哈哈","longitude":"116.453903","latitude":"40.021626","create_time":1540199693,"accept_time":1540199693,"expect_service_time":1540199693,"reason_name":null,"product_name":["鲜萃香草奶茶","拿铁咖啡","卡布奇诺"]},{"delivery_order_id":"52","delivery_order_code":"W1810221714534055","delivery_order_status":4,"delivery_cost":"0.00","actual_fee":0,"order_cups":2,"receiver":"高永立","phone":"13501167215","area":"朝阳区来广营北京国生汽车销售服务有限公司","address":"哈哈","longitude":"116.453903","latitude":"40.021626","create_time":1540199693,"accept_time":1540199693,"expect_service_time":1540199693,"reason_name":null,"product_name":["巧克力","香草拿铁咖啡","冰拿铁"]}],"list_count":{"wait_list_count":"11","accept_list_count":"2","complete_list_count":"0"}}}
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
                alert(apiData.msg)
            }
        },
        error:function(XMLHttpRequest,textStatus){
            console.log("fail:",textStatus)
        }
    })
}

function refreshOrderList(){
    console.log("刷新");
    $("#orderList").html("<p>数据加载中</p>");
    getDoingOrder();
}
var currentOrderid;
var confirmOrderFlag = true;
var changTimeBtnActiveFlag = true;
var deliveryTime = "";
function confirmOrder(orderId) {
    console.log("确认送达吗",orderId);
    currentOrderid = orderId;
    $("#popUpWinConfirm").show();
}
function confirmCancel(){
    $("#popUpWinConfirm").hide();
}
// 确认送达
function confirmOrderId() {
    // console.log("确认送达",currentOrderid);
    if(!confirmOrderFlag) return false;
    confirmOrderFlag = false;
    $("#popUpWinConfirm").find(".pop-tip").html("提交中...");
    // return false;
    $.ajax({
        type: "POST",
        url:"/delivery/complete-delivery-order",
        data:{"delivery_order_id":currentOrderid},
        dataType:"json",
        success:function(apiData){
            // var apiData = JSON.parse(apiData);
            console.log("complete-delivery-order data..",apiData);
            if(apiData.status=="success"){
                $("#popUpWinConfirm").find(".pop-tip").html("确认成功");
                window.location.href = "/delivery/completed-order";
            } else {
                refreshOrderList();
                // alert(apiData.msg);
                $("#popUpWinConfirm").find(".pop-tip").html(apiData.msg);
                setTimeout(function(){
                    confirmOrderFlag = true;
                    $("#popUpWinConfirm").hide();
                },1000);
            }
        },
        error:function(XMLHttpRequest,textStatus){
            console.log("fail:",textStatus);
            confirmOrderFlag = true;
        }
    })
}
function makeCoffee(orderId){
    console.log("制作：",orderId);
    window.location.href = "/delivery/get-deli-detail?orderid="+orderId;
}
function callUser(tel) {
    console.log("打电话",tel)
    window.location.href = "tel:"+tel;
}
function changeTime(orderId){
    currentOrderid = orderId;
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
                refreshOrderList();
                setTimeout(function(){
                    $("#popUpChangeTime").hide();
                },1000)
            } else {
                $("#popUpChangeTime").find(".pop-tip2").html(apiData.msg);
                // changTimeBtnActiveFlag = true;
                refreshOrderList();
                // alert(apiData.msg);
                window.setTimeout(function(){
                    $("#popUpChangeTime").hide();
                },1000);
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