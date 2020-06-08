<template>
  <div class="content-body">
    <div class="group-form">
      <el-form :model="groupForm" ref="groupForm">
        <div class="title">编组管理</div>
        <el-row>
          <el-col :span="12" v-for="(items,index) in groupForm.groupInfoArr" :key="items.index">
            <table class="el-table">
              <thead>
                <tr>
                  <th class="name">姓名 </th>
                  <th>职位</th>
                  <th>组长</th>
                  <th>组别</th>
                </tr>
              </thead>
              <tbody class="el-table__body">
                <tr class="el-table__row" v-for="(item,childrenIndex) in groupForm.groupInfoArr[index]" :key="item.index">
                  <td>
                    {{item.name}}
                  </td>
                  <td>
                    <el-form-item>
                      <el-select v-model="item.is_leader" @change="getLeader($event,item.userid)">
                        <el-option label="运维专员" value="2"></el-option>
                        <el-option label="运维组长" value="1"></el-option>
                      </el-select>
                    </el-form-item>
                  </td>
                  <td>
                    <el-form-item v-if="item.is_leader == '1'" :prop="'groupInfoArr.'+index+'.'+childrenIndex+'.leader_id'" :show-message="false">
                      <el-select v-model="item.userid" disabled >
                        <el-option v-for="(value,index) in managers" :key="index" v-if="item.group_id==value.group_id" :label="value.name" :value="value.userid"></el-option>
                      </el-select>
                      </el-form-item>
                       <el-form-item v-else :prop="'groupInfoArr.'+index+'.'+childrenIndex+'.leader_id'" :rules="leaderIdRules" :id="'groupInfoArr.'+index+'.'+childrenIndex+'.leader_id'">
                      <el-select v-model="item.leader_id">
                        <el-option v-for="(value,index) in managers" :key="index" v-if="item.group_id==value.group_id" :label="value.name" :value="value.userid"></el-option>
                      </el-select>
                    </el-form-item>
                  </td>
                  <td>
                    <el-form-item :prop="'groupInfoArr.'+index+'.'+childrenIndex+'.group_id'" :rules="groupIdRules" :id="'groupInfoArr.'+index+'.'+childrenIndex+'.group_id'">
                      <el-select v-model="item.group_id" @change="getGroup($event,item.is_leader,item.userid)" @visible-change="setIsDisabled($event,item.group_id)">
                        <el-option v-for='(value,keys) in groupNumberArr' :key="keys" :disabled="value.isdisabled" :label="value.num" :value="keys+''">
                        </el-option>
                      </el-select>
                    </el-form-item>
                  </td>
                </tr>
              </tbody>
            </table>
          </el-col>
        </el-row>
        <el-form-item>
          <el-button type="primary" @click="submitForm('groupForm')">编组生效</el-button>
        </el-form-item>
      </el-form>
    </div>
    <!-- 排班管理 -->
    <div class="scheduling-form">
      <el-row>
        <el-col :span="12">
          <div class="title">排班管理</div>
        </el-col>
        <el-col :span="12">
          <span class="demonstration">日期：</span>
          <el-date-picker
            :editable="false"
            v-model="schedulingForm.date"
            type="month"
            value-format="yyyy-MM"
            placeholder="选择日期"
            :clearable="false"
            @change="getScheduleData">
          </el-date-picker>
        </el-col>
      </el-row>
      <el-form v-show="isShowScheduleInfo" :model="schedulingForm">
        <el-table
        :data="scheduleInfo"
        style="width:95%"
        max-height="400">
          <el-table-column
            fixed
            label="组"
            prop="group_id">
          </el-table-column>
          <el-table-column
            fixed
            label="姓名"
            prop="name">
          </el-table-column>
          <el-table-column  v-for="(value, index) in weeksArr" :key="index+1" :label="1+index+''">
            <el-table-column  :label="''+value">
              <template slot-scope="scope">
                <el-button v-model="scope.row.schedule[index]"  type="text" @click="editScheduleStatus($event,scope.$index,scope.row,index)" :disabled="isScheduleDisabled">{{scope.row.schedule[index]|splitString}}</el-button>
              </template>
            </el-table-column>
          </el-table-column>
        </el-table>
        <el-form-item>
          <el-button class="go-back" type="primary"  @click="goBack">返回</el-button>
          <el-button type="primary" @click="submit('schedulingForm')">排班生效</el-button>
        </el-form-item>
      </el-form>
    <div class="tip-txt" v-show="!isShowScheduleInfo">暂无排班数据</div>
    </div>
  </div>
</template>
<script>
/* eslint-disable */
import axios  from  'axios'
export default {
  data() {
    var validatePass = (rule, value, callback) => {
      if (value === '0') {
        callback(new Error('请选择分组'));
      }
      callback();
    };
    return {
      groupForm: {
        groupInfoArr: { leader_id: "", group_id: "" },
      },
      groupInfo: rootInitData.groupInfo,
      groupNumber: rootInitData.groupNumber,
      managers: rootInitData.managers,
      isShowManagers: false,
      isdisable: false,
      groupNumberArr: [],
      leaderIdRules: { required: true, message: '请选择组长', trigger: 'change' },
      groupIdRules: { required: true,validator: validatePass, trigger: 'change' },
      scheduleInfo: rootInitData.scheduleInfo,
      scheduleDate: rootInitData.date,
      isChange: rootInitData.isChange,
      schedulingForm:{
        date:""
      },
      weeksArr: [],
      scheduleArr: [],
      saveScheduleData:[],
      isScheduleDisabled: false,
      isShowScheduleInfo: true,
      beforeChangeVal: null,
      afterChangeVal: null,
      orgId: rootOrgId
    }
  },
  mounted() {
    this.init(rootInitData.groupInfo);
  },
  filters: {
    splitString(value) {
      if(value){
        let statusMsg = "";
        value = Number(value.split('-')[1]);
        switch(value)
          {
          case 1:
            statusMsg = "班";
            break;
          case 2:
            statusMsg = "休";
            break;
          default:
            statusMsg = "请假";
          }
        return statusMsg
     }
   }
  },
  methods: {
    init(res) {
      window.parent.onscroll = (e)=>{
        this.scrollMsg();
      }
      let _this = this;
      let groupNumber = {};
      for (let i = 0; i < _this.groupNumber.length; i++) {
          groupNumber.num = _this.groupNumber[i][0];
          groupNumber.isdisabled = false;
          _this.groupNumberArr.push(groupNumber);
          groupNumber = [];
      }
      this.setGroup(res);
      // 排班
      if(this.scheduleInfo){
        this.setWeek(this.scheduleDate.year,this.scheduleDate.month);
        this.schedulingForm.date = this.scheduleDate.year + '-' + this.scheduleDate.month;
        console.log('date', this.schedulingForm.date)
        this.isEdit();
      }else{
        this.isShowScheduleInfo = false
      }
    },
    getLeader(event, userID) {
      let groupInfoArr = this.groupInfo;
      let managers = [];
      for (var i = 0; i < groupInfoArr.length; i++) {
        if (event != 1 && groupInfoArr[i].leader_id == userID) {
          groupInfoArr[i].leader_id = "";
        }
        if (groupInfoArr[i].is_leader == 1) {
          managers.push(groupInfoArr[i]);
        }
      }
      this.managers = managers;
    },
    setGroup(res){
      let _this = this;
      let groupInfoArr = res;
      let groupInfoObj = {},group = [];
      for (var i = 0; i < groupInfoArr.length; i++) {
        let groupID = groupInfoArr[i].group_id;
        group.push(groupInfoArr[i]);
        if (groupInfoArr[i + 1]) {
          if (groupInfoArr[i].group_id != groupInfoArr[i + 1].group_id) {
            groupInfoObj[groupID] = group;
            group = [];
          }else{
            if(group.length >=6 && groupInfoArr[i].group_id != 0){
              let groupId = groupInfoArr[i].group_id;
              _this.groupNumberArr[groupId].isdisabled= true;
            }
          }
        } else {
          groupInfoObj[groupID] = group;
        }
      }
      this.groupForm.groupInfoArr = groupInfoObj;
    },
    getGroup(event, isLeader, userID) {
      let _this = this;
      let groupInfoArr = this.groupInfo;
      let managers = [],group = [];
      for (let i = 0; i < groupInfoArr.length; i++) {
        if (groupInfoArr[i].userid == userID) {
          groupInfoArr[i].leader_id = "";
        }
        if(isLeader == 1 && groupInfoArr[i].leader_id == userID){
          groupInfoArr[i].leader_id = "";
        }
        if (groupInfoArr[i].is_leader == 1) {
          managers.push(groupInfoArr[i]);
        }
        if (groupInfoArr[i].group_id == event) {
          group.push(groupInfoArr[i])
        }
      }

      if (group.length >= 7 && event != 0) {
        _this.alertMsg('添加成功，现在'+event+'组已满员');
        for (let i = 0; i < _this.groupNumberArr.length; i++) {
          if(i == event){
            _this.groupNumberArr[i].isdisabled= true;
          }
        }
      }
      this.managers = managers;
    },
    setIsDisabled(event,val){
      var _this = this;
      if(event === true){
        _this.beforeChangeVal = val;
      }else{
        _this.afterChangeVal = val;
      }

      if (_this.beforeChangeVal != _this.afterChangeVal){
          let beforeChangeGroup = [];
          for (let i = 0; i < _this.groupInfo.length; i++) {
            if(_this.beforeChangeVal && _this.groupInfo[i].group_id == _this.beforeChangeVal){
              beforeChangeGroup.push(_this.groupInfo[i]);
            }
          }
        let groupNumberArr = [];
        if (beforeChangeGroup.length < 7 && _this.groupNumberArr[_this.beforeChangeVal].isdisabled ==true){
           _this.groupNumberArr[_this.beforeChangeVal].isdisabled = false;
           groupNumberArr = _this.groupNumberArr[_this.beforeChangeVal];
          _this.groupNumberArr.splice(_this.beforeChangeVal, 1, groupNumberArr);
        }
      }
    },
    saveGroupData(){
      let _this = this;
      let params = [];
      for (let i in _this.groupForm.groupInfoArr) {
         for (let j in _this.groupForm.groupInfoArr[i]) {
            params.push(_this.groupForm.groupInfoArr[i][j]);
         }
      }
      axios.post(rootErpUrl+"/distribution-user/update-group", {groupInfo:params,org_id: _this.orgId})
      .then(function (response) {
          if(response.data){
              _this.alertMsg("保存成功",'success');
             _this.groupInfo = response.data.groupInfo;
            _this.groupNumber = response.data.groupNumber;
            _this.managers = response.data.managers;
            _this.scheduleInfo = response.data.scheduleInfo;
            // _this.scheduleDate = response.data.date;
            // _this.isChange = response.data.isChange;
            _this.setGroup(_this.groupInfo);
          }else{
            _this.alertMsg("保存失败,请稍后重试",'error');
          }
      }).catch(function (error) {
        // 由网络或者服务器抛出的错误
        _this.alertMsg(error,'error');
      });
    },
    submitForm(formName) {
      let _this = this;
      this.$refs[formName].validate((valid,obj) => {
          if (valid) {
            _this.saveGroupData();
          } else {
           for(let key in obj){
                document.getElementById(key).scrollIntoView();
                break;
            }
            return false;
          }
      });
    },
    //排班管理
    getWeek(year,month,day){
    //根据年度和月份，创建日期
    //对year,month进行整数及范围校验的。
        let d = new Date();
        d.setYear(year);
        d.setMonth(month-1);
        d.setDate(day);
        //获得周几
        let weeks = ['日','一','二','三','四','五','六'];
        return weeks[d.getDay()];
    },
    setWeek(year,month){
      // 计算一个月的周期
      let _this = this;_this.weeksArr = [];
      for (let i = 0; i < _this.scheduleDate.days; i++) {
        let week = this.getWeek(year,month,i+1);
        _this.weeksArr.push(week);
      }
    },
    getScheduleData() {
      let _this = this;
      let params = _this.schedulingForm.date
      axios.get(rootErpUrl+"/distribution-user/get-schedule?date="+params+"&org_id="+_this.orgId)
      .then(function (response) {
          if(response.data){
            if(response.data.scheduleInfo.length> 0){
              _this.isShowScheduleInfo = true;
              _this.scheduleDate = response.data.date;
              _this.setWeek( _this.scheduleDate.year, _this.scheduleDate.month);
              _this.scheduleInfo = response.data.scheduleInfo;
              _this.isChange = response.data.isChange;
              _this.isEdit();
             } else{
              _this.isShowScheduleInfo = false;
             }

          }else{
             _this.alertMsg("获取数据失败,请稍后重试",'error');
          }
      }).catch(function (error) {
         _this.alertMsg(error,'error');
      });
    },
    editScheduleStatus(event,row,value,index){
       let dateSelect=this.schedulingForm.date;
       if(index+1<10){
       	dateSelect+='-0'+(index+1);
       }else{
       	dateSelect+='-'+(index+1);
       }
       let date=new Date(dateSelect).getTime();
       let dateNow=new Date().getTime();
       console.log('date',date)
       console.log('dateNow',dateNow)
       if(dateNow-date<0){
       	this.isScheduleDisabled = false;
	       let day = value.schedule[index].split('-')[0];
	       let status = Number(value.schedule[index].split('-')[1]);
	        switch(status)
	        {
	        case 1:
	          status = 2;
	          break;
	        case 2:
	          status = 3;
	          break;
	        default:
	          status = 1;
	        }
	       value.schedule[index] = day+"-"+status;
	       this.scheduleInfo.splice(row, 1, value);
       }
    },
    submit(formName) {
       let _this = this;
       let params = {};
       params.date = this.schedulingForm.date;
       params.scheduleInfo = this.scheduleInfo;
       params.org_id = this.orgId;
       axios.post(rootErpUrl+"/distribution-user/update-schedule",params ,{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'})
      .then(function (response) {
          if(response.data){
             _this.alertMsg("保存成功",'success');
             // console.log(response.data.scheduleInfo);
             _this.scheduleInfo = response.data.scheduleInfo;
             // _this.scheduleDate = response.data.date;
          }else{
             _this.alertMsg("保存失败,请稍后重试",'error');
          }
      }).catch(function (error) {
         _this.alertMsg(error,'error');
      });
    },
    alertMsg(msg,type)
    {
      this.$message({
        message: msg,
        duration:3000,
        type: type
      });
      this.scrollMsg();
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
    goBack(){
      window.location = '/distribution-user/index';
    },
    isEdit(){
      if(this.isChange == 1){
          this.isScheduleDisabled = false;
        }else{
          this.isScheduleDisabled = false;
        }
    }
  }
}

</script>
<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.group-form table {
  width: 90%;
  border: 1px solid #ebeef5;
  margin-left: 3%;
  margin-bottom: 5%;
}
.group-form .el-table td, .el-table th {
    padding: 5px 0;
    min-width: 0;
    box-sizing: border-box;
    text-overflow: ellipsis;
    vertical-align: middle;
    position: relative;
}
.group-form .name{
  width: 100px;
}
.group-form th{
  text-align: center;
}
.group-form table td,
.group-form table th {
  border-right: 1px solid #ebeef5;
  border-bottom: 1px solid #ebeef5;
}
.group-form table tr:last-child td{
  border-bottom: 0; 
}
.group-form table tr td:last-child{
  border-right: 0; 
}
.group-form {
  margin-bottom: 5%;
}
.scheduling-form .el-table{
  margin: 0 auto;
  margin-top: 1%;
}
.scheduling-form .el-form-item__content button{
  margin-top: 2%;
}
.scheduling-form .tip-txt{
  margin: 50px;
}
.el-message{
  margin: 50%;
  position: fixed;
}
.go-back{
  margin-right: 150px;
}
</style>
