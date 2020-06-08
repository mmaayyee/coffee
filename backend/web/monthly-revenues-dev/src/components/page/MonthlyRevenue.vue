<template>
  <div class="content-body">
    <el-form :label-position="labelPosition" ref="form" :model="form" size="small" label-width="120px" v-if="showDateSearch!='0'">
      <el-row :gutter="10">
        <el-col :span="6" style="width:350px" v-if="showDateSearch!='1'">
          <span class="demonstration">选择检索月</span>
            <el-date-picker
              v-model="form.searchDateStart"
              type="month"
              placeholder="开始月" format="yyyy-MM" value-format="yyyy-MM" :picker-options="searchDatePickerOption">
            </el-date-picker> 至
        </el-col>
        <el-col :span="6" style="width:250px" v-if="showDateSearch!='1'">
            <el-date-picker
              v-model="form.searchDateEnd"
              type="month"
              placeholder="结束月" format="yyyy-MM" value-format="yyyy-MM" :picker-options="searchDatePickerOption">
            </el-date-picker>
        </el-col>
        <el-col :span="3" v-if="showDateSearch!='1'">
          <el-button type="primary" plain @click="searchDate">检索</el-button>
        </el-col>
        <el-col :span="5">
          <a :href="exportUrl+exportDate" v-if="showDateSearch!='2'"><el-button type="primary" plain>导出</el-button></a>
        </el-col>
      </el-row>
    </el-form>
    <el-table :data="monthlyRevenueData" border class="el-table-common">
      <el-table-column prop="year" label="年份">
      </el-table-column>
      <el-table-column prop="month" label="月份">
      </el-table-column>
      <el-table-column prop="equip_no_mini" label="设备台数(除mini)">
      </el-table-column>
      <el-table-column prop="equip_total" label="总设备台数">
      </el-table-column>
      <el-table-column prop="monthly_turnover" label="本期营业额">
      </el-table-column>
      <el-table-column label="营业额增长率">
        <template slot-scope="scope">
          <span v-if="trimPercent(scope.row.monthly_growth)<0"><i class="el-icon-download" style="color:green;"></i></span>
            <span v-else-if="trimPercent(scope.row.monthly_growth)>0"><i class="el-icon-upload2" style="color:red;"></i></span>{{scope.row.monthly_growth}}
        </template>
      </el-table-column>
      <el-table-column prop="average_daily_turnover" label="日均营业额">
      </el-table-column>
      <el-table-column prop="equip_operating_no_mini" label="运营设备台天数(除mini)">
      </el-table-column>
      <el-table-column prop="equip_operating_days" label="总设备台天数">
      </el-table-column>
      <el-table-column prop="monthly_cups" label="总杯数">
      </el-table-column>
      <el-table-column prop="monthly_pay_cups" label="付费杯数">
      </el-table-column>
      <el-table-column prop="equip_daily_average" label="总台日均(杯数)">
      </el-table-column>
      <el-table-column prop="monthly_cups_average" label="总杯均价">
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
      <el-table-column prop="monthly_free_cups" label="免费杯数">
      </el-table-column>
      <el-table-column prop="monthly_gross_profit" label="毛利润">
      </el-table-column>
    </el-table>
    <div>
      <!-- <div class="sub-title" style="margin-bottom:20px;">月报用户数据图表</div> -->
      <!-- 设备台数(除mini) equip_no_mini -->
      <div id="equipNoMini" class="charts"></div>
      <!-- 营业额 monthly_turnover -->
      <div id="monthlyTurnover" class="charts"></div>
      <!-- 免费杯数 monthly_free_cups 对比 新增注册用户 new_registered_user-->
      <div id="monthlyFreeCups" class="charts"></div>
      <!-- 总杯数 monthly_cups -->
      <div id="monthlyCups" class="charts"></div>
      <!-- 总台日均(杯数) equip_daily_average -->
      <div id="equipDailyAverage" class="charts"></div>
      <!-- 总杯均价 monthly_cups_average -->
      <div id="monthlyCupsAverage" class="charts"></div>
      <!-- 付费杯数 monthly_pay_cups -->
      <div id="monthlyPayCups" class="charts"></div>
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
      getMonthlyRevenueUrl: rootCoffeeStieUrl+'monthly-report-api/monthly-revenues-list.html',
      monthlyRevenueData: [],
      labelPosition: 'right',
      form: {
        searchDateStart: '',
        searchDateEnd: ''
      },
      searchDatePickerOption: {
        disabledDate(time){
          return time.getTime() > Date.now();
        }
      },
      exportDate:'',
      exportUrl:'/monthly-revenue/export?',
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
      this.getMonthlyRevenue();
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
      let start = new Date(this.form.searchDateStart);
      let end = new Date(this.form.searchDateEnd);
      if(end>=start) {
        this.getMonthlyRevenue([this.form.searchDateStart,this.form.searchDateEnd]);
      } else {
        this.alertMsg("结束月必须大于等于开始月，请重新选择。")
      }
    },
    exportExcel() {
      //
    },
    goBack() {
      // window.location.href = this.listUrl;
    },
    clearCharts(){
      this.monthlyRevenueData = [];
      this.initCharts();
    },
    getMonthlyRevenue(date) {
      const url = date?this.getMonthlyRevenueUrl+"?start="+date[0]+"&end="+date[1]:this.getMonthlyRevenueUrl;
      this.exportDate = date?"start="+date[0]+"&end="+date[1]:"";
      // console.log("url.....",url);
      axios.get(url)
      .then((response)=> {
        // console.log(response.data)
        if(response.data && response.data.code=="200"){
          if(response.data.data.length==0){
            this.alertMsg("没有数据，请选择其他日期");
          }
          this.monthlyRevenueData = [];
          window.setTimeout(()=>{
            this.monthlyRevenueData = response.data.data;
            this.initCharts();
          },300);
        } else {
          this.clearCharts();
          this.alertMsg('获取月报营收数据错误');
        }
      })
      .catch((error)=> {
        this.clearCharts();
        this.alertMsg('月报营收接口错误');
      });
    },
    initCharts() {
      this.equipNoMiniChart();
      this.monthlyTurnoverChart();
      this.monthlyFreeCupsChart();
      this.monthlyCupsChart();
      this.equipDailyAverageChart();
      this.monthlyCupsAverageChart();
      this.monthlyPayCupsChart();
      this.payEquipDailyAverageChart();
      this.payCupsAverageChart();
    },

    // 设备台数(除mini)
    equipNoMiniChart() {
      this.initSingleChart('monthlyRevenueData','month','equipNoMini','equip_no_mini','设备台数(除mini)');
    },
    // 营业额
    monthlyTurnoverChart() {
      this.initSingleChart('monthlyRevenueData','month','monthlyTurnover','monthly_turnover','营业额');
    },
    // 免费杯数 对比 新增注册用户
    monthlyFreeCupsChart() {
      this.initDoubleChart('monthlyRevenueData','month','monthlyFreeCups','monthly_free_cups','new_registered_user','免费杯数 对比 新增注册用户','免费杯数','新增注册用户');
    },
    // 总杯数
    monthlyCupsChart() {
      this.initSingleChart('monthlyRevenueData','month','monthlyCups','monthly_cups','总杯数');
    },
    // 总台日均(杯数)
    equipDailyAverageChart() {
      this.initSingleLineChart('monthlyRevenueData','month','equipDailyAverage','equip_daily_average','总台日均(杯数)');
    },
    // 总杯均价
    monthlyCupsAverageChart() {
      this.initSingleLineChart('monthlyRevenueData','month','monthlyCupsAverage','monthly_cups_average','总杯均价');
    },
    // 付费杯数
    monthlyPayCupsChart() {
      this.initSingleChart('monthlyRevenueData','month','monthlyPayCups','monthly_pay_cups','付费杯数');
    },
    // 付费台日均(杯数)
    payEquipDailyAverageChart() {
      this.initSingleLineChart('monthlyRevenueData','month','payEquipDailyAverage','pay_equip_daily_average','付费台日均(杯数)');
    },
    // 付费杯均价
    payCupsAverageChart() {
      this.initSingleLineChart('monthlyRevenueData','month','payCupsAverage','pay_cups_average','付费杯均价');
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
<style scoped>
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
