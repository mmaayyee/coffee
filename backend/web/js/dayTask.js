/**
 * Created by wangxl on 17/6/20.
 */
$(function(){
    //点击楼宇 获取参数值
    $("#distributiontaskequipsetting-build_id").change(function(){
        var build_id = $('#distributiontaskequipsetting-build_id').val();
        if(build_id.length > 0){
            $('.field-distributiontaskequipsetting-org_id').hide();
            $('.field-distributiontaskequipsetting-equip_type_id').hide();
            $('.field-distributiontaskequipsetting-material_id').hide();
        }else{
            $('.field-distributiontaskequipsetting-org_id').show();
            $('.field-distributiontaskequipsetting-equip_type_id').show();
            $('.field-distributiontaskequipsetting-material_id').show();
        }
    })

    $("#distributiontaskequipsetting-material_id").change(function(){
        var marterial_id = $("#distributiontaskequipsetting-material_id").val();
        if(marterial_id > 0){
            $('.field-distributiontaskequipsetting-org_id').hide();
            $('.field-distributiontaskequipsetting-equip_type_id').hide();
            $('.field-distributiontaskequipsetting-build_id').hide();
            $('#distributiontaskequipsetting-org_id').find("option[text='请选择']").attr("selected",true);//removeAttr('selected');
            $('#distributiontaskequipsetting-equip_type_id').find("option[text='请选择']").attr("selected",true);
            $('#distributiontaskequipsetting-build_id').find("option[text='请选择']").attr("selected",true);
        }else{
            $('.field-distributiontaskequipsetting-org_id').show();
            $('.field-distributiontaskequipsetting-equip_type_id').show();
            $('.field-distributiontaskequipsetting-build_id').show();
        }
    })
    //修改时
    var marterial_id = $("#distributiontaskequipsetting-material_id").val();
    if(marterial_id > 0){
        $('.field-distributiontaskequipsetting-org_id').hide();
        $('.field-distributiontaskequipsetting-equip_type_id').hide();
        $('.field-distributiontaskequipsetting-build_id').hide();
        $('#distributiontaskequipsetting-org_id option:selected').find("option[text='请选择']").attr("selected",true);
        $('#distributiontaskequipsetting-equip_type_id option:selected').find("option[text='请选择']").attr("selected",true);
        $('#distributiontaskequipsetting-build_id option:selected').find("option[text='请选择']").attr("selected",true);
    }

    var build_id = $('#distributiontaskequipsetting-build_id').val();
    if(build_id.length > 0){
        $('.field-distributiontaskequipsetting-org_id').hide();
        $('.field-distributiontaskequipsetting-equip_type_id').hide();
        $('.field-distributiontaskequipsetting-material_id').hide();
    }


})