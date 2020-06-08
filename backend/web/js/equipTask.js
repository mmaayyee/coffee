$(function(){
    var buildid = $('#equiptask-build_id').val();
    var userid = $('#userid').val();
    var taskType = $('#task_type').val();
    var url = $('#equiptask-build_id').data('url');
    if (buildid) {
        $.get(
            url,
            {'build_id':buildid, 'userid':userid, 'taskType':taskType},
            function(data) {
                if (data.length != 0) {
                    $('#build_name').html(data.build_name);
                    $('#equip_code').html(data.equip_code);
                    $('#equip_model').html(data.equip_type);
                    $('#equip_id').val(data.equip_id);
                    $('#equiptask-assign_userid').html(data.memberArr);
                } else {
                    $('#build_name').html('');
                    $('#equip_code').html('');
                    $('#equip_model').html('');
                    $('#equip_id').val('');
                    $('#equiptask-assign_userid').html("<option value=''>请选择</option>");
                }
            },
            'json'
        );
    }
    $("#equiptask-build_id").change(function(){
        $.get(
            $(this).data('url'),
            {'build_id':$(this).val(), 'taskType':taskType},
            function(data) {
                if (data.length != 0) {
                    $('#build_name').html(data.build_name);
                    if (data.equip_code) {
                        $('#equip_code').html(data.equip_code);
                    } else {
                        $('#equip_code').html('');
                    }
                    if (data.equip_type) {
                        $('#equip_model').html(data.equip_type);
                    } else {
                        $('#equip_model').html('');
                    }
                    $('#equip_id').val(data.equip_id);
                    $('#equiptask-assign_userid').html(data.memberArr);
                } else {
                    $('#build_name').html('');
                    $('#equip_code').html('');
                    $('#equip_model').html('');
                    $('#equip_id').val('');
                    $('#equiptask-assign_userid').html("<option value=''>请选择</option>");
                }
            },
            'json'
        );
    })

    $('.btn').click(function(){
        if($('#equiptask-content label input:checked').val() == undefined && $("[name='EquipTask[remark]']").val() == ''){
            $('.field-equiptask-remark').addClass('has-error');
            $('.field-equiptask-remark .help-block').html('设备附件和备注不可同时为空');
            return false;
        }else{
            $('.field-equiptask-remark').removeClass('has-error');
            $('.field-equiptask-remark').removeClass('help-block');
        }
        if($(".has-error .help-block").length == 0){
            $('form').submit();
        }
    });
})