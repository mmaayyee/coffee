// console.log($("#consumeChart").width)
$(function() {
  var chartsColorArr = ['#32c040', '#146479', '#e560cd', '#faae4e', '#354b5e', '#c7e1f0'];
  // var chartsColorArr2 = ['#54c7f1','#3ab2de', '#289eca','#198db8','#0a7da7','#016b93'];
  var currentChartData = [];
  var compareChartData = [];
  var pieChartData = [];
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
      url: rootCoffeeStieUrl + "user-consumes-api/consume-report.html",
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
              obj.consume = Number(apiData.reportList[key].consume_amount);
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
    initSingleChart(currentChartData, "date", "consumeChart", "consume", "hour", "消费金额", "消费总额", "每小时增加额");
  }
  getPieChart();

  function getPieChart(pieDate) {
    var defaultUrl = rootCoffeeStieUrl + "user-consumes-api/consume-product.html";
    var _url = pieDate ? defaultUrl + "?date=" + pieDate : defaultUrl;
    console.log("_url..", _url)
    pieChartData = [];
    $.ajax({
      type: "GET",
      url: _url,
      dataType: "json",
      success: function(data) {
        console.log("pie data..", data);
        var apiData = data.data;
        if (data.code == 200) {
          if (apiData != [] && apiData.productList) {
            for (var key in apiData.productList) {
              var obj = {};
              obj.coffee = key;
              obj.amount = apiData.productList[key];
              pieChartData.push(obj);
            }
            // console.log(pieChartData)
          }
          if (apiData.productTotal!=undefined) {
            $("#pieTotal").html(apiData.productTotal);
          }
          if (apiData.time!=undefined) {
            $("#pieTime").html(apiData.time);
          }
          if (apiData.date!=undefined) {
            $("#datepicker").val(apiData.date);
          }
          initPChart();
        } else {
          alert(data.message);
        }
      },
      error: function(XMLHttpRequest, textStatus) {
        console.log("fail:", textStatus)
        initPChart();
      }
    })
  }

  function initPChart() {
    initPieChart(pieChartData, "pieChart", "coffee", "amount", "销量饼图");
  }

  function initPieChart(chartData, chartId, dataName, dataValue, titleName) {
    var myChart = echarts.init(document.getElementById(chartId));
    myChart.clear();
    var tmpData = chartData;
    for (var i = 0, len = tmpData.length; i < len; i++) {
      var obj = {};
      obj.value = Number(tmpData[i][dataValue]);
      obj.name = tmpData[i][dataName];
      chartData.push(obj);
    }
    // console.log(tmpData)
    // 指定图表的配置项和数据
    var option = {
      title: {
        text: titleName,
        subtext: '',
        x: 'center'
      },
      calculable: true,
      series: [{
        name: '饮品名称',
        type: 'pie',
        radius: '70%',
        center: ['50%', '53%'],
        data: chartData,
        itemStyle: {
          normal: {
            label: {
              show: true,
              position: 'outside',
              formatter: function(params) {
                return params.name + '\n' + (params.value - 0).toFixed(2) + '\n' + (params.percent - 0).toFixed(2) + '%'
              }
            }
          }
        }
      }]
    };
    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);
  }

  function initCompareChart(dateId) {
    compareChartData = [];
    $.ajax({
      type: "GET",
      url: rootCoffeeStieUrl + "user-consumes-api/consume-report-compare.html?date=" + dateId,
      dataType: "json",
      success: function(data) {
        console.log("data compare..", data);
        var apiData = data.data;
        if (data.code == 200) {
          if (apiData == [] || !apiData.reportList) {
            initSChart();
          } else {
            for (var key in apiData.reportList) {
              var obj = {};
              obj.date = key;
              obj.hour = Number(apiData.reportList[key].hour_amount);
              obj.consume = Number(apiData.reportList[key].consume_amount);
              compareChartData.push(obj);
            }
            // console.log(chartData)
            compareChartData.sort(function(a, b) {
              // console.log(a.date.split("-")[0],"..",b.date.split("-")[0]);
              return Number(a.date.split("-")[0]) - Number(b.date.split("-")[0]);
            })
            if (apiData.dayData) {
              $("#compareCupAverage").html(apiData.dayData.cups_average);
              // $("#compareEquipAverage").html(apiData.dayData.equipments_average);
            }
            $("#compareName").html(compareNameArr[Number(dateId)]);
            // console.log(compareChartData)
            initDoubleChart(currentChartData, compareChartData, compareNameArr[Number(dateId)], "date", "consumeChart", "consume", "hour", "消费金额", "消费总额", "每小时增加额");
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
  $("#datepicker").datepicker({
    /* 区域化周名为中文 */
    dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"],
    /* 每周从周一开始 */
    firstDay: 1,
    /* 区域化月名为中文习惯 */
    monthNames: ["01", "02", "03", "04", "05", "06",
      "07", "08", "09", "10", "11", "12"
    ],
    /* 月份显示在年后面 */
    showMonthAfterYear: true,
    /* 年份后缀字符 */
    yearSuffix: "年",
    /* 格式化中文日期
    （因为月份中已经包含“月”字，所以这里省略） */
    dateFormat: "yy-MM-dd",
    maxDate: "+0D"
  });
  $("#datepicker").on("change", function() {
    // console.log("change")
    getPieChart($("#datepicker").val())
  })
})