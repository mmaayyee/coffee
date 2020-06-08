<!-- 消费城市 -->
<template>
    <div class="content-body">
            <el-row>
              <el-col :span="12">
                <el-form ref="formPhoneText" :model="phoneForm" :rules="phoneRules" label-width="100px">
                  <el-form-item label="手机号码：" prop="phoneNumber">
                    <el-input v-model="phoneForm.phoneNumber"></el-input>
                  </el-form-item>
                </el-form>
              </el-col>
              <el-col :span="4">
                <el-button  icon="el-icon-circle-plus-outline" class="btnAdd" @click="addPhoneNumberList">添加</el-button>
              </el-col>
            </el-row>
            <el-row>
                <el-form-item>
                   <el-col :span="4">
                      <el-button plain icon="el-icon-delete" @click="clearPhoneNumberList">清空</el-button>
                  </el-col>
                  <el-col :span="6">
                    <el-input placeholder="请输入内容" prefix-icon="el-icon-search" v-model.trim="searchPhoneText"></el-input>
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
            <div class="buildNameBoxPhone">
                <el-row v-for="item in searchPhoneNumberData"  :key="item.guid"  class="buildNameText" >
                <el-col :span="20">
                  <span>{{item.data}}</span>
                </el-col>
                <el-col :span="4">
                <i class="el-icon-delete" @click="deletePhoneNumberList(item.guid)"></i>
                </el-col>
              </el-row>
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
        files: [],//上传文件
        uploadActionUrl: rootCoffeeStieUrl+"/task-api/verify-file.html",
        uploadData: {
          dataType: "0",
          oldFilePath:""
        },
        uploadFileName:"",
        submitFormUrl: rootCoffeeStieUrl+"/task-api/coupon-send-task-create.html",
        uploadTxtCallbackInfo: '',
        phoneForm:{
          phoneNumber:""//手机号码
        },
        searchPhoneText:"",//搜索手机号码
        phoneNumberList: [],
        phoneNumberListData:[],//楼宇添加列表
        phoneRules: {
          phoneNumber: [
            { validator: checkPhoneNum, trigger: 'click' }//手机号验证
          ]
        },
        myConditionData:this.conditionData,//接收父组件数据---巧----
        isClickSaveData:false,//是否保存数据
        myRenderFlag:this.renderFlag,
        myRenderData:this.renderData,
        myLogicRelationFlag:this.logicRelationFlag,
        phonePath:""
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
         if(this.myRenderFlag) this.render();
      },
      //渲染页面数据
      render(){
        this.phoneNumberList = [];
        var data = this.myRenderData.user_mobiles;
        for(let i=0;i<data.length;i++){
          this.phoneNumberList.push({data:data[i],guid:this.guid()});
          this.phoneNumberListData.push(data[i]);
        }
        //渲染上传文件
        let txtUrl = this.myRenderData.user_mobiles_path||"";
        let urlTxtName = txtUrl!=""?txtUrl.split("/").pop():"";
        console.log(txtUrl+urlTxtName);
        this.files = [{name:urlTxtName,url:txtUrl}];
        this.uploadData.oldFilePath = txtUrl;
        this.phonePath=txtUrl;
      },
      //添加用户列表
      addPhoneNumberList(){
        this.$refs["formPhoneText"].validate((valid) => {
          if (valid) {
            var phoneText = this.phoneForm.phoneNumber;
            if(!this.phoneNumberListData.find((com)=>com==phoneText)){
              this.addPhoneNumberListAction();
            } else {
              this.alertMsg("请勿重复导入用户");
            }
          } else {
            console.log("phone wrong..");
            return false;
          }
        });
      },
      addPhoneNumberListAction(){
        var  _this=this;
        var params = new URLSearchParams();
        params.append("check_type",1);
        params.append("mobile",this.phoneForm.phoneNumber);
        params.append("build_name","");
        params.append("company_name","");
        axios.post('/user-selection-task/check-legal',params
        ).then(function (response) {
          if(response.data){
              if(!_this.phoneNumberListData.find((phone)=>phone==_this.phoneForm.phoneNumber)){
                       var myGuid = _this.guid();
                      _this.phoneNumberList.push({data:_this.phoneForm.phoneNumber,guid:myGuid});
                      _this.phoneNumberListData.push(_this.phoneForm.phoneNumber);
                      _this.phoneForm.phoneNumber="";//添加完置空
                    }else {
                      _this.alertMsg("请勿重复导入用户");
                }

          }else{
            _this.alertMsg("添加手机号失败");
          }
        })
        .catch(function (error) {
          _this.alertMsg(error);
        });
      },
      //清空用户列表
      clearPhoneNumberList(){
        this.phoneNumberList=[];
        this.phoneNumberListData=[];
        this.phoneForm.phoneNumber="";
        this.changeSaveStatus(false);
      },
      //删除对应用户
      deletePhoneNumberList(guidData){
        var guidIndex;
        this.phoneNumberList.findIndex(function(value, index, arr) {
          if(value.guid==guidData){
                guidIndex=index;
          }
        })
        this.phoneNumberList.splice(guidIndex,1);
        this.phoneNumberListData.splice(guidIndex,1);
        this.changeSaveStatus(false);
      },
      //保存条件数据
      saveConditionData(){
        console.log("上传文件路径",this.phonePath);
        if(this.phonePath==""&&this.phoneNumberListData.length==0){
          this.alertMsg("请添加手机号");
        }else{
            this.myConditionData.logic_condition={
                 "condition":{
                    "user_mobiles":this.phoneNumberListData,
                    "user_mobiles_path":this.phonePath
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
        console.log("上传文件成功后的路径",filePath);
        this.phonePath = filePath;
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
        this.phonePath = "";
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
      //搜索用户列表
       searchPhoneNumberData: function() {
                var search = this.searchPhoneText;//获取搜索内容
                if (search) {
                    return this.phoneNumberList.filter(function(product) {
                        return Object.keys(product).some(function(key) {
                            return String(product[key]).toLowerCase().indexOf(search) > -1
                        })
                    })
                }
                return this.phoneNumberList;
        }
    }
  }
</script>
<style>
  /*消费楼宇、点位、公司导入样式相同*/
.buildNameBoxPhone{
  width:80%;
  margin-left:8%;
  border:1px solid #b3d8ff;
  min-height: 90px;
  border-radius:5px;
  margin-bottom: 20px;
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
