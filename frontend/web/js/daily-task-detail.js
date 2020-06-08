// $(function () {
  var localIds="";
  var voice1 = document.getElementById('voice1');
  var voice2 = document.getElementById('voice2');
  var localIdstring=localStorage.getItem("distributionTaskCleanImgList_"+taskId);
  var localidList = [];
  if (localIdstring && localIdstring.length>0) {
      localidList = localIdstring.split(',');
     $(".img").each(function(index,item) {
        if ($(item).attr("src") == "/images/uploads-icon.jpg") {
          $(item).attr("src",localidList[index]);
        }
      });
  }
  //检测物料
  function checkMaterial(){
    var name=$("#myTab li.active").find("a").html();
    if(name=="日常任务"){
        var waterAddNum=$(".water_add").val();
        var waterRemainNum=$(".water_remain").val();
        var cupAddNum=$(".cups_add").val();
        var cupRemainNum=$(".cups_remain").val();
        var coverAddNum=$(".cover_add").val();
        var coverRemainNum=$(".cover_remain").val();
        if(waterAddNum=="" || waterRemainNum==""){
          alert("水量不能为空")
          return true;
        }else if(cupAddNum=="" ||cupRemainNum==""){
          alert("杯子不能为空")
          return true;
        }else if(coverAddNum=="" || coverRemainNum==""){
          alert("杯盖不能为空")
          return true;
        }
        return false;
    }
  }
  $("#clearCache").click(function(){
    localStorage.removeItem("distributionTaskCleanImgList_"+taskId);
    document.location.reload();
  })
  //点击下一步跳转到维修页面
  $('#next').on('click',function(){
    var isEmpty=checkMaterial();
    if(!isEmpty){
      $('#next').hide();
      $('.tab').trigger('click');
      $('#save').show();
    }
  });
  wx.config({
    debug: false,
    appId: appId,//企业微信的corpID
    timestamp: timestamp,//生成签名的时间戳
    nonceStr: nonceStr,//生成签名的随机串
    signature: signature,
    jsApiList: [
        'chooseImage',
        'uploadImage',
        'getLocation'
    ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
  });
  wx.ready(function () {
    wx.checkJsApi({
      jsApiList: [
        'chooseImage',
        'uploadImage',
        'getLocation'
      ],
      success: function(res) {
        console.log(res);
      }
    });
    function chooseImage(obj) {
      wx.chooseImage({
        count: 1, // 默认9
        sizeType: ['compressed'], // 可以指定是原图还是压缩图，默认二者都有
        sourceType: ['camera'], // 可以指定来源是相册还是相机，默认二者都有
        isSaveToAlbum: 1,
        success: function (res) {
          console.log("androidchooseImage:",res);
          localidList.push(res.localIds[0]);
          console.log(localidList);
          $(obj).attr("src", res.localIds[0]);
        },
        error: function(data){
          console.log(data);
        }
      });
    }
    function getLocation(){
      // 微信定位获取坐标
      wx.getLocation({
          type: "gcj02",
          success: function (res) {
              // 根据坐标获取地址
              $.ajax({
                  type: "GET",
                  url:"http://apis.map.qq.com/ws/geocoder/v1/?location="+res.latitude+","+res.longitude+"&output=jsonp&key=RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ",
                  async: false,
                  dataType: "jsonp",
                  success: function (msg, textStatus) {
                       $(".loaded").hide();
                      // 提交数据
                      $("#Modal1").modal();
                      $("#Modal1 #btn_submit").click(function (){
                          $("#end_address").val(msg.result.formatted_addresses.recommend);
                          $("#end_longitude").val(res.longitude);
                          $("#end_latitude").val(res.latitude);
                          $("#w0").submit();
                      })
                  }
              })
          }
      })
      wx.error(function (res) {
          alert("获取定位失败，请重试");
      });
    }
    function uploadImg(){
      console.log("locallist:",localidList);
      var localIdstr = localidList.toString();
      console.log("locallist2:",localIdstr);
      if (localIdstr && localIdstr.length>0) {
        localStorage.setItem("distributionTaskCleanImgList_"+taskId,localIdstr);
        console.log("localIds:",localIdstr);
        localidList.forEach(function(id,index){
          console.log('id:',id);
          wx.uploadImage({
            localId: id, // 图片的localID
            success: function (res) {
              console.log("androiduploadImageImage:",res);
              $(".img").each(function(index,item) {
                if ($(item).next().val()=='') {
                  console.log("androiduploadImageImage1:",res.serverId);
                  $(item).next().val(res.serverId);
                  console.log("androiduploadImageImage2:",$(item).parent().html());
                  return false;
                }
              })
            },
            error:function(res){
              console.log('uploadFail:'.res);
            }
          });
        })
      }
    }
    $("#detectionBnt").on("click", function(){
      uploadImg();
      //检测图片的数量
      var srcIds = [];
      $(".img").each(function(index,item) {
        if ($(item).attr("src") == "/images/uploads-icon.jpg") {
          srcIds.push($(item).attr("src"));
        }
      });
      if(srcIds.length > 2){
        alert('请上传6到8张图片')
        return false
      }
      detectionMaterial();
    });
    function detectionMaterial() {
      $.ajax({
        url: "/distribution-task/testing",
        type:"GET",
        data:{id:taskId},
        success: function(res){
          var data = JSON.parse(res);
            if(data.distribution.done==0){
              $('.change-state').html('未完成')
            }else{
              $('.change-state').html('已完成');
              $('.change-date').html('时间: '+data.distribution.date);
              var str = '';
              for (var key in data.distribution.material){
                str += `<span>${data.distribution.material[key]['material_type_name']}:</span>
                  <span>${data.distribution.material[key]['gram']}&nbsp;&nbsp;&nbsp;&nbsp;</span>`
              }
              $('.change-list').html(str)
            }
          if(data.clean.done==0){
            $('.wash-state').html('未完成')
          }else{
            $('.wash-state').html('已完成');
            $('.wash-date').html('时间: '+data.clean.date)
          }
        },
        error: function(data){
            console.log(data);
        }
      })
    };
    document.body.addEventListener("touchmove", function (event) {
        if ($("body").attr("class")=="modal-open") {
            event.preventDefault();
        }
    });
    $(".img").on("click", function() {
        if($(this).attr("src") == "/images/uploads-icon.jpg"){
          chooseImage(this);
        }
        //调后台接口，将返回来的图片ID传给后台
    });
    $("#save").on("click", function(){
      //检测图片的数量
      var srcIds = [];
      $(".img").each(function(index,item) {
        if ($(item).next().val()=='') {
          srcIds.push(1);
        }
        console.log($(item).next().val());
      });
      if(srcIds.length > 2){
        alert('请上传6到8张图片')
        return false
      }
      if ($('.wash-state').html()=='未完成' || $('.change-state').html()=='未完成') {
        alert('请清洗设备并上传物料');
        return false;
      }
      var isEmpty=checkMaterial();
      if(!isEmpty){
        $(".loaded").show();
        getLocation();
        localStorage.removeItem("distributionTaskCleanImgList_"+taskId);
      } else {
        return false;
      }
    });
  })