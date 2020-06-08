var normalArr		=	[]; //正常
var malfunctionArr	=	[];	//故障
var lockingArr		=	[];	//锁定

var normalArrNo		=	[];
var malfunctionArrNo=	[];
var lockingArrNo	=	[];
//初始化函数
$(function(){
    top.scrollTo(0,0);
	var checkedProvice	=	$("#checkedProvice").val();
	$("#city").val(checkedProvice);
	var city	=	$("#city").val();
    var build_list = $("#build_list").data('list');// 楼宇列表数据
	var st_lng = 116.403694, st_lat = 39.927552, zoom = 12;// 初始化中心点坐标

	// 初始化所选城市中心点坐标 116.40717 39.90469
    if(build_list[0]) {
        st_lng = build_list[0].longitude
        st_lat = build_list[0].latitude
        //无数据时，默认显示北京
        if(!st_lng && !st_lat){
        	var center = new qq.maps.LatLng(39.90469, 116.40717);
			var map = new qq.maps.Map(
			    document.getElementById("map"),
			    {
			        center: center,
			        zoom: zoom
			    }
			);
			var marker = new qq.maps.Marker({
			    position: center,
			});
			return false;

        }
    } else {
        geocoder = new qq.maps.Geocoder({
            complete : function(result){
                map.setCenter(result.detail.location);
            }
        });
        geocoder.getLocation(org_city);
    }
    // 设备详情页中跳转过来的则跑此数据
    var buildId = $("#build_id").val();
    if(buildId){
        $("#build_name").val(buildId);
        st_lng = $("#build_name").find("option:selected").attr('lng');
        st_lat = $("#build_name").find("option:selected").attr('lat');
        zoom = 18;
    }
    //初始化地图中心点
    var map = new qq.maps.Map(document.getElementById("map"),{
        //加载地图经纬度信息
        center:  new qq.maps.LatLng(st_lat,st_lng),
        zoom: zoom,                       //设置缩放级别
        draggable: true,               //设置是否可以拖拽
        scrollwheel: true,             //设置是否可以滚动
        disableDoubleClickZoom: false    //设置是否可以双击放大
    });
    //调用地址解析类
    geocoder = new qq.maps.Geocoder({
        complete : function(result){
            map.setCenter(result.detail.location);
            var marker = new qq.maps.Marker({
                map:map,
                position: result.detail.location
            });
        }
    });
    var currentSelectIndex;
    var info2 = new qq.maps.InfoWindow({
            map: map
    });
    $("#address").blur(function(){
        var address = $(this).val();
        geocoder.getLocation(city+address);
    })

    if(buildId){
        currentSelectIndex = $("#build_name").get(0).selectedIndex-1;
        info2.open();
        info2.setContent('<div style="text-align:center;white-space:nowrap;'+
                'margin:10px;">楼宇名称：'+build_list[currentSelectIndex].name+'<br/><div>楼宇地址：'+build_list[currentSelectIndex].province+build_list[currentSelectIndex].city+build_list[currentSelectIndex].area+build_list[currentSelectIndex].address+'</div></div>');
        info2.setPosition(new qq.maps.LatLng(st_lat,st_lng));
    }
   	// 选择不同分公司显示不同分公司下楼宇 默认显示北京下的设备
    $("#city").change(function(){
        window.location.href="?city="+$(this).val();
    })

    // 选择不同楼宇在地图中显示
    $("#build_name").change(function(){
        var b_lng = $(this).find("option:selected").attr('lng');
        var b_lat = $(this).find("option:selected").attr('lat');
        if (b_lng && b_lat) {
            map.panTo(new qq.maps.LatLng(b_lat, b_lng));
            map.zoomTo(18);
            currentSelectIndex = $("#build_name").get(0).selectedIndex-1;
            info2.close();
            info2.open();
            info2.setContent('<div style="text-align:center;white-space:nowrap;'+
                    'margin:10px;">楼宇名称：'+build_list[currentSelectIndex].name+'<br/><div>楼宇地址：'+build_list[currentSelectIndex].province+build_list[currentSelectIndex].city+build_list[currentSelectIndex].area+build_list[currentSelectIndex].address+'</div></div>');
            info2.setPosition(new qq.maps.LatLng(b_lat,b_lng));
        }
    })
    var tipMark;
    //勾选条件框
    // 操作正常未锁定设备
    $(".normal").change(function() {
        var normal = $(".normal").val();
        showOrHide($('.normal'), normalArr, normalArrNo);
    });

    // 操作正常未锁定设备
    $(".malfunction").change(function() {
        var malfunction = $(".malfunction").val();
        showOrHide($('.malfunction'), malfunctionArr, malfunctionArrNo);
    });

    // 操作正常未锁定设备
    $(".locking").change(function() {
        var locking = $(".locking").val();
        showOrHide($('.locking'), lockingArr, lockingArrNo);
    });

    function showOrHide(kind, arr, arrNo){
        //ture是选中 false是没有选中
        var marker;
        if(kind.is(':checked')) {
            for (var i in arr) {
                arrNo[i].setMap(map);
            }
        }else{
            for (var i in arr) {
                if (arrNo[i] == tipMark) {
                   info.close();
                }
                arrNo[i].setMap(null);
            }
        }
    }


	// 自定义图标
    var anchor = new qq.maps.Point(20, 30), 		//	默认 代表正常 图标尺寸
        size   = new qq.maps.Size(30, 30),
        origin = new qq.maps.Point(0, 0),
        malfunctionSize = new qq.maps.Size(30, 30), // 	故障图标尺寸
        lockingSize = new qq.maps.Size(30, 30),		// 	已锁定图标尺寸
        // 正常状态
    	normalIcon 	= new qq.maps.MarkerImage("/images/normal1.png", size, origin,anchor, malfunctionSize, lockingSize);
    	// 故障状态
    	malfunctionIcon = new qq.maps.MarkerImage('/images/fault.png', size, origin, anchor, malfunctionSize, lockingSize);
    	//	已锁定状态
    	lockigIcon = new qq.maps.MarkerImage('/images/lock.png', size, origin, anchor, malfunctionSize, lockingSize);
    // 添加到提示窗
    var info = new qq.maps.InfoWindow({
	        map: map
	 });
    // 生成楼宇覆盖点
    $.each(build_list, function(i, el) {
        var position = new qq.maps.LatLng(build_list[i].latitude,build_list[i].longitude);
        var marker;
        if (build_list[i].equipment_status == 1 && build_list[i].is_lock == 1) {    //  = 正常 未锁定
            marker = new qq.maps.Marker({
                icon:normalIcon,
                position: position,
                map: map
            });
            normalArrNo.push(marker);
            normalArr.push(build_list[i]);

        } else if(build_list[i].equipment_status == 2 && build_list[i].is_lock != 2) { //设备状态 = 故障未锁定
            marker = new qq.maps.Marker({
                icon:malfunctionIcon,
                position: position,
                map: map
            });
            malfunctionArrNo.push(marker);
            malfunctionArr.push(build_list[i]);

        }else if (build_list[i].is_lock == 2 ) {			//已锁定状态
        	marker = new qq.maps.Marker({
                icon:lockigIcon,
                position: position,
                map: map
            });
            lockingArrNo.push(marker);
            lockingArr.push(build_list[i]);
        }
        if (marker) {
    	    //监听鼠标移入图标时的事件  （出现楼宇名称及地址。）
        	qq.maps.event.addListener(marker, 'mouseover', function() {
    	        tipMark = marker;
                info.open();
    	        info.setContent('<div style="text-align:center;white-space:nowrap;'+
    	        'margin:10px;">楼宇名称：'+build_list[i].name+'<br/><div>楼宇地址：'+build_list[i].province+build_list[i].city+build_list[i].area+build_list[i].address+'</div></div>');
    	        info.setPosition(position);
    	    });
            qq.maps.event.addListener(marker, 'mouseout', function() {
                info.close();
            });
        }
    });

})