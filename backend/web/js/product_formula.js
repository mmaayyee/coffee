$(function() {
    /*equipTypeStockList：设备类型以及对应的料仓信息，proStockRecipe：修改的数据*/
    var productRecipeAttr = {'equipTypeStockList':equipTypeStockList, 'proStockRecipe':cofProStockRecipeList.proStockRecipe};
    var isAddFormula = true;
    if (productRecipeAttr) {
        var productRecipeTpl =　$("#formulaTpl").html();
        laytpl(productRecipeTpl).render(productRecipeAttr,function(html){
            $(".formulas").html(html);
        });
    }
    $.extend($.fn.validation.defaults.validRules.push(
        {name: 'plus', validate: function(value) {return (!/^[+]{0,1}(\d+)$|^[+]{0,1}(\d+\.\d+)$/.test(value));}, defaultMsg: '请输入正数。'},
        {name: 'positive', validate: function(value) {return (!/^(|0|[1-9]\d*)$/.test(value));}, defaultMsg: '请输入大于或等于0的整数。'},
        {
            name: 'compareDate',
            validate: function(value,err) {
                var endDate = Date.parse(new Date( value )) / 1000;
                var startDate = Date.parse(new Date( $(this).parent().parent().prev().find("input[type='text']").val() )) / 1000;
                if( parseFloat(endDate) <= parseFloat(startDate)){
                    return true;
                }
            },
            defaultMsg: '不能小于等于开始时间。'
        },
        {name: 'compare', validate: function(value,err) {
            if( parseFloat($(this).val()) > parseFloat($(this).parent().parent().find("input[name*=brew_up]").val())){
                    return true;
            }
        }, defaultMsg: '下限值不能大于上限值。' }
    ));
    $("#coffeeproduct-price_start_time").datetimepicker({
        minDate: new Date(),
        onClose: function( selectedDate ) {
            $("#coffeeproduct-price_end_time").datepicker( "option", "minDate", selectedDate );
        }
    });
    $("#coffeeproduct-price_end_time").datetimepicker({
        onClose: function(selectedDate) {
            $("#coffeeproduct-price_start_time").datepicker( "option", "maxDate", selectedDate );
        }
    });
    $("#coffeeproduct-price_end_time").change(function() {
        $(this).blur();
    });
    $(".equipment-type[type=checkbox]:checked").each(function() {
        isChecked(this);
    });
    $("form").validation();
    $(".field-coffeeproduct-price_start_time,.field-coffeeproduct-cf_texture,.field-coffeeproduct-price_end_time,.field-coffeeproduct-cf_special_price,.field-coffeeproduct-cf_product_english_name").find('#autoreqmark').remove();

    //跳转到bootstrap3-validation验证出错的元素位置
    function  jumpError() {
        if ($(".has-error").length > 0 ){
            var top = $(".has-error").offset().top - 50;
            $("html, body").animate({
                    scrollTop: top
                });
        }
    }
    $("#w0").on('beforeSubmit', function (e) {
        if ($("#w0").valid() == false) {
            jumpError();
            $("#w0").find(".btn-success").removeAttr("disabled");
            return false;
        } else {
           // var isIngre=checkIngredient();
           // if(isIngre){
            uploadFile();
           // }
        }
    }).on('submit', function (e) {
        jumpError();
        e.preventDefault();
    });

    $("#w1").on('beforeSubmit', function (e) {
        if ($(".formulas .equipment-type:checked").length < 1) {
            $("#tsModal #myModalLabel").text("提示框");
            $("#tsModal .title").html('请选择设备类型');
            $("#tsModal").modal();
            $("#w1").find(".btn-success").removeAttr("disabled");
            return false;
        }else{
            if ($("#w1").valid() == false) {
                jumpError();
                $("#w1").find(".btn-success").removeAttr("disabled");
                return false;
            } else {
               // var isIngre=checkIngredient();
               // if(isIngre){
                uploadSetup();
               // }
            }
        }
    }).on('submit', function (e) {
        jumpError();
        e.preventDefault();
    });

    $("#w2").on('beforeSubmit', function (e) {
        if ($(".formulas .equipment-type:checked").length < 1) {
            $("#tsModal #myModalLabel").text("提示框");
            $("#tsModal .title").html('请选择设备类型');
            $("#tsModal").modal();
            $("#w2").find(".btn-success").removeAttr("disabled");
            return false;
        }else{
            if ($("#w2").valid() == false) {
                jumpError();
                $("#w2").find(".btn-success").removeAttr("disabled");
                return false;
            } else {
               // var isIngre=checkIngredient();
               // if(isIngre){
                uploadCoffeeProduct();
               // }
            }
        }
    }).on('submit', function (e) {
        jumpError();
        e.preventDefault();
    });

    function uploadCoffeeProduct() {
    // var formData = $("#w2").serializeObject();
    var formData = new FormData($('#w2')[0]);
    formData.append('CoffeeProduct[cf_product_cover]', $("#imageShow").attr('src'));
    formData.append('CoffeeProduct[cf_product_thumbnail]', $("#imgShow").attr('src'));
    console.log(formData);
    $.ajax({
        url: url+"coffee-product-api/create.html?"+verifyPassword,
        dataType: 'json',
        type: 'post',
        data: formData,
        processData: false,
        contentType: false,
        success : function(data) {
            if (data.ret) {
                saveLog()
                setTimeout(function () {
                    window.location.href="/coffee-product/index";
                }, 1500);
            } else {
                $(".submit-error").html(data.msg);
                $("form").find(".btn-success").removeAttr("disabled");
            }
        },
        error : function(data) {
            $("form").find(".btn-success").removeAttr("disabled");
            $(".submit-error").html('服务器上传失败。');
        }
    });
}

function uploadFile() {
    var formData = $("#w0").serializeObject();
    $.ajax({
        url: url+"coffee-product-api/save-coffee-product.html?"+verifyPassword,
        dataType: 'json',
        type: 'post',
        data: new FormData($('#w0')[0]),
        processData: false,
        contentType: false,
        success : function(data) {
            if (data.ret == 1) {
                saveLog()
                setTimeout(function () {
                    window.location.href="/coffee-product/index";
                }, 1500);
            } else {
                $(".submit-error").html(data.msg);
                $("#w0").find(".btn-success").removeAttr("disabled");
            }
        },
        error : function(data) {
            $("#w0").find(".btn-success").removeAttr("disabled");
            $(".submit-error").html('服务器上传失败。');
        }
    });
}

function uploadSetup() {
    var formData = $("#w1").serializeObject();
    $.ajax({
        url: url+"coffee-product-api/save-product-setup.html?"+verifyPassword,
        dataType: 'json',
        type: 'post',
        data: new FormData($('#w1')[0]),
        processData: false,
        contentType: false,
        success : function(data) {
            if (data.ret === true) {
                saveLog()
                setTimeout(function () {
                    window.location.href="/coffee-product/index";
                }, 1500);
            } else {
                $(".submit-error").html(data.msg);
                $("#w1").find(".btn-success").removeAttr("disabled");
            }
        },
        error : function(data) {
            $("#w1").find(".btn-success").removeAttr("disabled");
            $(".submit-error").html('服务器上传失败。');
        }
    });
}

});
/**
 * 检测单品成分，最少选择一个，最多选择6个
 * @param   object   obj
 */
function checkIngredient(){
    var ingredientLength=$('.ingredient .check:checked').length;
    if(ingredientLength<1||ingredientLength>6){
        $('.ingredient .error').html('成分最少选择一项,最多选择6项');
        return false;
    }
    return true;
}

/**
 * 选择设备类型显示不同的单品配方信息
 * @param   object   obj
 */
function isChecked(obj) {
    var  _this = $(obj);
     if ($(obj)[0].checked == true){
        isAddFormula = true;
        var addFormulaTpl = $("#addFormulaTpl").html();
        var  productRecipeAttr = {'equipTypeStockList':equipTypeStockList, "checked":　"1", 'equipTypeId': $(obj).parent().parent().attr("id"), 'proStockRecipe':cofProStockRecipeList.proStockRecipe};
        laytpl(addFormulaTpl).render(productRecipeAttr,function(html){
             $(obj).parent().next().html(html);
        })
        var selectElement = $(obj).parent().next().find("select");
        selectElement.each(function() {
            $(this).attr("data-value", $(this).val());
        })
        $(".setup-choose-sugare input[type=radio]").change(function() {
             isChooseSugar($(this));
         });
        $(obj).parent().parent().find(".formula").validation();
     }else{
        $("#delTsModal .title").html('是否删除该设备类型配方?');
        $("#delTsModal").modal();
        $("#delTsModal #btn_submit").on("click", function(){
            $(obj).parent().next().html("");
            $(obj).prop("checked", false);
        })
        if($(obj).parent().next().html() != ""){
            $(obj).prop("checked", true);
        }
     }
};

/**
 * 勾选糖显示糖量信息
 * @param   object   obj
 */
function isChooseSugar(obj) {
     if (obj.val() == 1) {
        obj.parent().parent().parent().find(".choose-sugar").show();
        obj.parent().parent().parent().find(".choose-sugar input[type=text]").attr("check-type", "required number decimal");
     } else {
        obj.parent().parent().parent().find(".choose-sugar").hide();
        obj.parent().parent().parent().find(".choose-sugar input[type=text]").removeAttr("check-type");
        obj.parent().parent().parent().find(".choose-sugar input[type=text]").val(0);
     }
}

/**
 * 点击添加单品配方按钮增加一条新的单品配方信息
 * @param   object   obj
 */

var  addFormula = function(obj) {
    if($(obj).next().find("select:last option").length > 1 && isAddFormula != false){
        var addFormulaTpl = $("#addFormulaTpl").html();
        var formulaNum = $(obj).next("table").find("tbody tr:last-child").data("length");
        formulaNum++;
        var productRecipeAttr = {
            'equipTypeStockList': equipTypeStockList,
            'equipTypeId': $(obj).parent().parent().attr("id"),
            'formulaNum': formulaNum
        };
        laytpl(addFormulaTpl).render(productRecipeAttr, function (html) {
            $(obj).next().find("tbody").append(html);
        });
        isAddFormula = getSelectAttr(obj);
        $("table").validation();
    } else {
        $("#tsModal #myModalLabel").text("提示框");
        $("#tsModal .title").html('单品配方添加数量已达上限.');
        $("#tsModal").modal();
    }
};

/**
 * 点击减号按钮删除一条品配方信息
 * @param   object   obj
 */
var delFormula = function(obj) {
    $(obj).parent().parent().remove();
    isAddFormula = true;
}

$.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        o[this.name] = this.value || '';
    });
    return o;
};

function saveLog(){
        var type = 0;
        if($('#coffeeproduct-cf_product_id').val() != ''){
            type = 1;
        }
        $.ajax({
              type : "get",
              url : '/coffee-product/save-log',
              data : {'type':type,'cfProductName':$('#coffeeproduct-cf_product_name').val()},
              success : function(data){
              }
        });
}

//获取料仓选项,设置料仓的默认选项
function getSelectAttr(obj) {
    var selectOptionAttr = [], selectValueAttr = [];
    $(obj).next().find("select:last option").each(function(i,e){
        selectOptionAttr.push($(e).val());
    });
    $(obj).next().find("select").each(function(i,e){
        selectValueAttr.push($(e).val());
    });
    return setDefaultValue(obj, selectOptionAttr, selectValueAttr);
}
/*
 *
 *selectValueAttr存放selected值, selectOptionAttr存放option的选项值
 */
function setDefaultValue(obj, selectOptionAttr, selectValueAttr) {
    if(selectValueAttr.length > 0){
            $.each(selectOptionAttr,function(i,e){
                if ($.inArray(e, selectValueAttr) == -1) {
                   $(obj).next().find("tr:last select option[value='"+e+"']").prop("selected", true);
                   $(obj).next().find("tr:last select").attr('data-value', e);
                   return false;
                }
            })
        if (selectValueAttr.length >= selectOptionAttr.length){
            return false;
        };
    }
}

/*
 * 同一料仓只能添加一个
 */
function selectValueChange(obj)
{
    var selectValueAttr = [];
    var index = $(obj).parent().parent().parent().find("select").index(obj);
    $(obj).parent().parent().parent().find("select").each(function(i,e){
        if (index != i) {
         selectValueAttr.push($(e).val());
        }
    });
    if ($.inArray($(obj).val(), selectValueAttr) != -1) {
        $("#tsModal #myModalLabel").text("提示框");
        $("#tsModal .title").html($(obj).find("option:selected").text()+'已存在');
        $("#tsModal").modal();
        $(obj).val($(obj).attr('data-value'));
    } else {
        $(obj).attr('data-value', $(obj).val());
    }
}
