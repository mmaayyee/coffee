$(function(){
	var buildingData = null;
	var getSearchResults=function(page){
			var pageSize = 20;//每页展示的条数
			var buildingName = $("input[name='buildingName']").val();
			var buildingType = $("select[name='buildingType']").val();
			var branch = $("select[name='branch']").val();
			var agent = $("select[name='agent']").val();
			var partner = $("select[name='partner']").val();
			var equipmentType = $("select[name='equipmentType']").val();
			var equipNumber = $("input[name='equipNumber']").val();
			$.ajax({
				 type: "POST",
	             url: "/discount-building-assoc/get-search-build",
				 data: {"buildingName":buildingName,'equipNumber':equipNumber,"buildingType":buildingType,"branch":branch,"agent":agent,"partner":partner,"equipmentType":equipmentType,"page":page,"pageSize":pageSize},
	             dataType: "json",
	             success: function(data){
					if(data){
						if(data.totalCount > 0){
						    $(".block-a .no-data").hide();
                            $(".block-a .searchResult").show();
						    buildingData = data.buildArr;

						    var gettpl = document.getElementById("building_template").innerHTML;
							$(".searchResult tbody").html("");
							laytpl(gettpl).render(buildingData,function(html){
								$(".searchResult tbody").html(html);
							});
							initButStatus();
							var counts=data.totalCount;
							paging(counts,page,pageSize);
							$(".searchResult .SortId").each(function(index, value) {
							    var serialNumber = (parseInt(page) - 1) * parseInt(pageSize) + parseInt(index) + 1;
							    $(this).attr("data-text", serialNumber);
							})
						} else {
						    $(".block-a .searchResult").hide();
                            $(".block-a .no-data").show();
					    }
					    $('#searchResult').attr('disabled',false)
	                }
	                $('#searchResult').attr('disabled',false)
			    }
			});
	}
	//楼宇搜索结果分页
	function paging(counts,page,pageSize){
		var pagecount= counts % pageSize == 0 ? counts / pageSize:counts/pageSize+1;
		var laypages = laypage({
		    cont: $(".searchResult .pages"),
		    pages: pagecount, //通过后台拿到的总页数
            curr: page,
            hash: true,
            first: false,
            last: false, //将尾页显示为总页数。若不显示，设置false即可
            prev: '&laquo;', //若不显示，设置false即可
            next: '&raquo;', //若不显示，设置false即
		    jump: function(obj,first){
		    	if(!first){
		    		getSearchResults(obj.curr);
		    		window.location.hash = "#searchResult";
		    	}
		    }
		})
	}
	//搜索
    getSearchResults(1);
    $(".search").on("click",function(){
    	$(this).attr('disabled','disabled')
        getSearchResults(1);
    });
	//批量添加
	$(".allAdd").on("click",function(){
		var html = null;
		$(".searchResult tbody tr").each(function(){
            if ($(this).find("button").attr("disabled")!="disabled") {
                var tr = "<tr>"+$(this).html()+"</tr>";
                html += tr;
            }
		});
		$(".addPreview table").find("tbody").append(html);
		$(this).parents(".searchResult").find("button").attr("disabled",true);
		$(".addPreview tbody").find("button").removeClass("add").addClass("delete");
		$(".addPreview tbody").find(".glyphicon").removeClass("glyphicon-plus").addClass("glyphicon-minus");
		$(".allDelete").removeAttr("disabled");
		if(isPayment()){
			$(".btn-success").removeAttr("disabled");
		}
		deleteItem();

	});
	function addBuilding(){
		$(".add").on("click",function(){
			var buildingItem = $(this).parent().parent("tr").html();
			var html="<tr>"+buildingItem+"</tr>";
			$(".addPreview table").find("tbody").append(html);
			$(this).attr("disabled",true);
			$(".addPreview tbody").find("button").removeClass("add").addClass("delete");
			$(".addPreview tbody").find(".glyphicon").removeClass("glyphicon-plus").addClass("glyphicon-minus");
			deleteItem();
			if(isPayment()){
				$('.btn-success').removeAttr("disabled")
			}
			$(".allDelete").removeAttr("disabled");
			if($(".searchResult .add").length==$(".searchResult .add:disabled").length){
				$(".allAdd").attr("disabled",true);
			}else{
				$(".allAdd").removeAttr("disabled");
			}
		});
	}
	//批量删除
	$(".allDelete").on("click",function(){
		$(this).parents(".addPreview").find("tbody").html("");
		$(".searchResult").find("button").removeAttr("disabled");
		$(this).attr("disabled",true);
		$(".btn-success").attr("disabled",true);
	});
	function deleteItem(){
		$(".delete").on("click",function(){
			$(this).parent().parent().remove();
			buttonStatus(this);
			if(!isPayment() || !isSortId()){
				$(".btn-success").attr("disabled",true);
			}
		});
	}
	function buttonStatus(obj){
		if($(".addPreview tbody").find("tr").length>0){
			var val=$(obj).prev().val();
			$(".searchResult input[value="+val+"]").next().removeAttr("disabled");
			if($(".searchResult .add").length==$(".searchResult .add:disabled").length){
			    $(".allAdd").attr("disabled",true);
			}else{
                $(".allAdd").attr("disabled",false);
			}
		}else{
			$(".searchResult").find("button").removeAttr("disabled");
			$(".addPreview").find("button").attr("disabled",true);
		}
	}
    function initButStatus(){
		addBuilding();
		$(".addPreview .delete").each(function(){
			var val=$(this).prev().val();
			if($(".searchResult input[value="+val+"]")){
				$(".searchResult input[value="+val+"]").next().attr("disabled",true);
			}
		});
		if($(".searchResult .add").length==$(".searchResult .add:disabled").length){
				$(".allAdd").attr("disabled",true);
		}else{
			$(".allAdd").removeAttr("disabled");
		}
    }
    $('.payment').each(function(e,i){
		var payTypeId = $(this).data('id');
    	if ($(this).is(":checked")) {
    		$(".discount-holicy-"+payTypeId).show();
    	} else {
    		$(".discount-holicy-"+payTypeId).hide();
    	}
    })
    $('.payment').click(function(){
    	var payTypeId = $(this).data('id');
    	if ($(this).is(":checked")) {
    		$(".discount-holicy-"+payTypeId).show();
    		if (isSortId()) {
    			$(".btn-success").removeAttr("disabled");
    		}
    	} else {
    		$(".discount-holicy-"+payTypeId).hide();
    		if(!isPayment()){
    			$(".btn-success").attr("disabled",true);
    		} else {
    			$(".btn-success").removeAttr("disabled");
    		}
    		$(this).parents('.col-lg-6').find('.weight-error').hide();
    	}
    });
    function isPayment(){
    	var pay = 0;
    	$(".payment").each(function(){
    		if($(this).is(':checked')){
    			pay=1;
    		}
    	})
    	return pay;
    }
    function isSortId(){
    	if($(".overflow").find('.SortId').length > 0){
    		return true;
    	}else{
    		return false;
    	}
    }

    //上传
    $('.upload-building').click(function(){
    	if($('.search-building').attr('disabled')){
    		$('.search-building').removeAttr('disabled')
    		$('.search-building').attr('background-color','#2e6da4')
    		$('#upload-building').show();
    		$('.form-inline').hide();
    		$('.block-a').hide();
    		$(this).attr('disabled',true)
    		$(this).attr('background-color','#EDEDED')
    	}
    })
    $('.search-building').click(function(){
    	if($('.upload-building').attr('disabled')){
    		$('.upload-building').removeAttr('disabled')
    		$('.upload-building').attr('background-color','#2e6da4')
    		$('#upload-building').hide();
    		$('.form-inline').show();
    		$('.block-a').show();
    		$(this).attr('disabled',true)
    		$(this).attr('background-color','#EDEDED')
    	}
    })

    $('.upload-payment').click(function(){
    	var paymentId 	= $(this).val();
    	var paymentName = $(this).attr('payment-name');
    	var url 		= 'get-dis-holicy-payment-list';
    	if($(this).is(":checked")){
    		$.get(
	            url,
	            {'paymentId': paymentId},
	            function (data) {
	               var data = jQuery.parseJSON(data);
	               if(!data.code){
	               		str='<div class=discount-holicy-upload holicy-type='+paymentId+' style=border:1px solid #ddd;><div style="margin: 10px 10px";><span>'+paymentName+'</span><select name=holicyID[] style=width:200px;>'
	                    str+='<option value=0>请选择</option>'
	                    $(data).each(function(k,v){
	                    		str+='<option  value='+v.holicy_id+'>'+v.holicy_name+'</option>'
	                    })
	                    str+='</select></div></div>'
	                    $('#upload-flag').before(str)
	                    discountHolicyUploadChange()
	               }
	            }
	        );
    		
    	}else{
    		$('.discount-holicy-upload').each(function(){
    			if($(this).attr('holicy-type') == paymentId){
    				$(this).remove();
    			}
    		})
    		if(isHolicyTypeUpload()){
    			$('.btn-upload-success').removeAttr("disabled");
    		}else{
    			$('.btn-upload-success').attr("disabled",true);
    		}
    		if(!isUploadBuildingExcel() || !isUploadFileVerify()){
    			$('.btn-upload-success').attr("disabled",true);
    		}
    	}
    })

    $('#upload-building-excel').change(function(){
    	isUploadBuildingExcel();
    	isUploadFileVerify();
    })

    function isUploadBuildingExcel(){
    	file = $('#upload-building-excel').val().split('.');
    	if(file[file.length - 1] !="txt"){ 
        	$('#upload-building-excel-error').show()
        	$('.btn-upload-success').attr("disabled",true);
        	return false;
     	}else{
     		$('#upload-building-excel-error').hide()
     		if(isHolicyTypeUpload()){
     			$('.btn-upload-success').removeAttr("disabled");
     			return true
     		}else{
     			return false
     		}
     	}
    	
    }

    function isHolicyTypeUpload(){
    	pay=0;
    	$("#upload-building .payment").each(function(){
    		if($(this).is(':checked')){
    			pay=1;
    		}
    	})
    	return pay;
    }
    function discountHolicyUploadChange(){
    	$('.discount-holicy-upload').find("select").on('change',function(){
	    	if($(this).val() > 0 && isUploadBuildingExcel() && isUploadFileVerify()){
	    		$('.btn-upload-success').removeAttr('disabled');
	    		return false
	    	}else{
	    		$('.btn-upload-success').attr('disabled','disabled');
	    	}
	    })
    }
    $(".delete").on("click",function(){
			$(this).parent().parent().remove();
			buttonStatus(this);
			if(!isPayment() || !isSortId()){
				$(".btn-success").attr("disabled",true);
			}
	});

	function isUploadFileVerify(){
		formObj = new FormData($('#infoLogoForm')[0]);
		$.ajax({
	        url: '/discount-building-assoc/upload-file-verify',
	        type: 'POST',
	        cache: false,
	        data: formObj,
	        processData: false,
	        contentType: false,
	        dataType:"json",
	        beforeSend: function(){
	            uploading = true;
	        },
	        success : function(data) {
	            if(data.code == 1){
	            	$('#upload_error').html(data.msg)
	            	$('#upload_error').show()
	            	$('.btn-upload-success').removeAttr("disabled");
	            }else if(data.code == 2){
	            	$('#upload_error').html(data.msg+'<br/>'+data.data)
	            	$('#upload_error').show()
	            	$(".btn-upload-success").attr("disabled",true);
	            }else{
	            	$('#upload_error').hide()
	            	if(isHolicyTypeUpload()){
		     			$('.btn-upload-success').removeAttr("disabled");
		     			return true
		     		}else{
		     			return false
		     		}
	            }
	        }
	    });
	}

	//提交数据验证
	$("#buildPayType,#infoLogoForm").submit(function(){
		var isVerify=1;
		var subObj = $(this);
		$(this).find('.payment').each(function(e){
			if($(this).is(':checked')){
				var obj = $(this).parents('.col-lg-6').find('.form-control');
				var val = obj.val();
				var name = obj.attr('name');
				if(val == ''){
					$(this).parents('.col-lg-6').find('.weight-error').show().html('请填写所选支付方式的前端展示顺序');
					isVerify = 0;
					return false;
				} else {
					$(this).parents('.col-lg-6').find('.weight-error').hide();
					subObj.find('.weight').each(function(){
						if (name != $(this).attr('name') && val == $(this).val()) {
							$(this).parents('.col-lg-6').find('.weight-error').show().html('支付方式的前端展示顺序不能与其它值相同');
							obj.parents('.col-lg-6').find('.weight-error').show().html('支付方式的前端展示顺序不能与其它值相同');
							isVerify = 0;
							return false;
						} else {
							$(this).parents('.col-lg-6').find('.weight-error').hide();
							obj.parents('.col-lg-6').find('.weight-error').hide();
						}
					})
					if (isVerify == 0) {
						return false;
					}
				}
			}
		})
		var buildPayTypeName = $(this).find('.build-pay-type-name').val();
		if (buildPayTypeName == '') {
			$(this).find('.name-tip').show();
			isVerify=0;
		} else {
			$(this).find('.name-tip').hide();
		}
		if (isVerify == 0) {
			$(this).find('.btn').removeAttr('disabled');
			return false;
		}
		
	})
	// 失去焦点验证
	$("#buildPayType,#infoLogoForm").find('.build-pay-type-name').blur(function(){
		if ($(this).val() == '') {
			$(this).parent().parent().find('.name-tip').show();
		} else {
			$(this).parent().parent().find('.name-tip').hide();
		}
	})
	// 失去焦点验证
	$("#buildPayType,#infoLogoForm").find('.weight').blur(function(){
		var obj = $(this);
		if (!obj.parent().find('.payment').is(':checked')) {
			return true;
		}
		var val = obj.val();
		var name = obj.attr('name');
		if (val == '') {
			$(this).parents('.col-lg-6').find('.weight-error').show().html('请填写所选支付方式的前端展示顺序');
		} else {
			obj.parents('.row').siblings().find('.weight').each(function(){
				if (name != $(this).attr('name') && val == $(this).val()) {
					$(this).parents('.col-lg-6').find('.weight-error').show().html('支付方式的前端展示顺序不能与其它值相同');
					obj.parents('.col-lg-6').find('.weight-error').show().html('支付方式的前端展示顺序不能与其它值相同');
					return false;
				} else {
					$(this).parents('.col-lg-6').find('.weight-error').hide();
					obj.parents('.col-lg-6').find('.weight-error').hide();
				}
			})
		}
	})
	
})