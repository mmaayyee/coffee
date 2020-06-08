<template>
  <div class="content-body">
    <el-form :label-position="labelPosition" ref="form" :model="form" size="small" label-width="120px" v-if="showDateSearch!='0'">
      <el-row :gutter="10">
        <el-col :span="12" style="width:500px" v-if="showDateSearch!='1'">
          <el-form-item label="选择检索日期" prop="sendTime">
              <el-date-picker type="daterange" range-separator="至" start-placeholder="开始日期"  end-placeholder="结束日期" format="yyyy-MM-dd" value-format="yyyy-MM-dd" v-model="form.searchDate" :picker-options="searchDatePickerOption">
              </el-date-picker>
            </el-form-item>
        </el-col>
        <el-col :span="5" v-if="showDateSearch!='1'">
          <el-button type="primary" plain @click="searchDate">检索</el-button>
        </el-col>
        <el-col :span="5">
          <a :href="exportUrl+exportDate" v-if="showDateSearch!='2'"><el-button type="primary" plain>导出</el-button></a>
        </el-col>
      </el-row>
    </el-form>
    <el-table :data="weeklyRevenueData" border class="el-table-common">
      <el-table-column prop="year" label="年份">
      </el-table-column>
      <el-table-column prop="cycle_str" label="日期">
      </el-table-column>
      <el-table-column prop="weekly_number" label="周次">
      </el-table-column>
      <el-table-column prop="equip_no_mini" label="设备台数(除mini)">
      </el-table-column>
      <el-table-column prop="equip_total" label="总设备台数">
      </el-table-column>
      <el-table-column prop="weekly_turnover" label="本期营业额">
      </el-table-column>
      <el-table-column label="营业额增长率">
        <template slot-scope="scope">
          <span v-if="trimPercent(scope.row.weekly_growth)<0"><i class="el-icon-download" style="color:green;"></i></span>
            <span v-else-if="trimPercent(scope.row.weekly_growth)>0"><i class="el-icon-upload2" style="color:red;"></i></span>{{scope.row.weekly_growth}}
        </template>
      </el-table-column>
      <el-table-column prop="average_daily_turnover" label="日均营业额">
      </el-table-column>
      <el-table-column prop="equip_operating_no_mini" label="运营设备台天数(除mini)">
      </el-table-column>
      <el-table-column prop="equip_operating_days" label="总设备台天数">
      </el-table-column>
      <el-table-column prop="weekly_cups" label="总杯数">
      </el-table-column>
      <el-table-column prop="weekly_pay_cups" label="付费杯数">
      </el-table-column>
      <el-table-column prop="equip_daily_average" label="总台日均(杯数)">
      </el-table-column>
      <el-table-column prop="weekly_cups_average" label="总杯均价">
      </el-table-column>
      <el-table-column prop="pay_equip_daily_average" label="付费台日均(杯数)">
      </el-table-column>
      <el-table-column label="环比值">
        <template slot-scope="scope">
          <span v-if="trimPercent(scope.row.pay_equip_average_ratio)<0"><i class="el-icon-download" style="color:green;"></i></span>
            <span v-else-if="trimPercent(scope.row.pay_equip_average_ratio)>0"><i class="el-icon-upload2" style="color:red;"></i></span>{{scope.row.pay_equip_average_ratio}}
        </template>
      </el-table-column>
      <el-table-column prop="pay_cups_average" label="付费杯均价">
      </el-table-column>
      <el-table-column label="环比值">
        <template slot-scope="scope">
          <span v-if="trimPercent(scope.row.pay_cups_ratio)<0"><i class="el-icon-download" style="color:green;"></i></span>
            <span v-else-if="trimPercent(scope.row.pay_cups_ratio)>0"><i class="el-icon-upload2" style="color:red;"></i></span>{{scope.row.pay_cups_ratio}}
        </template>
      </el-table-column>
      <el-table-column prop="weekly_free_cups" label="免费杯数">
      </el-table-column>
      <el-table-column prop="weekly_gross_profit" label="毛利润">
      </el-table-column>
    </el-table>
    <div>
      <!-- <div class="sub-title" style="margin-bottom:20px;">周报用户数据图表</div> -->
      <!-- 设备台数(除mini) equip_no_mini -->
      <div id="equipNoMini" class="charts"></div>
      <!-- 营业额 weekly_turnover -->
      <div id="weeklyTurnover" class="charts"></div>
      <!-- 免费杯数 weekly_free_cups 对比 新增注册用户 new_registered_user-->
      <div id="weeklyFreeCups" class="charts"></div>
      <!-- 总杯数 weekly_cups -->
      <div id="weeklyCups" class="charts"></div>
      <!-- 总台日均(杯数) equip_daily_average -->
      <div id="equipDailyAverage" class="charts"></div>
      <!-- 总杯均价 weekly_cups_average -->
      <div id="weeklyCupsAverage" class="charts"></div>
      <!-- 付费杯数 weekly_pay_cups -->
      <div id="weeklyPayCups" class="charts"></div>
      <!-- 付费台日均(杯数) pay_equip_daily_average -->
      <div id="payEquipDailyAverage" class="charts"></div>
      <!-- 付费杯均价 pay_cups_average -->
      <div id="payCupsAverage" class="charts"></div>
    </div>
  </div>
</template>
<script>
/* eslint-disable */
import axios  from  'axios'
const chartsColorArr = ['#328e55', '#d66e49', '#be384b','#a7aa9d', '#354b5e'];
const chartsColorArr2 = ['#54c7f1','#3ab2de', '#289eca','#198db8','#0a7da7','#016b93'];
export default {
  data() {
    return {
      listUrl:'',
      getWeeklyRevenueUrl: rootCoffeeStieUrl+'weekly-report-api/weekly-revenues-list.html',
      weeklyRevenueData: [],
      labelPosition: 'right',
      form: {
        searchDate: ''
      },
      searchDatePickerOption: {
        disabledDate(time){
          return time.getTime() > Date.now();
        }
      },
      exportDate:'',
      exportUrl:'/weekly-revenue/export?',
      showDateSearch: ''
    }
  },
  filters: {
    formatDate: function(val) {
      return String(val).substr(0,10);
    }
  },
  computed: {
  },
  mounted() {
    this.init();
  },
  methods: {
    init() {
      this.showDateSearch = String(rootLogin);
      window.parent.onscroll = (e)=>{
        this.scrollMsg();
      }
      this.getWeeklyRevenue();
    },
    trimPercent(val) {
      // console.log(Number(String(val).substr(0,String(val).length-1)));
      let lastChar = String(val).charAt(String(val).length-1);
      if(lastChar=="%") {
        return Number(String(val).substr(0,String(val).length-1));
      } else {
        return Number(val);
      }
    },
    searchDate() {
      this.getWeeklyRevenue(this.form.searchDate);
    },
    exportExcel() {
      //
    },
    goBack() {
      // window.location.href = this.listUrl;
    },
    clearCharts(){
      this.weeklyRevenueData = [];
      this.initCharts();
    },
    getWeeklyRevenue(date) {
      const url = date?this.getWeeklyRevenueUrl+"?start="+date[0]+"&end="+date[1]:this.getWeeklyRevenueUrl;
      this.exportDate = date?"start="+date[0]+"&end="+date[1]:"";
      // console.log("url.....",url);
      axios.get(url)
      .then((response)=> {
        // console.log(response.data)
        if(response.data && response.data.code=="200"){
          if(response.data.data.length==0){
            this.alertMsg("没有数据，请选择其他日期");
          }
          // this.exportDate = date?"start="+date[0]+"&end="+date[1]:"";
          this.weeklyRevenueData = [];
          window.setTimeout(()=>{
            this.weeklyRevenueData = response.data.data;
            this.initCharts();
          },300);
        } else {
          this.clearCharts();
          this.alertMsg('获取周报营收数据错误');
        }
      })
      .catch((error)=> {
        this.clearCharts();
        this.alertMsg('周报营收接口错误');
      });
    },
    initCharts() {
      this.equipNoMiniChart();
      this.weeklyTurnoverChart();
      this.weeklyFreeCupsChart();
      this.weeklyCupsChart();
      this.equipDailyAverageChart();
      this.weeklyCupsAverageChart();
      this.weeklyPayCupsChart();
      this.payEquipDailyAverageChart();
      this.payCupsAverageChart();
    },

    // 设备台数(除mini)
    equipNoMiniChart() {
      this.initSingleChart('weeklyRevenueData','weekly_number','equipNoMini','equip_no_mini','设备台数(除mini)');
    },
    // 营业额
    weeklyTurnoverChart() {
      this.initSingleChart('weeklyRevenueData','weekly_number','weeklyTurnover','weekly_turnover','营业额');
    },
    // 免费杯数 对比 新增注册用户
    weeklyFreeCupsChart() {
      this.initDoubleChart('weeklyRevenueData','weekly_number','weeklyFreeCups','weekly_free_cups','new_registered_user','免费杯数 对比 新增注册用户','免费杯数','新增注册用户');
    },
    // 总杯数
    weeklyCupsChart() {
      this.initSingleChart('weeklyRevenueData','weekly_number','weeklyCups','weekly_cups','总杯数');
    },
    // 总台日均(杯数)
    equipDailyAverageChart() {
      this.initSingleLineChart('weeklyRevenueData','weekly_number','equipDailyAverage','equip_daily_average','总台日均(杯数)');
    },
    // 总杯均价
    weeklyCupsAverageChart() {
      this.initSingleLineChart('weeklyRevenueData','weekly_number','weeklyCupsAverage','weekly_cups_average','总杯均价');
    },
    // 付费杯数
    weeklyPayCupsChart() {
      this.initSingleChart('weeklyRevenueData','weekly_number','weeklyPayCups','weekly_pay_cups','付费杯数');
    },
    // 付费台日均(杯数)
    payEquipDailyAverageChart() {
      this.initSingleLineChart('weeklyRevenueData','weekly_number','payEquipDailyAverage','pay_equip_daily_average','付费台日均(杯数)');
    },
    // 付费杯均价
    payCupsAverageChart() {
      this.initSingleLineChart('weeklyRevenueData','weekly_number','payCupsAverage','pay_cups_average','付费杯均价');
    },
    initSingleLineChart(dataName,dateChar,chartId,dataVal,titleName){
      const myChart = echarts.init(document.getElementById(chartId));
      myChart.clear();
      let category=[];
      let data=[];
      const tmpData = this[dataName];
      for(let i=0,len=tmpData.length;i<len;i++){
        category.push(tmpData[i][dateChar]);
        data.push(Number(tmpData[i][dataVal]));
      }
      // let max = data.length>0?data.reduce((a,b)=>a>b?a:b):0;
      // console.log(max)
      // 指定图表的配置项和数据
      const option = {
          color: chartsColorArr,
          backgroundColor: '#fff',
          title: {
              text: titleName,
              textStyle: {fontSize:16}
          },
          tooltip: {},
          legend: {
              data:[titleName]
          },
          xAxis: {
              data: category
          },
          yAxis: {},
          series: [{
              name: titleName,
              type: 'line',
              data: data,
              itemStyle: {
                normal: {
                  label : {show: true}
                }
              }
          }]
      };
      // 使用刚指定的配置项和数据显示图表。
      myChart.setOption(option);
    },
    initSingleChart(dataName,dateChar,chartId,dataVal,titleName){
      const myChart = echarts.init(document.getElementById(chartId));
      myChart.clear();
      let category=[];
      let data=[];
      const tmpData = this[dataName];
      for(let i=0,len=tmpData.length;i<len;i++){
        category.push(tmpData[i][dateChar]);
        data.push(Number(tmpData[i][dataVal]));
      }
      let max = data.length>0?data.reduce((a,b)=>a>b?a:b):0;
      // console.log(max)
      // 指定图表的配置项和数据
      const option = {
          color: chartsColorArr2,
          backgroundColor: '#fff',
          title: {
              text: titleName,
              textStyle: {fontSize:16}
          },
          tooltip: {},
          legend: {
              data:[titleName]
          },
          xAxis: {
              data: category
          },
          yAxis: {},
          series: [{
              name: titleName,
              type: 'bar',
              barMaxWidth:30,
              data: data,
              itemStyle: {
                normal: {
                  label : {show: true},
                  color:function(params) {
                    let newColor = "";
                    let value = Number(params.value);
                    value = Math.max(0,value);
                    let index = max>0?Math.floor(value*5/max):0;
                    newColor = chartsColorArr2[index];
                    return newColor;
                  }
                }
              }
          }]
      };
      // 使用刚指定的配置项和数据显示图表。
      myChart.setOption(option);
    },
    initDoubleChart(dataName,dateChar,chartId,dataVal1,dataVal2,titleName,legendName1,legendName2){
      const myChart = echarts.init(document.getElementById(chartId));
      myChart.clear();
      var category=[];
      var data=[[],[]];
      const tmpData = this[dataName];
      for(let i=0,len=tmpData.length;i<len;i++){
        category.push(tmpData[i][dateChar]);
        data[0].push(tmpData[i][dataVal1]);
        data[1].push(tmpData[i][dataVal2]);
      }
      // 指定图表的配置项和数据
      const option = {
          color: chartsColorArr,
          backgroundColor: '#fff',
          title: {
              text: titleName,
              textStyle: {fontSize:16}
          },
          tooltip: {},
          legend: {
              data:[legendName1,legendName2]
          },
          xAxis: {
              data: category
          },
          yAxis: {},
          series: [{
              name: legendName1,
              type: 'bar',
              barMaxWidth:20,
              data: data[0],
              itemStyle: {
                normal: {
                  label : {show: true}
                }
              }
          },
          {
              name: legendName2,
              type: 'bar',
              barMaxWidth:20,
              data: data[1],
              itemStyle: {
                normal: {
                  label : {show: true}
                }
              }
          }]
      };
      // 使用刚指定的配置项和数据显示图表。
      myChart.setOption(option);
    },
    alertMsg(msg,type)
    {
      // alert(msg);
      this.scrollMsg();
      let msgType = type?type:"error";
      this.$message({
        message: msg,
        duration:3600,
        type: msgType
      });
    },
    scrollMsg()
    {
      if(self!=top){
        let scrollTop = window.parent.document.documentElement.scrollTop||window.parent.document.body.scrollTop-50;
        let mycss=document.getElementsByClassName("el-message")[0];
        if(mycss){
          mycss.style.cssText="top: "+scrollTop+"px;z-index:1000;";
        }
      }
    },
  },
  components: {}
}

</script>
<style>
.el-table .cell,th,td {
  text-align: center;
}
.newregister {
    background-color: #c8fbca;
}
.week {
  min-width: 60px;
}
.day {
  min-width: 95px;
}
.charts {
  width: 1060px;
  height: 500px;
}
</style>
