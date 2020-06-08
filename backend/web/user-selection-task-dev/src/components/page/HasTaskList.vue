<!-- 消费城市 -->
<template>
    <div class="content-body">
      <el-form ref="form" :model="form" :rules="rules" label-width="100px">
        <el-row :gutter="10">
             <el-form-item label="已有任务：" prop="hasTaskList">
                   <el-select v-model="form.hasTaskList" clearable filterable multiple placeholder="请选择" style="width:500px">
                      <el-option   v-for="task in taskList"
                        :label="task.name"
                        :value="task.key"
                        :key="task.key"
                        >
                      </el-option>
                  </el-select>
            </el-form-item>
        </el-row>
        <div class="div_center">
              <div>
                  <span class="tip_red" v-show="!isClickSaveData">您尚未保存数据</span>
                  <span class="tip_green" v-show="isClickSaveData">您已经保存以上数据</span>
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
      conditionIndex:{type:Number},conditionKey:{type:String},conditionData:{type:Object,default:function(){
        return {}
      }},
      renderFlag:{type:Boolean,default:false},
      renderData:null,logicRelationFlag:{type:Boolean,default:false},
    },
    data()
    {
      return {
        form:{
           hasTaskList:[]//已有任务
        },
        taskList:[],
        conditionTemp:"",//条件类型对应数据
        myConditionData:this.conditionData,//接收父组件数据---巧----
        rules: {
          hasTaskList: [
            { required: true, message: '请至少选择一个任务', trigger: 'change' }//
          ]
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
        for(var i in this.conditionTemp.taskList){
          if(i!=""){
            this.taskList.push({"key":i,"name":this.conditionTemp.taskList[i]});
          }
        }
        this.taskList.sort((a,b)=>{
          return Number(b.key)-Number(a.key);
        });
        console.log("task",this.taskList)
        if(this.myRenderFlag) this.render();//编辑的话，进行渲染
      },
      //页面编辑时渲染数据
      render(){
        this.form.hasTaskList = [];
        var data = this.myRenderData.selection_task_id;
        // console.log("data..",data);
        for(let i=0;i<data.length;i++){
          if(data[i].id!=""){
            this.form.hasTaskList.push(data[i]);
          }
        }
      },
      //设置保存状态
      changeSaveStatus(flag)
      {
        this.isClickSaveData=flag;//设置保存状态
        this.$emit('sendissave',this.isClickSaveData);
      },
      //修改设置
      changeTaskList(){
        this.changeSaveStatus(false);
      },
      //保存条件数据
      saveConditionData(){
        console.log("list",this.form.hasTaskList)
        this.$refs["form"].validate((valid) => {
          if(valid){
            this.myConditionData.logic_condition={
                "condition":{
                  "selection_task_id":this.form.hasTaskList
                }
            }
           //验证逻辑类型
            if(this.myLogicRelationFlag){
              this.changeSaveStatus(true);
            }else{
              this.$emit('validForm');//触发上级逻辑类型验证
            }
          }
        })
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
