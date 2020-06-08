$(function(){
    //初始化是否上报
    if ($("#equipwarn-is_report").val() == 1){
        $("#report_setting").show();
    } else {
        $("#report_setting").hide();
    }
    //点击是否上报按钮
    $("#equipwarn-is_report").change(function(){
        if ($(this).val() == 1) {
            $("#report_setting").show();
        } else {
            $("#report_setting").hide();
        }
    });
    var html = '';
    if (report_setting) {
        report_setting = JSON.parse(report_setting);
        for (var i=1; i <= $("#equipwarn-report_num").val(); i++) {
            html += "<dl>出现<select name='EquipWarn[report_setting]["+i+"][num]'>";
            for (var j in hournum) {
                if (j == report_setting[i]['num']){
                    html += "<option selected value='"+j+"'>"+hournum[j]+"</option>"
                } else {
                    html += "<option value='"+j+"'>"+hournum[j]+"</option>"
                }
            }
            html += "</select>未解决，以 ";
            for (var k in sendtype) {
                if (report_setting[i]['type'] && k == report_setting[i]['type'][k]) {
                    html += "<input type='checkbox' checked name='EquipWarn[report_setting]["+i+"][type]["+k+"]' value='"+k+"' />"+sendtype[k];
                } else {
                    html += "<input type='checkbox' name='EquipWarn[report_setting]["+i+"][type]["+k+"]' value='"+k+"' />"+sendtype[k];
                }
            }
            html += "方式发给 "+i+"<input type='hidden' name='EquipWarn[report_setting]["+i+"][top]' value='"+i+"' /> 级领导</dl>";
        }
        $("#report_num_set").html(html);
    }

    // 选择上报级数
    $("#equipwarn-report_num").change(function(){
        var html = '';
        var level = $(this).val();  //当前选择的等级
        // 展示上报内容
        for (var i=1; i <= level; i++) {
            html += "<dl>出现<select name='EquipWarn[report_setting]["+i+"][num]'>";
            for (var j in hournum) {
                html += "<option value='"+j+"'>"+hournum[j]+"</option>"
            }
            html += "</select>未解决，以 ";
            for (var k in sendtype) {
                html += "<input type='checkbox' name='EquipWarn[report_setting]["+i+"][type]["+k+"]' value='"+k+"' />"+sendtype[k];
            }
            html += "方式发给 "+i+"<input type='hidden' name='EquipWarn[report_setting]["+i+"][top]' value='"+i+"' /> 级领导</dl>"
        }
        $("#report_num_set").html(html);
    });

    $('form').on('afterValidate', function (event, messages, errorAttributes) {
        if (errorAttributes.length>0) {
            $(':submit').removeAttr('disabled');
        }
    });
    $("#wareSave").click(function(){
        $(this).attr('disabled', true);
        if ($("#equipwarn-is_report").val() == 1) {
            if (!$("#equipwarn-report_num").val()) {
                alert('请选择上报级数');
                $("#equipwarn-report_num").focus();
                $(this).removeAttr('disabled');
                return false;
            }
        }
        $("form").submit();
    })

});