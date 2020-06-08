$(function(){
	buildPayTypeId = '';
	$('.select-pay-type').click(function(){
		var url 		= '/build-pay-type/get-build-pay-holicy-list';
		buildPayTypeId 		= $(this).attr('buildPayTypeId');
		$.get(	
			url,
	        {'buildPayTypeId':buildPayTypeId},
	        function (data) {
	            var data = jQuery.parseJSON(data);
	            var str=""
	            if(data.error_code == 0){
	            	str+='<table class="table table-striped table-bordered .table-responsive" border=1 width=580 ><tr><th>序号</th><th>支付方式</th><th>优惠策略</th></tr>'
		            $(data.data).each(function(k,v){
		            	holicName = v.holicy_name ? v.holicy_name : '无';
		            	str+="<tr><td>"+parseInt(k+1)+"</td><td>"+v.pay_type_name+"</td><td>"+holicName+"</td></tr>"
		            })
		            str+="</table>"
		            $("#payTypeContent").html(str)
		            $('#payTypeModal').modal()
	            }
	        }
	    ) 
	})
	$('.select-building').click(function(){
		var url 		= '/discount-building-assoc/select-building-list';
		buildPayTypeId 		= $(this).attr('buildPayTypeId');
		buildName 		= $(this).attr('buildName');
		$.get(	
			url,
	        {'build_pay_type_id':buildPayTypeId,'buildName':buildName},
	        function (data) {
	            var data = jQuery.parseJSON(data);
	            var str=""
	            if(data.code == 0){
	            	str+='<table class="table table-striped table-bordered .table-responsive" border=1 width=580 ><tr><th>序号</th><th>楼宇名称</th><th>楼宇类型</th><th>设备类型</th></tr>'
		            $(data.list).each(function(k,v){
		            	str+="<tr><td>"+parseInt(k+1)+"</td><td>"+v.name+"</td><td>"+v.build_type_name+"</td><td>"+v.equip_type_name+"</td></tr>"
		            })
		            str+="</table>"
		            $("#buildingModal").html(str)
		            $('#myModal').modal()
	            }
	        }
	    ) 
	})
	$('.select-discount-details').click(function(){
		var url 		= '/discount-building-assoc/select-discount-details';
		var holicyID 	= $(this).attr('holicyID');
		$.get(	
			url,
	        {'holicy_id':holicyID},
	        function (data) {
	            var data = jQuery.parseJSON(data);
	            var str=""
	            if(data.code == 0){
	            	str+='<table class="table table-striped table-bordered .table-responsive">'
	            	str+='<tr><th>策略名称</th><td>'+data.list.holicy_name+'</td></tr>'
	            	str+='<tr><th>支付方式</th><td>'+data.list.holicy_payment_name+'</td></tr>'
	            	str+='<tr><th>优惠类型</th><td>'+data.list.holicy_type_name+'</td></tr>'
	            	str+='<tr><th>优惠价格</th><td>'+data.list.public_field+'</td></tr><table>'
		            str+="</table>"
		            $("#disDetails").html(str)
		            $('#select-discount-details').modal()
	            }
	        }
	    )
	})
	$('.numberButton').click(function(){
		var buildName = $("input[name='buildName']").val();
		var equipType = $("select[name='equipType']").val();
		var buildType = $("select[name='buildType']").val();
		var url 		= '/discount-building-assoc/select-building-list';
		$.get(	
			url,
	        {'build_pay_type_id':buildPayTypeId,'equipType':equipType,'buildType':buildType,'buildName':buildName},
	        function (data) {
	            var data = jQuery.parseJSON(data);
	            var str=""
	            if(data.code == 0){
	            	str+='<table class="table table-striped table-bordered .table-responsive" border=1 width=580 ><tr><th>序号</th><th>楼宇名称</th><th>楼宇类型</th><th>设备类型</th></tr>'
		            $(data.list).each(function(k,v){
		            	str+="<tr><td>"+parseInt(k+1)+"</td><td>"+v.name+"</td><td>"+v.build_type_name+"</td><td>"+v.equip_type_name+"</td></tr>"
		            })
		            str+="</table>"
		            $("#buildingModal").html(str)
	            }else{
	            	$("#buildingModal").html('')
	            }
	        }
	    )
	})
})