<!-- 消费时间 -->
<template>
    <div class="content-body">
      <el-form ref="formList" :model="formList" label-width="100px">
        <el-row :gutter="10" v-for="(item,index) in formList.payTimeList" :key="item.guid" >
          <el-col :span="12">
            <el-form-item label="消费时间：" :prop="'payTimeList.' +index +'.date'" :rules="timeRules">
              <el-date-picker v-model="item.date" type="datetimerange" range-separator="至" start-placeholder="开始日期"  end-placeholder="结束日期" @change="changeDateTimeRange(item.date,index)" format="yyyy-MM-dd HH:mm:ss" value-format="yyyy-MM-dd HH:mm:ss">
              </el-date-picker>
            </el-form-item>
          </el-col>
          <el-col :span="6">
            <el-form-item :prop="'payTimeList.' +index +'.select'" :rules="selectRules">
              <el-select  placeholder="请选择" clearable v-model="item.select" @change="changeSelect">
                  <el-option value="2" label="全部"></el-option>
                  <el-option value="1" label="付费"></el-option>
                  <el-option value="0" label="免费"></el-option>
              </el-select>
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
    // 接收
    props:{
      conditionKey:{type:String},conditionData:{type:Object,default:function(){
        return {}
      }},
      renderFlag:{type:Boolean,default:false},
      renderData:{type:Array,default:[]},
      logicRelationFlag:{type:Boolean,default:false},
    },
    data()
    {
      return {
        formList:{
          payTimeList:[{date:"",select:"",guid:this.guid()}],//新增注册时间模板
        },
        selectRules: {
          required: true, message: '请选择是否有付费', trigger: 'change'
        },
        timeRules: {
            required: true, message: '请选择日期', trigger: 'change'
        },
        isClickSaveData:false,//是否保存数据
        myConditionData:this.conditionData,//接收父组件数据---巧----
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
        //渲染
        // console.log(this.myRenderFlag);
        if(this.myRenderFlag) this.render();
      },
      render()
      {
        this.formList.payTimeList = [];
        var data = this.myRenderData;
        for(let i=0;i<this.myRenderData.length;i++){
          this.formList.payTimeList.push({date:[data[i].pay_start_time,data[i].pay_end_time],select:String(data[i].is_pay),guid:this.guid()});
        }
      },
      changeDateTimeRange(val,index){
        // console.log(val,index);
        // console.log(this.formList.payTimeList[index].date[0]);
        this.changeSaveStatus(false);
      },
      changeSelect(val)
      {
        this.changeSaveStatus(false);
      },
      //设置保存状态
      changeSaveStatus(flag)
      {
        this.isClickSaveData=flag;//设置保存状态
        this.$emit('sendissave',this.isClickSaveData);
      },
      saveConditionData(){//保存条件数据
        this.$refs["formList"].validate((valid) => {
          if(valid){
            this.myConditionData.logic_condition={
                "condition":[]
            }
            for(let i=0;i<this.formList.payTimeList.length;i++){
              this.myConditionData.logic_condition.condition[i] = {
                "pay_start_time":this.formList.payTimeList[i].date[0],
                "pay_end_time":this.formList.payTimeList[i].date[1],
                "is_pay":this.formList.payTimeList[i].select
              };
            }
            //验证逻辑类型
            if(this.myLogicRelationFlag){
              this.changeSaveStatus(true);
            }else{
              this.$emit('validForm');//触发上级逻辑类型验证
            }
          }else{
           return false;
          }
        })
      },
      //生成guid ,解决排序
      guid()
      {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
            return v.toString(16);
        });
      },
    },
    watch:{
      logicRelationFlag(val){
        // console.log("val..",val)
        this.myLogicRelationFlag=val;
        if(!this.myLogicRelationFlag){
          this.changeSaveStatus(false);
        }
      },
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