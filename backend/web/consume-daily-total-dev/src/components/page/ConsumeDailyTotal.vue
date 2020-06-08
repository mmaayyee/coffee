<template>
  <div class="content-body">
    <el-form :label-position="labelPosition" ref="form" :model="form" size="small" label-width="120px" v-if="showDateSearch!='0'">
      <el-row :gutter="10">
        <el-col style="width:350px" v-if="showDateSearch!='1'">
          <el-form-item label="选择检索日期" prop="sendTime">
            <el-date-picker v-model="form.searchDate" format="yyyy-MM-dd" value-format="yyyy-MM-dd" type="date" placeholder="选择日期" :picker-options="searchDatePickerOption"></el-date-picker>
          </el-form-item>
        </el-col>
        <el-col :span="10" v-if="showDateSearch!='1'">
          <el-button type="primary" plain @click="searchDate">检索</el-button>
        </el-col>
        <el-col :span="4">
          <a :href="exportUrl+exportDate" v-if="showDateSearch!='2'"><el-button type="primary" plain>导出</el-button></a>
        </el-col>
      </el-row>
    </el-form>
    <div class="sub-title" style="margin-top:0">零售数据--表单</div>
    <div>
      <table class="gridtable">
        <tr>
          <th width="60">星期</th>
          <th width="95">日期</th>
          <th width="50">设备台数（除mini）</th>
          <th width="50">设备总台数</th>
          <th width="60">总销售额</th>
          <th width="60">总杯数</th>
          <th>付费杯数</th>
          <th>总台日均（杯数）</th>
          <th width="80">周同比</th>
          <th>上周同期</th>
          <th>付费台日均</th>
          <th width="80">周同比</th>
          <th>上周同期</th>
          <th>总杯均价</th>
          <th>付费杯均价</th>
          <th>免费杯数</th>
        </tr>
        <tr v-for="(item,index) in consumeDailyTotalData.list" :key="index">
          <td>{{item.week}}</td>
          <td>{{item.date|formatDate}}</td>
          <td>{{item.equipments_number}}</td>
          <td>{{item.equipments_number_count}}</td>
          <td>{{item.consume_total_amount}}</td>
          <td>{{item.consume_total_cups}}</td>
          <td>{{item.consume_pay_cups}}</td>
          <td>{{item.equipments_daily_average}}</td>
          <td><span v-if="Number(item.week_compare_daily_average)<0"><i class="el-icon-download" style="color:green;"></i></span>
            <span v-else-if="Number(item.week_compare_daily_average)>0"><i class="el-icon-upload2" style="color:red;"></i></span>{{item.week_compare_daily_average}}</td>
          <td>{{item.last_week_daily_average}}</td>
          <td>{{item.pay_daily_average}}</td>
          <td><span v-if="Number(item.week_compare_pay)<0"><i class="el-icon-download" style="color:green;"></i></span>
            <span v-else-if="Number(item.week_compare_pay)>0"><i class="el-icon-upload2" style="color:red;"></i></span>{{item.week_compare_pay}}</td>
          <td>{{item.week_pay_daily_average}}</td>
          <td>{{item.cups_daily_average}}</td>
          <td>{{item.pay_cups_daily_average}}</td>
          <td>{{item.free_cups_daily_average}}</td>
        </tr>
        <tr>
          <td colspan="2" style="text-align: center;">MTD</td>
          <td>{{consumeDailyTotalData.MTD.equipments_number}}</td>
          <td>{{consumeDailyTotalData.MTD.equipments_number_count}}</td>
          <td>{{consumeDailyTotalData.MTD.consume_total_amount}}</td>
          <td>{{consumeDailyTotalData.MTD.consume_total_cups}}</td>
          <td>{{consumeDailyTotalData.MTD.consume_pay_cups}}</td>
          <td>{{consumeDailyTotalData.MTD.equipments_daily_average}}</td>
          <td>{{consumeDailyTotalData.MTD.week_compare_daily_average}}</td>
          <td>{{consumeDailyTotalData.MTD.last_week_daily_average}}</td>
          <td>{{consumeDailyTotalData.MTD.pay_daily_average}}</td>
          <td>{{consumeDailyTotalData.MTD.week_compare_pay}}</td>
          <td>{{consumeDailyTotalData.MTD.week_pay_daily_average}}</td>
          <td>{{consumeDailyTotalData.MTD.cups_daily_average}}</td>
          <td>{{consumeDailyTotalData.MTD.pay_cups_daily_average}}</td>
          <td>{{consumeDailyTotalData.MTD.free_cups_daily_average}}</td>
        </tr>
      </table>
      <div class="sub-title" style="margin-bottom:20px;">零售数据--图表</div>
      <!-- 总销售额 -->
      <div id="totalAmount" class="charts"></div>
      <!-- 总杯数 -->
      <div id="totalCups" class="charts"></div>
      <!-- 饮品杯数 -->
      <div id="drinkCups" class="charts"></div>
      <!-- 总台日均 -->
      <div id="equipmentsDailyAverage" class="charts"></div>
      <!-- 付费台日均杯数 -->
      <div id="payDailyAverage" class="charts"></div>
      <!-- 总杯均价 -->
      <div id="cupsDailyAverage" class="charts"></div>
      <!-- 付费杯均价 -->
      <div id="payCupsDailyAverage" class="charts"></div>
    </div>
    <div class="sub-title" style="margin-top:0">用户增长及活跃--表单</div>
    <div>
      <table class="gridtable">
        <tr>
          <th width="60">星期</th>
          <th width="95">日期</th>
          <th>总用户数</th>
          <th>新增用户数</th>
          <th>用户增长率</th>
          <th>活跃用户数</th>
          <th>上周同期对比</th>
          <th>付费活跃用户数</th>
          <th>上周同期对比</th>
          <th>免费活跃用户</th>
          <th>人均付费购买频次</th>
        </tr>
        <tr v-for="(item,index) in userGrowthAndActivityData.list" :key="index">
          <td>{{item.create_week_day}}</td>
          <td>{{item.created_at|formatDate}}</td>
          <td>{{item.users_total_number}}</td>
          <td>{{item.new_total_number}}</td>
          <td>{{item.users_total_up}}</td>
          <td>{{item.user_active_total}}</td>
          <td>
            <span v-if="Number(item.week_active_total)<0"><i class="el-icon-download" style="color:green;"></i></span>
            <span v-else-if="Number(item.week_active_total)>0"><i class="el-icon-upload2" style="color:red;"></i></span>{{item.week_active_total}}
          </td>
          <td>{{item.user_pay_active}}</td>
          <td>
            <span v-if="Number(item.week_pay_active)<0"><i class="el-icon-download" style="color:green;"></i></span>
            <span v-else-if="Number(item.week_pay_active)>0"><i class="el-icon-upload2" style="color:red;"></i></span>{{item.week_pay_active}}
          </td>
          <td>{{item.user_free_active}}</td>
          <td>{{item.per_capita_pay}}</td>
        </tr>
        <tr>
          <td colspan="2" style="text-align: center;">MTD</td>
          <td>{{userGrowthAndActivityData.MTD.users_total_number}}</td>
          <td>{{userGrowthAndActivityData.MTD.new_total_number}}</td>
          <td>{{userGrowthAndActivityData.MTD.users_total_up}}</td>
          <td>{{userGrowthAndActivityData.MTD.user_active_total}}</td>
          <td>{{userGrowthAndActivityData.MTD.week_active_total}}</td>
          <td>{{userGrowthAndActivityData.MTD.user_pay_active}}</td>
          <td>{{userGrowthAndActivityData.MTD.week_pay_active}}</td>
          <td>{{userGrowthAndActivityData.MTD.user_free_active}}</td>
          <td>{{userGrowthAndActivityData.MTD.per_capita_pay}}</td>
        </tr>
      </table>
      <div class="sub-title" style="margin-bottom:20px;">用户增长及活跃--图表</div>
      <!-- 总用户数 -->
      <div id="usersTotalNumber" class="charts"></div>
      <!-- (日)新增用户数 -->
      <div id="newTotalNumber" class="charts"></div>
      <!-- (日)活跃用户数-->
      <div id="userActiveTotal" class="charts"></div>
      <!-- (日)付费活跃用户数 -->
      <div id="userPayActive" class="charts"></div>
      <!-- 人均付费购买频次 -->
      <div id="perCapitaPay" class="charts"></div>
    </div>
    <!-- 沉睡用户 -->
    <div class="sub-title">沉睡用户</div>
    <el-table :data="getUserSleepListData" border class="el-table-common">
      <el-table-column prop="create_week_day" label="星期">
      </el-table-column>
      <el-table-column label="日期">
        <template slot-scope="scope">
          {{scope.row.created_at | formatDate}}
        </template>
      </el-table-column>
      <el-table-column prop="user_sleep_total" label="总沉睡用户">
      </el-table-column>
      <el-table-column prop="sleep_two_weeks" label="沉睡2周-1个月">
      </el-table-column>
      <el-table-column prop="sleep_one_month" label="沉睡1个月-2个月">
      </el-table-column>
      <el-table-column prop="sleep_two_month" label="沉睡2个月-3个月">
      </el-table-column>
      <el-table-column prop="sleep_three_month" label="沉睡3个月-4个月">
      </el-table-column>
      <el-table-column prop="sleep_four_month" label="沉睡4个月-5个月">
      </el-table-column>
      <el-table-column prop="sleep_five_month" label="沉睡5个月-6个月">
      </el-table-column>
      <el-table-column prop="sleep_six_month" label="沉睡6个月以上">
      </el-table-column>
    </el-table>
    <!-- 召回用户 -->
    <div class="sub-title">召回用户</div>
    <el-table :data="getUserRecallListData" border class="el-table-common">
      <el-table-column prop="create_week_day" label="星期">
      </el-table-column>
      <el-table-column label="日期">
        <template slot-scope="scope">
          {{scope.row.created_at | formatDate}}
        </template>
      </el-table-column>
      <el-table-column prop="user_recall_total" label="总召回用户">
      </el-table-column>
      <el-table-column prop="recall_two_weeks" label="召回2周-1个月">
      </el-table-column>
      <el-table-column prop="recall_one_month" label="召回1个月-2个月">
      </el-table-column>
      <el-table-column prop="recall_two_month" label="召回2个月-3个月">
      </el-table-column>
      <el-table-column prop="recall_three_month" label="召回3个月-4个月">
      </el-table-column>
      <el-table-column prop="recall_four_month" label="召回4个月-5个月">
      </el-table-column>
      <el-table-column prop="recall_five_month" label="召回5个月-6个月">
      </el-table-column>
      <el-table-column prop="recall_six_month" label="召回6个月以上">
      </el-table-column>
    </el-table>
    <div class="sub-title">留存用户</div>
    <table class="gridtable">
      <tr>
        <th v-for="(item,index) in getUserRetainListData.head" :key="index" :class="{'week':index==0,'retain-day':index==1}">{{item}}</th>
      </tr>
      <tr v-for="(item,index) in getUserRetainListData.list" :key="index">
        <td v-for="(item2,index2) in getUserRetainListData.list[index]" :key="index2" :class="{'newregister':item2.new}">{{item2.content}}</td>
      </tr>
    </table>
    <div class="div-center">
        <!-- <el-button type="primary" plain @click="goBack">返回</el-button> -->
        <a href="#"><el-button type="primary" plain>返回顶部</el-button></a>
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
      // listUrl:'/consume-channel-daily/index',
      listUrl:'',
      consumeDailyTotalData: {"list":[],"MTD":{}},
      consumeDailyTotalUrl: rootCoffeeStieUrl+'consume-daily-total-api/get-consume-daily-total-list.html',
      userGrowthAndActivityData: {"list":[],"MTD":{}},
      userGrowthAndActivityUrl: rootCoffeeStieUrl+'consume-daily-total-api/get-user-growth-and-activity.html',
      labelPosition: 'right',
      form: {
        searchDate: ''
      },
      searchDatePickerOption: {
        disabledDate(time){
          return time.getTime() > Date.now();
        }
      },
      getUserSleepListData: [],
      getUserSleepListUrl: rootCoffeeStieUrl+'consume-daily-total-api/get-user-sleep-list.html',
      getUserRecallListData: [],
      getUserRecallListUrl: rootCoffeeStieUrl+'consume-daily-total-api/get-user-recall-list.html',
      getUserRetainListData: {'head':[],'head0':[],'list':[]},
      getUserRetainListUrl: rootCoffeeStieUrl+'consume-daily-total-api/get-user-retain-list.html',
      exportDate:'',
      exportUrl:'/consume-daily-total/export?date=',
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
      this.consumeDailyTotal();
    },
    searchDate() {
      this.consumeDailyTotal(this.form.searchDate);
    },
    exportExcel() {
      //
    },
    goBack() {
      // window.location.href = this.listUrl;
    },
    clearCharts(date){
      this.consumeDailyTotalData = {"list":[],"MTD":{}};
      this.initUserConsumeDailyTotalCharts();
      this.userGrowthAndActivity(date);
      this.getUserSleepList(date);
    },
    // 日报总表-零售表单
    consumeDailyTotal(date) {
      const url = date?this.consumeDailyTotalUrl+"?date="+date:this.consumeDailyTotalUrl;
      this.exportDate = date?date:"";
      // console.log("url.....",url);
      axios.get(url)
      .then((response)=> {
        console.log(response.data)
        if(response.data && response.data.code=="200"){
          if(response.data.data.length==[]){
            this.alertMsg("零售数据没有数据,请选择其他日期");
          }
          this.consumeDailyTotalData = {"list":[],"MTD":{}};
          window.setTimeout(()=>{
            if(response.data.data.length!=[]){
              this.consumeDailyTotalData = response.data.data;
            }
            this.initUserConsumeDailyTotalCharts();
            this.userGrowthAndActivity(date);
            this.getUserSleepList(date);
          },300)
        } else {
          this.alertMsg('获取零售数据错误');
          this.clearCharts(date);
        }
      })
      .catch((error)=> {
        this.alertMsg('零售数据接口错误');
        this.clearCharts(date);
      });
    },
    initUserConsumeDailyTotalCharts() {
      this.chartConsumeTotalAmount();
      this.chartConsumeTotalCups();
      this.chartDrinkCups();
      this.chartEquipmentsDailyAverage();
      this.chartPayDailyAverage();
      this.chartCupsDailyAverage();
      this.chartPayCupsDailyAverage();
    },
    initSingleChart(dataName,dateChar,chartId,dataVal,titleName){
      const myChart = echarts.init(document.getElementById(chartId));
      myChart.clear();
      let category=[];
      let data=[];
      const tmpData = this[dataName].list;
      for(let i=0,len=tmpData.length;i<len;i++){
        let month = Number(tmpData[i][dateChar].substr(5,2));
        let day = Number(tmpData[i][dateChar].substr(8,2));
        // console.log("day..",day)
        category.push(month+"月"+day+"日");
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
                    let index = Math.floor(params.value*5/max);
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
      const tmpData = this[dataName].list;
      for(let i=0,len=tmpData.length;i<len;i++){
        let month = Number(tmpData[i][dateChar].substr(5,2));
        let day = Number(tmpData[i][dateChar].substr(8,2));
        // console.log("day..",day)
        category.push(month+"月"+day+"日");
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
    // 总销售额
    chartConsumeTotalAmount() {
      this.initSingleChart('consumeDailyTotalData','date','totalAmount','consume_total_amount','总销售额');
    },
    // 总杯数
    chartConsumeTotalCups() {
      this.initSingleChart('consumeDailyTotalData','date','totalCups','consume_total_cups','总杯数');
    },
    // 饮品杯数
    chartDrinkCups() {
      this.initDoubleChart('consumeDailyTotalData','date','drinkCups','consume_pay_cups','free_cups_daily_average','饮品杯数','免费杯数','付费杯数');
    },
    // 总台日均杯数
    chartEquipmentsDailyAverage() {
      this.initSingleChart('consumeDailyTotalData','date','equipmentsDailyAverage','equipments_daily_average','总台日均杯数');
    },
    // 付费台日均杯数
    chartPayDailyAverage() {
      this.initSingleChart('consumeDailyTotalData','date','payDailyAverage','pay_daily_average','付费台日均杯数');
    },
    // 总杯均价
    chartCupsDailyAverage() {
      this.initSingleChart('consumeDailyTotalData','date','cupsDailyAverage','cups_daily_average','总杯均价');
    },
    // 付费杯均价
    chartPayCupsDailyAverage() {
      this.initSingleChart('consumeDailyTotalData','date','payCupsDailyAverage','pay_cups_daily_average','付费杯均价');
    },
    clearCharts2(){
      this.userGrowthAndActivityData = {"list":[],"MTD":{}};
      this.initUserGrowthAndActivityCharts();
    },
    // 用户增长及活跃
    userGrowthAndActivity(date){
      const url = date?this.userGrowthAndActivityUrl+"?date="+date:this.userGrowthAndActivityUrl;
      // console.log("url..",url);
      axios.get(url)
      .then((response)=> {
        // console.log(response.data.data)
        if(response.data && response.data.code=="200"){
          if(response.data.data.length!=[]){
            this.userGrowthAndActivityData = response.data.data;
          } else {
            this.userGrowthAndActivityData = {"list":[],"MTD":{}};
          }
          // console.log(this.userGrowthAndActivityData.list[0])
          this.initUserGrowthAndActivityCharts();
          // this.getUserSleepList(date);
        } else {
          this.clearCharts2();
          this.alertMsg('获取用户增长及活跃数据错误');
        }
      })
      .catch((error)=> {
        this.clearCharts2();
        this.alertMsg('用户增长及活跃接口错误');
      });
    },
    initUserGrowthAndActivityCharts() {
      this.chartUsersTotalNumber();
      this.chartNewTotalNumber();
      this.chartUserActiveTotal();
      this.chartUserPayActive();
      this.chartPerCapitaPay();
    },
    // 总用户数
    chartUsersTotalNumber() {
      this.initSingleChart('userGrowthAndActivityData','created_at','usersTotalNumber','users_total_number','总用户数');
    },
    // (日)新增用户数
    chartNewTotalNumber() {
      this.initSingleChart('userGrowthAndActivityData','created_at','newTotalNumber','new_total_number','(日)新增用户数');
    },
    // (日)活跃用户数
    chartUserActiveTotal() {
      this.initSingleChart('userGrowthAndActivityData','created_at','userActiveTotal','user_active_total','(日)活跃用户数');
    },
    // (日)付费活跃用户数
    chartUserPayActive() {
      this.initSingleChart('userGrowthAndActivityData','created_at','userPayActive','user_pay_active','(日)付费活跃用户数');
    },
    // 人均付费购买频次
    chartPerCapitaPay() {
      this.initSingleChart('userGrowthAndActivityData','created_at','perCapitaPay','per_capita_pay','人均付费购买频次');
    },
    // 沉睡用户
    getUserSleepList(date) {
      const url = date?this.getUserSleepListUrl+"?date="+date:this.getUserSleepListUrl;
      axios.get(url)
      .then((response)=> {
        // console.log(response.data)
        if(response.data && response.data.code=="200"){
          this.getUserSleepListData = response.data.data;
          this.getUserRecallList(date);
        } else {
          this.alertMsg('获取沉睡用户数据错误');
          this.getUserRecallList(date);
        }
      })
      .catch((error)=> {
        this.alertMsg('沉睡用户接口错误');
        this.getUserRecallList(date);
      });
    },
    // 召回用户
    getUserRecallList(date) {
      const url = date?this.getUserRecallListUrl+"?date="+date:this.getUserRecallListUrl;
      axios.get(url)
      .then((response)=> {
        // console.log(response.data)
        if(response.data && response.data.code=="200"){
          this.getUserRecallListData = response.data.data;
          this.getUserRetainList(date);
        } else {
          this.alertMsg('获取召回用户数据错误');
          this.getUserRetainList(date);
        }
      })
      .catch((error)=> {
        this.alertMsg('召回用户接口错误');
        this.getUserRetainList(date);
      });
    },
    // 留存用户
    getUserRetainList(date) {
      const url = date?this.getUserRetainListUrl+"?date="+date:this.getUserRetainListUrl;
      axios.get(url)
      .then((response)=> {
        // console.log(response.data)
        if(response.data && response.data.code=="200"){
          const data1 = response.data.data;
          this.getUserRetainListData.head0 = ['',''];
          this.getUserRetainListData.list=[];
          this.getUserRetainListData.head=[];
          this.getUserRetainListData.head[0]="星期";
          this.getUserRetainListData.head[1]="日期";
          const len = data1.length;
          for(let i=0;i<len;i++){
            this.getUserRetainListData.head0.push(data1[i].retain_at);
            this.getUserRetainListData.head.push(data1[i].retain_at.substr(5,5));
            this.getUserRetainListData.list[i] = [];
            for(let j=0;j<(len+2);j++){
              this.getUserRetainListData.list[i].push({});
            }
            this.getUserRetainListData.list[i][0].content = data1[i].retain_week;
            this.getUserRetainListData.list[i][1].content = data1[i].retain_at.substr(5,5);
            this.getUserRetainListData.list[i][i+2].new = true;
            this.getUserRetainListData.list[i][i+2].content = data1[i].retain_number.today_new_register;
          }
          for(let i=0;i<len;i++){
            for(let j=0;j<len;j++){
              if(data1[i].retain_number[this.getUserRetainListData.head0[j+2]]){
                this.getUserRetainListData.list[i][j+2].content = data1[i].retain_number[this.getUserRetainListData.head0[j+2]];
              }
            }
          }
          // console.log(this.getUserRetainListData.head)
        } else {
          this.alertMsg('获取留存用户数据错误');
        }
      })
      .catch((error)=> {
        this.alertMsg('留存用户接口错误');
      });
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
.retain-day {
  min-width: 65px;
}
.charts {
  width: 1060px;
  height: 500px;
}
</style>
