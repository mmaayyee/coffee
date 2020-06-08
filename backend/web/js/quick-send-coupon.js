$(function(){
	function loadFinished(){
		if($('#quicksendcoupon-coupon_sort').val() == 2){
			$('#singleProduct').show()
			$('#package').hide()
			var url = '/quick-send-coupon/get-quick-send-coupon-list'
			var couponType = $('#quicksendcoupon-coupon_type').val();
			var isProduct = $('#quicksendcoupon-is_product').val();
			$.get(
					url,
		            {'coupon_type': couponType,'is_product':isProduct},
		            function (data) {
		            	var data = jQuery.parseJSON(data);
		            	if(data){
		            		str = '<option value= >请选择</option>'
		            		$(data).each(function(k,v){
		            			str+='<option value='+v.coupon_id+'>'+v.coupon_name+'</option>'
		            		})
		            		$('#quicksendcoupon-coupon_id').html(str)
		            	}
		            }
		        );
		}else{
			$('#singleProduct').hide()
			$('#package').show()
		}
	}
	$('#quicksendcoupon-coupon_sort,#quicksendcoupon-is_product,#quicksendcoupon-coupon_type').change(function(){
		loadFinished();

	})
	$('.sendPhoneAdd').click(function(){
		var sendPhone = $('#quicksendcoupon-phone').val();
		var url 	  = '/quick-send-coupon/verify-quick-send-coupon-phone'
		var flag 	  = true;
		if($('#quicksendcoupon-send_phone_list').find('option').length > 9){
			$('.error-message').modal()
		    $('.error-message').find('.modal-body').html('每次最多添加10个')
			return false;
		}
		$('#quicksendcoupon-send_phone_list').find('option').each(function(){
			if($(this).val() == sendPhone){
				$('.error-message').modal()
		    	$('.error-message').find('.modal-body').html('该账号已存在')
				flag = false;
			}
		})
		if(!flag){
			return false;
		}
		var sendPhoneObj = $('#quicksendcoupon-phone');
		if(sendPhone != ''){
			$.get(
					url,
		            {'sendPhone': sendPhone},
		            function (data) {
		            	var data = jQuery.parseJSON(data);
		            	if(data){
		            		if(data.code == 1){
		            			$('.error-message').modal()
		            			$('.error-message').find('.modal-body').html(data.msg)
		            		}else{
		            			str = '<option value='+sendPhone+'>'+sendPhone+'</option>'
		            			$(str).appendTo('#quicksendcoupon-send_phone_list')
		            			$("#quicksendcoupon-send_phone_list").find("option:first-child").attr("selected",true)
		            			sendPhoneObj.val('')
		            			mosaicPhone()
		            		}
		            		removeOption()
		            	}
		            }
		        );
		}
	})
	// 修改时给option绑定双击删除楼宇事件
    function removeOption(){
        $("#quicksendcoupon-send_phone_list").find("option").on("dblclick",function(){
            $(this).remove();
            if($("#quicksendcoupon-send_phone_list").find("option").length > 0){
                valId = $("#quicksendcoupon-send_phone_list").find("option:first-child").val()
                equip_type = $("#quicksendcoupon-send_phone_list").find("option:first-child").attr("equip_type")
                $("#quicksendcoupon-send_phone_list").find("option:first-child").remove()
                $("#quicksendcoupon-send_phone_list").prepend('<option  selected="selected" value='+valId+'>'+valId+'</option>');
            }
            mosaicPhone()
            removeOption()
        });
    }
    $("#quicksendcoupon-send_phone_list").find("option").on("dblclick",function(){
        $(this).remove();
            if($("#quicksendcoupon-send_phone_list").find("option").length > 0){
                valId = $("#quicksendcoupon-send_phone_list").find("option:first-child").val()
                equip_type = $("#quicksendcoupon-send_phone_list").find("option:first-child").attr("equip_type")
                $("#quicksendcoupon-send_phone_list").find("option:first-child").remove()
                $("#quicksendcoupon-send_phone_list").prepend('<option  selected="selected" value='+valId+'>'+valId+'</option>');
            }
           	mosaicPhone()
            removeOption()
    });
    $('.btn-success').click(function(){
    	$('#quicksendcoupon-send_phone_list').find('option').each(function(){
    		$(this).attr("selected",'selected')
    	})
    })
    function mosaicPhone(){
    	var str=[]
    	$("#quicksendcoupon-send_phone_list").find('option').each(function(k,v){
    		str[k] = $(this).val();
    	})
    	$('#quicksendcoupon-send_phone').val(str)
    }
})
$(function(){
	if($('#quicksendcoupon-coupon_sort').val() == 1){
    		$('#singleProduct').hide();
    		$('#package').show();
    }
    if($('#quicksendcoupon-coupon_sort').val() == 2){
    		$('#package').hide();
    		$('#singleProduct').show();
    }
})