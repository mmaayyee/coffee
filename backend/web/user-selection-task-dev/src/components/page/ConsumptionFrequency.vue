<!-- 消费城市 -->
<template>
    <div class="content-body">
      <el-form ref="formList" :model="formList" label-width="100px">
        <el-row :gutter="10">
           <el-form-item label="统计时间：" prop="accountTime" :rules="timeRules">
              <el-date-picker  type="daterange" @change="changeDateTime" range-separator="至" start-placeholder="开始日期"  end-placeholder="结束日期" format="yyyy-MM-dd HH:mm:ss" value-format="yyyy-MM-dd HH:mm:ss" v-model="formList.accountTime">
              </el-date-picker>
            </el-form-item>
        </el-row>
        <el-row :gutter="10" v-for="(item,index) in formList.consumptionList" :key="item.guid">
          <el-col :span="12">
          <el-form-item label="复购次数：" :prop="'consumptionList.' +index +'.logical_relation'" :rules="logical_relationRules">
              <el-select placeholder="请选择逻辑关系" clearable v-model="item.logical_relation" @change="changeLogicType">
                <el-option value=">" label="大于"></el-option>
                <el-option value="<" label="小于"></el-option>
                <el-option value="=" label="等于"></el-option>
                <el-option value=">=" label="大于等于"></el-option>
                <el-option value="<=" label="小于等于"></el-option>
              </el-select>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item :prop="'consumptionList.' +index +'.after_purchase_num'" :rules="after_purchase_numRules">
              <el-input v-model="item.after_purchase_num" placeholder="请输入次数" @change="changeafter_purchase_num"></el-input>
          </el-form-item>
        </el-col>
        </el-row>
        <div class="div_center">
            <div>
              <span class="tip_red" v-if="!isClickSaveData">您尚未保存数据</span>
              <span class="tip_green" v-if="isClickSaveData">您已经保存以上数据</span>
            </div>
            <el-button type="primary" plain @click="saveConditionData" class="saveData">保存</el-button>
        </div>
      </el-form>
    </div>
</template>
<script>
/* eslint-disable */
  export default
  {
    props:{
      conditionData:{type:Object,default:function(){
        return {}
      }},
      renderFlag:{type:Boolean,default:false},
      renderData:null,logicRelationFlag:{type:Boolean,default:false},
    },
    data()
    {
      var checkTimesNum = (rule,value,callback) => {
        setTimeout(() => {
          var myreg=/^[0-9]*[1-9][0-9]*$/;
          if (!myreg.test(value)) {
            callback(new Error('请输入正整数'));
          } else {
            callback();
          }
        }, 200);
      };
      return {
        formList:{
          consumptionList:[ {guid:this.guid(),after_purchase_num:'',logical_relation:''}],//新增注册时间模板
          accountTime:"",//统计时间
        },
        accountTimePickerOption: {//设置选取日期时间规则
          disabledDate(time){
            return time.getTime() < (Date.now()-3600*1000*24);
          }
        },
        myConditionData:this.conditionData,//接收父组件数据---巧----
        after_purchase_numRules: {
          required: true, validator: checkTimesNum, trigger: 'blur'
        },
        logical_relationRules: {
          required: true, message: '请选择逻辑关系', trigger: 'change'
        },
        timeRules: {
            required: true, message: '请选择日期', trigger: 'change'
        },
        getLogicText:[],//获取逻辑列表
        isClickSaveData:false,//是否保存数据
        myRenderFlag:this.renderFlag,
        myRenderData:this.renderData,
        myLogicRelationFlag:this.logicRelationFlag,
      }
    },
    mounted()
      {
        this.init();
      },
    methods: {
      init(){
        window.parent.onscroll = (e)=>{
          this.scrollMsg();
        }
        if(this.myRenderFlag) this.render();//编辑的话，进行渲染
      },
      render(){
        this.formList.accountTime=[];
        this.formList.consumptionList=[];//条件初始化
        var data = this.myRenderData;
        this.formList.accountTime=[data.start_time,data.end_time]
        //渲染多个条件
        for(let i=0;i<data.list.length;i++){
          this.formList.consumptionList.push({after_purchase_num:data.list[i].after_purchase_num,logical_relation:data.list[i].logical_relation,guid:this.guid()});
        }
      },
      //修改逻辑关系
      changeLogicType(){
        this.changeSaveStatus(false);
      },
      //修改消费数量
      changeafter_purchase_num(){
        this.changeSaveStatus(false);
      },
      //修改统计时间
      changeDateTime(){
        this.changeSaveStatus(false);
      },
      //设置保存状态
      changeSaveStatus(flag)
      {
        this.isClickSaveData=flag;//设置保存状态
        this.$emit('sendissave',this.isClickSaveData);
      },
      //生成guid ,解决排序
      guid()
      {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
            return v.toString(16);
        });
      },
      //保存条件数据
      saveConditionData(){
        this.$refs["formList"].validate((valid) => {
          if(valid){
            this.myConditionData.logic_condition={
              "condition":{
                "start_time":this.formList.accountTime[0],//开始时间
                "end_time":this.formList.accountTime[1],//结束时间
                "list": this.formList.consumptionList//获取逻辑列表
              }
            }
            console.log(this.myConditionData.logic_condition);
              //验证逻辑类型
            if(this.myLogicRelationFlag){
              this.changeSaveStatus(true);
            }else{
              this.$emit('validForm');//触发上级逻辑类型验证
            }
          }else{
            this.alertMsg("表单错误");
          }
        });
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
    watch:{
      logicRelationFlag(val){
        // console.log("val..",val)
        this.myLogicRelationFlag=val;
        if(!this.myLogicRelationFlag){
          this.changeSaveStatus(false);
        }
      }
    }
  }
</script>
<style>
  .tip_red{
    color:red;
  }
  .tip_green{
    color:green;
  }
  .saveData{
    margin-top:20px;
  }
</style>