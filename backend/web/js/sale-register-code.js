$(function(){
    var qrurl = "qrcode";
    var uploadUrl = "upload";
    $('#two-img').click(function(){
        buildId = $('#salebuildingassoc-build_id').val()
        saleId = $('#salebuildingassoc-sale_id').val()
        $.get(
            qrurl,
            {'buildId': buildId,'saleId':saleId},
            function (data) {
               var data = jQuery.parseJSON(data);
               if(data.code){
                    $('#qrcode-error').html(data.msg);
                    $('#qrcode-error').show();
               }else{
                     if(data.status){
                        $('#errorMsg').hide();
                     }else{
                        $('#errorMsg').show();
                     }
                     $('#qrcode-error').hide();
                     $('#img-value').attr('src',data.src)
                     $('#upload-img').attr('href',$('#upload-img').attr('href')+'?src='+data.src)
                     $('#img-border').show();
               }
            }
        );
    })
});
