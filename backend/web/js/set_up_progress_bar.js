$(function(){
    var isNewRecord = $(".is_new_record").val();
    var strs= new Array(); //定义一数组
        strs=$(".isCreateArray").val().split(","); //字符分割
    if(!isNewRecord){
        $("#equiptypeprogressproductassoc-product_id").prop("disabled", true);
    }
    $("#equiptypeprogressproductassoc-product_id").change(function(){
        if(isNewRecord){
            var productId = $("#equiptypeprogressproductassoc-product_id").val();
            if(!productId) {
                $(".building-product_id").addClass("has-error");
                $(".building-product_id").find(".help-block").html("产品不可为空");
                return false;
            }
            if($.inArray($(this).val(),strs) >= 0 ){
                $(".building-product_id").addClass("has-error");
                $(".building-product_id").find(".help-block").html("产品已添加过");
                return false;
            }else{
                $(".building-product_id").removeClass("has-error");
                $(".building-product_id").find(".help-block").html("");
            }
        }
    })
    if (equipTypeProcess) {
        var equipmentTypeTpl =　$("#equipmentTypeTpl").html();
        laytpl(equipmentTypeTpl).render(equipTypeProcess,function(html){
            $("#equipmentType").html(html);
        });
    }
    $("input.equipmentType:checked").each(function() {
        equipmentChange(this);
    });
    $("input.equipmentType").change(function() {
        equipmentChange(this);
    });

    $(".btn-success").on("click", function() {
        $(this).attr("disabled",true);
        if ($("input.procedureName:checked").length > 0){
            if ($("#equipmentType").valid() == false) {
                $(this).removeAttr("disabled");
                return false;
            }else{
                if(isNewRecord){
                    var productId = $("#equiptypeprogressproductassoc-product_id").val();
                    if(!productId) {
                        $(".building-product_id").addClass("has-error");
                        $(".building-product_id").find(".help-block").html("产品不可为空");
                        $(this).removeAttr("disabled");
                        return false;
                    }
                    if($.inArray($('#equiptypeprogressproductassoc-product_id').val(),strs) >= 0 ){
                        $(".building-product_id").addClass("has-error");
                        $(".building-product_id").find(".help-block").html("产品已添加过");
                        $(this).removeAttr("disabled");
                        return false;
                    }
                    $(".building-product_id").removeClass("has-error");
                    $(".building-product_id").find(".help-block").html("");
                }
                $("form").submit();
            }
        } else{
            $("#tsModal #myModalLabel").text("提示框");
            $("#tsModal .title").html("请选择工序并设置对应时间以及顺序");
            $("#tsModal").modal();
            $(this).removeAttr("disabled");
        }
     });
});

/**
 * 选择不同的工序并对相关工序时间以及工序的顺序添加表单验证
 */
function timeVaild(obj) {
     if (obj[0].checked == true) {
         obj.parent().parent().nextAll().removeClass("has-success");
         obj.parent().parent().parent().find("input[type=text]").each(function(){
                var checkType = $(this).attr("check-type").replace(/required /i, "");
                $(this).attr("check-type", "required "+checkType);
        })
     }else{
          obj.parent().parent().nextAll().removeClass("has-error");
          obj.parent().parent().nextAll().find("#valierr").remove();
          obj.parent().parent().parent().find("input[type=text]").each(function(){
                var checkType = $(this).attr("check-type").replace(/required /i, "");
                $(this).attr("check-type", checkType);
            })
     }

}
/**
 * 选择不同的设备类型展示不同的不同的工序内容
 */
function equipmentChange(obj) {
    var  _this = $(obj);
    if ($(obj)[0].checked == true){
         var htmlTlp = $("#installProcedureTpl").html();
         var data = { "equipmentType":_this.val(), "equipTypeProcess":equipTypeProcess};
         laytpl(htmlTlp).render(data ,function(html) {
            _this.parent().parent().parent().find(".installProcedure").html(html);
         });
         $(".installProcedure input.procedureName").each(function(){
            timeVaild($(this));
            $(this).change(function() {
                timeVaild($(this));
            });
         });
         $(".installProcedure").validation();
    }else{
          _this.parent().parent().parent().find(".installProcedure").html("");
    }
}
//验证输入顺序是否重复
function sortDistinct(obj) {
    var sortAttr = [];
    var sortValue = $(obj).parent().parent().siblings().find(".sort");
    $.each(sortValue, function(){
        if ($(this).val()) {
            sortAttr.push($(this).val());
        };
    });
    if (sortAttr.length > 0){
        if ($.inArray($(obj).val(), sortAttr) != -1) {
            $("#tsModal #myModalLabel").text("提示框");
            $("#tsModal .title").html($(obj).val()+'已存在');
            $("#tsModal").modal();
            $(obj).val("");
        }
    }
}