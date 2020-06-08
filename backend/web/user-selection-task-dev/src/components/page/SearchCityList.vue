<!-- 楼宇筛选 -->
<template>
    <div class="content-body">
         <div class="conditionType">
            <el-row><h4>楼宇筛选</h4></el-row>
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
                     <el-form-item label="渠道类型：">
                        <el-checkbox  @change="handleCheckAllChannelChange" v-model="checkAllChannel" :indeterminate="isIndeterminateChannel" >全选</el-checkbox>
                        <el-checkbox-group v-model="checkChannelList" @change="handleCheckedChannelChange">
                            <el-checkbox v-for="channel in ChannelTypes" :label="channel.key" :key="channel.key" @change="changeSaveData">{{ channel.name }}</el-checkbox>
                        </el-checkbox-group>
                      </el-form-item>
                </el-row>
                <el-row :gutter="10">
                     <el-form-item label="设备类型：">
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
                        <el-checkbox  @change="handleCheckAllBuildChange" v-model="checkAllBuild" :indeterminate="isIndeterminateBuild" >全选</el-checkbox>
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
            <el-button type="primary" icon="el-icon-circle-plus-outline"  v-show="!isActive" @click="addSearchBuildList">加入列表</el-button>
        </div>
        <!-- 添加楼宇 -->
        <div>
            <el-row>
              <el-col :span="16">
                <el-form-item label="楼宇名称：">
                  <el-autocomplete
                          class="inline-input"
                          v-model.trim="buildingName"
                          :fetch-suggestions="querySearch"
                          placeholder="请输入内容"
                        style="width:350px">
                  </el-autocomplete>
                </el-form-item>
              </el-col>
              <el-col :span="4">
                <el-button  icon="el-icon-circle-plus-outline" class="btnAdd" @click="addCityList">添加</el-button>
              </el-col>
            </el-row>
            <el-row>
                <el-form-item>
                   <el-col :span="4"><el-button plain icon="el-icon-delete" @click="clearBuildNameList">清空</el-button>
                  </el-col>
                  <el-col :span="6">
                    <el-input placeholder="请输入内容" prefix-icon="el-icon-search" v-model.trim="searchText"></el-input>
                  </el-col>
                  <el-col :span="10">
                    <el-upload class="upload-file"
                      name="uploadPhoneTxt"
                      :action="uploadActionUrl"
                      :data="uploadData"
                      :onError="uploadError"
                      :on-remove="onRemoveTxt"
                      :onSuccess="uploadSuccess"
                      :before-upload="onBeforeUpload"
                      :on-exceed="handleExceed"
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
                <el-row v-for="item in searchData"  :key="item.guid"  class="buildNameText" >
                  <el-col :span="20">
                    <span>{{item.data}}</span>
                  </el-col>
                  <el-col :span="4">
                  <i class="el-icon-delete" @click="deleteBuildList(item.guid)"></i>
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
    </div>
</template>
<script>
/* eslint-disable */
import axios  from  'axios'
import Qs from 'qs'
  export default
  {
    // 验证
    props:{
      conditionKey:{type:String},conditionData:{type:Object,default:function(){
        return {}
      }},
      renderFlag:{type:Boolean,default:false},
      logicRelationFlag:{type:Boolean,default:false},
      renderData:null,logicRelationFlag:{type:Boolean,default:false}
    },
    data()
    {
      return {
        files: [],//上传文件
        uploadActionUrl: rootCoffeeStieUrl+"/task-api/verify-file.html",
        uploadData: {// 上传楼宇txt文件参数，oldFilePath编辑时页面获取，提交时带上参数
          dataType: "1",
          oldFilePath:""
        },
        uploadFileName:"",
        submitFormUrl: rootCoffeeStieUrl+"/task-api/coupon-send-task-create.html",
        conditionTemp:'',//根据条件类型得出对应模板数据
        buildingName:'',
        checkCityList: [],//城市列表
        checkChannelList:[],//渠道类型列表
        checkDevicelList:[],//设备类型列表
        SearchCityList:"",//楼宇筛选
        isActive:true,
        cities: [],
        ChannelTypes:[],
        deviceTypes:[],
        buildNameList: [],
        buildNameListData:[],//楼宇添加列表
        searchText:"",//搜索楼宇内容
        myConditionKey: this.conditionKey,
        uploadTxtCallbackInfo: '',
        hasSearchBuildList:'',//搜索楼宇列表
        checkBuildList:[],//选中的搜索楼宇列表
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
        for(let i in this.conditionTemp.cityList){
          this.cities.push({'key':i,'name':this.conditionTemp.cityList[i]});
        }
        for(let i in this.conditionTemp.buildTypeList){
          this.ChannelTypes.push({'key':i,'name':this.conditionTemp.buildTypeList[i]});
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
        this.checkCityList = data.city;
        this.checkChannelList = data.build_type;
        this.checkDevicelList = data.equip_type;

        for(let i=0;i<data.build_list.length;i++){
          this.buildNameList.push({data:data.build_list[i],guid:this.guid()});
          this.buildNameListData.push(data.build_list[i]);
        }
        //渲染上传文件
        let txtUrl = this.myRenderData.build_name_path||"";
        let urlTxtName = txtUrl!=""?txtUrl.split("/").pop():"";
        this.files = [{name:urlTxtName,url:txtUrl}];
        this.uploadData.oldFilePath = txtUrl;
        this.buildPath = txtUrl;
      },
      //筛选城市列表
      searchCitiesChange(){
        if(this.checkCityList.length==0&&this.checkChannelList.length==0&&this.checkDevicelList.length==0){
          this.alertMsg("请选择筛选条件");
          return;
        }else{
          var _this=this;
          var params={
            "city":this.checkCityList,"build_type":this.checkChannelList,"equip_type":this.checkDevicelList
          }
          console.log("筛选楼宇前传递的参数",params);
          axios.post('/user-selection-task/get-build-level-build-list',params,{
              headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
              }}).then(function (response) {
            console.log("筛选后后台传来的数据",response.data);
              if(JSON.stringify(response.data)!="{}"){
                _this.isActive=false;
                _this.hasSearchBuildList=response.data;
              }else{
                _this.isActive=true;
                _this.alertMsg("没有查询到对应楼宇");
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
      //加入列表
      addCityList(){
        var  _this=this;
        var params = new URLSearchParams();
          params.append("check_type",2);
          params.append("mobile","");
          params.append("build_name",_this.buildingName);
          params.append("company_name","");
          axios.post('/user-selection-task/check-legal',params
          ).then(function (response) {
            console.log("后台传来的数据",response.data);
            if(response.data){
               if(!_this.buildNameListData.find((build)=>build==_this.buildingName)){
                   var myGuid = _this.guid();
                  _this.buildNameList.push({data:_this.buildingName,guid:myGuid});
                  _this.buildNameListData.push(_this.buildingName);
                  _this.buildingName="";
                }else {
                  _this.alertMsg("请勿重复添加楼宇");
                }
              _this.changeSaveStatus(false);
            }else{
              _this.alertMsg("添加楼宇失败");
            }
          })
          .catch(function (error) {
            _this.alertMsg(error);
          });
      },
      //删除楼宇列表
      deleteBuildList(guidData){
          var guidIndex;
          this.buildNameList.findIndex(function(value, index, arr) {
            if(value.guid==guidData){
                  guidIndex=index;
            }
          })
          this.buildNameList.splice(guidIndex,1);
          this.buildNameListData.splice(guidIndex,1);
          this.changeSaveStatus(false);
      },
      //清空楼宇列表
      clearBuildNameList(){
        this.buildNameList=[];
        this.buildNameListData=[];
        this.changeSaveStatus(false);
      },
      //生成guid ,解决排序
      guid()
      {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
            return v.toString(16);
        });
      },
      //将搜索的楼宇添加到列表中(并判重)
      addSearchBuildList(){
        var buildList=this.checkBuildList;
        console.log(this.checkBuildList);
        // var len = this.buildNameList.length;
        if(buildList.length>0){
            for(var i=0; i<buildList.length; i++){
               if(!this.buildNameListData.find((build)=>build==buildList[i])){
                   var myGuid = this.guid();
                    this.buildNameList.push({data:buildList[i],guid:myGuid});
                    this.buildNameListData.push(buildList[i]);
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
      //保存条件数据
      saveConditionData(){
        console.log("上传文件的路径",this.buildPath);
        if(this.buildPath==""&&this.buildNameListData.length==0){
          this.alertMsg("请添加楼宇");
        }else{
            this.myConditionData.logic_condition={
                "condition":{
                  "city":this.checkCityList,
                  "build_type":this.checkChannelList,
                  "equip_type":this.checkDevicelList,
                  "build_list":this.buildNameListData,
                  "build_name_path":this.buildPath
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
        console.log(response.data);
        var noExistsList = data.noExistsList||"";
        var filePath = data.filePath||"";
        console.log("上传文件返回的路径",filePath);
        this.buildPath = filePath;
        this.uploadTxtCallbackInfo = noExistsList;
        this.changeSaveStatus(false);
      },
      // 上传错误
      uploadError (response, file, fileList)
      {
        this.alertMsg('上传失败，请重试！');
        this.changeSaveStatus(false);
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
      //搜索楼宇列表
       searchData: function() {
          var search = this.searchText;
          if (search) {
              return this.buildNameList.filter(function(product) {
                  return Object.keys(product).some(function(key) {
                      return String(product[key]).toLowerCase().indexOf(search) > -1
                  })
              })
          }
          return this.buildNameList;
      }
    }
  }
</script>
<style>
/*消费楼宇、点位、公司导入样式相同*/
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
 .uploadTxtCallback{
  font-size: 12px;
  line-height: 18px;
  margin-top: 10px;
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
