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
    <el-table :data="weeklyUserListData" border class="el-table-common">
      <el-table-column prop="year" label="年份">
      </el-table-column>
      <el-table-column prop="cycle_str" label="日期">
      </el-table-column>
      <el-table-column prop="weekly_number" label="周次">
      </el-table-column>
      <el-table-column prop="users_total" label="总用户数">
      </el-table-column>
      <el-table-column prop="new_users_total" label="新增用户">
      </el-table-column>
      <el-table-column prop="new_users_growth" label="用户增长率">
      </el-table-column>
      <el-table-column prop="registered_user" label="注册用户">
      </el-table-column>
      <el-table-column prop="new_registered_user" label="新增注册用户">
      </el-table-column>
      <el-table-column prop="new_registered_growth" label="注册用户增长率">
      </el-table-column>
      <el-table-column prop="guest_user" label="非注册用户">
      </el-table-column>
      <el-table-column prop="active_user" label="活跃人数">
      </el-table-column>
      <el-table-column prop="active_user_growth" label="活跃增长率">
      </el-table-column>
      <el-table-column prop="pay_active_user" label="付费活跃用户">
      </el-table-column>
      <el-table-column prop="pay_user_growth" label="付费活跃增长率">
      </el-table-column>
      <el-table-column prop="free_active_user" label="免费活跃人数">
      </el-table-column>
    </el-table>
    <div>
      <!-- <div class="sub-title" style="margin-bottom:20px;">周报用户数据图表</div> -->
      <!-- 用户增长 -->
      <div id="usersGrowth" class="charts"></div>
      <!-- 新增用户 -->
      <div id="newUsersTotal" class="charts"></div>
      <!-- 新增注册用户 -->
      <div id="newRegisteredUser" class="charts"></div>
      <!-- 活跃用户数 -->
      <div id="activeUser" class="charts"></div>
      <!-- 付费、免费活跃用户数-->
      <div id="payFreeActiveUser" class="charts"></div>
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
      getWeeklyUserListUrl: rootCoffeeStieUrl+'weekly-report-api/weekly-user-list.html',
      weeklyUserListData: [],
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
      exportUrl:'/weekly-user/export?',
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
      this.getWeeklyUserList();
    },
    trimPercent(val) {
      // console.log(String(val).charAt(String(val).length-1));
      let lastChar = String(val).charAt(String(val).length-1);
      if(lastChar=="%") {
        return Number(String(val).substr(0,String(val).length-1));
      } else {
        return Number(val);
      }
    },
    searchDate() {
      this.getWeeklyUserList(this.form.searchDate);
    },
    exportExcel() {
      //
    },
    goBack() {
      // window.location.href = this.listUrl;
    },
    clearCharts(){
      this.weeklyUserListData = [];
      this.initCharts();
    },
    getWeeklyUserList(date) {
      const url = date?this.getWeeklyUserListUrl+"?start="+date[0]+"&end="+date[1]:this.getWeeklyUserListUrl;
      this.exportDate = date?"start="+date[0]+"&end="+date[1]:"";
      // console.log("url.....",url);
      axios.get(url)
      .then((response)=> {
        // console.log(response.data)
        if(response.data && response.data.code=="200"){
          if(response.data.data.length==0){
            this.alertMsg("没有数据，请选择其他日期");
          }
          this.weeklyUserListData = [];
          window.setTimeout(()=>{
            this.weeklyUserListData = response.data.data;
            this.initCharts();
          },300);
        } else {
          this.clearCharts();
          this.alertMsg('获取周报用户数据错误');
        }
      })
      .catch((error)=> {
        this.clearCharts();
        this.alertMsg('周报用户接口错误');
      });
    },
    initCharts() {
      this.chartUsersGrowth();
      this.chartNewUsersTotal();
      this.chartNewRegisteredUser();
      this.chartActiveUser();
      this.chartPayFreeActiveUser();
    },
    // 用户增长
    chartUsersGrowth() {
      const myChart = echarts.init(document.getElementById("usersGrowth"));
      myChart.clear();
      var category=[];
      var data=[[],[],[]];
      const tmpData = this.weeklyUserListData;
      for(let i=0,len=tmpData.length;i<len;i++){
        category.push(tmpData[i].weekly_number);
        data[0].push(tmpData[i].guest_user);
        data[1].push(tmpData[i].registered_user);
        data[2].push(tmpData[i].users_total);
      }
      const option = {
        title: {
            text: "用户增长",
            textStyle: {fontSize:16}
        },
        tooltip : {},
        legend: {
            data:['非注册用户','注册用户','总用户数']
        },
        calculable : true,
        yAxis : [
            {
                type : 'value'
            }
        ],
        xAxis : [
            {
                type : 'category',
                data : category
            }
        ],
        series : [
            {
                name:'非注册用户',
                type:'bar',
                stack: '总量',
                barMaxWidth:20,
                itemStyle : { normal: {label : {show: true, position: 'insideRight'}}},
                data:data[0]
            },
            {
                name:'注册用户',
                type:'bar',
                stack: '总量',
                barMaxWidth:20,
                itemStyle : { normal: {label : {show: true, position: 'insideRight'}}},
                data:data[1]
            },
            {
                name: "总用户数",
                type: 'bar',
                barMaxWidth:20,
                itemStyle : { normal: {label : {show: true}}},
                data: data[2]
            }
        ]
      };
      myChart.setOption(option);
    },
    // 新增用户
    chartNewUsersTotal() {
      this.initSingleChart('weeklyUserListData','weekly_number','newUsersTotal','new_users_total','新增用户');
    },
    // 新增注册用户
    chartNewRegisteredUser() {
      this.initSingleChart('weeklyUserListData','weekly_number','newRegisteredUser','new_registered_user','新增注册用户');
    },
    // 活跃用户数
    chartActiveUser() {
      this.initSingleChart('weeklyUserListData','weekly_number','activeUser','active_user','活跃用户数');
    },
    // 付费、免费活跃用户数
    chartPayFreeActiveUser() {
      this.initDoubleChart('weeklyUserListData','weekly_number','payFreeActiveUser','pay_active_user','free_active_user','付费、免费活跃人数','付费活跃人数','免费活跃人数');
    },
    initSingleChart(dataName,dateChar,chartId,dataVal,titleName){
      const myChart = echarts.init(document.getElementById(chartId));
      myChart.clear();
      let category=[];
      let data=[];
      const tmpData = this[dataName];
      for(let i=0,len=tmpData.length;i<len;i++){
        category.push(tmpData[i][dateChar]);
        let dataValTmp = this.trimPercent(tmpData[i][dataVal]);
        data.push(dataValTmp);
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
