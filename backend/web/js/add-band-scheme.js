$(function(){
    //开始时间和结束时间的验证
    $.extend($.fn.validation.defaults.validRules.push(
        {name: "ints", validate: function(value) {return (!/^(|0|\+?[1-9]\d*)$/.test(value));}, defaultMsg: "请输入整数。"},
        {name: "compareSize", validate: function(value) {
                if($("#end_time").val()&& parseFloat($("#start_time").val())>=parseFloat($("#end_time").val())){
                    return true;
                }else{
                    return false;
                }
            }, defaultMsg: "结束时间必须大于开始时间。"}
    ))
    $(".table-bordered").validation();
    $(".scheme > div:nth-child(1)").hide();
    // 搜索时进行ajax查询数据
    $(".search").click(function(){
        var startTime   =   $("#start_time").val();
        var endTime     =   $("#end_time").val();
        var equipScenarioName   =   $("#equip_scenario_name").val();
        var scenarioName=   $("#scenario_name").val();
        var productGroup=   $("#product_group_id").val();
        $.ajax({
            type: "POST",
            data: {equip_scenario_name: equipScenarioName, scenario_name: scenarioName, product_group_id: productGroup, start_time: startTime, end_time: endTime},
            url:"/light-belt-program/get-search-scenario",
            async: false,
            dataType: "json",
            success: function (data) {
                if ($.isArray(data) && data.length <= 0) {
                     $(".scheme > div:nth-child(1)").show();
                     $(".checkbox").html("暂无数据");
                     $(".add_scene").hide();
                } else {
                    var gettpl = document.getElementById("scene_template").innerHTML;
                    laytpl(gettpl).render(data,function(html){
                        $(".checkbox").html(html);
                    });
                    $(".add_scene").show();
                    $(".scheme > div:nth-child(1)").show();
                }
            }
        })
    })
    //往右侧添加灯带场景
    $(".add_scene").on("click",function(){
        var html = null;
        var noRepeat=null;
        if($("input[type=\'checkbox\']:checked").length>0){
            $("input[type=\'checkbox\']:checked").each(function(){
                var _this=$(this);
                if($("form input[type=hidden]").length>0){
                    $("form input[type=hidden]").each(function(){
                        if($(this).val()==_this.attr("data-value")){
                            noRepeat = false;
                            $("#tsModal").modal();
                            $("#tsModal .title").html(_this.parent().text()+"场景重复");
                            return false;
                        }else{
                            noRepeat = true;
                        }
                    });
                }else{
                     noRepeat = true;
                }
                if(noRepeat){
                     html ="<li class=\'list-group-item\'>"+_this.parent().text()+"<input name=\'scenarioArr[]\' type=\'hidden\' value=\'"+_this.attr("data-value")+"\'/><span class=\"glyphicon glyphicon-trash del-scene\"></span></li>";
                    $("form ul").append(html);
                    // 删除左边的勾选添r加项
                    $(this).removeAttr("checked");
                }
            });
        }

        $(".del-scene").on("click",function(){
            $(this).parent().remove();
        })
    });
    $("input[name=program_name]").parent().validation();
    $("input[name=default_strategy_id]").parent().validation();
    $("#lightbeltprogram-default_strategy_id").change(function(){
        var strategyId = $("#lightbeltprogram-default_strategy_id").val();
        if(strategyId)
        {
            $(".default_strategy_id").removeClass("has-error");
            $(".default_strategy_id").find(".help-block").html("");
        }
    })

    $(".btn-success").on("click",function(){
        var _this = $(this);
        if($("[name=program_name],[name=default_strategy_id]").parent().valid() == false){
             return false;
         }else{
            if($(".list-group li").length>0){
                var defaultStrategyId = $("#lightbeltprogram-default_strategy_id").val();
                if(!defaultStrategyId){
                    $(".default_strategy_id").addClass("has-error");
                    $(".default_strategy_id").find(".help-block").html("默认策略不可为空");
                    return false;
                }
                _this.attr('disabled',true);
                var scenarioArr = [];
                $("input[name^='scenarioArr']").each(function(index, value) {
                        scenarioArr.push($(this).val());
                });
                $.ajax({
                    type: "POST",
                    data: {"scenarioArr":scenarioArr},
                    url: "/light-belt-program/check-scenario",
                    dataType: "json",
                    success: function (data) {
                        if(data.length > 1){
                            var html = " ";
                            $.each(data,function(key, value) {
                                if (value !="") {
                                    html += '<p>' + value + '</p>';
                                }
                            });
                           $("#tsModal").modal();
                           $("#tsModal .title").html(html);
                           _this.removeAttr('disabled');
                        }else{
                            $("form").submit();

                        }

                    }
                })
            }else{
                $("#tsModal").modal();
                $("#tsModal .title").html("场景不能为空。");
            }
        }

    });
    if(schemeData.length != 0 ){
        $("input[name=program_name]").val(schemeData.program_name);
        $("select[name=default_strategy_id").val(schemeData.default_strategy_id);
        var gettpls = document.getElementById("checked_scene_template").innerHTML;
        laytpl(gettpls).render(schemeData.scenarioArr,function(html){
            $("form ul").html(html);
        });
        $(".del-scene").on("click",function(){
            $(this).parent().remove();
        })
    }
})