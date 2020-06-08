/**
 * Created by wangxl on 17/7/5.
 */
$(function () {
    $('#buildingtasksetting-building_id').on('change', function () {
        var buildId = $('#buildingtasksetting-building_id').val();
        var url = $('#buildingtasksetting-building_id').data('url');
        $.get(
            url,
            {'buildId': buildId},
            function (data) {
                var data = jQuery.parseJSON(data);
                if (data.length > 0) {
                    var str = '<div class="form-group field-buildingtasksetting-day_num required">';
                    str += '<label class="control-label" for="buildingtasksetting-day_num">换料天数</label></div>';
                    $.each(data, function (key, obj) {
                        str += '<div>';
                        str += '<div class="form-group field-buildingtasksetting-refuel_cycle required">';
                        str += '<label class="control-label" for="buildingtasksetting-refuel_cycle">' + obj.material_type_name + '(天)</label>';
                        str += '<input type="text" class="form-control" name="BuildingTaskSetting[refuel_cycle][' + obj.material_type + ']" check-type="number required" range="0~100">';
                        str += '</div>';
                        str += '</div><br/>';
                    })

                    $('#refuelCycleId').html(str);
                    $("form").validation();
                }
            }
        );
    })

    var buildId = $('#buildingtasksetting-building_id').val();
    if (buildId) {
        var url = $('#buildingtasksetting-building_id').data('url');
        var refuelCycleData = jQuery.parseJSON(refuel_cycle);
        console.log(refuelCycleData[0].refuel_cycle);

        $.get(
            url,
            {'buildId': buildId},
            function (data) {

                var data = jQuery.parseJSON(data);
                 console.log(refuelCycleData);
                console.log(data);
                if (data.length > 0) {
                    var str = '<div class="form-group field-buildingtasksetting-day_num required">';
                    str += '<label class="control-label" for="buildingtasksetting-day_num">换料天数</label></div>';
                    $.each(data, function (key, obj) {
                        var refuel_cycle = 0;
                        var type=obj.material_type
                        for(var i in refuelCycleData){
                            if(refuelCycleData[i].material_type==type){
                                refuel_cycle=refuelCycleData[i].refuel_cycle;
                                break;
                            }
                        }
                        str += '<div>';
                        str += '<div class="form-group field-buildingtasksetting-refuel_cycle required">';
                        str += '<label class="control-label" for="buildingtasksetting-refuel_cycle">' + obj.material_type_name + '(天)</label>';
                        str += '<input type="text" class="form-control" name="BuildingTaskSetting[refuel_cycle][' + obj.material_type + ']" value="' + refuel_cycle + '" check-type="number required" range="0~100">';
                        str += '</div>';
                        str += '</div><br/>';
                    })
                    $('#refuelCycleId').html(str);
                    $("form").validation();
                }
            }
        );
    }

    $('.btn').click(function(){
        if($("form").valid()){
            $('form').submit();
        }
    });


})
