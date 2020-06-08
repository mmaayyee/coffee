// console.log($("#consumeChart").width)
$(function(){
  var pieChartData = [];
  var pieChart2Data = [];

  getPieChart();
  function getPieChart(pieDate){
    var defaultUrl = rootCoffeeStieUrl+"equipments-count-api/equipments-count-list.html";
    var _url = pieDate?defaultUrl+"?date="+pieDate:defaultUrl;
    console.log("_url..",_url)
    pieChartData = [];
    pieChart2Data = [];
    $.ajax({
        type: "GET",
        url:_url,
        dataType:"json",
        success:function(apiData){
          console.log("pie apiData..",apiData);
          if(apiData.code==200){
            if(apiData.data!=[]&&apiData.data.branch){
              for(var key in apiData.data.branch) {
                var obj = {};
                obj.branch = key;
                obj.amount = apiData.data.branch[key];
                pieChartData.push(obj);
              }
            }
            if(apiData.data!=[]&&apiData.data.agent){
              for(var key in apiData.data.agent) {
                var obj = {};
                obj.agent = key;
                obj.amount = apiData.data.agent[key];
                pieChart2Data.push(obj);
              }
            }
            if(apiData.data!=[]&&apiData.data.date){
              $("#datepicker").val(apiData.data.date);
            }
            initPChart();
          } else {
            alert(apiData.message);
          }
        },
        error:function(XMLHttpRequest,textStatus){
          console.log("fail:",textStatus)
          initPChart();
        }
    })
  }
  function initPChart(){
    if(pieChartData.length>0) {
      $("#pieChart").show();
      $("#pieChartTxt").hide();
      initPieChart(pieChartData,"pieChart","branch","amount","分公司");
    } else {
      $("#pieChart").hide();
      $("#pieChartTxt").show();
    }
    if(pieChart2Data.length>0) {
      $("#pieChart2").show();
      $("#pieChartTxt2").hide();
      initPieChart(pieChart2Data,"pieChart2","agent","amount","代理商公司");
    } else {
      $("#pieChart2").hide();
      $("#pieChartTxt2").show();
    }
  }
  function initPieChart(chartData,chartId,dataName,dataValue,titleName){
    var myChart = echarts.init(document.getElementById(chartId));
    myChart.clear();
    var tmpData = chartData;
    for(var i=0,len=tmpData.length;i<len;i++){
      var obj = {};
      obj.value = Number(tmpData[i][dataValue]);
      obj.name = tmpData[i][dataName];
      chartData.push(obj);
    }
    // console.log(tmpData)
    // 指定图表的配置项和数据
    var option = {
        title : {
            text: titleName,
            subtext: '',
            x:'center'
        },
        calculable : true,
        series : [
            {
                name:'设备数量',
                type:'pie',
                radius : '70%',
                center: ['50%', '53%'],
                data:chartData,
                itemStyle: {
                  normal: {
                    label : {
                      show: true,
                      position : 'outside',
                      formatter : function (params) {
                        return params.name+'\n'+(params.value - 0)+'\n'+(params.percent - 0).toFixed(2) + '%'
                      }
                    }
                  }
                }
            }
        ]
    };
    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);
  }
  $("#datepicker").datepicker({
        /* 区域化周名为中文 */
        dayNamesMin : ["日", "一", "二", "三", "四", "五", "六"],
        /* 每周从周一开始 */
        firstDay : 1,
        /* 区域化月名为中文习惯 */
        monthNames : ["01", "02", "03", "04", "05", "06",
                    "07", "08", "09", "10", "11", "12"],
        /* 月份显示在年后面 */
        showMonthAfterYear : true,
        /* 年份后缀字符 */
        yearSuffix : "年",
        /* 格式化中文日期
        （因为月份中已经包含“月”字，所以这里省略） */
        dateFormat : "yy-MM-dd",
        maxDate: "-1D"
    });
    $("#datepicker").on("change",function(){
      // console.log("change")
      getPieChart($("#datepicker").val())
    })
})
