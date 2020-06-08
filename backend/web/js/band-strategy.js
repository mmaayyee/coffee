$(function(){
	var	lightData=[
		   "1号灯带","2号灯带", "3号灯带", "4号灯带","5号灯带","6号灯带", "7号灯带", "8号灯带","9号灯带","10号灯带", "11号灯带","12号灯带"
		];
	var add_status = true;
	var checkedVal = (sessionStorage.getItem('key'))? sessionStorage.getItem('key'): 0;

	var lightBelt ={};
	//灯带组模板
	var gettpl = document.getElementById("band_template").innerHTML;
	//颜色渐变组模板
	var gettpls = document.getElementById("gradient_template").innerHTML;

	laytpl(gettpl).render(lightData,function(html){
		$("#band").html(html);
	});
	if(bandStrategy){
		$("input[name=strategy_name]").val(bandStrategy.strategy_name);
		$("select[name=light_belt_type]").val(bandStrategy.light_belt_type);
		if(bandStrategy.light_belt_type==1){
			$("input[name=total_length_time]").val(bandStrategy.total_length_time);
			var referrerUrl = document.referrer;
            if(referrerUrl.indexOf("/light-belt-strategy/update")  <= -1 && referrerUrl.indexOf("/light-belt-strategy/create") <= -1){
                checkedVal = 0;
                sessionStorage.clear();
            }
			if(bandStrategy.lightBeltArr){
				$("#allcheck").attr("disabled","disabled");
				$("input[name^='lightName'][value="+checkedVal+"]").prop("checked",true);
				$.each(bandStrategy.lightBeltArr,function(index,item){
					$("input[name^=lightName][value="+index+"]").attr("data-radio",index);
					$("input[name^=lightName][value="+index+"]").parent().addClass("text-primary");
					if(index==checkedVal){
						lightBelt["ligth"+index] = item;
					}
				});
			}
			//在页面添加灯带颜色渐变组模板
			laytpl(gettpls).render(lightBelt,function(html){
				$(".gradient_group").html(html);
			});
		}else{
			$("select[name=light_status]").val(bandStrategy.light_status);

			if(bandStrategy.light_status==1){
				$("input[name=light_belt_color]").val(bandStrategy.light_belt_color);
			}else if(bandStrategy.light_status == 0) {
				$("input[name=light_status]").val(bandStrategy.light_status);
				$("input[name=light_status]").show();
			}else if(bandStrategy.light_status==2){
				$("input[name=flicker_frequency]").val(bandStrategy.flicker_frequency);
				$("input[name=light_belt_color]").val(bandStrategy.light_belt_color);
			}
		}
		initGradient();
	}else{
			laytpl(gettpls).render(lightData,function(html){
				$(".gradient_group").html(html);
			});
			initGradient();
	}
	lightIsShow();
	//添加灯带颜色渐变组
	$(".add").on("click",function(){
		if(add_status){
			laytpl(gettpls).render(lightData,function(html){
				$(".gradient_group").append(html);
			});
			initGradient();
			$("input[name*=Time]").change(function(){
				verificationTime(this);
			});
			$("input[name*=Time]").trigger("change");
		}else{
			$("#tsModal").modal();
		}
		//删除渐变组
		$(".del").on("click",function(){
			$(this).parent().remove();
			$("input[name*=Time]").trigger("change");
		});
	});
	//选中的灯带显示相对应的颜色渐变组
	function lightBeltArr(){
		$.each(bandStrategy.lightBeltArr,function(index,item){
				if(index==checkedVal){
					lightBelt["ligth"+index] = item;
				};
			});
		//在页面添加灯带颜色渐变组模板
		$(".gradient_group").html("");
		laytpl(gettpls).render(lightBelt,function(html){
			$(".gradient_group").html(html);
		});
		initGradient();
	}
	//全选
	$("#allcheck").on("click",function(){
		if(this.checked){
	        $("input[type=checkbox]").prop("checked", true);
	    }else{
	        $("input[type=checkbox]").prop("checked", false);
	    }
	});
	$("input[name^=light]").change(function(){
		if($(this).attr("data-radio")){
			var _this=$(this);
			checkedVal = _this.val();
			$("input[name^=light]").each(function(){
				if($(this).val()!= _this.val()){
					$(this).prop("checked", false);
				}
			});
		}else{
			$("input[name^=light]").each(function(){
				if($(this).attr("data-radio")){
					$(this).prop("checked", false);
				}
			});
			if($(this).attr("checked")!=true && $("#allcheck").prop("checked")==true){
				$("#allcheck").prop("checked", false);
			}else {
				if($("input[name^=light]").length == $("input[name^=light]:checked").length){
					$("#allcheck").prop("checked", true);
				}
			}
		}
		if(bandStrategy){
			lightBeltArr();
		}
	});

	$("input[name*=Time]").change(function(){
		verificationTime(this);
	});
	$.extend($.fn.validation.defaults.validRules.push(
		{name: "ints", validate: function(value) {return (!/^(0|\+?[1-9]\d*)$/.test(value));}, defaultMsg: "请输入整数。"}
	))

	$("select[name=light_belt_type]").change(function(){
		lightIsShow();
	});
	$("select[name=light_status]").change(function(){
		flickerIsShow();
	});
	//表单提交
	$(".btn-success").on("click",function(){
		if($("select[name=light_belt_type]").val()==="1"){
			if ($("input[name=strategy_name]").parent().valid() == false||$("form .single").valid() == false){
	           return false;
	        }else{
	        	if($("input[name^=lightNameArr]:checked").length>0){
	        		$("#band .errs").remove();
		        	$("input[name*=Time]").trigger("change");
					if(add_status){
						var totalLengthTime = $("input[name=total_length_time]").val();
						var lastEndTime = $(".gradient_group>div:last-child").find("input[name^=endTime]").val();
						if(parseFloat(totalLengthTime) >=parseFloat(lastEndTime)){
							$("input[name^='lightName']").each(function(){
								if(this.checked){
									sessionStorage.setItem('key', $(this).val());
							     	return false;
								}
							});
							$("form").submit();
						}else{
							$("#tsModal").modal();
							$("#tsModal #myModalLabel").text("提示框");
							$("#tsModal .title").text("最后一组颜色渐变的结束时间不能大于灯带周期时间");
						}
					}else{
						$("#tsModal").modal();
					}
	        	}else{
	        		if($(".errs").text()==""){
		        		$("#band").append("<span class=\"errs\">必须选中1个灯带。</span>");
	        		}
	        	}
	        }
		}else{
			if($("select[name=light_status]").val()==="0" && $("input[name=strategy_name]").parent().valid() == false){
					return false;
			}else if($("select[name=light_status]").val()==="2" && ($("input[name=strategy_name]").parent().valid() == false || $("input[name=flicker_frequency]").parent().valid() == false || $("input[name=light_belt_color]").parent().parent()== false)){
					return false;
			}else if($("select[name=light_status]").val()==="1" && ($("input[name=strategy_name]").parent().valid() == false || $("input[name=light_belt_color]").parent().parent().valid() == false)){
	          		return false;
	        }else{
	        	$("form").submit();
	        }
		}
	});
	//判断开始时间不能小于结束时间
	var verificationTime = function(obj){
		if($(".bordered").length>0){
			var startTime=$(obj).parent().parent().parent().find("input[name^=startTime]").val();
			var endTime=$(obj).parent().parent().parent().find("input[name^=endTime]").val();
			if(parseFloat(startTime)>parseFloat(endTime)){
				$("#tsModal #myModalLabel").text("提示框");
				$("#tsModal .title").text("同一组颜色渐变的开始时间不能大于结束时间");
				add_status = false;
			}else{
				if($(".bordered").length>1){
					var prevEndTime = $(obj).parent().parent().parent().prev().find("input[name^=endTime]").val();
					var currentStartTime = $(obj).parent().parent().parent().find("input[name^=startTime]").val();
					var currentEndTime = $(obj).parent().parent().parent().find("input[name^=endTime]").val();
					var nextStartTime = $(obj).parent().parent().parent().next().find("input[name^=startTime]").val();
					if(parseFloat(prevEndTime)>parseFloat(currentStartTime)||parseFloat(currentEndTime)>parseFloat(nextStartTime)){
						$("#tsModal #myModalLabel").text("提示框");
						$("#tsModal .title").text("后一组颜色渐变的开始时间不能小于前一组颜色渐变的结束时间");
						add_status = false;
					}else{
						add_status = true;
					}
				}else{
					add_status = true;
				}
			}
		}
	}
	//根据选择的灯带控制类型不同，是否显示单个灯带
    function lightIsShow(){
        if($("select[name=light_belt_type]").val()==="1"){
            $(".whole").hide();
            $(".single").show();
            if($(".gradient_group .bordered").length < 1){
                    lightBeltArr();
            }
        }else{
            $(".whole").show();
            $(".single").hide();
            flickerIsShow();
        }
    }
})


function flickerIsShow(){
	if($("select[name=light_status]").val()=="0"){
		// $("#").show();
		$("input[name=flicker_frequency]").parent().hide();
		$("input[name=light_belt_color]").parent().parent().hide();
	}else if($("select[name=light_status]").val()=="2"){
		$("input[name=flicker_frequency]").parent().show();
		$("input[name=light_belt_color]").parent().parent().show();
	}else {
		$("input[name=flicker_frequency]").parent().hide();
		$("input[name=light_belt_color]").parent().parent().show();
	}
}
function initGradient(){
	//表单验证
	$("form").validation();
	//调用取色器
	$(".color").each( function() {
		$(this).minicolors({
			control: $(this).attr("data-control") || "hue",
			defaultValue: $(this).attr("data-defaultValue") || " ",
			inline: $(this).attr("data-inline") === "true",
			letterCase: $(this).attr("data-letterCase") || "lowercase",
			opacity: $(this).attr("data-opacity"),
			position: $(this).attr("data-position") || "bottom left",
			change: function(hex, opacity) {
				if( !hex ) return;
				if( opacity ) hex += ", " + opacity;
				try {
				} catch(e) {}
			},
			theme: "bootstrap"
		});
	});
}
