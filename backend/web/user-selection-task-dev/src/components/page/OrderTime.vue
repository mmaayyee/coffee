<!-- 订单时间 -->
<template>
    <div class="content-body">
      <el-form ref="formName" :model="formName" label-width="100px" :rules="rules">
        <el-row :gutter="10">
              <el-form-item label="订单时间：" prop="accountTime">
                <el-date-picker  type="datetimerange" range-separator="至" start-placeholder="开始日期"  end-placeholder="结束日期" format="yyyy-MM-dd HH:mm:ss" value-format="yyyy-MM-dd HH:mm:ss" v-model="formName.accountTime"  @change="changeSaveData">
                </el-date-picker>
             </el-form-item>
        </el-row>
        <el-row>
          <el-col :span="6">
            <el-form-item label="付费类型：" prop="payType">
              <el-select  placeholder="请选择" clearable v-model="formName.payType" @change="changeSaveData">
                  <el-option value="2" label="全部"></el-option>
                  <el-option value="1" label="付费"></el-option>
                  <el-option value="0" label="免费"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24">
             <el-form-item label="订单数量：" style="float:left;" prop="logic">
              <el-select  placeholder="请选择" clearable v-model="formName.logic" @change="changeSaveData">
                  <el-option value="geq" label="大于等于"></el-option>
                  <el-option value="leq" label="小于等于"></el-option>
                  <el-option value="eq" label="等于"></el-option>
              </el-select>
              </el-form-item>
              <el-form-item  prop="orderNum" style="float:left">
                <el-input style="width:150px;" type="number"  v-model="formName.orderNum" @input="changeSaveData">
                </el-input>
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
    // 验证
    props:{
      conditionKey:{type:String},conditionData:{type:Object,default:function(){
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
            callback(new Error('数字格式不对,请输入正整数'));
          } else {
            callback();
          }
        }, 200);
      };
      return {
        conditionTypeList:rootData.conditionTypeList,//条件类型
        formName: {
          accountTime:[],//订单时间
          payType:"2",//付费类型
          logic:"geq",//付费类型
          orderNum:1,//订单数量
        },
        conditionTemp:"",//对应模板数据
        myConditionData:this.conditionData,//接收父组件数据---巧----
        rules: {
            orderNum: [
              {required:true,validator: checkTimesNum, trigger: 'blur' }//订单数量
            ],
            accountTime: [
              { required: true, message: '请选择日期', trigger: 'change' }//时间
            ],
            logic: [
              { required: true, message: '请选择类型', trigger: 'change' }//时间
            ],
            payType: [
              { required: true, message: '请选择类型', trigger: 'change' }//时间
            ]

        },
        accountTimePickerOption: {//设置选取日期时间规则
          disabledDate(time){
            return time.getTime() < (Date.now()-3600*1000*24);
          }
        },
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
        for(var i in rootData.addConditionTypeTemp){
          if(this.conditionKey==i){
            this.conditionTemp=rootData.addConditionTypeTemp[i];
          }
        }
        //渲染数据
        if(this.myRenderFlag) this.render();
      },
      render()
      {
        var data = this.myRenderData;
        this.formName.accountTime = [data.pay_start_time,data.pay_end_time];
        this.formName.payType=data.is_pay;
        this.formName.logic=data.order_num_logic;
        this.formName.orderNum=data.order_num;
      },
      //设置保存状态
      changeSaveStatus(flag)
      {
        this.isClickSaveData=flag;//设置保存状态
        this.$emit('sendissave',this.isClickSaveData);
      },
      //保存后，继续修改则改变保存状态
      changeSaveData(){
         this.changeSaveStatus(false);
      },
      saveConditionData(){//保存条件数据
         this.$refs["formName"].validate((valid) => {
          if (valid) {
              this.myConditionData.logic_condition={
                  "condition":{
                    "pay_start_time":this.formName.accountTime[0],//开始时间
                    "pay_end_time":this.formName.accountTime[1],//结束时间
                    "is_pay": this.formName.payType,
                    "order_num":this.formName.orderNum,
                    "order_num_logic":this.formName.logic
                  }
                }
                //验证逻辑类型
                if(this.myLogicRelationFlag){
                  this.changeSaveStatus(true);
                }else{
                  this.$emit('validForm');//触发上级逻辑类型验证
                }
          } else {
            return false;
          }
        });
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
