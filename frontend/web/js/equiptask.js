$(function(){
    var address = $("#address").html(),
        geocoder,map,marker = null;
    map = new qq.maps.Map(document.getElementById('allmap'),{
        center: new qq.maps.LatLng(39.916527,116.397128),
        zoom: 12,
        disableDefaultUI: true
    });

    geocoder = new qq.maps.Geocoder({
        complete : function(result){
            map.setCenter(result.detail.location);
            map.zoomTo(18);
            marker = new qq.maps.Marker({
                map:map,
                position: result.detail.location
            });
        }
    });

    geocoder.getLocation(address);

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
    $("#task_start").click(function(){
    	$(".loaded").show();
        // 微信定位获取坐标
    	wx.getLocation({
		    type: 'gcj02', 
		    success: function (res) {
                // 根据坐标获取地址
                $.ajax({
                    type: 'GET',
                    url:'http://apis.map.qq.com/ws/geocoder/v1/?location='+res.latitude+','+res.longitude+'&output=jsonp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ',                  
                    async: false,
                    dataType: 'jsonp',
                    success: function (msg, textStatus) {
                    	$(".loaded").hide();
                        // 提交数据
                        $("#Modal1").modal();
                        $("#Modal1 #btn_submit").click(function (){
                            $("#start_longitude").val(res.longitude);
                            $("#start_latitude").val(res.latitude);
                            $("#start_address").val(msg.result.formatted_addresses.recommend);
                            $("#reciveSave").submit();
                        }) 
                    }
                })
	    		           
        	}
		})
        wx.error(function (res) {
        	$(".loaded").hide();
            alert("获取定位失败，请重试"); 
        });
    });

    /* 灯箱验收任务js*/
    //全选
    /*$('#check_all').click(function(){
        if (this.checked){
            $('.checkbox').each(function(){this.checked=true});
        }else{
            $('.checkbox').each(function(){this.checked=false});
        }
    })*/
    if ($("#acceptance_process").length>0) {
        $("#acceptance_process").validation();
        //提交验证
        $("#acceptance-save").click(function(){
            if ($('#acceptance_process').valid() == false){
               return false;
            } else {
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
                                    $("#acceptance_process").submit();
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
    }
    /* 灯箱验收任务js*/

    /* 维修反馈 */
    //新增故障原因
    $("#add_malfunction").click(function(){
        var malfunction = $(this).data("malfunction");
        var html = '<dl><select style="width:75%;" name="malfunction_reason[]" class="form-control"><option value="">请选择</option>';
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
        var html='<div class="fitting"><div class="form-group"><label>备件名称（已换）</label><input type="text" class="form-control" name="fitting['+j+'][fitting_name]" check-type="required" maxlength=50 /></div><div class="form-group"><label>备件型号</label><input type="text" class="form-control" name="fitting['+j+'][fitting_model]" maxlength="30"/></div><div class="form-group"><label>原厂编号</label><input type="text" class="form-control" name="fitting['+j+'][fitting_number]" maxlength="30"/></div><div class="form-group"><label>数量</label><input type="text" class="form-control num" name="fitting['+j+'][fitting_num]" check-type = "required number" range="1~255"/></div><div class="form-group"><label>备注</label><textarea class="form-control" name="fitting['+j+'][remark]" row="5" maxlength="500"/></textarea></div><button type="button" class="del_fitting btn btn-danger"style="float:right"> 删除 </button><br/><br/><input type="hidden" name="fitting['+j+'][task_id]" value="'+$(this).data("id")+'" /></div>';
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
    document.body.addEventListener('touchmove', function (event) {
    	if ($("body").attr("class")=="modal-open") {
    		event.preventDefault();    		
    	} 
	});
    if ($("#task_process").length>0) {
        $("#task_process").validation();
        //提交验证
        $("#repair_submit").click(function(){

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

        	if ($("#task_process").valid() == false){
               	return false;
            }else{
            	wx.getLocation({
		    		type: 'gcj02', 
		    		success: function (res) {
                        if (res.latitude && res.longitude) {
		    			   $("#latitude1").val(res.latitude);
	    				   $("#longitude1").val(res.longitude);
                           // 根据坐标获取地址
                            $.ajax({
                                type: 'GET',
                                url:'http://apis.map.qq.com/ws/geocoder/v1/?location='+res.latitude+','+res.longitude+'&output=jsonp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ',                  
                                async: false,
                                dataType: 'jsonp',
                                success: function (msg, textStatus) {
                                    // 提交数据
                                    $("#end_address").val(msg.result.formatted_addresses.recommend);
                                    $("#task_process").submit();
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
    }
    /* 维修反馈 */
   
	//在附件任务中根据任务状态选择备注是否必填
   $("textarea[name='remark']").parent().find("#autoreqmark").hide();
	$("#process_result").change(function(){
		if($(this).val()==3){
			$("textarea[name='remark']").attr("check-type","required");
			$("textarea[name='remark']").parent().addClass("form-group");
			$("textarea[name='remark']").parent().find("#autoreqmark").show();
		}else{
			$("textarea[name='remark']").parent().attr("class","form-group");
			$("textarea[name='remark']").parent().find("#autoreqmark").hide();
			$("textarea[name='remark']").removeAttr("check-type");
			$("textarea[name='remark']").parent().find("#valierr").remove();
		}
	});
})