<!-- 最近消费商品 -->
<template>
    <div class="content-body">
      <el-form ref="formName" :model="formName" label-width="100px" :rules="rules">
        <el-row :gutter="10">
              <el-form-item label="统计时间：" prop="accountTime">
                <el-date-picker  type="datetimerange" range-separator="至" start-placeholder="开始日期"  end-placeholder="结束日期" format="yyyy-MM-dd HH:mm:ss" value-format="yyyy-MM-dd HH:mm:ss" v-model="formName.accountTime"  @change="changeSaveData">
                </el-date-picker>
             </el-form-item>
        </el-row>
         <el-row :gutter="10">
            <el-form-item label="消费次数：" prop="consumptionNumber">
              <el-input  style="width:20.5%" v-model="formName.consumptionNumber" type="number" @change="changeSaveData"></el-input>
              <el-checkbox v-model="isRecentConsumer" @change="changeSaveData">是否最近消费</el-checkbox>
            </el-form-item>
        </el-row>
        <el-row :gutter="10">
             <el-form-item label="消费商品：" prop="checkGoodsList">
                <el-radio-group v-model="formName.checkGoodsList">
                    <el-radio  v-for="goods in goodsList" :label="goods.key" :key="goods.key" @change="changeSaveData">{{ goods.name }}</el-radio>
                </el-radio-group>
              </el-form-item>
        </el-row>
        <el-row :gutter="10">
             <el-form-item label="消费机型：" prop="checkDeviceTypes">
                  <el-checkbox  @change="handleCheckAllDeviceChange" v-model="checkAllDevice" :indeterminate="isIndeterminateDevice" >全选</el-checkbox>
                  <el-checkbox-group v-model="formName.checkDeviceTypes" @change="handleCheckedDeviceChange">
                    <el-checkbox v-for="device in deviceTypes" :label="device.key" :key="device.key" @change="changeSaveData">{{ device.name }}</el-checkbox>
                  </el-checkbox-group>
            </el-form-item>
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
        deviceTypes:[],//消费机型
        // checkDeviceKey:[],//选中消费机型key
        goodsList:[],//消费商品
        // checkGoodsKey:"",//选中消费商品Key
        isRecentConsumer:false,//是否最近消费
        formName: {
          consumptionNumber:"",//消费次数
          accountTime:[],//最近消费统计时间
          checkGoodsList:"",//选中消费商品
          checkDeviceTypes:[]//选中消费机型
        },
        conditionTemp:"",//对应模板数据
        myConditionData:this.conditionData,//接收父组件数据---巧----
        rules: {
            consumptionNumber: [
              {required:true,validator: checkTimesNum, trigger: 'blur' }//消费次数验证
            ],
            accountTime: [
              { required: true, message: '请选择日期', trigger: 'change' }//时间
            ],
            checkGoodsList:[
              { required: true, message: '请选择一种消费商品',trigger: 'change'}//消费商品
            ],
            checkDeviceTypes:[{ required: true, message: '请至少选择一种消费机型', trigger: 'change' }],//消费商品
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
        checkAllDevice:false,//是否全选设备类型
        checkDeviceKey:[],//用来判断是否全选设备类型
        isIndeterminateDevice: true,//标识设备类型是否全选的状态
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
        //获取到对应模板数据后，显示初始值
        // this.goodsList=this.conditionTemp.consumeGoodList;
        for(let i in this.conditionTemp.consumeGoodList){
          this.goodsList.push({'key':i,'name':this.conditionTemp.consumeGoodList[i]});
        }
        for(let i in this.conditionTemp.equipTypeList){
          this.deviceTypes.push({'key':i,'name':this.conditionTemp.equipTypeList[i]});
        }
        //渲染数据
        if(this.myRenderFlag) this.render();
      },
      render()
      {
        var data = this.myRenderData;
        this.formName.consumptionNumber = data.consume_num;
        this.isRecentConsumer = (data.is_recent_consume=="1");
        this.formName.checkGoodsList = data.consume_good[0];
        this.formName.checkDeviceTypes = data.consume_equip_type;
        this.formName.accountTime = [data.start_time,data.end_time];
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
              var isRecentConsumer = this.isRecentConsumer?"1":"0";
              var checkGoodsList = [this.formName.checkGoodsList];
              this.myConditionData.logic_condition={
                  "condition":{
                    "start_time":this.formName.accountTime[0],//开始时间
                    "end_time":this.formName.accountTime[1],//结束时间
                    "consume_num":this.formName.consumptionNumber,//消费数量
                    "is_recent_consume":isRecentConsumer,//是否最近消费
                    "consume_good":checkGoodsList,//消费商品
                    "consume_equip_type":this.formName.checkDeviceTypes//消费机型
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
      },
      //设备类型全选
      handleCheckedDeviceChange(value) {
        let checkedCount = value.length;
        var deviceLength=Object.keys(this.deviceTypes).length;
        this.checkAllDevice = checkedCount === deviceLength;
        this.isIndeterminateDevice= checkedCount > 0 && checkedCount < this.deviceTypes.length;
      },
      handleCheckAllDeviceChange(val){
        this.checkDeviceKey=[];
        for(let i in this.deviceTypes){
          this.checkDeviceKey.push(this.deviceTypes[i].key);
        }
        this.formName.checkDeviceTypes = val ? this.checkDeviceKey : [];
        this.isIndeterminateDevice =false;
      },
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
