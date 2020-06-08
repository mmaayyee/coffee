// console.log($("#orderChart").width)
$(function() {
  var chartsColorArr = ['#32c040', '#146479', '#e560cd', '#faae4e', '#354b5e', '#c7e1f0'];
  // var chartsColorArr2 = ['#54c7f1','#3ab2de', '#289eca','#198db8','#0a7da7','#016b93'];
  var currentChartData = [];
  var compareChartData = [];
  var compareNameArr = ["", "昨日", "上周", "上月", "去年"];
  initCurrentChart();
  $("input[name='compareId']:radio").each(function(index) {
    // console.log(index,this)
    $(this).on("click", function() {
      if ($(this).val() == "0") {
        console.log($(this).val())
        initCurrentChart();
      } else {
        initCompareChart($(this).val())
      }
    })
  })

  function initCurrentChart() {
    currentChartData = [];
    $.ajax({
      type: "GET",
      url: rootCoffeeStieUrl + "order-info-api/order-info-report.html",
      dataType: "json",
      success: function(data) {
        console.log("data..", data);
        var apiData = data.data;
        if (data.code == 200) {
          if (apiData != [] && apiData.reportList) {
            for (var key in apiData.reportList) {
              var obj = {};
              obj.date = key;
              obj.hour = Number(apiData.reportList[key].hour_amount);
              obj.order = Number(apiData.reportList[key].order_amount);
              currentChartData.push(obj);
            }
            // console.log(chartData)
            currentChartData.sort(function(a, b) {
              // console.log(a.date.split("-")[0],"..",b.date.split("-")[0]);
              return Number(a.date.split("-")[0]) - Number(b.date.split("-")[0]);
            })
          }
          if (apiData.dayData) {
            $("#todayCupAverage").html(apiData.dayData.cups_average);
          }
          initSChart();
        } else {
          alert(data.message);
        }
      },
      error: function(XMLHttpRequest, textStatus) {
        console.log("fail:", textStatus)
        initSChart();
      }
    })
  }

  function initSChart() {
    initSingleChart(currentChartData, "date", "orderChart", "order", "hour", "订单金额", "订单总额", "每小时增加额");
  }

  function initCompareChart(dateId) {
    compareChartData = [];
    $.ajax({
      type: "GET",
      url: rootCoffeeStieUrl + "order-info-api/order-info-compare.html?date=" + dateId,
      dataType: "json",
      success: function(data) {
        console.log("data..", data);
        var apiData = data.data;
        if (data.code == 200) {
          if (apiData == [] || !apiData.reportList) {
            initSChart();
          } else {
            for (var key in apiData.reportList) {
              var obj = {};
              obj.date = key;
              obj.hour = Number(apiData.reportList[key].hour_amount);
              obj.order = Number(apiData.reportList[key].order_amount);
              compareChartData.push(obj);
            }
            // console.log(chartData)
            compareChartData.sort(function(a, b) {
              // console.log(a.date.split("-")[0],"..",b.date.split("-")[0]);
              return Number(a.date.split("-")[0]) - Number(b.date.split("-")[0]);
            })
            if (apiData.dayData) {
              $("#compareCupAverage").html(apiData.dayData.cups_average);

            }
            $("#compareName").html(compareNameArr[Number(dateId)]);
            // console.log(compareChartData)
            initDoubleChart(currentChartData, compareChartData, compareNameArr[Number(dateId)], "date", "orderChart", "order", "hour", "订单金额", "订单总额", "每小时增加额");
          }
        } else {
          alert(data.message);
        }
      },
      error: function(XMLHttpRequest, textStatus) {
        console.log("fail:", textStatus)
        initSChart();
      }
    })
  }

  function initSingleChart(dataName, dateChar, chartId, dataLine, dataBar, titleName, lineName, barName) {
    $("#comparePart").hide();
    var myChart = echarts.init(document.getElementById(chartId));
    myChart.clear();
    var category = [];
    var data = [
      [],
      []
    ];
    var tmpData = dataName;
    for (var i = 0, len = tmpData.length; i < len; i++) {
      category.push(tmpData[i][dateChar]);
      data[0].push(tmpData[i][dataLine]);
      data[1].push(tmpData[i][dataBar]);
    }
    // 指定图表的配置项和数据
    var option = {
      color: chartsColorArr,
      backgroundColor: '#fff',
      title: {
        text: titleName,
        textStyle: {
          fontSize: 16
        }
      },
      tooltip: {
        trigger:'axis',
        formatter: '{a}:<br />{b}点:&nbsp;{c}'
      },
      legend: {
        data: [lineName, barName]
      },
      xAxis: {
        data: category
      },
      yAxis: {},
      series: [{
        name: lineName,
        type: 'line',
        data: data[0],
        itemStyle: {
          normal: {
            label: {
              show: true
            }
          }
        }
      }, {
        name: barName,
        type: 'bar',
        barMaxWidth: 20,
        data: data[1],
        itemStyle: {
          normal: {
            label: {
              show: true
            }
          }
        }
      }]
    };
    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);
  }

  function initDoubleChart(dataName1, dataName2, compareName, dateChar, chartId, dataLine, dataBar, titleName, lineName, barName) {
    $("#comparePart").show();
    var myChart = echarts.init(document.getElementById(chartId));
    myChart.clear();
    var category = [];
    var data1 = [
      [],
      []
    ];
    var data2 = [
      [],
      []
    ];
    var tmpData1 = dataName1;
    var tmpData2 = dataName2;
    for (var i = 0, len = tmpData2.length; i < len; i++) {
      category.push(tmpData2[i][dateChar]);
      data2[0].push(tmpData2[i][dataLine]);
      data2[1].push(tmpData2[i][dataBar]);
    }
    for (var i = 0, len = tmpData1.length; i < len; i++) {
      data1[0].push(tmpData1[i][dataLine]);
      data1[1].push(tmpData1[i][dataBar]);
    }
    // 指定图表的配置项和数据
    var option = {
      color: chartsColorArr,
      backgroundColor: '#fff',
      title: {
        text: titleName,
        textStyle: {
          fontSize: 16
        }
      },
      tooltip: {
        trigger:'axis',
        formatter: '{a}:<br />{b}点:&nbsp;{c}'
      },
      legend: {
        data: [lineName, compareName + lineName, barName, compareName + barName]
      },
      xAxis: {
        data: category
      },
      yAxis: {},
      series: [{
        name: lineName,
        type: 'line',
        data: data1[0],
        itemStyle: {
          normal: {
            label: {
              show: true
            }
          }
        }
      }, {
        name: compareName + lineName,
        type: 'line',
        data: data2[0],
        itemStyle: {
          normal: {
            label: {
              show: true
            }
          }
        }
      }, {
        name: barName,
        type: 'bar',
        barMaxWidth: 20,
        data: data1[1],
        itemStyle: {
          normal: {
            label: {
              show: true
            }
          }
        }
      }, {
        name: compareName + barName,
        type: 'bar',
        barMaxWidth: 20,
        data: data2[1],
        itemStyle: {
          normal: {
            label: {
              show: true
            }
          }
        }
      }]
    };
    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);
  }
})