
    function uploadFile() {
        var formData = $("form").serializeArray();
        $.ajax({
            url: url+"coffee-language-api/coffee-language-add-or-update.html",
            dataType: 'json',
            type: 'post',
            data: formData,
            success: function(data) {
                console.log(data);
                if(data.code == 200) {
                    window.location.href="/coffee-language/index";
                }else {
                    alert(data.message);
                    $(".submit-error").html("咖语创建失败!");
                }
            },
            error: function() {
                $(".submit-error").html('咖语添加失败!');
            }
        });
    }
    if ( $("#coffeelanguage-language_type").find("option[value="+0+"]").attr("selected") == 'selected'){
        $(".field-coffeelanguage-language_product").hide();
    }
    $(".field-coffeelanguage-language_type select").on("change",function(){
        var value=$(this).val();
        if(value=="0"){
            $(".field-coffeelanguage-language_product").hide();
        }else{
            $(".field-coffeelanguage-language_product").show();
        }
    })
