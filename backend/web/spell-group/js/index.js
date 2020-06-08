window.onload = function () {
  var dataArr = [];
  var nowIndex="";
rootinit(0);//0代表初始化,1代表取消
  function rootinit(index){
      $.ajax({
        url: rootCoffeeStieUrl + 'group-booking-api/get-sort.html',
        type: "post",
        success: function(res){
          // console.log(res);
          var data = JSON.parse(res).data.group;
          console.log(data);
          dataArr = data;
          // init(dataArr);
          oldFun(dataArr);
          newFun(dataArr);
          if(index==0){
            $('.tab-old').trigger("click");
          }else{
              switch(nowIndex){
              case 0:
              $('.tab-old').trigger("click");
              break;
              case 1:
              $('.tab-new').trigger("click");
              break;
              case 2:
              $('.tab-all').trigger("click");
              break;
            }
          }
          
        }
      })
  }

// 点击老带新
function oldFun(dataArr){
  $('.tab-old').on('click',function(){
    //console.log(111111111111111);
    var newArr = [];
    $.each(dataArr,function(index,item){
      if(item.type == 2){
        newArr.push(item);
        console.log(newArr);
      }
    })
     init(newArr,0)
  });
}
function newFun(dataArr){
   $('.tab-new').on('click',function(){
    // rootinit();
    var newArr = [];
    $.each(dataArr,function(index,item){
      if(item.type == 1){
        newArr.push(item);
      }
    })
     init(newArr,1);
     console.log(newArr);
  });

}

  


    $('.tab-all').on('click',function(){
        var newArr = [];
    $.each(dataArr,function(index,item){
      // console.log(item)
      if(item.type == 3){
        newArr.push(item);
        // console.log("new",newArr);
      }
    })
    init(newArr,2)
    
  })

  var oBox = document.querySelector('.box');
  function init(dataArr,index) {
    bindHtml(dataArr,index)
    bindEvent(oBox, dataArr,index)
    bindDrop(oBox, dataArr,index)
    console.log("bdy");
  }

  function bindHtml(dataArr,index) {
    dataArr = dataArr.sort(function (a, b) {
      return a.group_sort - b.group_sort
    })
    var str = ''
    for (var i = 0; i < dataArr.length; i++) {
      str += `<tr group_id="${dataArr[i]['group_id']}" draggable="true">
                <td>${dataArr[i]['main_title']}</td>
                <td>${i+1}</td>
                <td>
                  <a href="javascript:;"><p class="glyphicon glyphicon-arrow-up">置顶</p></a>
                </td>
              </tr>`
    }

    $(".tab-pane").eq(index).find(".box").html(str);
  }

  function bindEvent(dom, dataArr,index) {
    console.log('zhidingSsS');
    $(".tab-pane").eq(index).find(".box").on("click",function(e){
      var currentBtn = e.target.innerHTML;
      var currentId = Number(e.target.parentNode.parentNode.parentNode.getAttribute("group_id"));
      if (currentBtn == '置顶') {
        console.log(111111111,'置顶');
        for (var i = 0; i < dataArr.length; i++) {
          if (dataArr[i]['group_id'] == currentId) {
            dataArr[i]['group_sort'] = 1
            for (var j = 0; j < i; j++){
              dataArr[j]['group_sort']++
            }
          }
        }
        bindHtml(dataArr,index)
      }
    })
  }

  function bindDrop(dom, dataArr,index) {

    var currentDragId, currentDropId, tempDragSerial, tempDropSerial;
    $(".tab-pane").eq(index).find(".box").on("dragstart",function(e){
      e = e ? e : event;
      e.dataTransfer = e.originalEvent.dataTransfer;
      e.dataTransfer.setData('text/plain', 'data');
      currentDragId=e.target.getAttribute("group_id");
    })
    $(".tab-pane").eq(index).find(".box").on("dragover",function(e){
      e = e ? e : event;
      e.preventDefault();
    })

    $(".tab-pane").eq(index).find(".box").on("drop",function(e){
      e = e ? e : event;
      //阻止火狐默认打开百度
      e.stopPropagation();
      e.preventDefault();
      e.dataTransfer = e.originalEvent.dataTransfer;
      if (e.target.tagName == 'TD') {
        currentDropId = Number(e.target.parentNode.getAttribute("group_id"))
      }
      for (var i = 0; i < dataArr.length; i++) {
        if (dataArr[i]['group_id'] == currentDragId) {
          tempDragSerial = dataArr[i]['group_sort'];
        }
      }
      console.log("77",tempDragSerial);

      for (var i = 0; i < dataArr.length; i++) {
        if (dataArr[i]['group_id'] == currentDropId) {
          tempDropSerial = dataArr[i]['group_sort']
          dataArr[i]['group_sort'] = tempDragSerial
        }
      }

      for (var i = 0; i < dataArr.length; i++) {
        if (dataArr[i]['group_id'] == currentDragId) {
          dataArr[i]['group_sort'] = tempDropSerial
        }
      }
      console.log(dataArr);
      bindHtml(dataArr,index)
      e.preventDefault();
    })



  }

  $('.save').on('click',function(){
    var $tr=$("tr");
    var subData=[];
    var uploadData={};
    // var newData;
    $(".box tr").each(function(i,obj){
      var id=$(obj).attr("group_id");
      var sort=$(obj).find("td:nth-child(2)").html();
      subData.push({group_id:id, group_sort:sort});
      console.log(subData);
      uploadData={"sort":subData};
      console.log(uploadData);
    });


    var uploadData=JSON.stringify({"sort":subData});
    console.log(uploadData);
    console.log(rootCoffeeStieUrl);
    $.post(rootCoffeeStieUrl + 'group-booking-api/save-sort.html',{"subdata":uploadData},function(data){
      var data = JSON.parse(data).state;
      console.log(data);
        if(data == 1){
          alert('保存成功')
        }else{
          alert('保存失败')
        }
    });
  });

  $('.cancel').on('click',function(){
    var newArr = [];
    $(".sort-nav li").each(function(i,obj){
      if($(this).hasClass("active")){
        nowIndex=i;
      }
      rootinit(1);


    })
  })
}
