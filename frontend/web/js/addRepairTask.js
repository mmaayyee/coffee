/**
 * Created by wangxl on 17/6/13.
 */
$(function() {
    var build_id = $('#equiptask-build_id').val();
    var url = $('#equiptask-build_id').data('url');
    var userid = $(".hide_assign_userid").val();
    // 初始化地图
    var geocoder, map, marker = null;
    var center = new qq.maps.LatLng(39.916527, 116.397128);
    map = new qq.maps.Map(document.getElementById('allmap'), {
        center: center,
        zoom: 12,
        disableDefaultUI: true
    });

    // 创建地址解析器实例
    geocoder = new qq.maps.Geocoder({
        complete: function (result) {
            if (marker)
                marker.setMap(null);
            map.setCenter(result.detail.location);
            map.zoomTo(18);
            marker = new qq.maps.Marker({
                map: map,
                position: result.detail.location
            });
        }
    });

    if (build_id) {
        $.get(
            url,
            {'build_id':build_id, 'userid':userid, 'type':3},
            function(data) {
                if (data.length != 0) {
                    $('#build_name').html(data.build_name);
                    $('#equip_code').html(data.equip_code);
                    $('#equip_model').html(data.equip_type);
                    $('#equip_id').val(data.equip_id);
                } else {
                    $('#build_name').html('');
                    $('#equip_code').html('');
                    $('#equip_model').html('');
                    $('#equip_id').val('');
                }
                // 将地址解析结果显示在地图上,并调整地图视野
                geocoder.getLocation(data.build_address);

            },
            'json'
        );
    }

    //点击楼宇 获取参数值
    $("#equiptask-build_id").change(function(){
        $.get(
            $(this).data('url'),
            {'build_id':$(this).val(),'type':3},
            function(data) {
                if (data.length != 0) {
                    $('#build_name').html(data.build_name);
                    $('#equip_code').html(data.equip_code);
                    $('#equip_model').html(data.equip_type);
                    $('#equip_id').val(data.equip_id);

                    // 将地址解析结果显示在地图上,并调整地图视野
                    geocoder.getLocation(data.build_address);

                } else {
                    $('#build_name').html('');
                    $('#equip_code').html('');
                    $('#equip_model').html('');
                    $('#equip_id').val('');

                }
            },
            'json'
        );
    })

});

// 表单提交
$().ready(function(){
    $('form').on('afterValidate', function (event, messages, errorAttributes) {
        if (errorAttributes.length>0) {
            $(':submit').removeAttr('disabled');
        }
    });
    $(".btn").click(function(){

        if($('#equiptask-content input:checked').length == 0 && $('#equiptask-remark').val() == ''){
            $('.field-equiptask-content').addClass('has-error');
            $('.field-equiptask-content #error-tip').remove();
            $('.field-equiptask-content').append('<div id="error-tip" class="help-block">任务内容和备注不能同时为空。</div>');
            return false;
        }

        if($(".has-error .help-block").length > 0){
            if ($(".has-error .help-block").html().length > 0) {
                $(this).removeAttr('disabled');
                return false;
            }
        }
        $('form').submit();
        return false;
    })
})