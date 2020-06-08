var distribution_userid = '' //修改的配送员id
    enableDrawingTool = false, //用来区分是否为配送分工模式
    assign_build_marker_arr = [],   //已分配的楼宇覆盖物列表
    assign_build_marker_arr_no = [], //未分配的楼宇覆盖物列表
    build_list_assign_no = [], //未分配的楼宇列表
    build_list_assign = []; //已经分配的楼宇列表
$(function(){
    var org_id = $('#org_id').val(), //分公司id
        org_city = $('#org_city').val(), //分公司所在城市名称
        st_lng = 116.403694, st_lat = 39.927552,// 初始化中心点坐标
        build_list = $("#build_list").data('list');// 楼宇列表数据

    // 选择不同分公司显示不同分公司下楼宇
    $("#org_id").change(function(){
        window.location.href="?org_id="+$(this).val();
    })

    // 初始化所选城市中心点坐标
    if(build_list[0]) {    
        st_lng = build_list[0].longitude
        st_lat = build_list[0].latitude
    } else {
        geocoder = new qq.maps.Geocoder({
            complete : function(result){
                map.setCenter(result.detail.location);
            }
        });
        geocoder.getLocation(org_city);
    }

    //初始化地图中心点
    var map = new qq.maps.Map(document.getElementById("map"),{
        center:  new qq.maps.LatLng(st_lat,st_lng),
        zoom: 12
    });

    //配送分工
    $('#assignUser').click(function(){
        var type = $(this).html();
        if (type == '取消配送分工') {
            window.location.href="?org_id="+org_id+"&type=1";
        } else {
            window.location.href="?org_id="+org_id+"&type=2";
        }
    })

    if ($('#assignUser').html() == '取消配送分工') {
        enableDrawingTool = true;
    }

    // 显示隐藏已分配楼宇
    $('#showHideBuild').click(function(){
        var type = $(this).html();
        if (type == '显示已分配的楼宇') {
            if (assign_build_marker_arr) {
                for(var i in assign_build_marker_arr) {
                    assign_build_marker_arr[i].setMap(map);
                }
            }
            $(this).html('隐藏已分配的楼宇');
        } else {
            for(var i in assign_build_marker_arr) {
                assign_build_marker_arr[i].setMap(null);
            }
            $(this).html('显示已分配的楼宇');
        }
    })

    // 选择不同楼宇在地图中显示
    $("#build_name").change(function(){
        var b_lng = $(this).find("option:selected").attr('lng');
        var b_lat = $(this).find("option:selected").attr('lat');
        if (b_lng && b_lat) {
            map.panTo(new qq.maps.LatLng(b_lat, b_lng));
            map.zoomTo(18)
        }
    })

    // 隐藏楼宇选择弹出框
    $('.close, #logout').click(function(){
        distribution_userid = '';
        $('.step_1 .modal-body').html('');
        for(var i in assign_build_marker_arr_no) {
            assign_build_marker_arr_no[i].setIcon(onIcon);
        }
    })

    // 点击确认楼宇选择
    $('#step_2').click(function(){
        //获取已选者的楼宇id
        if (distribution_userid) { // 修改配送员楼宇
            submit(distribution_userid, 1);
        } else {
            $('.step_1').hide();
            $('.step_2').show();
        }
    })

    // 点击返回上一步
    $('#step_1').click(function(){
        $('.step_1').show();
        $('.step_2').hide();
    })

    // 编辑配送员负责的楼宇
    $('#edit_distribution_build').click(function(){
        $('.step_1').show();
        $('.step_3, .step_2').hide();
        for (var i in build_list_assign) {
            if (build_list_assign[i].distribution_userid == distribution_userid) {
                addHtml(build_list_assign[i],assign_build_marker_arr[i]);
            }
        }
    })
    
    // 提交操作
    $('#submit').click(function(){
        distribution_userid = $(".step_2 .modal-body input:checked").val();
        if (!distribution_userid) {
            alert('请选择配送员');
            return false;
        }
        submit(distribution_userid, 2);
    })
    
    // 自定义标记
    var anchor = new qq.maps.Point(10, 30),
        size = new qq.maps.Size(30, 30),
        origin = new qq.maps.Point(0, 0),
        scaleSize = new qq.maps.Size(30, 30),
        offIcon = new qq.maps.MarkerImage('/images/normal1.png', size, origin, anchor, scaleSize);
        onIcon = new qq.maps.MarkerImage('/images/fault.png', size, origin, anchor, scaleSize);

    var info = new qq.maps.InfoWindow({
            map: map
        });
    // 生成楼宇覆盖点
    $.each(build_list, function(index, buildObj) { 
        var position = new qq.maps.LatLng(buildObj.latitude,buildObj.longitude);

        if (buildObj.distribution_userid) {    //已分配楼宇
            var marker = new qq.maps.Marker({
                icon:offIcon,
                position: position,
                map: map
            });
            assign_build_marker_arr.push(marker);
            build_list_assign.push(buildObj);

        } else { //未分配楼宇
            var marker = new qq.maps.Marker({
                icon:onIcon,
                position: position,
                map: map
            });
            assign_build_marker_arr_no.push(marker);
            build_list_assign_no.push(buildObj);
        }
        
        //标记Marker点击事件
        qq.maps.event.addListener(marker, 'mouseover', function() {
            info.open();
            info.setContent('<div style="text-align:center;white-space:nowrap;' +
                'margin:10px;">'+buildObj.name+'</div>');
            info.setPosition(marker.getPosition());
        });

        if (enableDrawingTool) {
            addClickHandler(marker,buildObj,onIcon,offIcon);
        }
    })

    //绘制工具
    if (enableDrawingTool) {
        init(map,offIcon);
    }
});

// 提交数据
function submit(userid,type){
    //获取已选者的楼宇编号
    var build_number_arr = [];
    $('.step_1 .modal-body dl').each(function(e){
        build_number_arr.push($(this).data('build_number'));
    })

    $.get(
        'save-user-build',
        {build_id_arr:build_number_arr, distribution_userid:userid, type:type},
        function(data){
            if (data == 1) {
                alert('人员分配成功');
                window.location.reload();
            } else {
                alert('人员分配失败');
            }
        }
    )
}

//点击楼宇覆盖物操作
function addClickHandler(marker,build,onIcon,offIcon){
    // 点击楼宇覆盖点操作
    qq.maps.event.addListener(marker, 'click', function() {
        $("#dialog").modal();
        if (build.distribution_userid) {
            $('.step_1 .modal-body').html('');
            for(var i in assign_build_marker_arr_no) {
                assign_build_marker_arr_no[i].setIcon(onIcon);
            }
            if (marker.getIcon() == onIcon) {
                marker.setIcon(offIcon);
                addHtml(build,this);
            } else {
                $('.step_1, .step_2').hide();
                $('.step_3').show();
                
                distribution_userid = build.distribution_userid;
                $.get(
                    '/distribution-user/build-detail',
                    {id:build.id},
                    function(data){
                        $('.step_3 .modal-body').html(data);
                    }
                );
            }
        } else {
            if ($('.step_1').is(":hidden")) {
                distribution_userid = '';
                $('.step_1').show();
                $('.step_3, .step_2').hide();
            }
            // 点击更换覆盖物更换图标
            marker.setIcon(offIcon);
            addHtml(build,this);
        }
    });
}

// 楼宇选择弹出框追加内容
function addHtml(build, marker){
    var i = 0;
    $('.step_1 .modal-body dl').each(function(){
        if ($(this).data('id') == build.id) {
           i = 1;
        }
    })
    if (i == 1) {
        return false;
    }
    var html = '<dl data-id = "'+build.id+'" data-build_number = "'+build.build_number+'"><dt>'+build.name+'<span class="del_build" data-lng="'+build.longitude+'" data-lat="'+build.latitude+'">&times;</span></dt><dd>'+build.city+build.area+build.address+'</dd></dl>'
    $('.step_1 .modal-body').append(html);
    $('.del_build').click(function(){
        if (marker.getPosition().lng == $(this).data('lng') && marker.getPosition().lat == $(this).data('lat')) {
            marker.setIcon(onIcon);
        }
        $(this).parents('dl').remove();
    })
}

// 多边形覆盖物
function init(map,offIcon){
    var drawingManager = new qq.maps.drawing.DrawingManager({
        drawingMode: qq.maps.drawing.OverlayType.NULL,
        drawingControl: true,
        drawingControlOptions: {
            position: qq.maps.ControlPosition.TOP_LEFT,
            drawingModes: [
                qq.maps.drawing.OverlayType.CIRCLE,
                qq.maps.drawing.OverlayType.RECTANGLE
            ]
        },
        circleOptions: {
            fillColor: new qq.maps.Color(255, 208, 70, 0.3),
            strokeColor: new qq.maps.Color(88, 88, 88, 1),
            strokeWeight: 3,
            clickable: false
        }
    });
    drawingManager.setMap(map);
    
    drawingClick(drawingManager,offIcon);
}
// 多边形覆盖物完成事件
function drawingClick(drawingManager,offIcon){
    var overlay;
    qq.maps.event.addDomListener(map, 'click', function(event) {
        if (overlay)
            overlay.setMap(null);
    }); 
    qq.maps.event.addListener(drawingManager, 'overlaycomplete', function(res) {
        overlay = res.overlay;
        var start_lng = overlay.getBounds().lng.minX, 
            start_lat = overlay.getBounds().lat.minY, 
            end_lng = overlay.getBounds().lng.maxX, 
            end_lat = overlay.getBounds().lat.maxY;

        for (var i in assign_build_marker_arr_no) { 
            var lng = assign_build_marker_arr_no[i].getPosition().lng,
                lat = assign_build_marker_arr_no[i].getPosition().lat;
            if (lng > start_lng && lng < end_lng && lat > start_lat && lat < end_lat) {
                $("#dialog").modal();
                $('.step_1').show();
                $('.step_3, .step_2').hide();
                assign_build_marker_arr_no[i].setIcon(offIcon);
                for (var j in build_list_assign_no) {
                    if (build_list_assign_no[j].longitude == lng && build_list_assign_no[j].latitude == lat) {
                        addHtml(build_list_assign_no[j], assign_build_marker_arr_no[i]);
                    }
                }
            }
        }
    })
}