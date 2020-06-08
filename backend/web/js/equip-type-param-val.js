$(function(){
	//下拉框切换数据
	$('.select').on('change',function(){
		delAllDoms();
		var data = {};
		//获取地区id
		data.org_id = $(this).val();
		var url	= '';//当前链接
		$.ajax({
			url:url,
			type:'post',
			data:data,
			dataType:'json',
			success:function(resData){
				if(resData.length < 1){
					alert('暂无数据！');
					return false;
				}
				var htmls = '';
				for(var i=0;i<resData.length;i++){
					htmls += '<div class="parameter">';
					htmls += '<div class="add-row" parameter_id="' + resData[i].id + '" max_parameter="'+ resData[i].max_parameter+'"  min_parameter="'+ resData[i].min_parameter+'" >';
					htmls += '<span>' + resData[i].parameter_name + '  (' + resData[i].min_parameter + '~' + resData[i].max_parameter + ')：  </span>';
					htmls += '<input type="text" class="top" value="' + resData[i].parameter_value + '">';
					htmls += '<button class="btn btn-primary save" style="margin-bottom: 12px">同步</button>';
					htmls += '</div></div>';
				}
				$('.allparameter').append(htmls);
			},
			error:function(){
				alert('请求失败！');
				return false;
			}
		});
	});
	//删除所有节点
	function delAllDoms(){
		$('.parameter').remove();
	}
	//点击同步数据
	$(document).on('click','.save',function(){
		//要传递的参数数组
		var data = {};
		//获取参数
		var rows = $(this).parent();
		data.parameter_id 		= rows.attr('parameter_id');
		data.equipment_type_id 	= $('.allparameter').attr('equipment_type_id');
		data.parameter_value 	= rows.find('input').val();
		data.org_id			 	= $('.select').val();
        //获取验证参数
        var max_parameter = rows.attr('max_parameter');
        console.log(max_parameter);
        var min_parameter = rows.attr('min_parameter');
        if(parseInt(data.parameter_value) < parseInt(min_parameter)){
            alert('不可小于设定的最小值！');
            return false;
        }
        if(parseInt(data.parameter_value) > parseInt(max_parameter)){
            alert('不可大于设定的最大值');
            return false;
        }
        if(data.parameter_value == ''){
            alert('不可设置为空值~');
            return false;
        }
		//发送ajax请求		console.log(data);
		var url = 'synchronous';
		$.ajax({
			url:url,
			type:'post',
			data:data,
			dataType:'json',
			success:function(resData){
				if(resData['status'] == 'error'){
					alert(resData['msg']);
					return false;
				}
				//同步成功
				alert('同步成功！');
			},
			error:function(){
				alert('请求失败！');
				return false;
			}
		});
	});
});