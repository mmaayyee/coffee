<!-- 消费城市 -->
<template>
    <div class="content-body">
      <el-form ref="formName" :model="formName" label-width="100px" :rules="rules">
        <el-row :gutter="10">
             <el-form-item label="付费时间：" prop="payTime">
                <el-date-picker  type="datetimerange" @change="changeSaveData" range-separator="至" start-placeholder="开始日期"  end-placeholder="结束日期" format="yyyy-MM-dd HH:mm:ss" value-format="yyyy-MM-dd HH:mm:ss" v-model="formName.payTime">
                </el-date-picker>
              </el-form-item>
          </el-row>
          <el-row :gutter="10">
             <el-form-item label="选择城市：" prop="checkCitys">
                <el-checkbox  @change="handleCheckAllChange" v-model="checkAll" :indeterminate="isIndeterminate">全国</el-checkbox>
                <el-checkbox-group v-model="formName.checkCitys" @change="handleCheckedCitiesChange">
                    <el-checkbox v-for="city in cities" :label="city.key" :key="city.key" @change="changeSaveData">{{ city.name }}</el-checkbox>
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
    // 接收
    props:{
      conditionKey:{type:String},conditionData:{type:Object,default:function(){
        return {}
      }},
      renderFlag:{type:Boolean,default:false},
      renderData:null,logicRelationFlag:{type:Boolean,default:false},
    },
    data()
    {
      return {
        formName:{
          payTime:[],//付费时间
          checkCitys:[]//选中的城市
        },
        cities: [],
        checkCitiesKey:[],
        isIndeterminate: true,
        checkAll:false,
        conditionTemp:"",//条件类型对应数据
        myConditionData:this.conditionData,//接收父组件数据---巧----
        accountTimePickerOption: {//设置选取日期时间规则
          disabledDate(time){
            return time.getTime() < (Date.now()-3600*1000*24);
          }
        },
        rules: {
            payTime: [{ required: true, message: '请选择日期'}],
            checkCitys:[{ required: true, message: '请至少选择一个城市', trigger: 'change' }],//消费商品
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
        //获取到对应模板数据后，显示初始值
        for(let i in this.conditionTemp.cityList){
          this.cities.push({'key':i,'name':this.conditionTemp.cityList[i]});
        }
        //渲染数据
        if(this.myRenderFlag) this.render();
      },
      render()
      {
        var data = this.myRenderData;
        this.formName.checkCitys = data.city;
        this.formName.payTime = [data.start_time,data.end_time];
      },
      saveConditionData(){//保存条件数据
        console.log("this.formName.checkCitys..."+this.formName.checkCitys);
        this.$refs["formName"].validate((valid) => {
          if(valid){
             this.myConditionData.logic_condition={
                "condition":{
                  "start_time":this.formName.payTime[0],
                  "end_time":this.formName.payTime[1],
                  "city":this.formName.checkCitys
                }
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
      //保存后，继续修改则改变保存状态
      changeSaveData(){
         this.changeSaveStatus(false);
      },
      //设置保存状态
      changeSaveStatus(flag)
      {
        this.isClickSaveData=flag;//设置保存状态
        this.$emit('sendissave',this.isClickSaveData);
      },
      handleCheckedCitiesChange(value) {
        console.log(this.formName.checkCitys);
        let checkedCount = value.length;
        var cityLength=Object.keys(this.cities).length;
        this.checkAll = checkedCount === cityLength;
        this.isIndeterminate = checkedCount > 0 && checkedCount < this.cities.length;
      },
      handleCheckAllChange(val){
        this.checkCitiesKey=[];
        for(let i in this.cities){
          this.checkCitiesKey.push(this.cities[i].key);
        }
        this.formName.checkCitys = val ? this.checkCitiesKey : [];
        this.isIndeterminate =false
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
