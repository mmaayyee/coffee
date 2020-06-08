$(function(){
    //点击同步数据
    $(document).on('click','.save',function(){
        //要传递的参数数组
        var dataArry = [];
        var quitFlag = false;
        //遍历获取参数
        $('.add-row').each( function(i, e){
            var data = {};
            data.parameter_id       = $(this).attr('parameter_id');
            data.parameter_name     = $(this).find('span').html();
            data.parameter_value    = $(this).find('input').val();
            //获取验证参数
            var max_parameter = $(this).attr('max_parameter');
            var min_parameter = $(this).attr('min_parameter');
            if(String(data.parameter_value) == ''){
                alert('值不可设定为空!');
                quitFlag = true;
                return false;
            }
            if(parseInt(data.parameter_value) < parseInt(min_parameter)){
                alert('不可小于设定的最小值！');
                quitFlag = true;
                return false;
            }
            if(parseInt(data.parameter_value) > parseInt(max_parameter)){
                alert('不可大于设定的最大值');
                quitFlag = true;
                return false;
            }
            dataArry.push(data); 
        });
        if(quitFlag){
            return false;
        }
        //获取设备code
        var equipments_code  = $('.allparameter').attr('equipments_code');
        var sendData = {equipments_code:equipments_code,dataList:dataArry};
        //发送ajax请求  
        var url = 'synchronous';
        $.ajax({
            url:url,
            type:'post',
            data:sendData,
            dataType:'json',
            success:function(resData){
                if(resData['status'] == 'error'){
                    alert(resData['msg']);
                    return false;
                }
                //同步成功
                alert('修改成功!');
            },
            error:function(){
                alert('请求失败！');
                return false;
            }
        });
    });
});