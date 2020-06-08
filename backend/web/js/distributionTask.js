$(function(){
    var build_id = $('#distributiontask-build_id').val();
    var url = $('#distributiontask-build_id').data('url');
    var userid =   $(".hide_assign_userid").val();
    var userName = $(".hide_assign_userName").val()
    // 初始化地图
    var geocoder,map,marker = null;
    var center = new qq.maps.LatLng(39.916527,116.397128);
    map = new qq.maps.Map(document.getElementById('allmap'),{
        center: center,
        zoom: 12,
        disableDefaultUI: true
    });

    // 创建地址解析器实例
    geocoder = new qq.maps.Geocoder({
        complete : function(result){
            if (marker)
                marker.setMap(null);
            map.setCenter(result.detail.location);
            map.zoomTo(18);
            marker = new qq.maps.Marker({
                map:map,
                position: result.detail.location
            });
        }
    });

    if (build_id) {
        changeBuild(url,build_id,userid,userName,geocoder);
    }
    //点击楼宇 获取参数值
    $("#distributiontask-build_id").change(function(){
        //首先验证是否存在该楼宇没有分配的任务
        checkBuildTask($(this).val());
        changeBuild(url,$(this).val(),userid,userName,geocoder);
    })

});
function changeBuild(url,build_id,userid,userName,geocoder)
{
    $.get(
        url,
        {'build_id':build_id, 'userid':userid},
        function(data) {
            if (data.length != 0) {
                $('#build_name').html(data.build_name);
                $('#equip_code').html(data.equip_code);
                $('#equip_model').html(data.equip_type);
                $('#equip_id').val(data.equip_id);
                $.get(
                    '/distribution-temporary-task/get-distribution-content',
                    {equipId:data.equip_id, taskId: $("#hide_id").val()},
                    function(dataVal){
                        if(!dataVal){
                            $(".create_distribution_task_content").html("<div style='color:red;margin-left:10%;'>请先同步相应料仓！</div>")
                        }else{
                            $(".create_distribution_task_content").html(dataVal);

                            $(".check-info").blur(function(){
                                var obj = $(this).val();
                                if (obj.length>0){
                                    reg = /^\+?[1-9][0-9]{0,2}$/;
                                    if(!reg.test(obj)){
                                        $(this).parent().find(".help-block").html('物料内容必须为"[1~1000]"的正整数。');
                                        $(this).parent().addClass('has-error');
                                    } else {
                                        $(this).parent().find(".help-block").html('');
                                        $(this).parent().removeClass('has-error');
                                    }
                                }else{
                                    $(this).parent().find(".help-block").html('');
                                    $(this).parent().removeClass('has-error');
                                }
                            })
                        }
                    }
                );

                $('#distributiontask-assign_userid').html(data.deliveryPersonArr);
                $('#user_surplus_material').html(data.userSurplusMaterial);
                $('.select2-selection__placeholder').html(userName);
            } else {
                $('#build_name').html('');
                $('#equip_code').html('');
                $('#equip_model').html('');
                $('#equip_id').val('');
                $('#distributiontask-assign_userid').html("<option value=''>请选择</option>");
                $('#user_surplus_material').html(data.userSurplusMaterial);
            }
            // 将地址解析结果显示在地图上,并调整地图视野
            geocoder.getLocation(data.build_address);
        },
        'json'
    );
}
//首先验证是否存在该楼宇没有分配的任务
function checkBuildTask(buildId) {
    $.get(
        '/distribution-temporary-task/get-check-build-task',
        {'buildId': buildId},
        function (data){
            if(data !== null){
                if($('#hide_id').val() === '' || $('#hide_id').val() === data.id){
                    $('#tip').css('display','block');
                    $(':submit').attr('disabled',true);
                    return false;
                }
            } else {
                $('#tip').css('display','none');
                $(':submit').removeAttr('disabled');
                return false;
            }

        },
        'json'
    );
}
// 表单提交
$().ready(function(){
    $('form').on('afterValidate', function (event, messages, errorAttributes) {
        if (errorAttributes.length>0) {
            $(':submit').removeAttr('disabled');
        }
    });
    $(".btn").click(function(){

        // if(($('#distributiontask-malfunction_task input').length > 0) && ($('#distributiontask-malfunction_task input:checked').val() == undefined)){
        //     var flag = true;
        //     $('.delivery_content div input').each(function(i){
        //         if($(this).val() > 0){
        //             $('.field-distributiontask-malfunction_task').removeClass('has-error');
        //             $('.field-distributiontask-malfunction_task').removeClass('help-block');
        //             flag = false;
        //         }
        //     });
        //     if(flag){
        //         $('.field-distributiontask-malfunction_task').addClass('has-error');
        //         $('.field-distributiontask-malfunction_task .help-block').html('故障现象和配送内容不可同时为空');
        //         $(this).removeAttr('disabled');
        //         return false;
        //     }
        // }

        if($(".has-error .help-block").length == 0){
            $('form').submit();
        }
        return false;
    })
})