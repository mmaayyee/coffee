<!-- 消费城市 -->
<template>
    <div class="content-body">
            <el-row>
              <el-col :span="16">
              <el-form ref="formCompanyName" :model="companyForm" :rules="companyRules" label-width="100px">
                <el-form-item label="公司名称：" prop="companyName">
                     <el-autocomplete
                          class="inline-input"
                          v-model.trim="companyForm.companyName"
                          :fetch-suggestions="querySearch"
                          placeholder="请输入内容"
                        style="width:350px">
                      </el-autocomplete>
                </el-form-item>

                </el-form>
              </el-col>
              <el-col :span="4">
                <el-button  icon="el-icon-circle-plus-outline" class="btnAdd" @click="addCompanyList">添加</el-button>
              </el-col>
            </el-row>
            <el-row>
                <el-form-item>
                   <el-col :span="4">
                    <el-button plain icon="el-icon-delete" @click="clearCompanyNameList">清空</el-button>
                  </el-col>
                  <el-col :span="6">
                    <el-input placeholder="请输入内容" prefix-icon="el-icon-search" v-model.trim="searchCompanyText"></el-input>
                  </el-col>
                  <el-col :span="10">
                      <el-upload class="upload-file"
                        name="uploadPhoneTxt"
                        :on-remove="onRemoveTxt"
                        :action="uploadActionUrl"
                        :data="uploadData"
                        :onError="uploadError"
                        :onSuccess="uploadSuccess"
                        :on-exceed="handleExceed"
                        :before-upload="onBeforeUpload"
                        accept="text/plain"
                        :limit="1"
                        :file-list="files"
                          >
                          <el-button size="small" type="primary">导入文件</el-button>
                          <span slot="tip" class="el-upload__tip">只能上传txt格式文件，且不超过1M</span>
                      </el-upload>
                      <div class="uploadTxtCallback">{{ uploadTxtCallbackInfo }}</div>
                  </el-col>
                </el-form-item>
            </el-row>
            <div class="buildBox">
              <div class="buildNameBox">
                <el-row v-for=" item in searchCompanyData"  :key="item.guid" class="buildNameText" >
                  <el-col :span="20">
                    <span>{{item.data}}</span>
                  </el-col>
                  <el-col :span="4">
                  <i class="el-icon-delete" @click="deleteCompanyList(item.guid)"></i>
                  </el-col>
                </el-row>
              </div>
            </div>

            <div class="div_center">
                <div>
                  <span class="tip_red" v-if="!isClickSaveData">您尚未保存数据</span>
                  <span class="tip_green" v-if="isClickSaveData">您已经保存以上数据</span>
                </div>
                <el-button type="primary" plain @click="saveConditionData" class="saveData">保存</el-button>
          </div>
        </div>
</template>
<script>
import axios from 'axios'
/* eslint-disable */
  export default
  {
    // 验证
    props:{
      conditionData:{type:Object,default:function(){
        return {}
      }},
      renderFlag:{type:Boolean,default:false},
      renderData:null,logicRelationFlag:{type:Boolean,default:false},
    },
    data()
    {
      return {
        files: [],//上传文件
        uploadActionUrl: rootCoffeeStieUrl+"/task-api/verify-file.html",
        uploadData: {
          dataType: "2",
          oldFilePath:""
        },
        uploadFileName:"",
        submitFormUrl: rootCoffeeStieUrl+"/task-api/coupon-send-task-create.html",
        uploadTxtCallbackInfo: '',
        companyForm:{
          companyName:"",//公司名称
        },
        searchCompanyText:"",//搜索公司名称
        companyNameList: [],
        companyNameListData:[],//楼宇添加列表
        myConditionData:this.conditionData,//接收父组件数据---巧----
        companyRules: {
          companyName: [
            { required: true, message: '请输入公司名称', trigger: 'click' }//楼宇名称验证
          ]
        },
        isClickSaveData:false,//是否保存数据
        myRenderFlag:this.renderFlag,
        myRenderData:this.renderData,
        myLogicRelationFlag:this.logicRelationFlag,
        companyPath:"",
        autoCompleteList:rootData.companyList,//自动补全楼宇列表
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
        console.log("list",this.autoCompleteList)
        if(this.myRenderFlag) this.render();
      },
      //渲染页面数据
      render(){
        this.companyNameList = [];
        var data = this.myRenderData.company_name_list;
        for(let i=0;i<data.length;i++){
          this.companyNameList.push({data:data[i],guid:this.guid()});
          this.companyNameListData.push(data[i]);
        }
        console.log("companyNameList..",this.companyNameList);
        //渲染上传文件
        let txtUrl = this.myRenderData.company_name_path||"";
        let urlTxtName = txtUrl!=""?txtUrl.split("/").pop():"";
        this.files = [{name:urlTxtName,url:txtUrl}];
        this.uploadData.oldFilePath = txtUrl;
        this.companyPath=txtUrl;
      },
      //添加公司名称列表
      addCompanyList(){
         this.$refs["formCompanyName"].validate((valid) => {
          if (valid) {
            var companyText = this.companyForm.companyName;
            if(!this.companyNameListData.find((com)=>com==companyText)){
              this.addCompanyListAction();
            } else {
              this.alertMsg("请勿重复添加公司");
            }
          } else {
            return false;
          }
        });
      },
      addCompanyListAction(){
        var  _this=this;
        var params = new URLSearchParams();
        params.append("check_type",3);
        params.append("mobile","");
        params.append("build_name","");
        params.append("company_name",this.companyForm.companyName);
        axios.post('/user-selection-task/check-legal',params
        ).then(function (response) {
          if(response.data){
            if(!_this.companyNameListData.find((company)=>company==_this.companyForm.companyName)){
                   var myGuid = _this.guid();
                  _this.companyNameList.push({data:_this.companyForm.companyName,guid:myGuid});
                  _this.companyNameListData.push(_this.companyForm.companyName);
                  _this.companyForm.companyName="";//添加完置空
                }else {
                  _this.alertMsg("请勿重复添加公司");
            }
            _this.changeSaveStatus(false);
          }else{
             _this.alertMsg("添加公司失败");
          }
        })
        .catch(function (error) {
          _this.alertMsg(error);
        });
      },
      //清空公司名称列表
      clearCompanyNameList(){
        this.companyNameList=[];
        this.companyNameListData=[];
        this.companyForm.companyName="";
        this.changeSaveStatus(false);
      },
      //删除对应公司列表
      deleteCompanyList(guidData){
        var guidIndex;
        this.companyNameList.findIndex(function(value, index, arr) {
          if(value.guid==guidData){
                guidIndex=index;
          }
        })
        this.companyNameList.splice(guidIndex,1);
        this.companyNameListData.splice(guidIndex,1);
        this.changeSaveStatus(false);
      },
      //保存条件数据
      saveConditionData(){
        console.log("导入文件的路径",this.companyPath);
        if(this.companyPath==""&&this.companyNameListData.length==0){
          this.alertMsg("请添加公司名称");
        }else{
            this.myConditionData.logic_condition={
              "condition":{
                  "company_name_list":this.companyNameListData,
                  "company_name_path":this.companyPath
              }
            }
             //验证逻辑类型
            if(this.myLogicRelationFlag){
              this.changeSaveStatus(true);
            }else{
              this.$emit('validForm');//触发上级逻辑类型验证
            }
        }
      },
      //自动补齐公司名称
      querySearch(queryString,cb) {
        var restaurants = this.autoCompleteList;
        var results = queryString ? restaurants.filter(this.createFilter(queryString)) : restaurants;
        // 调用 callback 返回建议列表的数据
        cb(results)
      },
      createFilter(queryString) {
        return (restaurant) => {
          return (restaurant.value.toLowerCase().indexOf(queryString.toLowerCase()) !== -1);
        }
      },
      // 导入文件
      onBeforeUpload(file)
      {
        const isText = file.type === 'text/plain';
        const isLt1M = file.size / 1024 / 1024 < 1;
        if (!isText) {
          this.alertMsg('上传文件类型只能是txt格式!');
        }
        if (!isLt1M) {
          this.alertMsg('上传文件大小不能超过 1MB!');
        }
        return isText && isLt1M;
      },
      // 上传成功后的回调
      uploadSuccess (response, file, fileList)
      {
        var data = response.data||{};
        var noExistsList = data.noExistsList||"";
        var filePath = data.filePath||"";
        this.companyPath = filePath;
        this.uploadTxtCallbackInfo = noExistsList;
        this.changeSaveStatus(false);
      },
      handleExceed(files, fileList)
      {
        this.alertMsg('请删除当前文件后再上传新的文件','warning');
      },
      onRemoveTxt(files, fileList)
      {
        console.log("remove txt");
        this.uploadData.oldFilePath = "";
        this.companyPath = "";
        this.changeSaveStatus(false);
      },
      // 上传错误
      uploadError (response, file, fileList)
      {
        this.alertMsg('上传失败，请重试！');
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
    },
    computed:{
      //搜索公司名称列表
       searchCompanyData: function() {
          var search = this.searchCompanyText;//获取搜索内容
          if (search) {
              return this.companyNameList.filter(function(item) {
                  return Object.keys(item).some(function(key) {
                      return String(item[key]).toLowerCase().indexOf(search) > -1
                  })
              })
          }
          return this.companyNameList;
      }
    }
  }
</script>
<style>
/*消费楼宇、点位、公司导入样式相同*/
.buildBox{
  width:81%;
  overflow: hidden;
  margin-left:10%;
  border:1px solid #b3d8ff;
  border-radius:5px;
  margin-bottom: 20px;
}
.buildNameBox{
  width:102%;
  height: 300px;
  overflow: hidden;
  overflow-y:scroll;
  min-height: 90px;
}
  .buildNameText{

    padding:0 10px;
    height:30px;
    line-height: 30px;
  }
  .buildNameText+.buildNameText{
    border-top:1px solid #b3d8ff;
  }
  .buildNameText:nth-child(2n){
     background: #ecf5ff;
  }
    .tip_red{
    color:red;
  }
  .saveData{
    margin-top:20px;
  }
</style>
