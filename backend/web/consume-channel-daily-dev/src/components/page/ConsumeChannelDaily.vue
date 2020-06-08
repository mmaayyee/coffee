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
    <div class="sub-title" style="margin-top:0">汇总表</div>
    <el-table :data="getUserConsumeCountListData" border class="el-table-common">
      <el-table-column prop="title" label="数据项">
      </el-table-column>
      <el-table-column prop="today" label="本日数据">
      </el-table-column>
      <el-table-column prop="last_week_today" label="上周同期">
      </el-table-column>
      <el-table-column label="周同比">
        <template slot-scope="scope">
          <span v-if="Number(scope.row.week_compare)<0"><i class="el-icon-download" style="color:green;"></i></span>
          <span v-else-if="Number(scope.row.week_compare)>0"><i class="el-icon-upload2" style="color:red;"></i></span>{{scope.row.week_compare}}
        </template>
      </el-table-column>
      <el-table-column prop="last_month_today" label="上月同期">
      </el-table-column>
      <el-table-column label="月同比">
        <template slot-scope="scope">
          <span v-if="Number(scope.row.month_compare)<0"><i class="el-icon-download" style="color:green;"></i></span>
          <span v-else-if="Number(scope.row.month_compare)>0"><i class="el-icon-upload2" style="color:red;"></i></span>{{scope.row.month_compare}}
        </template>
      </el-table-column>
      <el-table-column prop="month_begin_and_end" label="MTD">
      </el-table-column>
      <el-table-column label="日期">
        <template slot-scope="scope">
          {{scope.row.date | formatDate}}
        </template>
      </el-table-column>
    </el-table>
    <div v-for="(item,index) in getOrgListData" :key="index">
      <div class="sub-title" style="margin:10px 0;">{{item.organizationGroupName}}</div>
      <table class="gridtable">
        <tr>
          <th>{{index==0?'分公司':'机构名称'}}</th>
          <th>渠道</th>
          <th>总台数</th>
          <th>商用台数</th>
          <th>开机商用台数</th>
          <th>新增注册人数</th>
          <th>付费销量</th>
          <th>销售额</th>
          <th>付费台日均</th>
          <th>日期</th>
        </tr>
        <tr v-for="(item2,index2) in item.list" :key="index2">
          <td v-if="item2.headFlag" :rowspan="item2.rowspanNum">{{item2.headName}}</td>
          <td>{{item2.build_type_code}}</td>
          <td>{{item2.equipments_count}}</td>
          <td>{{item2.commercial_operation}}</td>
          <td>{{item2.equipments_be_count}}</td>
          <td>{{item2.new_register_users}}</td>
          <td>{{item2.consume_pay_cups}}</td>
          <td>{{item2.consume_pay_price}}</td>
          <td>{{item2.pay_daily_average}}</td>
          <td>{{item2.total_at|formatDate}}</td>
        </tr>
        <tr>
          <td colspan="2" style="text-align: center;">{{item.total.build_type_code}}</td>
          <td>{{item.total.equipments_count}}</td>
          <td>{{item.total.commercial_operation}}</td>
          <td>{{item.total.equipments_be_count}}</td>
          <td>{{item.total.new_register_users}}</td>
          <td>{{item.total.consume_pay_cups}}</td>
          <td>{{item.total.consume_pay_price}}</td>
          <td>{{item.total.pay_daily_average}}</td>
          <td>{{item.total.total_at|formatDate}}</td>
        </tr>
      </table>
    </div>
  </div>
</template>
<script>
/* eslint-disable */
import axios  from  'axios'
export default {
  data() {
    return {
      //渠道日报 汇总表
      getUserConsumeCountListData: [],
      getOrgListData: [],
      getUserConsumeCountListUrl: rootCoffeeStieUrl+'consume-channel-daily-api/get-user-consume-count-list.html',
      getOrgListUrl: rootCoffeeStieUrl+'consume-channel-daily-api/get-org-list.html',
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
      exportUrl:'/consume-channel-daily/export?date=',
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
      this.getUserConsumeCountList();
      // this.getUserConsumeCountList("2018-07");
    },
    searchDate() {
      // if(!this.form.searchDate) return false;
      console.log('searchDate..',this.form.searchDate);
      this.getUserConsumeCountListData = [];
      this.getOrgListData = [];
      this.getUserConsumeCountList(this.form.searchDate);
    },
    exportExcel() {
      //
    },
    // 渠道日报汇总表
    getUserConsumeCountList(date) {
      const url = date?this.getUserConsumeCountListUrl+"?date="+date:this.getUserConsumeCountListUrl;
      this.exportDate = date?date:"";
      axios.get(url)
      .then((response)=> {
        // console.log(response.data)
        if(response.data && response.data.code=="200"){
          if(response.data.data.length==0){
            this.alertMsg("汇总没有数据，请选择其他日期");
          }
          // this.alertMsg(response.data.message,"success");
          this.getUserConsumeCountListData = response.data.data;
          // console.log(this.getUserConsumeCountListData)
          this.getOrgList(date);
        } else {
          this.alertMsg(response.data.message);
          this.getOrgList(date);
        }
      })
      .catch((error)=> {
        this.alertMsg('汇总表接口错误');
        this.getOrgList(date);
      });
    },
    // 渠道日报-代理商以及包养加盟接口
    getOrgList(date) {
      const url = date?this.getOrgListUrl+"?date="+date:this.getOrgListUrl;
      // console.log("url..",url)
      axios.get(url)
      .then((response)=> {
        // console.log(response.data && response.data.code==200);
        // console.log(response.data);
        if(response.data && response.data.code==200){
          // this.alertMsg(response.data.message,"success");
          const data1 = response.data.data;
          let data = [];
          // console.log(data1);
          // console.log(data1.length);
          for(let i=0,len=data1.length;i<len;i++){
            let count1 = 0;
            data[i] = {};
            data[i].organizationGroupName = data1[i].organizationGroupName;
            data[i].total = data1[i].total;
            data[i].list = [];
            // console.log(data1[i].organizationGroupName)
            const len1 = data1[i].list.length;
            // console.log("len1..",len1)
            for(let j=0;j<len1;j++){
              const orgList = data1[i].list[j].orgList;
              // console.log("orgList..",orgList)
              for(let i1=0,len2=orgList.length;i1<len2;i1++){
                data[i].list[count1] = orgList[i1];
                if(i1==0){
                  data[i].list[count1].headFlag = true;
                  data[i].list[count1].headName = data1[i].list[j].name;
                  data[i].list[count1].rowspanNum = len2;
                }
                count1++;
              }
            }
            // console.log(data[i].total)
          }
          this.getOrgListData = data;
          // console.log("getOrgListData..",this.getOrgListData)
        } else {
          this.alertMsg(response.data.message);
        }
      })
      .catch((error)=> {
        this.alertMsg('分公司代理商以及包养加盟接口错误');
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
th,td {
  text-align: center;
}
</style>
