/**
    选择时间的输入框被选中时，创建datepicker控件
    根据设置不同dateFmt 格式，使空间支持选择日，周，月，季，年
*/
$(function(){
    if (consume.length>0) {
        var gettpl = document.getElementById("conditionTemplat").innerHTML;
        $(".searchResult tbody").html("");
        laytpl(gettpl).render(consume,function(html){
            $(".condition").html(html);
        });
    } else {
        var html = $("#addConditionTemplat").html();
        $(".condition").append(html);
    }
})

function isTime(obj) {
    obj.parent().next(".time").find(".form-control").hide();
    obj.parent().next(".time").find(".form-control").removeAttr("check-type");
    obj.parent().next(".time").find("#valierr").remove();
    obj.parent().next(".time").removeClass("has-success").removeClass("has-error");
   switch(obj.val())
    {
    case "1":
        obj.parent().next(".time").find("#dayTime").show().val("");
        obj.parent().next(".time").find("#dayTime").attr("check-type","required");
      break;
    case "2":
        obj.parent().next(".time").find("#weekTime").show().val("");
        obj.parent().next(".time").find("#weekTime").attr("check-type","required");
      break;
    case "3":
        obj.parent().next(".time").find("#TenTime").show().val("");
        obj.parent().next(".time").find("#TenTime").attr("check-type","required");
      break;
    case "4":
        obj.parent().next(".time").find("#monthTime").show().val("");
        obj.parent().next(".time").find("#monthTime").attr("check-type","required");
      break;
    case "5":
        obj.parent().next(".time").find("#seasonTime").show().val("");
        obj.parent().next(".time").find("#seasonTime").attr("check-type","required");
      break;
    case "6":
        obj.parent().next(".time").find("#halfYearTime").show().val("");
        obj.parent().next(".time").find("#halfYearTime").attr("check-type","required");
      break;
    default:
        obj.parent().next(".time").find("#YearTime").show().val("");
        obj.parent().next(".time").find("#YearTime").attr("check-type","required");
    }
}

function createWdatePicker(obj) {
    var type = $(obj).parent().prev().find(".timeUnit").val();
    switch(type)
    {
    case "1"://按日
        WdatePicker({
            readOnly : true,
            isShowClear :true,
            dateFmt : 'yyyy-MM-dd',
            isShowWeek : true,
            onpicked: function() {
              $(this).parent().find("#time").val(this.value);
            }
        });
        break;
    case "2"://按周
        WdatePicker({
            dateFmt : 'yyyy-MM-dd',
            readOnly : true,
            isShowClear :true,
            onpicked :function() {
               $(this).val($dp.cal.getP('y')+"年第"+$dp.cal.getP('W','WW')+"周");
               $(this).parent().find("#time").val(this.value);
            },
            isShowWeek : true,
            errDealMode :3,
            firstDayOfWeek :1,
        });
        break;
    case "3"://按旬
        WdatePicker({
            dateFmt : 'yyyy-MM-dd',
            readOnly : true,
            isShowWeek : true,
            onpicked : function() {
                 var  d = $dp.cal.getP('d', 'dd');
                if (parseInt(d) > 10 && parseInt(d) < 21) {
                    $(this).val($dp.cal.getP('y')+"年"+$dp.cal.getP('M','MM')+"月中旬");
                } else if (parseInt(d) > 20) {
                    $(this).val($dp.cal.getP('y')+"年"+$dp.cal.getP('M','MM')+"月下旬");
                } else {
                    $(this).val($dp.cal.getP('y')+"年"+$dp.cal.getP('M','MM')+"月上旬");
                }
              $(this).parent().find("#time").val(this.value);
            },
            errDealMode:3,
        })
        break;
    case "4"://按月
        WdatePicker({
            readOnly : true,
            isShowClear :true,
            dateFmt : 'yyyy-MM',
            onpicked: function() {
              $(this).parent().find("#time").val(this.value);
            }
        });
        break;
    case "5"://按季
        WdatePicker({
            dateFmt:'yyyy年MM季度',
            readOnly : true,
            disabledDates:['....-0[5-9]-..','....-1[0-2]-..'],
            startDate:'%y-01-01',
            onpicked: function() {
               $(this).parent().find("#time").val(this.value);
            },
            errDealMode:3
        })
        break;
    case "6"://按半年
        WdatePicker({
            readOnly : true,
            isShowClear :true,
            dateFmt : 'yyyy-MM',
            onpicked : function(){
            var  m = $dp.cal.getP('M', 'MM');
                if (parseInt(m) > 06 ) {
                    $(this).val($dp.cal.getP('y')+"年下半年");

                } else {
                    $(this).val($dp.cal.getP('y')+"年上半年");
                }
                $(this).parent().find("#time").val(this.value);
            },
             errDealMode:3,
        });
        break;
    case "7"://按年
        WdatePicker({
            readOnly : true,
            dateFmt : 'yyyy',
            onpicked: function() {
               $(this).parent().find("#time").val(this.value);
            }
        });
            break;
    case "7"://按年
        break;
    }
};

function addCondition()
{
    if ($(".groups").length < 3) {
        var html = $("#addConditionTemplat").html();
        html = html.replace('<button type="button" class="btn btn-success add-condition" onClick="addCondition()">添加条件</button>','<button type="button" class="btn btn-danger" onClick="delCondition(this)">删除条件</button>');
        $(".condition").append(html);
        $(".groups").validation({reqmark:false});
        $(".condition #valierr").each(function() {
            if ($(this).text() != "") {
                $(this).parent().addClass("has-error");
            }
        });
    } else {
        $("#tsModal").modal();
        $("#tsModal .title").text("添加条件最多3个。");
    }
}

function valueCompare(obj) {
    $(obj).parent().next().find("input").trigger("blur");
};
function delCondition(obj) {
    $(obj).parent().parent().remove();
};

$.extend($.fn.validation.defaults.validRules.push(
    {name: 'ints', validate: function(value) {return (!/^(|0|[1-9]\d*)$/.test(value));}, defaultMsg: '请输入整数。'},
    {name: 'compare', validate: function(value,err) {
        if( parseFloat(value) < parseFloat($(this).parent().prev().find("input").val())){
                return true;
        }
    }, defaultMsg: '开始值不能大于结束值。' }
));