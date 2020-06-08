var preRefreshTime = 1000;

function showOrderNames(index){
    $(".order-names-layer").show();
    console.log(orderData[index].product_name);
    var len = orderData[index].product_name.length;
    $("#orderNamesList").html("");
    for(var i=0;i<len;i++){
        $("#orderNamesList").append("<p>"+orderData[index].product_name[i].product_name+"</p>")
    }
}

function popUp(idName,potTxt,confirmTxt,cancelTxt,confirmFunc,cancelFunc){
    var popHtml = "<div class='pop-up-win' style='display:none;' id='"+idName+"'><div class='pop-up-bg'></div><div class='pop-content'><div class='pop-txt'><p>"+potTxt+"</p><div class='pop-tip'></div></div><div class='pop-btn pop-btn-confirm'>"+confirmTxt+"</div><div class='pop-btn pop-btn-cancel'>"+cancelTxt+"</div></div></div>";
    $('body').append(popHtml);
    $('#'+idName).find('.pop-btn-confirm').on('click',confirmFunc);
    $('#'+idName).find('.pop-btn-cancel').on('click',cancelFunc);
}
function popUpTime(idName,confirmFunc,cancelFunc,setDeliveryTimeFunc,type){
    var potTxt = "请选估计送达时间";
    var confirmTxt = "确 认";
    var cancelTxt = "取 消";
    var popHtml = "<div class='pop-up-win' style='display:none;' id='"+idName+"'><div class='pop-up-bg'></div><div class='pop-content2'><div class='pop-txt2'><p>"+potTxt+"</p><div id='timeBox' class='time-box-container'></div><div class='pop-tip2'></div></div><div class='pop-btn pop-btn-confirm'>"+confirmTxt+"</div><div class='pop-btn pop-btn-cancel'>"+cancelTxt+"</div></div></div>";
    $('body').append(popHtml);
    var timeArray = ['10','15','20','25','30','35','40','45','50','55'];
    if(type!=undefined&&type==2){
        timeArray = ['3','6','9','12','15','18','21','24','27','30'];
    }
    for(var i=0;i<10;i++){
        var timeBox = "<div class='time-box time-box-default' id='timeBox"+i+"'>"+timeArray[i]+"分</div>";
        $("#timeBox").append(timeBox);
        (function(i){
            $("#timeBox"+i).on("click",function(){
                $("#"+idName).find(".pop-tip2").html("");
                setDeliveryTimeFunc(timeArray[i]);
                $(".time-box").removeClass("time-box-selected");
                $(".time-box").addClass("time-box-default");
                $("#timeBox"+i).removeClass("time-box-default");
                $("#timeBox"+i).addClass("time-box-selected");
            });
        })(i)
    }
    // setDeliveryTimeFunc(20);
    $('#'+idName).find('.pop-btn-confirm').on('click',confirmFunc);
    $('#'+idName).find('.pop-btn-cancel').on('click',cancelFunc);
}

function initNav(id) {
    var navHtml = "<div class='nav-sub border-left'>未接单<div class='nav-num' id='notAcceptOrderNum'></div></div><div class='nav-sub border-left'>进行中<div class='nav-num' id='doingOrderNum'></div></div><div class='nav-sub border-left-right'>已完成<div class='nav-num' id='completedOrderNum'></div></div>";
    $("#navTop").append(navHtml);

    var navId = id;
    var navUrl = ["/delivery/not-accept-order","/delivery/doing-order","/delivery/completed-order"]
    $(".nav-sub").eq(navId).addClass("nav-sub-current");
    $.each($(".nav-sub"),function(index,obj){
        $(obj).on("click",function(){
            console.log(index);
            if(index!=navId){
                window.location.href=navUrl[index];
            }
        });
    })
}
function setCount(data){
    $(".nav-num").show();
    if(data.list_count){
        $("#notAcceptOrderNum").html(data.list_count.wait_list_count);
        $("#doingOrderNum").html(data.list_count.accept_list_count);
        $("#completedOrderNum").html(data.list_count.complete_list_count);
        if(Number(data.list_count.wait_list_count)==0){
            $("#notAcceptOrderNum").hide();
        }
        if(Number(data.list_count.accept_list_count)==0){
            $("#doingOrderNum").hide();
        }
        if(Number(data.list_count.complete_list_count)==0){
            $("#completedOrderNum").hide();
        }
    }
}
function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) {
        return decodeURI(r[2])
    }
    return null
}

function getTimeFromStamp(timestamp) {
    return new Date(parseInt(timestamp) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');
}
//打开或关闭调试工具
function nclickEvent(n,fn) {
    var dom = document.body;
    // dom.removeEventListener('dblclick',null);
    var n = parseInt(n) < 1 ? 1:parseInt(n),
        count = 0,
        lastTime = 0;//用于记录上次结束的时间
    var handler = function (event) {
        var currentTime = new Date().getTime();//获取本次点击的时间
        count = (currentTime-lastTime) < 500 ? count +1 : 0;
        // console.log("count..",count);
        //如果本次点击的时间和上次结束时间相比大于300毫秒就把count置0
        lastTime = new Date().getTime();
        if(count>=n-1){
            fn(event,n);
            count = 0;
        }
    };
    dom.addEventListener('click',handler);
}
var vConsole;
$(function(){
    $("#closeOrderName").on("click",function(){
        $(".order-names-layer").hide();
    })
    FastClick.attach(document.body);
    var consoleOnReady = function(){
        if(window.location.host.split(".")[0]=="erp") {
            vConsole.hideSwitch();
        }
    }
    vConsole = new VConsole({'onReady':consoleOnReady});
    nclickEvent(5,function (event,n) {
        console.log(n+'click');
        //这里面放置要处理的事件
        vConsole.showSwitch();
    })
    nclickEvent(7,function (event,n) {
        console.log(n+'click');
        //这里面放置要处理的事件
        vConsole.hideSwitch();
    })
})


// 微信打开地图
var jsApiList = ["checkJsApi", "openLocation"];
wx.config({
    debug: false,
    appId: signPackage.appId,
    timestamp: signPackage.timestamp,
    nonceStr: signPackage.nonceStr,
    signature: signPackage.signature,
    jsApiList: jsApiList
});
function showMap(index){
    console.log("lat..",orderData[index].latitude)
    var lat = orderData[index].latitude;
    var lng = orderData[index].longitude;
    var area = orderData[index].area;
    var address = orderData[index].address;
    openLocation(lat,lng,area,address);
}
function openLocation(lat,lng,area,address){
    var scale = /iphone/i.test(navigator.userAgent)?15:16;
    console.log("scale..",scale);
    console.log(lat,",",lng,",",address);
    var lat = Number(lat);
    var lng = Number(lng);
    wx.ready(function() {
        wx.openLocation({
            latitude: lat, // 纬度，浮点数，范围为90 ~ -90
            longitude: lng, // 经度，浮点数，范围为180 ~ -180。
            name: area, // 位置名
            address: address, // 地址详情说明
            scale: scale, // 地图缩放级别,整形值,范围从1~28。默认为最大
            infoUrl: '' // 在查看位置界面底部显示的超链接,可点击跳转
        });
    });
}