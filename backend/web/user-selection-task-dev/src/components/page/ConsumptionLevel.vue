<!-- 消费商品-->
<template>
    <div class="content-body">
      <el-form ref="form" :model="form" label-width="100px" :rules="rules">
        <div>
            <el-row :gutter="10">
              <el-form-item label="消费时间：" prop="accountTime">
                <el-date-picker  type="datetimerange" @change="changeSaveData" range-separator="至" start-placeholder="开始日期"  end-placeholder="结束日期" v-model="form.accountTime" format="yyyy-MM-dd HH:mm:ss" value-format="yyyy-MM-dd HH:mm:ss">
                </el-date-picker>
              </el-form-item>
            </el-row>
            <el-row :gutter="10">
                <el-form-item label="消费次数：" prop="recentConsumeNum">
                  <el-input v-model="form.recentConsumeNum" style="width:35.5%" @change="changeSaveData"></el-input>
                </el-form-item>
            </el-row>
        </div>
        <div class="conditionType">
            <div class="searchType" v-show="isActive">
                <el-row :gutter="10">
                 <el-form-item label="选择城市：">
                    <el-checkbox  @change="handleCheckAllChange" v-model="checkAllCity" :indeterminate="isIndeterminate" >全选</el-checkbox>
                    <el-checkbox-group  v-model="checkCityList" @change="handleCheckedCitiesChange">
                        <el-checkbox  v-for="city in cities" :label="city.key" :key="city.key" @change="changeSaveData">{{ city.name }}</el-checkbox>
                    </el-checkbox-group>
                  </el-form-item>
                </el-row>
                <el-row :gutter="10">
                     <el-form-item label="渠道类型：" >
                        <el-checkbox  @change="handleCheckAllChannelChange" v-model="checkAllChannel" :indeterminate="isIndeterminateChannel" >全选</el-checkbox>
                        <el-checkbox-group v-model="checkChannelList" @change="handleCheckedChannelChange">
                            <el-checkbox v-for="channel in ChannelTypes" :label="channel.key" :key="channel.key" @change="changeSaveData">{{ channel.name }}</el-checkbox>
                        </el-checkbox-group>
                      </el-form-item>
                </el-row>
                <el-row :gutter="10">
                     <el-form-item label="设备类型：" >
                        <el-checkbox  @change="handleCheckAllDeviceChange" v-model="checkAllDevice" :indeterminate="isIndeterminateDevice" >全选</el-checkbox>
                        <el-checkbox-group v-model="checkDevicelList" @change="handleCheckedDeviceChange">
                          <el-checkbox v-for="device in deviceTypes" :label="device.key" :key="device.key" @change="changeSaveData">{{ device.name }}</el-checkbox>
                        </el-checkbox-group>
                    </el-form-item>
                </el-row>
            </div>
            <div class="searchList" v-show="!isActive"> <!-- 搜索列表 -->
                   <el-row :gutter="10">
                     <el-form-item label="楼宇列表：">
                         <el-checkbox  @change="handleCheckAllBuildChange" v-model="checkAllBuild" :indeterminate="isIndeterminateBuild">全选</el-checkbox>
                        <el-checkbox-group v-model="checkBuildList" @change="handleCheckedBuildChange">
                            <el-checkbox v-for="build in hasSearchBuildList" :label="build" :key="build"></el-checkbox>
                        </el-checkbox-group>
                      </el-form-item>
                    </el-row>
            </div>
        </div>
        <div class="div_center">
            <el-button type="primary" icon="el-icon-search" @click="searchCitiesChange" v-show="isActive">筛选楼宇列表</el-button>
            <el-button type="primary" icon="el-icon-search" @click="searchCitiesAgain" v-show="!isActive">继续筛选列表</el-button>
            <el-button type="primary" icon="el-icon-circle-plus-outline" v-show="!isActive" @click="addSearchBuildList">加入列表</el-button>
        </div>
        <!-- 消费楼宇 -->
        <div>
            <el-row>
              <el-col :span="16">
                <el-form ref="formBuildText" :model="buildForm" :rules="buildRules" label-width="100px">
                  <el-form-item label="消费楼宇：" prop="buildName">
                    <el-autocomplete
                          class="inline-input"
                          v-model.trim="buildForm.buildName"
                          :fetch-suggestions="querySearch"
                          placeholder="请输入内容"
                        style="width:350px">
                      </el-autocomplete>
                  </el-form-item>
                </el-form>
              </el-col>
              <el-col :span="4">
                <el-button  icon="el-icon-circle-plus-outline" class="btnAdd" @click="addBuildList">添加</el-button>
              </el-col>
            </el-row>
            <el-row>
                <el-form-item>
                   <el-col :span="4">
                    <el-button plain icon="el-icon-delete" @click="clearConBuildNameList">清空</el-button>
                  </el-col>
                  <el-col :span="6">
                    <el-input placeholder="请输入内容" prefix-icon="el-icon-search" v-model.trim="searchConsumText"></el-input>
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
                <el-row v-for=" item in searchConsumData" :key="item.guid"  class="buildNameText" >
                  <el-col :span="20">
                    <span>{{item.data}}</span>
                  </el-col>
                  <el-col :span="4">
                  <i class="el-icon-delete" @click="deleteConBuildList(item.guid)"></i>
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
      </el-form>
    </div>
</template>
<script>
/* eslint-disable */
import axios  from  'axios'
  export default
  {
    // 验证
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
            callback(new Error('数字格式不对,请填写正整数'));
          } else {
            callback();
          }
        }, 200);
      };
      return {
        files: [],//上传文件
        uploadActionUrl: rootCoffeeStieUrl+"/task-api/verify-file.html",
        uploadData: {
          dataType: "1",
          oldFilePath:""
        },
        uploadFileName:"",
        submitFormUrl: rootCoffeeStieUrl+"/task-api/coupon-send-task-create.html",
        uploadTxtCallbackInfo: '',
        conditionTemp:"",//条件类型对应数据
        buildForm:{buildName:''},//消费楼宇名称
        checkCityList: [],//城市列表
        checkChannelList:[],//渠道类型列表
        checkDevicelList:[],//设备类型列表
        hasSearchBuildList:'',//搜索楼宇列表
        checkBuildList:[],//选中的搜索楼宇列表
        SearchCityList:"",//楼宇筛选
        isActive:true,
        cities:[],//初始化城市列表
        ChannelTypes:[],//初始化渠道类型
        deviceTypes:[],//初始化设备类型
        form:{
          recentConsumeNum:"",//消费次数
          accountTime:"",//消费时间
        },
        conBuildNameList: [],
        conBuildNameListData:[],//楼宇添加列表
        searchConsumText:"",//搜索消费楼宇内容
        conditionTypeList:rootData.conditionTypeList,//条件类型
        buildRules: {
          buildName: [
            { required: false, message: '请输入消费楼宇', trigger: 'click' }//楼宇名称验证
          ]
        },
        rules: {
            accountTime: [
              { required: true, message: '请选择日期'}
            ],
            recentConsumeNum:[{required:false, validator: checkTimesNum}]
        },
         accountTimePickerOption: {//设置选取日期时间规则
          disabledDate(time){
            return time.getTime() < (Date.now()-3600*1000*24);
          }
        },
        myConditionData:this.conditionData,//接收父组件数据---巧----
        isClickSaveData:false,//是否保存数据
        myRenderFlag:this.renderFlag,
        myRenderData:this.renderData,
        myLogicRelationFlag:this.logicRelationFlag,
        buildPath:"",
        checkAllCity:false,//是否全选城市
        checkCitiesKey:[],//用来判断是否全选城市
        isIndeterminate: true,//标识城市是否全选的状态
        checkAllChannel:false,//是否全选渠道类型
        checkChannelKey:[],//用来判断是否全选渠道类型
        isIndeterminateChannel: true,//标识渠道是否全选的状态
        checkAllDevice:false,//是否全选设备类型
        checkDeviceKey:[],//用来判断是否全选设备类型
        isIndeterminateDevice: true,//标识设备类型是否全选的状态
        checkAllBuild:false,//是否全选筛选楼宇
        checkBuildKey:[],//用来判断是否全选筛选的楼宇
        isIndeterminateBuild: true,//标识筛选楼宇是否全选的状态
        autoCompleteList:rootData.buildingList,//自动补全公司列表
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
        for(var i in rootData.addConditionTypeTemp){
          if(this.conditionKey==i){
            this.conditionTemp=rootData.addConditionTypeTemp[i];
          }
        }
        //获取到对应模板数据后，显示初始值
        this.cities=[];//城市列表
        this.ChannelTypes=[];//渠道类型
        this.deviceTypes=[];//设备类型
        //城市列表
        for(let i in this.conditionTemp.cityList){
          this.cities.push({key:i,name:this.conditionTemp.cityList[i]});
        }
        for(let j in this.conditionTemp.buildTypeList){
          this.ChannelTypes.push({key:j,name:this.conditionTemp.buildTypeList[j]});
        }
        for(let y in this.conditionTemp.equipTypeList){
          this.deviceTypes.push({key:y,name:this.conditionTemp.equipTypeList[y]});
        }
        if(this.myRenderFlag) this.render();//编辑的话，进行渲染
      },
      //页面初始渲染数据
      render(){
        this.form.accountTime=[];//初始化消费时间
        this.conBuildNameList=[];//初始化消费楼宇列表
        this.checkCityList=[];//初始化选中城市列表
        this.checkDevicelList=[];//初始化选中设备类型
        this.checkChannelList=[];//初始化选中渠道类型
        var data=this.myRenderData;
        this.form.accountTime=[data.start_time,data.end_time];//消费时间
        this.form.recentConsumeNum=data.recent_consume_num;//消费次数
        for(let j in data.city){//选中城市列表
          this.checkCityList.push(data.city[j]);
        }
        for(let y in data.build_type){//选中渠道类型
          this.checkChannelList.push(data.build_type[y]);
        }
        for(let z in data.equip_type){//选中设备类型
          this.checkDevicelList.push(data.equip_type[z]);
        }
        for(let i=0;i<data.build_list.length;i++){//楼宇列表
          this.conBuildNameList.push({data:data.build_list[i],guid:this.guid()});
          this.conBuildNameListData.push(data.build_list[i]);
        }
         //渲染上传文件
        let txtUrl = this.myRenderData.build_name_path||"";
        let urlTxtName = txtUrl!=""?txtUrl.split("/").pop():"";
        this.files = [{name:urlTxtName,url:txtUrl}];
        this.uploadData.oldFilePath = txtUrl;
        this.buildPath=txtUrl;

      },
      //保存后，继续修改数据
      changeSaveData(){
        this.changeSaveStatus(false);
      },
      //筛选楼宇列表
      searchCitiesChange(){
        if(this.checkCityList.length==0&&this.checkChannelList.length==0&&this.checkDevicelList.length==0){
          this.alertMsg("请选择筛选条件");
          return;
        }else{
          this.isActive=false;
          var params={
            "city":this.checkCityList,"build_type":this.checkChannelList,"equip_type":this.checkDevicelList
          }
          var _this=this;
          axios.post('/user-selection-task/get-build-level-build-list',params,{
              headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
              }}).then(function (response) {
              if(response.data){
                _this.hasSearchBuildList=response.data;
              }
            })
            .catch(function (error) {
              _this.alertMsg(error);
            });
          }
      },
      //继续筛选
      searchCitiesAgain(){
        this.isActive=true;
      },
      //加入消费楼宇列表
      addBuildList(){
         // this.$refs["formBuildText"].validate((valid) => {
          // if (valid) {
          //   let buildText = this.buildForm.buildName;
          //   if(!this.conBuildNameListData.find((build)=>build==buildText)){
          //     this.addBuildListAction();
          //   } else {
          //     this.alertMsg("请勿重复添加楼宇");
          //   }
          // } else {
          //   if(this.buildForm.buildName==""){
          //     this.alertMsg("请输入消费楼宇");
          //   }else{
          //     this.alertMsg("消费楼宇");
          //   }
          //   return false;
          // }
          // this.changeSaveStatus(false);
        // });
        let buildText = this.buildForm.buildName;
        if(buildText==""){
          this.alertMsg("请输入消费楼宇");
        } else {
          if(!this.conBuildNameListData.find((build)=>build==buildText)){
            this.addBuildListAction();
          } else {
            this.alertMsg("请勿重复添加楼宇");
          }
        }
      },
      addBuildListAction(){
        var  _this=this;
        var params = new URLSearchParams();
          params.append("check_type",2);
          params.append("mobile","");
          params.append("build_name",this.buildForm.buildName);
          params.append("company_name","");
          axios.post('/user-selection-task/check-legal',params
          ).then(function (response) {
            console.log("后台传来的值",response.data);
            if(response.data){
              if(!_this.conBuildNameListData.find((build)=>build==_this.buildForm.buildName)){
                   var myGuid = _this.guid();
                  _this.conBuildNameList.push({data:_this.buildForm.buildName,guid:myGuid});
                  _this.conBuildNameListData.push(_this.buildForm.buildName);
                  _this.buildForm.buildName="";//添加完置空
                }else {
                  _this.alertMsg("请勿重复添加楼宇");
              }
              this.changeSaveStatus(false);
            }else{
              _this.alertMsg("添加消费楼宇失败");
            }
          })
          .catch(function (error) {
             _this.alertMsg(error);
          });
      },
      //删除消费楼宇列表
      deleteConBuildList(guidData){
          var guidIndex;
          this.conBuildNameList.findIndex(function(value, index, arr) {
            if(value.guid==guidData){
                  guidIndex=index;
            }
          })
          this.conBuildNameList.splice(guidIndex,1);
          this.conBuildNameListData.splice(guidIndex,1);
          this.changeSaveStatus(false);
      },
      //清空消费楼宇列表
      clearConBuildNameList(){
        this.conBuildNameList=[];
        this.conBuildNameListData=[];
        this.buildForm.buildName="";
        this.changeSaveStatus(false);
      },
      //将搜索的楼宇添加到列表中(并判重)
      addSearchBuildList(){
        var buildList=this.checkBuildList;
        // var len = this.conBuildNameList.length;
        if(buildList.length>0){
            for(var i=0; i<buildList.length; i++){
                 if(!this.conBuildNameListData.find((build)=>build==buildList[i])){
                     var myGuid = this.guid();
                      this.conBuildNameList.push({data:buildList[i],guid:myGuid});
                      this.conBuildNameListData.push(buildList[i]);
                  }else {
                    this.alertMsg("请勿重复添加列表");
                  }
              }
              this.checkBuildList=[];//添加完后将搜索列表选中置空
              this.changeSaveStatus(false);
          }else{
             this.alertMsg("请选择要加入的楼宇名称");
          }
      },
      saveConditionData(){//保存条件数据
            this.$refs["form"].validate((valid) => {
              if (valid) {
                console.log("导入文件的路径",this.buildPath);
               if(this.buildPath==""&&this.conBuildNameListData.length==0){
                  this.alertMsg("请添加消费楼宇");
                }else{
                    this.myConditionData.logic_condition={
                        "condition":{
                          "start_time":this.form.accountTime[0],//开始时间
                          "end_time":this.form.accountTime[1],//结束时间
                          "city":this.checkCityList,//城市列表
                          "build_type":this.checkChannelList,//渠道列表
                          "equip_type":this.checkDevicelList,//设备列表
                          "build_list":this.conBuildNameListData,//消费楼宇列表
                          "build_name_path":this.buildPath,//上传文件地址
                          "recent_consume_num":this.form.recentConsumeNum
                        }
                      }
                     //验证逻辑类型
                    if(this.myLogicRelationFlag){
                      this.changeSaveStatus(true);
                    }else{
                      this.$emit('validForm');//触发上级逻辑类型验证
                    }
                }
            }else{
              this.alertMsg("验证不通过");
            }
            })//验证消费次数格式
      },
      //生成guid ,解决排序
      guid()
      {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
            return v.toString(16);
        });
      },
      //自动补齐楼宇列表
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
      handleExceed(files, fileList)
      {
        this.alertMsg('请删除当前文件后再上传新的文件','warning');
      },
      onRemoveTxt(files, fileList)
      {
        console.log("remove txt");
        this.uploadData.oldFilePath = "";
        this.buildPath = "";
        this.changeSaveStatus(false);
      },
      // 上传成功后的回调
      uploadSuccess (response, file, fileList)
      {
        var data = response.data||{};
        var noExistsList = data.noExistsList||"";
        var filePath = data.filePath||"";
        this.buildPath = filePath;
        this.uploadTxtCallbackInfo = noExistsList;
        this.changeSaveStatus(false);
      },
      // 上传错误
      uploadError (response, file, fileList)
      {
        this.alertMsg('上传失败，请重试！');
        this.changeSaveStatus(false);
        this.changeSaveStatus(false);
      },
      //设置保存状态
      changeSaveStatus(flag)
      {
        this.isClickSaveData=flag;//设置保存状态
        this.$emit('sendissave',this.isClickSaveData);
      },
       checkTimesNum(value){//验证消费次数
          var myreg=/^[0-9]*[1-9][0-9]*$/;
          if(value==""){
            return true;
          }else if(value!=""&&!myreg.test(value)){
            return false;
          }else{
            return true;
          }
      },
      //选择城市全选
      handleCheckedCitiesChange(value) {
        let checkedCount = value.length;
        var cityLength=Object.keys(this.cities).length;
        this.checkAllCity = checkedCount === cityLength;
        this.isIndeterminate = checkedCount > 0 && checkedCount < this.cities.length;
      },
      handleCheckAllChange(val){
        this.checkCitiesKey=[];
        for(let i in this.cities){
          this.checkCitiesKey.push(this.cities[i].key);
        }
        this.checkCityList = val ? this.checkCitiesKey : [];
        this.isIndeterminate =false
        console.log(this.checkCityList);
      },
      //渠道类型全选
      handleCheckedChannelChange(value) {
        let checkedCount = value.length;
        var channelLength=Object.keys(this.ChannelTypes).length;
        this.checkAllChannel = checkedCount === channelLength;
        this.isIndeterminateChannel = checkedCount > 0 && checkedCount < this.ChannelTypes.length;
      },
      handleCheckAllChannelChange(val){
        this.checkChannelKey=[];
        for(let i in this.ChannelTypes){
          this.checkChannelKey.push(this.ChannelTypes[i].key);
        }
        this.checkChannelList = val ? this.checkChannelKey : [];
        this.isIndeterminateChannel =false
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
        this.checkDevicelList = val ? this.checkDeviceKey : [];
        this.isIndeterminateDevice =false;
      },
      //筛选楼宇全选
      handleCheckedBuildChange(value) {
        let checkedCount = value.length;
        var buildLength=Object.keys(this.hasSearchBuildList).length;
        this.checkAllBuild = checkedCount === buildLength;
        this.isIndeterminateBuild= checkedCount > 0 && checkedCount < this.hasSearchBuildList.length;
      },
      handleCheckAllBuildChange(val){
        this.checkBuildKey=[];
        for(let i in this.hasSearchBuildList){
          this.checkBuildKey.push(this.hasSearchBuildList[i]);
        }
        this.checkBuildList = val ? this.checkBuildKey : [];
        this.isIndeterminateBuild =false;
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
      // 搜索消费楼宇列表
      searchConsumData: function() {
          var search = this.searchConsumText;
          if (search) {
              return this.conBuildNameList.filter(function(product) {
                  return Object.keys(product).some(function(key) {
                      return String(product[key]).toLowerCase().indexOf(search) > -1
                  })
              })
          }
          return this.conBuildNameList;
        }
    }
  }
</script>
<style>
/*消费楼宇和楼宇点位相同*/
.buildBox{
  width:81%;
  overflow: hidden;
  border:1px solid #b3d8ff;
  border-radius:5px;
  margin-bottom: 20px;
  margin-left:10%;
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
.tip_green{
  color:green;
}
.saveData{
  margin-top:20px;
}
</style>
