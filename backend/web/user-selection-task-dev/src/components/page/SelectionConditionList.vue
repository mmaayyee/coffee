<template>
  <div class="content-body">
    <el-form  ref="form" :model="form" :rules="rules">
      <el-row :gutter="10">
        <el-col :span="4">
          <div class="text select-txt">筛选条件{{ myIndex }} <i class="el-icon-close" @click="deleteCondition" v-show="myDomShow"></i></div>
        </el-col>
        <el-col :span="8">
          <el-form-item label="逻辑关系" v-show="myDomShow" prop="logic_relation">
            <el-select v-model="form.logic_relation" clearable placeholder="请选择" @change="logicSC" class="logic-select">
              <el-option
                v-for="item in logicTypeOptions"
                :label="item.label"
                :value="item.value"
                :key="item.value">
              </el-option>
            </el-select>
          </el-form-item>&nbsp;
        </el-col>
        <el-col :span="12">
          <el-form-item label="条件类型">
            <el-select v-model="form.conditionType" clearable placeholder="请选择" @change="conditionTypeSC" @clear="clearConditionType">
              <el-option
                v-for="(item,key) in conditionTypeList"
                :label="item.label"
                :value="item.value"
                :key="key">
              </el-option>
            </el-select>
          </el-form-item>
        </el-col>
      </el-row>
    </el-form>
    <!--  岗修改开始 0309 -->
      <!-- 楼宇点位开始 -->
      <div v-if="condtionText=='1'" class="conditionBox">
         <v-search-city-list :condition-key="condtionText" :logic-relation-flag="logicRelationFlag" :condition-data="myData" :render-flag="myRenderFlag" :render-data="itemRenderData" @sendissave="sendIsSave" @validForm="validLogicType"></v-search-city-list>
      </div>
      <!-- 注册时间 开始-->
      <div v-else-if="condtionText=='2'" class="conditionBox">
          <v-register-time :condition-key="condtionText" :logic-relation-flag="logicRelationFlag" :condition-data="myData" :render-flag="myRenderFlag" :render-data="itemRenderData" @sendissave="sendIsSave" @validForm="validLogicType"></v-register-time>
      </div>
      <!-- 最近消费商品开始 -->
      <div v-else-if="condtionText=='3'" class="conditionBox">
        <v-recent-consumer-goods :condition-key="condtionText"  :condition-data="myData" :render-flag="myRenderFlag" :render-data="itemRenderData" :logic-relation-flag="logicRelationFlag" @sendissave="sendIsSave" @validForm="validLogicType"></v-recent-consumer-goods>
      </div>
      <!-- 消费点位 开始-->
      <div v-else-if="condtionText=='4'" class="conditionBox">
        <v-consumption-level :condition-key="condtionText" :condition-data="myData" :render-flag="myRenderFlag" :render-data="itemRenderData" :logic-relation-flag="logicRelationFlag" @sendissave="sendIsSave" @validForm="validLogicType"></v-consumption-level>
      </div>
      <!-- 消费时间开始 -->
      <div v-else-if="condtionText=='5'" class="conditionBox">
          <v-pay-time :condition-key="condtionText" :condition-data="myData" :render-flag="myRenderFlag" :render-data="itemRenderData" :logic-relation-flag="logicRelationFlag" @sendissave="sendIsSave" @validForm="validLogicType"></v-pay-time>
      </div>
      <!-- 消费城市开始 -->
      <div v-else-if="condtionText=='6'" class="conditionBox">
          <v-pay-city :condition-key="condtionText" :condition-data="myData" :render-flag="myRenderFlag" :render-data="itemRenderData" :logic-relation-flag="logicRelationFlag" @sendissave="sendIsSave" @validForm="validLogicType"></v-pay-city>
      </div>
      <!-- 企业导入开始 -->
      <div v-else-if="condtionText=='9'" class="conditionBox">
         <v-companies-to-import :condition-key="condtionText" :condition-data="myData" :render-flag="myRenderFlag" :render-data="itemRenderData" :logic-relation-flag="logicRelationFlag" @sendissave="sendIsSave" @validForm="validLogicType"></v-companies-to-import>
      </div>
      <!-- 已有任务开始 -->
      <div v-else-if="condtionText=='10'" class="conditionBox">
         <v-has-task-list :condition-key="condtionText" :condition-index="myIndex" :condition-data="myData" :render-flag="myRenderFlag" :render-data="itemRenderData" :logic-relation-flag="logicRelationFlag"  @sendissave="sendIsSave" @validForm="validLogicType"></v-has-task-list>
      </div>
      <!-- 杯均价开始 -->
      <div v-else-if="condtionText=='7'" class="conditionBox">
          <v-cup-of-average :condition-key="condtionText" :condition-data="myData" :render-flag="myRenderFlag" :render-data="itemRenderData" @sendissave="sendIsSave" :logic-relation-flag="logicRelationFlag" @validForm="validLogicType"></v-cup-of-average>
      </div>
      <!-- 消费频次开始 -->
      <div v-else-if="condtionText=='8'" class="conditionBox">
          <v-consumption-frequency :condition-key="condtionText" :condition-data="myData" :render-flag="myRenderFlag" :render-data="itemRenderData" :logic-relation-flag="logicRelationFlag" @sendissave="sendIsSave" @validForm="validLogicType"></v-consumption-frequency>
      </div>
      <!-- 导入用户开始 -->
      <div v-else-if="condtionText=='11'" class="conditionBox">
          <v-import-user :condition-key="condtionText" :condition-data="myData" :render-flag="myRenderFlag" :render-data="itemRenderData" :logic-relation-flag="logicRelationFlag" @sendissave="sendIsSave" @validForm="validLogicType"></v-import-user>
      </div>
      <!-- 导入用户结束 -->
      <!-- 订单时间开始 -->
      <div v-else-if="condtionText=='12'" class="conditionBox">
          <v-order-time :condition-key="condtionText" :condition-data="myData" :render-flag="myRenderFlag" :render-data="itemRenderData" :logic-relation-flag="logicRelationFlag" @sendissave="sendIsSave" @validForm="validLogicType"></v-order-time>
      </div>
      <!-- 订单时间结束 -->
  </div>
</template>

<script>
/* eslint-disable */
import vRegisterTime from './RegisterTime';
import vSearchCityList from './SearchCityList';
import vRecentConsumerGoods from './RecentConsumerGoods';
import vConsumptionLevel  from './ConsumptionLevel';
import vPayTime  from './PayTime';
import vPayCity  from './PayCity';
import vCompaniesToImport  from './CompaniesToImport';
import vHasTaskList  from './HasTaskList';
import vCupOfAverage  from './CupOfAverage';
import vConsumptionFrequency  from './ConsumptionFrequency';
import vImportUser  from './ImportUser';
import vOrderTime  from './OrderTime';
  export default
  {
    // 验证
    name:"conditionlist",
    props:{
      // 岗修改开始 0309 添加了renderFlag
      conditionIndex:{type:Number},renderFlag:{type:Boolean,default:false},data:{type:Object,default:function(){
        return {}
      }},
      renderData:{type:Object,default:function(){
        return {}
      }},
      // renderData:null
      // 岗修改结束 0309
    },
    data()
    {
      return {
        checkCityList: [],//城市列表
        checkChannelList:[],//渠道类型列表
        checkDevicelList:[],//设备类型列表
        myIndex:this.conditionIndex+1,//巧
        myConditionIndex:this.conditionIndex,
        myDomShow:this.conditionIndex>0,
        myRenderFlag:this.renderFlag,
        form: {
          conditionType:'',
          // logicType:'',
          inputConditionName:'',//condition label
          logic_type:'',//条件ID
          logic_relation:''//条件
        },
        logicRelationFlag:false,
        //提交数据
        myData: this.data,
        //接收数据
        myRenderData: this.renderData,
        //子组件接收数据
        itemRenderData:null,
        // 岗修改开始 0309
        logicTypeOptions: [{
          value: 'and',
          label: '且'
        }, {
          value: 'or',
          label: '和'
        }, {
          value: 'not_in',
          label: '排除'
        }],
        conditionTypeList:[],//条件类型
        // 岗修改结束 0309
        isActive:true,
        condtionText:"",
        rules: {
            logic_relation: [
              { required: true, message: '请选择逻辑类型'}
            ]
        },
        saveKey:false
      }
    },
    mounted()
    {
      this.init();
    },
    methods: {
      // 岗修改开始 0309
      init()
      {
        this.$emit('sendissave',this.saveKey,this.myConditionIndex);
        //渲染筛选条件下拉框
        for(let key in rootData.conditionTypeList){
          this.conditionTypeList.push({value:key,label:rootData.conditionTypeList[key]});
        }
        //渲染编辑数据
        if(this.myRenderFlag){
          this.render(this.myRenderData);
        }
        if(this.conditionIndex==0){
          this.myData.logic_relation = "";
          this.logicRelationFlag = true;
        }
      },
      render(data){
        this.itemRenderData = data.condition;
        // console.log(this.itemRenderData);
        if(data.logic_type){
          this.form.conditionType = data.logic_type;
          this.changeConditionType(data.logic_type);
        }
        if(data.logic_relation){
          this.form.logic_relation = data.logic_relation;
          this.logicSC(data.logic_relation);
        }
      },
      conditionTypeSC(re)
      {
        if(re!=""){
          this.changeConditionType(re);
          this.myRenderFlag = false;
        }
      },
      changeConditionType(val)
      {
        // console.log(77);
        let obj = {};
        obj = this.conditionTypeList.find((item)=>{
            return item.value === val;
        });
        let label = obj.label;
        this.form.inputConditionName = label;
        this.condtionText=val;
        this.myData.logic_type = this.condtionText;//条件ID
        this.$emit('senddata',this.myData,this.myConditionIndex);
        this.sendIsSave(false);
      },
      sendIsSave(flag)
      {
        this.saveKey = flag;
        this.$emit('sendissave',this.saveKey,this.myConditionIndex);
      },
      //验证逻辑类型
      validLogicType(){
        if(this.myConditionIndex!==0){
          this.$refs["form"].validate((valid) => {
            if (valid) {
                this.logicRelationFlag=true
               }else{
                this.logicRelationFlag=false
                console.log("验证不通过，逻辑类型为false");
               }
          });
        }
      },
      // 选择逻辑关系---巧---
      logicSC(re){
        // console.log("re..",this.form.logic_relation);
        this.myData.logic_relation = re;
        if(re!=""){
          this.logicRelationFlag = true;
          this.$emit('senddata',this.myData,this.myConditionIndex);
        } else {
          this.logicRelationFlag = false;
          this.sendIsSave(false);
        }
      },
      // 岗修改结束 0309
      // 删除此筛选条件
      deleteCondition(){
        this.$emit('delete',this.myConditionIndex);
      },
      //清空筛选条件
      clearConditionType(){
        this.myRenderFlag = false;
        this.condtionText="";//将传值置空则不渲染条件
      },
      //修改筛选项
      handleCheckedCitiesChange(value) {
      },
      // 上传文件
       submitUpload() {
        this.$refs.upload.submit();
      },
      // 楼宇筛选结束
      handleRemove(file, fileList) {
        console.log(file, fileList);
      },
      handlePreview(file) {
        console.log(file);
      }
    },
    watch:{
      data(val){
        console.log(data);
        this.render(val);
      },
      conditionIndex(val){
        this.myConditionIndex = val;
        this.myIndex=val+1;
      },
      // 岗修改开始 0309 添加了renderFlag
      renderFlag(val){
        this.myRenderFlag = val;
      }

      // 岗修改结束 0309
    },
    components:{
      vRegisterTime,//注册时间
      vSearchCityList,//楼宇筛选列表
      vRecentConsumerGoods,//最近消费时间
      vConsumptionLevel,//消费商品
      vPayTime,//消费时间
      vPayCity,//消费城市
      vCompaniesToImport,//企业导入
      vHasTaskList,//已有任务
      vCupOfAverage,//杯均价
      vConsumptionFrequency,//消费频次
      vImportUser,//导入用户
      vOrderTime,//订单时间

    }
  }
</script>
<style>
  .logic-select {
    width:100px;
  }
  .conditionBox{
    width:88%;
    margin:10px auto;

  }
  .btnAdd,.upload-file{
    margin-left:15px;
  }
  .el-upload__tip{
    margin-left:5px;
  }
  .taskBox{
    width:40%;
    border:1px solid #c0c4cc;
    padding:5px;
  }
  .taskBox:hover{
    border:1px solid #409EFF;
  }
  .conditionType{
    width:98%;
    height: auto;
    max-height:500px;
    overflow: hidden;
    position: relative;
    margin-bottom: 30px;
  }
  .searchType,.searchList{
    width:104%;
    height: auto;
    max-height: 500px;
    overflow-x:hidden;
    overflow-y: scroll;
  }
</style>
