var attrArr = {};
var attrTpl = $("#attrTpl").html();
var attrNum = 1;
var customPropId = 1;//自定义属性类型ID
var customPropValId = -1;//自定义属性值id
var attrValueNum = 0;
var alreadySetSkuVals = {};
var isReviseSku = false;
//添加属性
function addAttr(obj){
        var _this = $(this);
        attrArr.type = 1;
        attrNum = attrNum+1
        attrArr.attrNum = attrNum;
        laytpl(attrTpl).render(attrArr, function (html) {
            $('.attribute').append(html);
        });
        $(obj).css("visibility","hidden");
        $(".attribute .form-group").validation();
        customPropId++;
        $(".product_attr:eq(1)" ).find(".Father_Title").attr("propid",customPropId);
}

//添加属性值
function addAttrValue(obj) {
    if($(obj).parent().parent().find(".attrValue").length < 10){
      attrValueNum++;
      if($(obj).parent().parent().find("div:last input").data("id")){
          attrArr.indx = parseInt($(obj).parent().parent().find("div:last input").data("id").split("_")[1])+1;
      }else{
          attrArr.indx = 0;
      }
      attrArr.type = 2
      attrArr.vauleID = $(obj).parent().parent().find("div:first input").attr("id");
      attrArr.id = $(obj).parent().parent().find("div:first input").attr("id").split("attr")[1];
      var attrName = $(obj).parent().parent().find(".attrValue:last input").attr("name");
      var index = attrName .lastIndexOf("\[");
      attrArr.attrValueNum = parseInt(attrName.substring(index + 1, attrName.length).split("]")[0]) + 1;
      laytpl(attrTpl).render(attrArr, function (html) {
          $(obj).parent().parent().append(html);
      });
      customPropValId--;
      $("input[data-id='prop-"+attrArr.vauleID+"_"+attrArr.indx+"']").parent().attr("propValid",customPropValId+"c");
      $(".attribute .form-group").validation();
    }else{
        $("#tsModal .modal-body p").text("添加的属性值已达上限");
        $("#tsModal").modal();
    }
}

//删除属性值
function delAttr(obj){
    obj.parent().remove();
    if(obj.hasClass("delCol") && obj.parent().find("input").val()){//属性
        getAttrValue(obj.parent().find("input"),2);
    }else if(obj.find("input").val()){//属性值
        getAttrValue(obj.find("input"),1);
    }
    if($(".product_attr").length < 2){
     $(".product_attr:first .addBtn").css("visibility","visible");
    }
}



function upImg(obj){
    //绑定图片上传事件
    var i = $(obj).attr("id").split("_")[1];
    new uploadPreview({UpBtn: "cover_"+i, DivShow: "imgdiv"+i, ImgShow: "imgShow_"+i});
}
function scrollMsg()
{
  if(self!=top){
    var scrollTop = window.parent.document.documentElement.scrollTop||window.parent.document.body.scrollTop+50;
    $(".edui-state-centered").css({"top":scrollTop+"px","z-index":1000});
  }
}
    //SKU信息
$(function () {
    window.setInterval(function(){
        scrollMsg();
    },200);
     window.getAttrValue = function(obj,type){
        if(type == 1 && $(obj).val() != "") {
            var arrValue = [];
            $(obj).parent().parent().parent().find(".attrValue").each(function(){
                arrValue.push($(this).find("input[type='text']").val());
            });
            $(obj).parent().parent().parent().find(".attrValue").each(function(){
                arrValue.push($(this).find("input[type='text']").val());
            });
            if(arrValue.length > 0){
                step.Creat_Table();
            }
        }else if(type == 2 && $(obj).val() != "" && $(obj).parent().parent().parent().find(".Father_Title input[type='text']").val() != ""){
            step.Creat_Table();
        }
    }
    var step = {
        //SKU信息组合
        Creat_Table: function () {

            step.hebingFunction();
            if(!isReviseSku) {
              getAlreadySetSkuVals();
            } else{
              isReviseSku = false;
            }
            var SKUObj = $(".attribute .Father_Title");
            //var skuCount = SKUObj.length;//
            var arrayTile = new Array();//一级组数
            var arrayInfor = new Array();//盛放每组选中的二级属性值的对象
            var arrayColumn = new Array();//指定列，用来合并哪些列
            var propvalidArr = [];//记录SKU值主键
            var bCheck = true;//是否全选
            var columnIndex = 0;
            $.each(SKUObj, function (i, item){
                if ($(item).find("input[type='text']").val() != ""){
                    var itemName = $(item).parent().find(".attrValue");
                    //选中的CHeckBox取值
                    var order = [];
                    var skuVal = [];
                    $.each(itemName, function (index,value){
                        if($(value).find("input[type='text']").val() != ""){
                            order.push($(value).find("input[type='text']").val());
                            var idx = $(item).attr("propid")+$(value).find("div").attr("propvalid");
                            skuVal.push($(item).attr("propid")+$(value).find("div").attr("propvalid"))
                        }
                    });
                    if(order.join()!= ""){
                        arrayTile.push($(item).find("input[type='text']").val());
                        arrayInfor.push(order);
                        arrayColumn.push(columnIndex);
                        columnIndex++;
                        propvalidArr.push(skuVal);
                    }else{
                        bCheck = false;
                    }
                }
            });
            //开始创建Table表
            if (bCheck == true) {
                var RowsCount = 0;
                $("#createTable").html("");
                var table = $("<table id=\"process\" border=\"1\" cellpadding=\"1\" cellspacing=\"0\" style=\"width:100%;padding:5px;\"></table>");
                table.appendTo($("#createTable"));
                var thead = $("<thead></thead>");
                thead.appendTo(table);
                var trHead = $("<tr></tr>");
                trHead.appendTo(thead);
                //创建表头
                $.each(arrayTile, function (index, item) {
                    var td = $("<th>" + item + "</th>");
                    td.appendTo(trHead);
                });
                var itemColumHead = $("<th  style=\"width:70px;\">价格</th><th style=\"width:70px;\">图片(尺寸要求750x750,大小200k以内)</th><th style=\"width:70px;\">库存</th> ");
                itemColumHead.appendTo(trHead);
                var tbody = $("<tbody></tbody>");
                tbody.appendTo(table);
                ////生成组合
                var zuheDate = step.doExchange(arrayInfor,propvalidArr);
                if (zuheDate[0].length > 0) {
                    //创建行
                    $.each(zuheDate[0], function (index, item) {
                        var td_array = item.split(",");
                        var tr = $("<tr propvalids='"+zuheDate[1][index]+"' class=\"sku_table_tr\"></tr>");
                        tr.appendTo(tbody);
                        $.each(td_array, function (i, values) {
                            var td = $("<td>" + values + "<input type='hidden' name='skulist["+index+"][col"+i+"]' value='" + values + "'/></td>");
                            td.appendTo(tr);
                        });
                        var propvalids = zuheDate[1][index];
                        var alreadySetSkuPrice = "";//已经设置的SKU价格
                        var alreadySetSkuPicture = "";//已经设置的SKU价格
                        var alreadySetSkuStock = "";//已经设置的SKU库存
                        var alreadySetSkuId = "";//skuid

                        //赋值
                        if(alreadySetSkuVals){
                            var currGroupSkuVal = alreadySetSkuVals[propvalids];//当前这组SKU值
                            if(currGroupSkuVal){
                                alreadySetSkuPrice = currGroupSkuVal.skuPrice;
                                alreadySetSkuPicture = currGroupSkuVal.skuPicture;
                                alreadySetSkuStock = currGroupSkuVal.skuStock;
                                alreadySetSkuId = currGroupSkuVal.skuId == '' ?  0 : currGroupSkuVal.skuId;
                            }
                        }
                        var td1 = $("<td class=\"form-group\"><input name='skulist["+index+"][price]' class=\"setting_sku_price\" type=\"text\" value='"+alreadySetSkuPrice+"' check-type=\"required plus decimal\" maxlength=\"10\"></td>");
                        td1.appendTo(tr);
                        var hiddenHtml = '<input name="skulist['+index+'][image]" type="hidden" value="'+alreadySetSkuPicture+'"/><input name="skulist['+index+'][sku_id]" type="hidden" class="skuValue" value="'+alreadySetSkuId+'"/>';
                        var td2 = $('<td class="form-group skuimg"><input  name="skuimg['+index+']" type="file" value="'+alreadySetSkuPicture+'" id="cover_'+index+'" value="cover_'+index+'"check-type="required"/><div id="imgdiv_'+index+'"><img class=\"setting_sku_picture\" id="imgShow_'+index+'" src="'+alreadySetSkuPicture+'" width="100" height="100">'+hiddenHtml+'</div><p class="error">图片文件不能超过200kb</p></td>');
                        td2.appendTo(tr);
                        var td3 = $("<td class=\"form-group\"><input name='skulist["+index+"][stock]' class=\"setting_sku_stock\" type=\"text\" value='"+alreadySetSkuStock+"' check-type=\"required nonnegativeInteger\" maxlength=\"10\"></td>");
                          td3.appendTo(tr);
                        //var td4 = $("<td ><input name=\"Txt_SnSon\" class=\"l-text\" type=\"text\" 
                        //td4.appendTo(tr);
                        tr.validation({reqmark:false});

                    });
                    $("tbody input[type='file']").each(function(){
                        upImg(this);
                    });
                   fileValid();
                }
                //结束创建Table表
                arrayColumn.pop();//删除数组中最后一项
                //合并单元格
                $(table).mergeCell({
                    // 目前只有cols这么一个配置项, 用数组表示列的索引,从0开始
                    cols: arrayColumn
                });
            }
        },//合并行
        hebingFunction: function () {
            $.fn.mergeCell = function (options) {
                return this.each(function () {
                    var cols = options.cols;
                    for (var i = cols.length - 1; cols[i] != undefined; i--) {
                        // fixbug console调试
                        mergeCell($(this), cols[i]);
                    }
                    dispose($(this));
                });
            };
            function mergeCell($table, colIndex) {
                $table.data('col-content', ''); // 存放单元格内容
                $table.data('col-rowspan', 1); // 存放计算的rowspan值 默认为1
                $table.data('col-td', $()); // 存放发现的第一个与前一行比较结果不同td(jQuery封装过的), 默认一个"空"的jquery对象
                $table.data('trNum', $('tbody tr', $table).length); // 要处理表格的总行数, 用于最后一行做特殊处理时进行判断之用
                // 进行"扫面"处理 关键是定位col-td, 和其对应的rowspan
                $('tbody tr', $table).each(function (index) {
                    // td:eq中的colIndex即列索引
                    var $td = $('td:eq(' + colIndex + ')', this);
                    // 取出单元格的当前内容
                    var currentContent = $td.html();
                    // 第一次时走此分支
                    if ($table.data('col-content') == '') {
                        $table.data('col-content', currentContent);
                        $table.data('col-td', $td);
                    } else {
                        // 上一行与当前行内容相同
                        if ($table.data('col-content') == currentContent) {
                            // 上一行与当前行内容相同则col-rowspan累加, 保存新值
                            var rowspan = $table.data('col-rowspan') + 1;
                            $table.data('col-rowspan', rowspan);
                            // 值得注意的是 如果用了$td.remove()就会对其他列的处理造成影响
                            $td.hide();
                            // 最后一行的情况比较特殊一点
                            // 比如最后2行 td中的内容是一样的, 那么到最后一行就应该把此时的col-td里保存的td设置rowspan
                            if (++index == $table.data('trNum'))
                                $table.data('col-td').attr('rowspan', $table.data('col-rowspan'));
                        } else { // 上一行与当前行内容不同
                            // col-rowspan默认为1, 如果统计出的col-rowspan没有变化, 不处理
                            if ($table.data('col-rowspan') != 1) {
                                $table.data('col-td').attr('rowspan', $table.data('col-rowspan'));
                            }
                            // 保存第一次出现不同内容的td, 和其内容, 重置col-rowspan
                            $table.data('col-td', $td);
                            $table.data('col-content', $td.html());
                            $table.data('col-rowspan', 1);
                        }
                    }
                });
            }
            // 同样是个private函数 清理内存之用
            function dispose($table) {
                $table.removeData();
            }
        },
        //组合数组
        doExchange: function (doubleArrays,propvalidArr) {
            var len = doubleArrays.length;

            if (len >= 2) {
                var arr1 = doubleArrays[0], propArr1 = propvalidArr[0];
                var arr2 = doubleArrays[1], propArr2 = propvalidArr[1];
                var len1 = doubleArrays[0].length, propLen1 = propvalidArr.length;
                var len2 = doubleArrays[1].length, propLen2 = propvalidArr.length;
                var newlen = len1 * len2, propnewlen = propLen1 * propLen2;
                var temp = new Array(newlen), proptemp = new Array(propnewlen);
                var index = 0;
                for (var i = 0; i < len1; i++) {
                    for (var j = 0; j < len2; j++) {
                        temp[index] = arr1[i] + "," + arr2[j];
                        proptemp[index] = propArr1[i] + "," + propArr2[j];
                        index++;
                    }
                }
                var newArray = new Array(len - 1);
                var propnewArray = new Array(len - 1);
                newArray[0] = temp, propnewArray[0] = proptemp;
                if (len > 2) {
                    var _count = 1;
                    for (var i = 2; i < len; i++) {
                        newArray[_count] = doubleArrays[i];
                        propnewArray[_count] = propvalidArr[i];
                        _count++;
                    }
                }
                return step.doExchange(newArray,propnewArray);

            }
            else {
                var propArr = [];
                propArr.push(doubleArrays[0]);
                propArr.push(propvalidArr[0]);
                return propArr;
            }
        }
    }
    return step;
})
$(function(){
    $(".attribute").validation({reqmark:false});
    if(skuAttr) reviseSku();
});
//跳转到列表页
function skipList(){
    window.location.href="/shop-goods/index";
}

/**
 * 获取已经设置的SKU值
 */
function getAlreadySetSkuVals(){
    alreadySetSkuVals = {};
    //获取设置的SKU属性值
    $("tr[class*='sku_table_tr']").each(function(){
        var skuPrice = $(this).find("input[type='text'][class*='setting_sku_price']").val();//SKU价格
        var skuPicture = $(this).find("img[class*='setting_sku_picture']").attr("src");//SKU图片
        var skuStock = $(this).find("input[type='text'][class*='setting_sku_stock']").val();//SKU库存
        var skuId = $(this).find("input[type='hidden'][class='skuValue']").val();
        if(skuPrice || skuPicture || skuStock){//已经设置了全部或部分值
            var propvalids = $(this).attr("propvalids");//SKU值主键集合
            alreadySetSkuVals[propvalids] = {
                "skuPrice" : skuPrice,
                "skuPicture" : skuPicture,
                "skuStock" : skuStock,
                "skuId" : skuId,
            }
        }
    });

}

/*
 *上传商品图片
 */
var photoNum = 0;
if(typeof(image) != "undefined"){
var imageArr = image.split(",");
    photoNum = imageArr.length;
}
$("#z_photo").on("click", function(){
    if($(".photo .form-group").length < 3){
        var html = "";
            html+= '<div class="form-group" id="photodiv'+photoNum+'">';
            html+= '<input id="photocover_'+photoNum+'" name="image['+photoNum+']"  value="" type="file" style="display:none;">';
            html+= '<img class="lazy" id="photoShow_'+photoNum+'" width="100" height="100" src="" onclick="imgRemove(this)" style="display: inline;">';
            html+= '<span class="error">图片文件不能超过200kb</span>';
            html+= '</div>';
        $(".photo .form-inline").append(html);
        new uploadPreview({UpBtn: "photocover_"+photoNum, DivShow: "photodiv"+photoNum, ImgShow: "photoShow_"+photoNum});
        $("#photocover_"+photoNum).trigger("click");
        photoNum++;
    }else{
        $("#tsModal .modal-body p").text("图片最多上传3张");
        $("#tsModal").modal();
    }

});

if(typeof(image) != "undefined"){
    for(var i=0;i<photoNum;i++){
        if(imageArr[i] == ''){
            break;
        }
        var html = "";
        html+= '<div class="form-group" id="photodiv'+i+'">';
        html+= '<input id="photocover_'+i+'" name="image[]" value="" type="file" style="display:none;">';
        html+= '<img class="lazy" id="photoShow_'+i+'" width="100" height="100" src="'+url+imageArr[i]+'" onclick="imgRemove(this)" style="display: inline;">';
        html+= '<span class="error">图片文件不能超过200kb</span>';        html+= '</div>';
        $(".photo .form-inline").append(html);
        new uploadPreview({UpBtn: "photocover_"+i, DivShow: "photodiv"+i, ImgShow: "photoShow_"+i});
        $("#photocover_"+i).trigger("click");
    }
}

function imgRemove(obj) {
    $("#delModal").modal();
    var deleteImg = $("#delete_image").val();
    $("#delModal .sure").on("click",function() {
        $("#delete_image").val(deleteImg+($(obj).parent().find('img').attr('src'))+',');
        $(obj).parent().remove();
    });
};

//检测上传banner图片
function uploadImgCheck(){
   var isOverSize=false;
   var aa=$(".photo .form-inline").find("div").length;
   if(aa==0){
        $(".photo .error").show();
        return false;
   }else{
        $(".photo .error").hide();

        $(".photo input[type='file']").each(function(i,obj){
            var obj=document.getElementById($(obj).attr("id"));
            if(obj.files.length!==0){
                var imgSize = 0;
                var isIE = /msie/i.test(navigator.userAgent) && !window.opera;
                if (isIE && !obj.files) {
                     var filePath = obj.value;
                     var fileSystem = new ActiveXObject("Scripting.FileSystemObject");
                     var file = fileSystem.GetFile (filePath);
                     imgSize = file.Size;
                }else {
                     imgSize = obj.files[0].size;
                }
                imgSize=Math.round(imgSize/1024*100)/100; //单位为KB
                if(imgSize>=200){
                    isOverSize=true;
                    obj.parentNode.lastChild.style.display="block";
                }
            }
        })
        if(!isOverSize){
            return true;
        }else{
            return false;
        }
   }
}

//检测商品图片
function  uploadProductImg(){
    var isOverProductSize=false;
     $(".skuimg input[type='file']").each(function(i,obj){
            var obj=document.getElementById($(obj).attr("id"));
            var imgSize = 0;
            var isIE = /msie/i.test(navigator.userAgent) && !window.opera;
            if (isIE && !obj.files) {
                 var filePath = obj.value;
                 var fileSystem = new ActiveXObject("Scripting.FileSystemObject");
                 var file = fileSystem.GetFile (filePath);
                 imgSize = file.Size;
            }else {
                 imgSize = obj.files[0].size;
            }
            imgSize=Math.round(imgSize/1024*100)/100; //单位为KB
            if(imgSize>=200){
                isOverProductSize=true;
                obj.parentNode.lastChild.style.display="block";
            }
        })
        if(!isOverProductSize){
            return true;
        }else{
            return false;
        }

}
//文本编辑器
var ue = UE.getEditor("editor",{
    autoHeightEnabled: false,
    zIndex : 1,
    toolbars: [
        ['undo','redo','bold', 'indent','italic','underline', 'strikethrough','justifyleft', //居左对齐
            'justifyright', 'justifycenter', 'justifyjustify', 'forecolor', 'backcolor', 'touppercase', //字母大写
            'tolowercase', 'directionalityltr', 'directionalityrtl', 'rowspacingtop', 'rowspacingbottom', //段后距
            'subscript', 'fontborder', 'superscript', 'formatmatch', 'blockquote', //引用
            'horizontal', 'removeformat', 'time', 'date',
            'fontfamily', 'fontsize', 'paragraph', 'spechars', 'searchreplace', //查询替换
            'insertimage', //多图上传
        ]
    ],
    textarea:'ShopGoods[content]',
    initialFrameHeight:320,
});
ue.ready(function(){
    ue.setContent(content);

});
//检测编辑器
function editorCheck(){
    var editorContent=ue.hasContents();
    if(editorContent==""){
          $(".table-field-activity-activity_desc .error").show();
          return false;
    }else{
         $(".table-field-activity-activity_desc .error").hide();
        return true;
    }
}
/**
 * ajax上传文件
 * @author  wxz
 * @version 2017-09-04
 * @param   object   obj js对象
 */
function uploadFile() {
    $("form").validation();
    var imgCheck=uploadImgCheck();//检测banner图
    var editor=editorCheck();//检测编辑器
    if($("form").valid()){
        if(imgCheck && editor){
             $.ajax({
                url: url+"shop-api/add-shop-goods-api.html",
                type:'post',
                dataType: 'json',
                data: new FormData($('#w0')[0]),
                processData: false,
                contentType: false,
                success: function (data) {
                    if(data.ret == 0){
                        createActivityLog();
                        window.location.href="/shop-goods/index";
                    }else if (data.ret == 1){
                        var failImageLength = data.failImage.length;
                        var imageString = '';
                        for(var i=0;i < failImageLength;i++){
                            imageString += data.failImage[i].name+','+data.failImage[i].error+';';
                        }
                        $(".submit-error").html("上传图片错误:"+imageString);
                    } else {
                         $(".submit-error").html(data.msg);
                    }
                },
                error: function (data) {
                    $(".submit-error").html('服务器上传失败');
                }
            });
        }
    }
}

/**
 * 修改商品页面的sku数据展示
 **/
function reviseSku() {
  $('.attribute').html("");
  var skuAttrData = {};
  var fatherEle = null;
  skuAttrData= JSON.parse(skuAttr);
      $.each(skuAttrData, function(index,item){
        var html = "";
        if(item.parent_id == 0){
          fatherEle = item.attribute_id;
          html = '<div class="product_attr product_attr'+fatherEle+'">'
//                    +'<div class="form-inline Father_Title" propid="'+customPropId+'">'
                      +'<div class="form-inline Father_Title" propid="'+item.attribute_id+'">'
                      +'<div class="form-group">'
                          +'<label>属性</label>'
                          +'<input class="form-control" id="attr'+item.attribute_id+'" type="text" name="attr['+item.attribute_id+'][name]" onchange="getAttrValue(this,1)" check-type="required" maxlength="10" value="'+item.goods_attribute+'"/>'
                      +'</div>';
                      if($('.attribute .product_attr').length > 0){
                        html = html +'<button class="btn btn-sm" type="button" onclick="delAttr($(this).parent())"><span class="glyphicon glyphicon-remove"></span></button>&nbsp'
                                    +'<button class="btn btn-sm" type="button" onclick="addAttrValue(this)"><span class="glyphicon glyphicon-plus"></span>属性值</button>'
                                    +'</div>'
                                    +'</div>';
                      }else{
                        html = html +' <button class="btn btn-sm addBtn" type="button" onclick="addAttr(this)"><span class="glyphicon glyphicon-plus"></span></button>&nbsp'
                                    +'<button class="btn btn-sm" type="button" onclick="addAttrValue(this)"><span class="glyphicon glyphicon-plus"></span>属性值</button>'
                                    +'</div>'
                                    +'</div>';

                      }
          attrNum++;
          $('.attribute').append(html);
        }else{
            var valueID = $(".product_attr"+item.parent_id).find(".Father_Title input").attr("id");
            var attrValueLength = $(".product_attr"+item.parent_id).find(".attrValue").length;
            var propvalids = attrValueLength+1 ;
            var htmls = '<div class="attrValue form-inline">'
                          +'<div class="form-group" propvalid="-'+item.attribute_id+'">'
                          +'<label>属性值</label>'
                          +'<input data-id="prop-'+valueID+'_'+attrValueLength+'" class="form-control" type="text" name="attr['+item.parent_id+'][value]['+item.attribute_id+']" onchange="getAttrValue(this,2);" check-type="required" value="'+item.goods_attribute+'"/>'
          htmls+='</div>'
          htmls+='</div>';
          $(".product_attr"+item.parent_id).append(htmls);
        }
      })
      if(skuList) {
        $.each(JSON.parse(skuList),function(index,item){
          var propvalids = item.sku_combin;//SKU值主键集合
          alreadySetSkuVals[propvalids] = {
              "skuPrice" : item.price,
              "skuPicture" : item.private_image,
              "skuStock" : item.stock,
              "skuId": item.sku_id
          }
        });
     }
     isReviseSku = true;
    if($('.attribute .product_attr').length > 1){
       $(".attribute .addBtn").css("visibility","hidden");
    }
    $('.attribute input').trigger("change");
}

function fileValid(){
    $("#createTable img").each(function(){
        if($(this).attr("src")) $(this).parent().parent().find("input[type='file']").removeAttr("check-type");
    });
}

// 添加操作记录
function createActivityLog()
{
    var type =1;
    if($("#goods_id").val()){
        type = 2;
    }
    $.ajax({
        type:'get',
        url:'/activity-combin-package-assoc/create-activity-log',
        data:{'type': type,'moduleType':7},
        success:function(data){

        },
    })
}

