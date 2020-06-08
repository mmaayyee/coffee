//图片上传  跨域请求
$(function(){
    /*选择文件后提交*/
    $('input[type=file]').change(function(){
        //禁止表单提交
        $('input[type=submit]').attr('disabled','disabled');
        var formData = new FormData();
        formData.append('file', $(this)[0].files[0]);
        var hiddenName = $(this).parent().find('input[type=hidden]').attr('name');
        var urls = url+'coffee-product-api/upload-coffee-label'+keys;
        var that = this;
        $.ajax({
            url: urls,
            type: 'POST',
            cache: false,
            data: formData,
            async:false,
            processData: false,
            contentType: false,
        }).done(function(data) {
            var res = JSON.parse(data);
            if(res.status == 'success'){
                //将文件名加入隐藏域
                $('input[name='+hiddenName+']').val(res.path);
                $(that).parent().find('img').attr('src',url+res.path);
                //开启表单提交
                $('input[type=submit]').removeAttr('disabled');
            }else{
                alert(res.msg);
            }

        }).fail(function(res) {
            alert('上传有误！');
            //开启表单提交
            $('input[type=submit]').removeAttr('disabled');
        });
    });
});

$(document).ready(function(){
    //这里是动态添加元素
    $(".add").click(function(){
        var labelName = $("body").find('input[name=label_name]').val();
        if (!labelName){
            $("#labelName_error").html("标签名称不能为空");
            return false;
        }
        if(labelName.length > 2){
            $("#labelName_error").html("标签名称不能大于2个字符");
            return false;
        }
        $("#labelName_error").html('');
        var productName = $('input[type=checkbox]:checked').length;
        if (productName == 0) {
            $("#product_error").html("请选择对应饮品");
            return false;
        }
        $("#product_error").html('');
        var sort = $("body").find('input[name=sort]').val();
        if (!sort) {
            $("#sort_error").html("排序不可为空!");
            return false;
        }
         $("#sort_error").html('');
        var deskImgUrlName = $("body").find('input[name=desk_img_url]').val();
        if (!deskImgUrlName) {
            $("#desk_img_url_error").html("标签图片不能为空");
            return false;
        }
        $("#desk_img_url_error").html('');
        var deskSelectedImgUrlName = $("body").find('input[name=desk_selected_img_url]').val();
        if (!deskSelectedImgUrlName) {
            $("#desk_selected_img_url_error").html("选中标签图片不能为空");
            return false;
        }
        $("#desk_selected_img_url_error").html('');
        $("form").submit();
    })　　　
});