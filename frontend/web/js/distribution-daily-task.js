$(function () {
    var currYear = (new Date()).getFullYear();
    var opt={};
    opt.date = {preset : 'date'};
    opt.datetime = {preset : 'datetime'};
    opt.time = {preset : 'time'};
    opt.default = {
        theme: 'android-ics light', //皮肤样式
        display: 'modal', //显示方式
        mode: 'scroller', //日期选择模式
        dateFormat: 'yyyy-mm-dd',
        lang: 'zh',
        showNow: true,
        nowText: "今天",
        startYear: currYear - 10, //开始年份
        endYear: currYear + 10 //结束年份
    };
    $('#start_repair_time, #end_repair_time').mobiscroll($.extend(opt['time'], opt['default'])).change(function() {
        $(this).trigger('blur');
    });

    // 供水商失去焦点隐藏错误提示信息
    $("#supplierWater, #start_repair_time, #end_repair_time").blur(function(){
        if ($(this).val) {
            $(this).parent().removeClass('has-error').addClass('has-success');
            $(this).next().html('');
        }
    });
    $("#malfunction_reason").on('change', function(){
        $("#select_malfunciton").removeClass('has-error').addClass('has-success');
        if ($('#select_malfunciton').find('.help-block').length>0) {
            $('#select_malfunciton').find('.help-block').html('');
        }
    });

    if(taskType == 2){
        $('#myTab a:last').tab('show');
    };

    $("#w0").validation();
    //提交验证
    $("#dailyTaskSubmit").click(function(){

        // 维修任务验证
        var st = $("#start_repair_time").val(), // 开始时间
            et = $("#end_repair_time").val(), // 结束时间
            // 故障原因
            malfunction_reason = $("select[name='malfunction_reason[]']").val(),
            // 故障描述
            malfunction_description = $("#malfunction_description").val(),
            // 处理方法
            process_method = $("#process_method").val(),
            stObj = $('#start_repair_time').next(),
            etObj = $('#end_repair_time').next();
        if (st || et || malfunction_reason || malfunction_description || process_method) {
            // 验证开始时间不能为空
            if (!st) {
                $('#start_repair_time').parent().removeClass('has-success').addClass('has-error');
               if (stObj.attr('class')) {
                   stObj.html('请填写开始维修时间！');
               } else {
                   $('#start_repair_time').parent().append('<span class="help-block" id="valierr">请填写开始维修时间！</span>');
               }
               return false;
            }
            // 验证结束时间不能为空
            if (!et) {
                $('#end_repair_time').parent().removeClass('has-success').addClass('has-error');
                if (etObj.attr('class')) {
                    etObj.html('请填写结束维修时间！');
                } else {
                    $('#end_repair_time').parent().append('<span class="help-block" id="valierr">请填写结束维修时间！</span>');
                }
                return false;
            }
            // 验证开始时间不能大于结束时间
            if (st > et){
                $('#start_repair_time').parent().removeClass('has-success').addClass('has-error');
                if (stObj.attr('class')) {
                    stObj.html('开始维修时间不能大于结束维修时间！');
                } else {
                    $('#start_repair_time').parent().append('<span class="help-block" id="valierr">开始维修时间不能大于结束维修时间！</span>');
                }
                return false;
            }
            // 验证开始时间不能小于打开时间，结束时间不能大于当前时间
            // 获取当前日期
            var myDate = new Date(),
            minutes = myDate.getMinutes() > 9 ? myDate.getMinutes() : '0' + myDate.getMinutes();
            houres = myDate.getHours() > 9 ? myDate.getHours() : '0' + myDate.getHours();
            nowTime = houres + ':' + minutes;

            var taskTime = parseInt(dakaTime+'000'),
            dakaDay = new Date(taskTime),
            taskMinutes = dakaDay.getMinutes() > 9 ? dakaDay.getMinutes() : '0' + dakaDay.getMinutes();
            taskHoures = dakaDay.getHours() > 9 ? dakaDay.getHours() : '0' + dakaDay.getHours();
            taskdate = taskHoures + ':' + taskMinutes,
            todayDate = (new Date()).toLocaleDateString()+' '+st+':59';
            startTime = parseInt((new Date(todayDate)).getTime());

            if (startTime < taskTime) {
                $('#start_repair_time').parent().removeClass('has-success').addClass('has-error');

                if (stObj.attr('class')) {
                    stObj.html('开始维修时间不能小于打卡时间！' + taskdate);
                } else {
                    $('#start_repair_time').parent().append('<span class="help-block" id="valierr">开始维修时间不能小于打卡时间！' + taskdate + '</span>');
                }
                return false;
            }
            if (et > nowTime) {
                $('#end_repair_time').parent().removeClass('has-success').addClass('has-error');
                if (etObj.attr('class')) {
                    etObj.html('结束维修时间不能大于当前时间！');
                } else {
                    $('#end_repair_time').parent().append('<span class="help-block" id="valierr">结束维修时间大于当前时间！</span>');
                }
                return false;
            }
            if (!malfunction_reason) {
                $('#select_malfunciton').removeClass('has-success').addClass('has-error');
                if ($('#select_malfunciton').find('.help-block').length>0) {
                    $('#select_malfunciton').find('.help-block').html('请填写故障原因！');
                } else {
                    $('#select_malfunciton').append('<span class="help-block" id="valierr">请填写故障原因！</span>');
                }
                return false;
            } else {
                $('#select_malfunciton').removeClass('has-error').addClass('has-success');
                if ($('#select_malfunciton').find('.help-block').length>0) {
                    $('#select_malfunciton').find('.help-block').html('');
                }
            }

        }

        // 水单验证
        var surplusWater = $("#surplusWater").val(),    // 剩余水量
            supplierWater = $("#supplierWater").val(),  // 供水商
            needWater = $("#needWater").val();          // 需水量

        if (surplusWater || supplierWater || needWater) {
            if (!surplusWater) {
                $('#surplusWater').parent().removeClass('has-success').addClass('has-error');
                if ($('#surplusWater').next().attr('class')) {
                    $('#surplusWater').next().html('请填写剩余水量！');
                } else {
                    $('#surplusWater').parent().append('<span class="help-block" id="valierr">请填写剩余水量！</span>');
                }
                return false;
            }
            if (!supplierWater) {
                $('#supplierWater').parent().removeClass('has-success').addClass('has-error');
                if ($('#supplierWater').next().attr('class')) {
                    $('#supplierWater').next().html('请填写供水商！');
                } else {
                    $('#supplierWater').parent().append('<span class="help-block" id="valierr">请填写供水商！</span>');
                }
                return false;
            }
            if (!needWater) {
                $('#needWater').parent().removeClass('has-success').addClass('has-error');
                if ($('#needWater').next().attr('class')) {
                    $('#needWater').next().html('请填写需水量！');
                } else {
                    $('#needWater').parent().append('<span class="help-block" id="valierr">请填写需水量！</span>');
                }
                return false;
            }
        }




        if ($("#w0").valid() == false){
                if($(".has-error:first").parent().parents().attr("id")=="ios"||$(".has-error:first").parent().parent().parent().parent().attr("id")=="ios"){
                    $('#myTab a:last').tab('show');
                }else{
                    $('#myTab a:first').tab('show');
                }
            return false;
        } else {
            //点击任务打卡使用微信接口获取经纬度定位
            wx.config({
                debug: false,
                appId: appId,
                timestamp: timestamp,
                nonceStr: nonceStr,
                signature: signature,
                jsApiList: [
                    'getLocation'
                  // 所有要调用的 API 都要加到这个列表中
                ]
            });
           wx.ready(function(){
                 wx.getLocation({
                type: 'gcj02',
                success: function (res) {
                    if (res.latitude && res.longitude) {
                       $("#end_latitude").val(res.latitude);
                       $("#end_longitude").val(res.longitude);
                       // 根据坐标获取地址
                        $.ajax({
                            type: 'GET',
                            url:'http://apis.map.qq.com/ws/geocoder/v1/?location='+res.latitude+','+res.longitude+'&output=jsonp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ',
                            async: false,
                            dataType: 'jsonp',
                            success: function (msg, textStatus) {
                                // 提交数据
                                $("#end_address").val(msg.result.formatted_addresses.recommend);
                                $("#w0").submit();
                            }
                        })
                    }
                }
            })
           });



            wx.error(function (res) {
                alert("获取定位失败，请重试,为什么这么对我");
            });
            // return false;
        }
        // return false;
  })

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      // 获取已激活的标签页的名称
      var activeTab = $(e.target).text();
      // 获取前一个激活的标签页的名称
      var previousTab = $(e.relatedTarget).text();
      $(".active-tab span").html(activeTab);
      $(".previous-tab span").html(previousTab);
    })
    //根据选择的供应商个数，限制新增选项的个数
    $(".groups select").each(function() {
        if (parseFloat($(this).length) === parseFloat($(this).find('option').length)) {
            $(this).parent().parent().find(".btn-primary").attr("disabled", true);
        } else {
            $(this).parent().parent().find(".btn-primary").removeAttr("disabled");
        }
    });

    //配送物料可以选择不同物料不同供应商,新增选项
        $(".delivery_content button").click(function(){
            var malfunction = $(this).data("malfunction");
            var id = $(this).data("id");
            var html = '<div class="form-group"><select class="form-control" check-type="required" required-message="此项不可为空！" name="deliveryTaskStock['+id+'][material_id][]">';
            for(var i in malfunction) {
                if (i) {
                    html += "<option value='"+i+"' data-default='"+malfunction[i]['default_val']+"'>"+malfunction[i]['option']+"</option>";
                }
            }
            html += '</select>';
            html += '<div class=" deliveryTaskStock1" style="vertical-align:top">';
            html += '<input type="text" class="form-control" maxlength="5" name="deliveryTaskStock['+id+'][packets][]" value="">';
            html += '</div><span class="text-type">包</span> ';
            html += '<div class=" deliveryTaskStock1"><input type="text" class="form-control" maxlength="5" name="deliveryTaskStock['+id+'][material_out_gram][]" value="">';
            html += '</div><span class="text-type">克</span> ';
            html += '<button type="button" class="del_material btn btn-danger pull-right" style="margin-top:10px;">删除</button></div>';
            $("[name='deliveryTaskStock["+id+"][total_surplus_material]']").parent().before(html);
            $("[name^=deliveryTaskStock]").change(function(){
                totalMaterial(this);
            });
            //限制新增选项的个数
            var selectNum = $(this).parent().find("select").length;
            var selectOptionNum = $(this).prevAll().find("select option").length;
            if (parseFloat(selectNum) === parseFloat(selectOptionNum)) {
                $(this).attr("disabled", true);
            }
            $(".del_material").click(function(){
                $(this).parent().parent().find(".btn-primary").removeAttr("disabled");
                $(this).parent().remove();
                $("[name^=deliveryTaskStock]").trigger("change");
            })

            $("#w0").validation();

        });

    //全选
    $('#check_all').click(function(){
        if (this.checked){
            $('.checkbox').each(function(){this.checked=true});
        }else{
            $('.checkbox').each(function(){this.checked=false});
        }
    })


    /* 维修反馈 */
    //新增故障原因
    $("#add_malfunction").click(function(){
        var malfunction = $(this).data("malfunction");
        var html = '<dl><select style="width:80%;display:inline-block;" name="malfunction_reason[]" class="form-control"><option value="">请选择</option>';
            for(var i in malfunction) {
                if (i) {
                    html += "<option value='"+i+"'>"+malfunction[i]+"</option>";
                }
            }
            html += '</select><button type="button" class="del_malfunction btn btn-danger pull-right">删除</button></dl>';
        $("#select_malfunciton").append(html);
        $("#select_malfunciton select").select2({
            placeholder: "请选择故障原因",
            allowClear: true,
            theme: "bootstrap"
        });
        $(".del_malfunction").click(function(){
            $(this).parent().remove();
        })
    });
    //新增配件
    var j = 0;
    $("#add_fitting").click(function(){
        var html='<div class="fitting"><div class="form-group"><label>备件名称（已换）</label><input type="text" class="form-control" name="fitting['+j+'][fitting_name]" check-type="required" maxlength=50 /></div><div class="form-group"><label>备件型号</label><input type="text" class="form-control" name="fitting['+j+'][fitting_model]" maxlength="30"/></div><div class="form-group"><label>原厂编号</label><input type="text" class="form-control" name="fitting['+j+'][fitting_number]" maxlength="30"/></div><div class="form-group"><label>数量 <em style="color:#e4393c;size:5px;">(此项需为整数)</em></label><input type="text" class="form-control num" name="fitting['+j+'][fitting_num]" check-type = "required number" range="1~255"/></div><div class="form-group"><label>备注</label><textarea class="form-control" name="fitting['+j+'][remark]" row="5" maxlength="500"/></textarea></div><button type="button" class="del_fitting btn btn-danger" style="float:right;">删除</button><br/><br/><input type="hidden" name="fitting['+j+'][task_id]" value="'+$(this).data("id")+'" /></div>';
        $("#fitting-list").append(html);
        $('.del_fitting').unbind('click');
        $(".del_fitting").click(function(){
            var del1=$(this).parent();
            $('#btn_submit').unbind('click');
            $('#myModal').modal();
            $("#btn_submit").click(function (){
                del1.remove();
            })
        });
        j++;
    })


   /*计算完成后剩余物料*/
   var total_material;
    $("[name^=deliveryTaskStock]").change(function(){
        totalMaterial(this);
    });
    function totalMaterial(obj){
        total_material =null;
        $(obj).parents(".groups").find("input[type='text']").each(function(index,item){
                var grammage = null;
                if($(item).val()!= 0){
                    grammage = $(item).val();
                    if($(item).attr("name").indexOf("[packets]")>0){
                        grammage=parseFloat($(item).val())*parseFloat($(item).parent().parent().find("select option:selected").data("default"));
                    }
                    total_material+=parseFloat(grammage);
                }
         });
         var old_total_material=$(obj).parents(".groups").find("[name*='total_surplus_material']").val();
         total_material = total_material - parseFloat(old_total_material);
         if($(obj).parents(".groups").find('input:radio:checked').val()==1){
            if($(obj).parents(".groups").find("[name*='change_surplus_materia']").val() !== "" && $(obj).parents(".groups").find("[name*='surplus_material']").val()!=0){
                total_material = total_material - parseFloat($(obj).parents(".groups").find("[name*='surplus_material']").val());
            }
         }else{
            if($(obj).parents(".groups").find("[name*='change_surplus_materia']").val() !== ""){
                total_material = total_material - parseFloat($(obj).parents(".groups").find("[name*='change_surplus_materia']").val());
            }
            if($(obj).parents(".groups").find("[name*='surplus_material']").val()!=0){
                total_material = total_material - parseFloat($(obj).parents(".groups").find("[name*='surplus_material']").val());

            }
         }
        if(!isNaN(total_material)){
            $(obj).parents(".groups").find("[name*='total_surplus_material']").val(total_material);
            $(obj).parents(".groups").find("[name*='total_surplus_material']").blur();
        }
    }

    //添料换料验证数据值范围
    $("input[type='radio']").change(function(){
        checkStockTop(this);
        if( $(this).parents(".groups").find(".help-block").length > 0){
            var _this = $(this).parents(".groups").find(".help-block");
            _this.prev("input[type='text']").blur();
        }
    });

    //选择添料或者换料时验证料仓上限制
    function checkStockTop(obj){
        //料仓的上限值
        var stockTopValue = $(obj).parents(".groups").find("[name*='packets']").attr('data-top');
        //添料(可以添加负值,大于负的上限值,小于上限值)
        if($(obj).val() == 1){
            $(obj).parents(".groups").find("[name*='packets']").attr('check-type','int');
            $(obj).parents(".groups").find("[name*='material_out_gram']").attr('check-type','int');

            if ($(obj).parents(".groups").find("[name*='total_surplus_material']").length < 1) {
                $(obj).parents(".groups").find("[name*='packets']").attr('range','-'+stockTopValue+'~'+stockTopValue);
                $(obj).parents(".groups").find("[name*='packets']").attr('check-type','number int');
            };
        }
        //换料(不可以添加负值,并限制不大于料仓上限值)
        if($(obj).val() == 2){
            $(obj).parents(".groups").find("[name*='packets']").attr('check-type','number int');
            $(obj).parents(".groups").find("[name*='material_out_gram']").attr('check-type','number int');
            $(obj).parents(".groups").find("[name*='packets']").attr('range','0'+'~'+stockTopValue);
            $(obj).parents(".groups").find("[name*='material_out_gram']").attr('range','0'+'~'+stockTopValue);
        }
    }

    //验证从后台传来的料仓值修改时不能为空
    $("input[data-flag!='']").change(function() {
        if ($(this).data("flag")) {
            var packets = $(this).parent().parent().find("input[name*='packets']").val();
            var gram = $(this).parent().parent().find("input[name*='gram']").val();
            if($(this).parent().parent().find("input[name*='gram']").length){
                if (packets =="" && gram =="") {
                    $(this).attr("check-type", "required int");
                }
            } else {
                $(this).attr("check-type", "required number int");
            }
        }
    });
    //验证从后台传来的料仓值修改时包和克如果有一个有值就可通过验证
    $("input[name*='material_out_gram']").change(function() {
        if ($(this).parent().parent().find("input[name*='packets']").data("flag")) {
            if ($(this).val() != "" ||$(this).parent().parent().find("input[name*='packets']").val() != "") {
                $(this).parent().siblings().find(".help-block").remove();
                $(this).parent().siblings().removeClass("has-error");
                $(this).parent().parent().find("input[name*='packets']").attr("check-type", "int");
                $(this).parent().parent().find("input[name*='packets']").trigger("blur");
            }else{
               $(this).parent().parent().find("input[name*='packets']").attr("check-type", "required int");
               $(this).parent().parent().find("input[name*='packets']").trigger("blur");
            }
        }
    });
    $.extend($.fn.validation.defaults.validRules.push(
        {name: 'int', validate: function(value) {return (!/^(|0|-?[1-9]\d*)$/.test(value));}, defaultMsg: '请输入整数。'},
        {name: 'ulimit', validate: function(value,err) {
            if( parseInt(value) > parseInt($(this).data("limit"))){
                    return true;
            }
        }, defaultMsg: '超出料仓上限值。' },
        {name: 'llimit', validate: function(value,err) {
            if (parseInt(value) < 0) {
                    return true;
            }
        }, defaultMsg: '低于料仓下限值。'}
    ));

    //触发物料验证规则
    $(".groups input[type='radio']:checked").each(function(){
        $(this).trigger('change');
    });
})