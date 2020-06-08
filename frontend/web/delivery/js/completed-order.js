$(function(){
    initNav(2);
    getDoingOrder();
    $("#refreshData").on("click",function(){
        refreshOrderList();
    });
})
var orderData;
function getDoingOrder(){
    $.ajax({
        type: "POST",
        url:"/delivery/completed-order",
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
                console.log(apiData.msg)
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