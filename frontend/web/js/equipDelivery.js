$(function(){
    // 选择产品组
    $("#equipments-pro_group_id").change(function(){
        var proGroupId  =   $("#equipments-pro_group_id").val();
        $(".equipments-product").empty();
        $.get(
            "/equip-delivery/pro-group-list",
            {'proGroupId': proGroupId},
            function(data){
                var tr = '';
                for (var i in data) {
                    tr += "<tr><td>"+data[i]+"</td></tr>";
                }
                $(".equipments-product").append(tr);
            },
        'json'
        );
    });
    $('#exampleInputEmail2').focus(function(){
        $('#exampleInputEmail2').prop("type","text");
        $('#exampleInputEmail1').prop("type","password");
    });
    // $('#exampleInputEmail2').blur(function(){
        // $('#exampleInputEmail2').prop("type","password");
    // });
    $('#exampleInputEmail1').focus(function(){
        $('#exampleInputEmail1').prop("type","text");
        $('#exampleInputEmail2').prop("type","password");
    });
    // ajax验证出厂编号是否合法
    var verify = true;
    $('#exampleInputEmail1').blur(function(){
        verify = false;
        // $('#exampleInputEmail1').prop("type","password");
        var factoryValue = $("#exampleInputEmail1").val();
        if(factoryValue == ''){
            $("#exampleInputEmail1").parent().removeClass('has-success').addClass('has-error');
            $("#exampleInputEmail1").next().html('出厂编号不可为空');
            $('#device').val('');
        }else{
            $.get(
                '/equip-delivery/ajax-verify-factory-code',
                {deliveryId:$("#exampleInputEmail1").attr('data-deliverId'), factoryCode:factoryValue},
                function(data) {
                    var datas = $.parseJSON(data);
                    verify = datas.result;
                    if(datas.result == true){
                        $('#device').val(datas.equip_code);
                    }else{
                        $("#exampleInputEmail1").parent().removeClass('has-success').addClass('has-error');
                        $("#exampleInputEmail1").next().html(datas.msg);
                        $('#device').val('');
                    }
                }
            )
        }

    });
    var verifyNext = null;
    $('[name=sim_number]').blur(function(){
        $.get(
            '/equip-delivery/ajax-verify-sim-number',
            {simNumber:$('[name=sim_number]').val()},
            function(data) {
                if (data == true) {
                    $('[name=sim_number]').parent().removeClass('has-success').addClass('has-error');
                    $('[name=sim_number]').next().html('卡号已存在');
                    verifyNext = false;
                }else{
                    verifyNext = true;
                }
            }
        )
    });
    // 点击第一页的下一步
    $(".load-acceptance").click(function() {
        var isSubmit = confirm('请确认出厂编号无误');
        if(!isSubmit){
            return false;
        }
        //验证出厂编号和重复出厂编号是否一致
        var factoryValue = $("#exampleInputEmail1").val();
        var repeatFactoryValue = $("#exampleInputEmail2").val();
        if(factoryValue != repeatFactoryValue){
            alert('两次输入的出厂编号不一致');
            return false;
        }
        if($("#exampleInputEmail1").val()==""||$("select").val()==""){
            $("#exampleInputEmail1,select").trigger('blur');
        }else{
            if (verify == false) {
                return false;
            }else{
                $(".bind").hide();
                $(".acceptance").show();
                $('.submit-acceptance').show();
                $(".button-return").show();
            }
                //首先保存产品组生成配送任务
                $.get(
                    '/equip-delivery/ajax-save-product-group',
                    {
                        deliveryId: $("#exampleInputEmail1").attr('data-deliverId'),
                        factoryCode: $("#exampleInputEmail1").val(),
                        productGroup: $("select").val()
                    },
                    function (data) {

                    });
        }
    });

    // 点击第二页的下一步时
    $(".submit-acceptance").click(function(event) {

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

        // 大屏app版本号
        var big_app_number =  $(".big_app_number").val();
        // 小屏app版本号
        var app_number     =  $(".app_number").val();
        // 验证app版本号是否存在
        if(!app_number || !big_app_number){
            $("#app_version_error").show();
            return false;
        }


        // app版本号错误提示信息
        $("#app_version_error").hide();

        // 验证错误信息调到对应标签页
        var ac1=$('#home').attr("class");
        var ac2=$('#ios').attr("class");
        if(ac1.indexOf("active") > 0){
            if($("[name=sim_card] :selected").val() !== '4' && $("input[name='sim_number']").val()==""||($("[name=sim_card] :selected").val() !== '4' &&verifyNext != true)||$("select").val()==""||$("input[name='concentration']").val()==""||$("#concentration").parent().attr("class")=="form-group has-error"){
                $("input[name='sim_number']").trigger('blur');
                $("input[name='concentration']").trigger('blur');
            }else{
                if(!($("#home").find("#valierr").text()=="请输入数字。")){
                    if($("input[name='leakage_circuit']").val()==""||$("input[name='power_value']").val()==""||$("input[name='meter_model']").val()=="" || $("input[name='timer_model']").val()==""){
                        $('#myTab a:last').tab('show');
                    }else{
                        // 检测设备调试项是否选中 判断是否成功或者失败
                        var ischecked=null;
                        $('input[name="id[]"]').each(function() {
					        if ($(this).prop('checked') ==true) {
					            ischecked = true;
					        }else{
					        	ischecked = false;
					        	return false;
					        }
						});
                        if(ischecked===true){
                                // 显示成功页面 隐藏其余页面
                            $(".acceptance_success").show();
                        }else{
                                // 显示失败页面
                            $(".acceptance_fail").show();
                        }
                        $(".acceptance").hide();
                        $('.submit-acceptance').hide();
                        $(".button-return").hide();
                            //显示2个按钮
                        $(".total_submit").show();
                        $(".back_page").show();
                    }
                }
            }
        }else if(ac2.indexOf("active") > 0){
            if($("input[name='leakage_circuit']").val()==""||$("input[name='power_value']").val()==""||$("input[name='meter_model']").val()=="" || $("input[name='timer_model']").val()==""){
                $("input[name='leakage_circuit']").trigger('blur');
                $("input[name='power_value']").trigger('blur');
                $("input[name='meter_model']").trigger('blur');
                $("input[name='timer_model']").trigger('blur');
            }else{
                if($("select[name='sim_card']").val()!="4" && $("input[name='sim_number']").val()==""||($("select[name='sim_card']").val()!="4" &&verifyNext != true)||$("input[name='concentration']").val()==""||$("#concentration").parent().attr("class")=="form-group has-error"){
                    $('#myTab a:first').tab('show');
                    $("input[name='sim_number']").trigger('blur');
                    $("input[name='concentration']").trigger('blur');
                }else{
                    var ischecked=null;
                    $('input[name="id[]"]').each(function() {
				        if ($(this).prop('checked') ==true) {
				            ischecked = true;
				        }else{
				        	ischecked = false;
				        	return false;
				        }
					});
                    if(ischecked===true){
                                // 显示成功页面 隐藏其余页面
                        $(".acceptance_success").show();
                    }else{
                                // 显示失败页面
                        $(".acceptance_fail").show();
                    }
                    $(".acceptance").hide();
                    $('.submit-acceptance').hide();
                    $(".button-return").hide();
                        //显示2个按钮
                    $(".total_submit").show();
                    $(".back_page").show();
                }
            }
        }
    })
    // 点击返回上一页
    $(".back_page").click(function() {
        $(".acceptance").show();
        $(".acceptance_success").hide();
        $(".acceptance_fail").hide();
         //隐藏2个按钮
        $(".total_submit").hide();
        $(".back_page").hide();
        $(".error1").hide();
        $(".button-return").show();
        $('.submit-acceptance').show();
    });

    $(".button-return").click(function() {
        $(".bind").show();
        $(".acceptance").hide();
        $('.submit-acceptance').hide();
        $(".button-return").hide();
    });

    //提交验证
    var t = null;
    $("#w0").validation();
    $("#task_submit").click(function(){

        //验收失败时验证故障现象和备注不能同时为空
        if($(".acceptance_fail").css("display")=="block"){
            if($("input:checkbox[name='content[]']:checked").length<=0 && $("textarea[name='fail_remark']").val()==""){
                $(".error1").show();
                return false;
            }
        }else if($(".acceptance_success").css("display")=="block"){
        	$("#delivery_result").parent().addClass("form-group");
        	$("#delivery_result").attr("check-type","required");
        }
        if ($('#w0').valid() == false){
            return false;
        } else {
        	$(".mask").show();
		    t = window.setTimeout(function(){
		        $(".mask").hide();
		        window.clearTimeout(t);
		        t = null;
		    }, 10000);
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
                            	//清除缓存
                            	if (localStorage) {
									if(localStorage.length>0 && localStorage.getItem("'"+relevant_id+"'")!=null){
										localStorage.removeItem("'"+relevant_id+"'");
									}
								}
                                // 提交数据
                                $("#end_address").val(msg.result.formatted_addresses.recommend);
								$("#w0").submit();
                            }
                        })
                    }
                }
            }),
            wx.error(function (res) {
                alert("获取定位失败，请重试");
            });
            return false;
        }
    })

    // 验收成功选择运营状态原因和备注的显示和隐藏
    $(".reson").hide();
    $(".remark").hide();
    $("#delivery_result").change(function() {
        var deliveryResult  = $("#delivery_result").val();
        if(deliveryResult==1){
            $(".reson").show();
            $(".remark").show();
        }else{
            $(".reson").hide();
            $(".remark").hide();
        }
    });
    //选择网络类型
    $("[name=sim_card]").on('change', function () {
        if ($(this).children('option:selected').val() == 4) {
            $('input[name=sim_number]').val('');
            $('#sim_number_id').hide();
            $('input[name=sim_number]').removeAttr("check-type");
        } else {
            $('input[name=sim_number]').attr("check-type","required number");
            $('#sim_number_id').show();
        }
    });
    //本地保存数据功能
    var relevant_id=$("form").attr("action");
    var index=relevant_id.indexOf("=");
   	relevant_id=relevant_id.slice(index+1);
   	var cache = new Object();
    if (localStorage) {
    	if(localStorage.length>0 && localStorage.getItem("'"+relevant_id+"'")!=null){
    		cache=JSON.parse(localStorage.getItem("'"+relevant_id+"'"));
			if (cache.factory_code) {
	          $("input[name='factory_code']").val(cache.factory_code);
	          $("input[name='factory_code']").trigger('blur');
              $("input[name='repeat_factory_code']").val(cache.repeat_factory_code);
              $("input[name='repeat_factory_code']").trigger('blur');
	        }
	        if (cache.device) {
	          $("input[name='device']").val(cache.device);
	        };
	        if (cache.pro_group_id) {
                $("select[name='pro_group_id']").val(cache.pro_group_id);
                $("select[name='pro_group_id']").find("option[value='"+cache.pro_group_id+"']").attr("selected", true);
	        }
	        if (cache.sim_card) {
	          $("select[name='sim_card']").find("option[value=" + cache.sim_card + "]").attr("selected", true);
	          if(cache.sim_card=="4"){
	          	$("input[name='sim_number']").val("");
	          	 $('input[name=sim_number]').removeAttr("check-type");
	          	$('#sim_number_id').hide();
	          	cache.sim_number="";
	          }
	        }
	       	if (cache.sim_number) {
	          $("input[name='sim_number']").val(cache.sim_number);
	          $("input[name='sim_number']").trigger('blur');
	        }
	       	if(cache.concentration){
				$("input[name='concentration']").val(cache.concentration);
				$("input[name='concentration']").trigger('blur');
			}
	       	if(cache.leakage_circuit){
	       		$("input[name='leakage_circuit']").val(cache.leakage_circuit);
	       	}
	       	if(cache.meter_model){
	       		$("input[name='meter_model']").val(cache.meter_model);
	       	}
	       	if(cache.timer_model){
	       		$("input[name='timer_model']").val(cache.timer_model);
	       	}
	       	if(cache.power_value){
	       		$("input[name='power_value']").val(cache.power_value);
	       	}
            if(cache.timer_model){
                $("input[name='timer_model']").val(cache.timer_model);
            }
	       	if(cache.debug_item){
	       		for(var j in cache.debug_item){
	       			$("input[name='debug_item[]'][value="+j+"]").prop("checked",cache.debug_item[j]);
	       		}
	       	}
	        if (cache.idcked) {
		       	var fizzle;
	        	var page = cache.idcked;
			    for(var j in page){
			    	$("input[name='id[]'][value="+j+"]").prop("checked",page[j]);
			    	if(page[j]===false){
			    	    fizzle="out";
			    	}
			    }
	        	if(fizzle==="out"){
		          	if (cache.contentcked) {
			        	var content = cache.contentcked;
					    for(var j in content){
					    	$("input[name='content[]'][value="+j+"]").prop("checked",content[j]);
					    }
			        }
		          	if (cache.fail_remark) {
			        	$("textarea[name='fail_remark']").text(cache.fail_remark);
			        }
	        	}else{

	        		if (cache.delivery_result) {
			            $("select[name='delivery_result']").find("option[value=" + cache.delivery_result + "]").attr("selected", true);
						if(cache.delivery_result=="1"){
						   $("textarea[name='reason']").parent().parent().show();
						   $("textarea[name='remark']").parent().parent().show();
							if(cache.reason){
								$("textarea[name='reason']").text(cache.reason);
							}
							if(cache.remark){
								$("textarea[name='remark']").text(cache.remark);
							}
	          			}
	        		}
	        	}
			}
    	}
        $("input[type=text],select,textarea").change(function(){
          $this = $(this);
          if($this.attr("name")=="delivery_result" && $this.val()==1){
          		cache.reason = $this.val();
          		cache.remark = $("textarea[name='remark']");
          }
          if($this.attr("name")=="sim_card" && $this.val()==4){
          		cache.sim_number="";
          }
          cache.device = $("input[name='device']").val();
          cache[$this.attr("name")] = $this.val();
        });

        var idcked=[],contentcked=[],debug_item=[];
        $("input[type=checkbox]").change(function(){
          	$this = $(this);
          	$("input[name='id[]']").each(function(){
          		 idcked[$(this).val()]=$(this).prop("checked");
          	});
          	cache.idcked = idcked;
      		$("input[name='content[]']").each(function(){
          		contentcked[$(this).val()]=$(this).prop("checked");
          	});
          	cache.contentcked=contentcked;
          	$("input[name='debug_item[]']").each(function(){
          		debug_item[$(this).val()]=$(this).prop("checked");
          	});
          	cache.debug_item = debug_item;
        })
        $(".submit-acceptance").bind('click',function(){
			if($(".acceptance_success").css("display")=="block"){
				$("input[name='content[]']").each(function(){
          			$(this).prop("checked",false);
          		});
          		$("textarea[name='fail_remark']").val("");
				cache.content="";
				cache.fail_remark="";
			}else if($(".acceptance_fail").css("display")=="block"){
				$("select[name='delivery_result']").val("");
				$(".reson").hide();
            	$(".remark").hide();
				$("textarea[name='reason']").val("");
				$("textarea[name='remark']").val("");
				cache.result="";
				cache.remark="";
			}
       })
        $("#w0").change(function(){
        	//console.log(localStorage);
        	localStorage.setItem("'"+relevant_id+"'", JSON.stringify(cache));
        });
      }
});