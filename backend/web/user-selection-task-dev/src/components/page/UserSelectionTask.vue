<template>
    <div class="content-body">
        <div class="line-title">添加用户筛选任务</div>
        <el-form ref="form" :model="form" label-width="100px" :rules="rules">
          <el-row :gutter="10">
            <el-col :span="12">
              <el-form-item label="任务名称" prop="selection_task_name">
                <el-input v-model="form.selection_task_name"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="参考任务">
                <el-select v-model="form.selectRefTask" clearable filterable placeholder="请选择" @change="selectRefTaskSC" @clear="clearTask">
                  <el-option
                    v-for="item in reference_task_id"
                    :label="item.name"
                    :value="item.id"
                    :key="item.id">
                  </el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <!-- 添加手机号 -->
          <el-row :gutter="10">
              <el-col :span="12">
                <el-form ref="formUserPhone" :model="userPhoneForm" :rules="userPhoneRules" label-width="100px">
                  <el-form-item label="验证手机号码" prop="inputVerifyPhoneNumber"><el-input v-model="userPhoneForm.inputVerifyPhoneNumber"></el-input></el-form-item>
                </el-form>
              </el-col>
              <el-col :span="8">
                <el-button plain icon="el-icon-circle-plus-outline" @click="addTaskVerifyPhone"
                >加入</el-button><el-button plain icon="el-icon-delete" @click="clearphoneNumbers">清空</el-button>
              </el-col>
          </el-row>
          <!-- 手机号列表 -->
          <div class="phoneNameBox">
              <el-row  v-for=" item in form.phoneNumbersTextArea"  :key="item.guid"  class="phoneNameText">
                <el-col :span="20">
                   <span>{{item.data}}</span>
                </el-col>
                <el-col :span="4">
                  <i class="el-icon-delete" @click="deletephoneNumbers(item.index)"></i>
                </el-col>
              </el-row>
          </div>
          <div>
              <v-selection-condition-list v-for="(item,index) in selectionConditionList" :key="item.guid" :condition-index="index" :render-flag="item.renderflag" :render-data="item.renderdata" :data="item.data" @delete="deleteSelectionCondition(index)" @senddata="sendData" @sendissave="sendIsSave"></v-selection-condition-list>
          </div>
          <div class="div_center">
            <el-button type="warning" plain native-type="reset" @click="resetForm(form)">取消任务</el-button> <el-button type="primary" plain @click="addNewSelectionTask">新建条件</el-button><el-button type="primary" plain @click="submitForm(form)">提交任务</el-button>
          </div>
        </el-form>
    </div>
</template>

<script>
/* eslint-disable */
import vSelectionConditionList from './SelectionConditionList'
import axios  from  'axios'
import Qs from 'qs'
  export default
  {
    data()
    {
      var checkPhoneNum = (rule,value,callback) => {
        if (!value) {
          return callback(new Error('手机号码不能为空'));
        }
        setTimeout(() => {
          var myreg=/^[1][3,4,5,6,7,8,9][0-9]{9}$/;
          if (!myreg.test(value)) {
            callback(new Error('手机号格式不对'));
          } else {
            callback();
          }
        }, 200);
      };
      return {
        selectionConditionList:[ {guid:this.guid()}],
        form: {
          selection_task_name: '',//任务名称
          selectRefTask: '',
          reference_taskId:'',//任务ID(巧)
          phoneNumbersTextArea: [],
          validate_mobile:[]//手机号数据列表
        },
        userPhoneForm:{
          inputVerifyPhoneNumber: '',//手机号
        },
        rules: {
            selection_task_name: [
              { required: true, message: '请输入任务名称', trigger: 'blur' }
            ]
        },
        userPhoneRules: {
            inputVerifyPhoneNumber: [
              { validator: checkPhoneNum, trigger: 'click' }//手机号验证
            ]
        },
        reference_task_id: rootData.reference_task_id,
        conditionListData: [],//条件整个数据
        queryWhere:[],//查询条件
        isSaveData:[],
        selectionTaskId:'',//任务ID
        isClickSubmit:false
      }
    },
    // 岗添加 开始
    // 初始化
    mounted()
    {
      this.init();
    },
    // 岗添加 结束
    methods: {
      // 岗添加 开始
      // 初始化
      init()
      {
        window.parent.onscroll = (e)=>{
          this.scrollMsg();
        }
        // 编辑时渲染筛选条件
        if(rootData.updateConditionTemp.length>0)
        {
          this.selectionTaskId = rootData.selection_task_id;
          this.renderDataFun(rootData,1);//1为编辑
        }else{
          //清除参考任务
          this.form.validate_mobile=[];
          this.form.selection_task_name="";
          this.form.phoneNumbersTextArea=[];
          this.userPhoneForm.inputVerifyPhoneNumber="";
          this.selectionConditionList=[ {guid:this.guid()}];
        }
      },
      // 渲染筛选条件
      renderCondition(conditionData)
      {
        this.selectionConditionList = [];
        for(let i=0;i<conditionData.length;i++){
          // console.log("i end ..."+i);
          const myGuid = this.guid();
          // var domShow = !conditionData[i].logic_relation=="";
          // const domShow = i>0;
          this.selectionConditionList.push({guid:myGuid,renderflag:true,renderdata:conditionData[i]});
        }
        //
      },
      // 岗添加 结束
      // 添加验证手机号码
      addTaskVerifyPhone(e){
        this.$refs["formUserPhone"].validate((valid) => {
          if (valid) {
            var phoneNum = this.userPhoneForm.inputVerifyPhoneNumber;
            if(!this.form.validate_mobile.find((num)=>num==phoneNum)&&phoneNum!==""){
                this.addTaskVerifyPhoneAction();
            } else {
              this.alertMsg("请勿重复添加手机号码");
            }
          } else {
            return false;
          }
        });
      },
      addTaskVerifyPhoneAction(){
          var _this = this;
          var params = new URLSearchParams();
          params.append("check_type",1);//1手机号 2为楼宇 3为公司
          params.append("mobile",this.userPhoneForm.inputVerifyPhoneNumber);
          params.append("build_name","");
          params.append("company_name","");
          axios.post('/user-selection-task/check-legal',params
          ).then(function (response) {
            if(response.data){
              console.log("后台检测结果",response.data);
              console.log("手机号码",_this.userPhoneForm.inputVerifyPhoneNumber);
              if(_this.userPhoneForm.inputVerifyPhoneNumber!==""){
                var myGuid = _this.guid();
                _this.form.phoneNumbersTextArea.push({data:_this.userPhoneForm.inputVerifyPhoneNumber,guid:myGuid});
                _this.form.validate_mobile.push(_this.userPhoneForm.inputVerifyPhoneNumber);
                _this.userPhoneForm.inputVerifyPhoneNumber="";
                }
            }else{
               _this.alertMsg("添加失败");
            }
          })
          .catch(function (error) {
            _this.alertMsg(error);
          });
      },
      // 删除手机号码
      deletephoneNumbers(index){
        this.form.phoneNumbersTextArea.splice(index,1);
        this.form.validate_mobile.splice(index,1);
      },
      //清空手机号
      clearphoneNumbers(){
        this.userPhoneForm.inputVerifyPhoneNumber = "";
        this.form.phoneNumbersTextArea=[];
        this.form.validate_mobile = [];
      },
      // 参考任务,获取数据
      renderDataFun(data,type){
        if(data!=""||data!=null){
          //编辑
          if(type==1){
                this.form.selection_task_name = data.selection_task_name;
                this.form.selectRefTask=data.reference_task;
                let obj2 = {};
                obj2 = this.reference_task_id.find((item)=>{
                    return item.id == data.reference_task;
                });
                // 渲染手机号
                if(data.validate_mobile.length>0){
                  for(let i=0;i<data.validate_mobile.length;i++){
                      this.form.validate_mobile[i] = data.validate_mobile[i];
                      this.form.phoneNumbersTextArea[i]={data:data.validate_mobile[i],guid:this.guid()};
                  }
                }else{
                  this.form.phoneNumbersTextArea=[];
                  this.form.validate_mobile = [];
                }
          }
          //渲染条件
          if(data.updateConditionTemp.length>0)
          {
            console.log("获取到的条件",data.updateConditionTemp);
            this.renderCondition(data.updateConditionTemp);
          }else{
            console.log("手机号获取值"+data.validate_mobile.length);
          }
        }
      },
      // 参考条件选中值发生变化时触发
      selectRefTaskSC(re)
      {
        if(re!=""){
          var _this = this;
          var params = new URLSearchParams();
          params.append("task_id",re);//选中的参考任务ID
          axios.post('/user-selection-task/get-where-by-task-id',params
            ).then(function (response) {
              console.log(response.data);
              if(response.data){
                var data=response.data
               _this.renderDataFun(data,2);//2为选择任务
              }else{
                _this.alertMsg("请求数据失败");
              }
            })
            .catch(function (error) {
              _this.alertMsg(error);
            });
          }
      },
      //清空参考任务
      clearTask(){
        this.init();
      },
      // 添加新的用户筛选条件
      addNewSelectionTask(){
        // var len = this.selectionConditionList.length;
        var myGuid=this.guid();
        this.selectionConditionList.push({guid:myGuid});
      },
      // 删除用户筛选条件
      deleteSelectionCondition(index){
        this.selectionConditionList.splice(index,1);
        this.isSaveData.splice(index,1);
        //岗添加 0311
        this.conditionListData.splice(index,1);
      },
      //生成guid ,解决排序
      guid()
      {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
            return v.toString(16);
        });
      },
      sendData(d,index){
        this.conditionListData[index] = d;
      },
      sendIsSave(d,index){
        this.isSaveData[index]=d;
      },
      submitForm(form){//验证表单
        this.$refs["form"].validate((valid) => {
        if (valid) {
            if(!this.isClickSubmit){
               console.log("测试请求执行几次");
              this.isClickSubmit=true;
              this.submitFormAction();
            }
            } else {
             return false;
           }
        });
      },
      submitFormAction(){//提交表单数据
        var _this=this;
        //检测是否有未保存数据
        for(var i in this.isSaveData){
          console.log(this.isSaveData);
          if(!this.isSaveData[i]){
            this.alertMsg("您有未保存的数据，请进行保存");
            this.isClickSubmit=false;
            return false;
          }
        }
        console.log("参考任务ID",this.form.selectRefTask);
        var params={
            "UserSelectionTask":{
              "selection_task_id":this.selectionTaskId,
              "selection_task_name":this.form.selection_task_name,//任务名称
              "reference_task":this.form.selectRefTask,//参考任务ID
              "validate_mobile": this.form.validate_mobile,//手机号列表
              "single_query_where":this.conditionListData
            }
          }
        console.log("传给后台的值",params);
        axios.post(rootCoffeeStieUrl+"task-api/user-selection-task-create.html", params)
        .then(function (response) {
          console.log(response);
            if(response.data){
              const type = _this.selectionTaskId==""?"1":"2";
              return axios.get('/activity-combin-package-assoc/create-activity-log?type='+type+'&moduleType=3');
            }else{
              _this.alertMsg("保存失败,请稍后重试");
              _this.isClickSubmit=false;
            }
        })
        .then(function (response) {
          _this.alertMsg("保存成功","success");
          window.setTimeout(function(){
            window.location.href = "/index.php/user-selection-task/index";
          },1000);
        })
        .catch(function (error) {
          _this.alertMsg("保存接口错误..."+error);
          _this.isClickSubmit=false;
        });
      },
      //取消表单
      resetForm(){
        this.$refs["form"].resetFields();
        this.form.phoneNumbersTextArea=[];
        this.selectionConditionList=[ {guid:this.guid()}];
        window.setTimeout(function(){
          window.location.href = "/index.php/user-selection-task/index";
        },1000);
      },
      alertMsg(msg,type)
      {
        // alert(msg);
        let msgType = type?type:"error";
        this.$message({
          message: msg,
          duration:3600,
          type: msgType
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
      }
    },
    components:{
      vSelectionConditionList,

    }
  }
</script>
<style>
 .div_center {
    text-align: center;;
    width:100%;
    margin:0 auto;
    margin-bottom: 20px;
  }
.phoneNameBox{
  width:50%;
  margin-left:8%;
  border:1px solid #b3d8ff;
  min-height: 90px;
  border-radius:5px;
}
.phoneNameText{

  padding:0 10px;
  height:30px;
  line-height: 30px;
}
.phoneNameText+.phoneNameText{
  border-top:1px solid #b3d8ff;
}
.phoneNameText:nth-child(2n){
   background: #ecf5ff;
}
</style>
