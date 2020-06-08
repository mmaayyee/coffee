$(function(){
  //页面初始化渲染的下拉菜单
  var taskListTpl = $('#taskListTpl').html();
  laytpl(taskListTpl).render(equipmentData,function(html){
    $('.equipmentType').html(html);
  })
//改变下拉菜单
  $('.select').change(function(){
    var id = $(this).children('option:selected').val();
    // console.log('下拉菜单',id);
    //此处应该清除dom节点
    $('.parameter').empty();
    // addParameter();
    showParameter(id);
  });
  //点击保存添加新的空白行
  $('.parameter').on('click','.save',function(){
    // console.log('保存');
    var newId = $(this).parent('.add-row').attr('id');
    var typeId = $('.select').children('option:selected').val();
    var parameterName = $(this).siblings('.parameters').val();
    var maxParameter = $(this).siblings('.top').val();
    var minParameter = $(this).siblings('.bottom').val();
    if(parseInt(maxParameter) < parseInt(minParameter)){
      alert('最大值不能小于最小值');
      return false;
    }
    if(parseInt(maxParameter) < parseInt(minParameter)){
      alert('最小值不能大于最大值');
      return false;
    }
    var localtions = $(this).siblings('.localtions').val();
    var _this = this;
    $.ajax({
      url: '/equipment-type-parameter/update',
      type: 'post',
      data: {equipment_type_id: typeId,id: newId,parameter_name: parameterName,max_parameter: maxParameter,min_parameter: minParameter,localtions:localtions},
      success: function(res){
        var res = JSON.parse(res);
        // console.log(res);
        if(res.status == 'success'){
          if($(_this).parent('.add-row').attr('id') == ''){
            var siblingsArr = $(_this).parent('.add-row').siblings('.empty');
            if(siblingsArr.length != 0){
              $(_this).parent('.add-row').attr('class','add-row have');
            }
            if(siblingsArr.length == 0){
              $(_this).parent('.add-row').attr('class','add-row have');
              $(_this).parent('.add-row').attr('id',res.id);
              addParameter();
            }
          }

          $(_this).hide();
          $(_this).siblings('.edit').show();
          $(_this).siblings('.delete').show();
          $(_this).siblings('.parameters').attr("disabled",true);
          $(_this).siblings('.top').attr("disabled",true);
          $(_this).siblings('.bottom').attr("disabled",true);
          $(_this).siblings('.localtions').attr("disabled",true);
        }else{
          alert('保存失败');
          return false
        }
      },error: function(msg){
        alert(msg);
        return false
      }
    })
  })
  //点击编辑按钮
  $('.parameter').on('click','.edit',function(){
    $(this).siblings('.parameters').attr("disabled",false);
    $(this).siblings('.top').attr("disabled",false);
    $(this).siblings('.bottom').attr("disabled",false);
    $(this).siblings('.localtions').attr("disabled",false);
    $(this).siblings('.parameters').focus();
    $(this).hide();
    $(this).siblings('.delete').hide();
    $(this).siblings('.save').show();
  })
  //点击删除
  $('.parameter').on('click','.delete',function(){
    var newId = $(this).parent('.add-row').attr('id');
    var _this = this;
    $.ajax({
      url: '/equipment-type-parameter/delete',
      type: 'post',
      data: {id: newId},
      success: function(res){
        var res = JSON.parse(res);
        // console.log(res);
        if(res.status == 'success'){
          //移除当前行dom
          $(_this).parent('.add-row').remove();
        }else{
          alert('删除失败');
        }
      },error: function(error){
        alert(error)
      }
    })
  })
  //添加空白行
  function addParameter(){
    var str = "";
    var str = `<div class="add-row empty" id="">
               <span>设备参数 </span><input type="text" class="parameters"/>
               <span>上限值 </span><input type="text" class="top"/>
               <span>下限值 </span><input type="text" class="bottom"/>
               <span>locations </span><input type="text" class="localtions"/>
               <button class="btn btn-primary save" style="margin-bottom: 8px">保存</button>
               <button class="btn btn-success edit" style="margin-bottom: 8px;display:none">编辑</button>
               <!--<button class="btn btn-danger delete" style="margin-bottom: 8px;display:none">删除</button>-->
              </div> 
              `
    $('.parameter').append(str);
  }
  //显示已有参数
  function showParameter(id){
    var value="";
    $.ajax({
      url: '/equipment-type-parameter/get-parameter-list',
      type: "POST",
      data: {equipment_type_id:id},
      success:function(res){
        var res = JSON.parse(res);
        // console.log(res);
        if(res.length ==0){
          addParameter()
        }else{
          $.each(res,function(index,item){
                 value+= '<div class="add-row" id="'+item.id+'">'
                      +'<span>设备参数 </span><input type="text" class="parameters" value="'+item.parameter_name+'"/>'
                      +'<span>上限值 </span><input type="text" class="top" value="'+item.max_parameter+'"/>'
                      +'<span>下限值 </span><input type="text" class="bottom" value="'+item.min_parameter+'"/>'
                      +'<span>locations </span><input type="text" class="localtions" value="'+item.localtions+'"/>'
                      +'<button class="btn btn-primary save" style="margin-bottom: 8px">保存</button>'
                      +'<button class="btn btn-success edit" style="margin-bottom: 8px;display:none">编辑</button>';
                      // if(item.parameterStatus == 1){
                      //     value+='<button class="btn btn-danger delete" style="margin-bottom: 8px;display:none">删除</button>';
                      // }
              value+='</div>';
            $('.parameter').html(value);
            $('.save').hide();
            $('.edit').show();
            $('.delete').show();
            $('.parameters').attr("disabled",true);
            $('.top').attr("disabled",true);
            $('.bottom').attr("disabled",true);
            $('.localtions').attr("disabled",true);
          })
          addParameter()
        }
      },error: function(msg){
        alert(msg)
      }
    })
  }
})
