<!-- 消费城市 -->
<template>
    <div class="content-body">
      <el-form ref="formList" :model="formList" label-width="110px">
        <el-row :gutter="10">
            <el-col>
                <el-form-item label="统计时间：" prop="accountTime" :rules="timeRules">
                  <el-date-picker  type="datetimerange" @change="changeDateTime" range-separator="至" start-placeholder="开始日期"  end-placeholder="结束日期" format="yyyy-MM-dd HH:mm:ss" value-format="yyyy-MM-dd HH:mm:ss" v-model="formList.accountTime">
                  </el-date-picker>
                </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="10">
              <el-col :span="12">
                <el-form-item label="最后消费次数：" prop="consumptionNum" :rules="consumptionNumRules">
                  <el-input v-model="formList.consumptionNum" @change="changeprice"></el-input>
                </el-form-item>
              </el-col>
          </el-row>
          <el-row :gutter="10" v-for="(item,index) in formList.priceRangeList"   :key="item.guid" class="el-price">
                <el-col :span="12">
                  <el-form-item label="价格区间：" :prop="'priceRangeList.' +index +'.logical_relation'" :rules="logical_relationRules">
                      <el-select  clearable v-model="item.logical_relation" @change="changeLogicType">
                          <el-option value=">" label="大于"></el-option>
                          <el-option value="<" label="小于"></el-option>
                          <el-option value="=" label="等于"></el-option>
                          <el-option value=">=" label="大于等于"></el-option>
                          <el-option value="<=" label="小于等于"></el-option>
                      </el-select>
                  </el-form-item>
                </el-col>
                <el-col :span="8">
                  <el-form-item :prop="'priceRangeList.' +index +'.price'" :rules="priceNumRules">
                        <el-input type="number"  @change="changeprice" v-model="item.price" placeholder="请输入价格"></el-input>
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
          if (!myreg.test(value)&&value!="") {
            callback(new Error('请输入大于0的正整数'));
          } else {
            callback();
          }
        }, 200);
      };
      var checkPriceNum = (rule,value,callback) => {
        setTimeout(() => {
           var myreg=/^[0-9]*[1-9][0-9]*$/;
          if (value!=""&&value<=0) {
            callback(new Error('请输入大于0的价格'));
          }else {
            if(value.split('.').length>1){
                if (value.split('.')[1].length > 2) {
                    callback(new Error('请保留两位小数'));
                }else if(value==""){
                  new Error('请输入价格')
                }else{
                  callback();
                }
            }else{
              callback();
            }
          }


        }, 200);
      };
      return {
        formList:{
          consumptionNum:"",
          accountTime:[],//最近消费统计时间
          priceRangeList:[ {guid:this.guid(),price:'',logical_relation:''}],//新增消费统计模板
        },
        accountTimePickerOption: {//设置选取日期时间规则
          disabledDate(time){
            return time.getTime() < (Date.now()-3600*1000*24);
          }
        },
        consumptionNumRules: {
          required: false, validator: checkTimesNum, trigger: 'change'
        },
        timeRules: {
            required: true, message: '请选择日期', trigger: 'change'
        },
        logical_relationRules: {
          required: true, message: '请选择价格区间', trigger: 'change'
        },
        priceNumRules: [
            {required: true,  trigger: 'blur',message: '请输入价格'},
            {validator: checkPriceNum}
        ],
        myConditionData:this.conditionData,//接收父组件数据---巧----
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
         if(this.myRenderFlag) this.render();//编辑的话，进行渲染
      },
      render(){
        this.formList.accountTime=[];//时间初始化
        this.formList.priceRangeList=[];//条件初始化
        var data = this.myRenderData;
        this.formList.accountTime=[data.start_time,data.end_time];
        this.formList.consumptionNum=data.recent_pay_num;//消费次数
        //渲染多个条件
        for(let i=0;i<data.list.length;i++){
          this.formList.priceRangeList.push({price:data.list[i].price,logical_relation:data.list[i].logical_relation,guid:this.guid()});
        }
      },
      //修改逻辑关系
      changeLogicType(){
        this.changeSaveStatus(false);
      },
      //修改消费数量
      changeprice(){
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
      //保存条件数据
      saveConditionData(){
        console.log("d",this.formList.priceRangeList)
        this.$refs["formList"].validate((valid) => {
          if(valid){
            this.myConditionData.logic_condition={
              "condition":{
                "start_time":this.formList.accountTime[0],//开始时间
                "end_time":this.formList.accountTime[1],//结束时间
                "recent_pay_num": this.formList.consumptionNum,//消费次数
                "list": this.formList.priceRangeList//获取逻辑列表
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
            return false;
          }
        });
      },
      //生成guid ,解决排序
      guid()
      {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
            return v.toString(16);
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
