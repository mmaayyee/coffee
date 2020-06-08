<template>
  <div class="content-body">
    <el-form ref="form" :model="form" :rules="rules" label-width="120px">
    <!-- 语音名称 -->
      <el-row :gutter="10">
        <el-col :span="12" :offset="0">
          <el-form-item label="语音标题：" prop="title" id="title">
            <el-input v-model.trim="form.title"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <!-- 上线时间 -->
      <el-row :gutter="10">
        <el-col :span="24">
          <el-form-item label="上线时间：" prop="launchTime" id="launchTime">
            <el-date-picker v-model="form.launchTime" type="datetimerange" range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期" value-format="yyyy-MM-dd" format="yyyy-MM-dd">
            </el-date-picker>
          </el-form-item>
        </el-col>
      </el-row>
      <!-- 状态 -->
      <el-row :gutter="10">
        <el-col :span="12">
          <el-form-item label="状态：" prop="status">
            <el-radio v-model="form.status" label="1">上线</el-radio>
            <el-radio v-model="form.status" label="5">下线</el-radio>
          </el-form-item>
        </el-col>
      </el-row>
      <!-- 语音内容 -->
      <el-row :gutter="10">
        <el-col :span="12" :offset="0">
          <el-form-item label="语音内容：" prop="cont" id="cont">
            <el-input v-model.trim="form.cont"></el-input>
          </el-form-item>
        </el-col>
        <el-col  :span="12" class="tip">提示：语音内容仅支持汉字、数字、逗号和句号。</el-col>
      </el-row>
      <!-- 选择场景 -->
      <el-row :gutter="10">
        <el-col :span="14" v-for="(item,index) in form.sceneList" :key="index">
          <el-form-item label="请选择场景：" v-if="index==0">
          </el-form-item>
          <el-form-item label=""  :prop="'sceneList.' +index +'.scene_id'" :rules="chooseScene" id="chooseScene">
            <el-select placeholder="进入选择支付方式" v-model="item.scene_id"  @change="checkScene" style="width:300px">
                <el-option v-for="sce in sceneListData"
                    :label="sce.scene_name"
                    :value="sce.scene_id"
                    :key="sce.scene_id">
                </el-option>
            </el-select>
            <el-button plain icon="el-icon-remove-outline" v-if="index!=0" @click="delSecne(index)">删除</el-button>
            <el-button plain icon="el-icon-circle-plus-outline" @click="addSecne" v-if="index==0">添加</el-button>
          </el-form-item>
        </el-col>
      </el-row>
      <!-- 选择饮品 -->
      <el-row :gutter="10">
        <el-col :span="20">
          <el-form-item label="选择饮品：">
            <el-tabs type="border-card" v-model="productPoint">
              <el-tab-pane label="上线普通" name="1" v-if="productTabs.onlineTabShow" value="1">
                <el-checkbox :indeterminate="isIndeterminateOnline" v-model="checkOnlineAll" @change="handleCheckAllOnline">全选</el-checkbox>
  <div style="margin: 15px 0;"></div>
                <el-checkbox-group v-model="productIdStr.online_product" @change="handleCheckedOnlineChange">
                  <el-checkbox v-for="item in onlineProductList" :label="item.product_id" :key="item.product_id">{{ item.product_name}}</el-checkbox>
                </el-checkbox-group>
              </el-tab-pane>
              <el-tab-pane label="下线普通" name="2" v-if="productTabs.allTabShow">
                <el-checkbox :indeterminate="isIndeterminateAll" v-model="checkAllAll" @change="handleCheckAllOutline">全选</el-checkbox>
  <div style="margin: 15px 0;"></div>
                <el-checkbox-group v-model="productIdStr.outline_product" @change="handleCheckedAllChange">
                  <el-checkbox v-for="item in allProductList" :label="item.product_id" :key="item.product_id">{{ item.product_name}}</el-checkbox>
                </el-checkbox-group>
              </el-tab-pane>
              <el-tab-pane label="臻选单品" name="3" v-if="productTabs.selectionTabShow">
                <el-checkbox :indeterminate="isIndeterminateSelection" v-model="checkSelectionAll" @change="handleCheckAllSelection">全选</el-checkbox>
  <div style="margin: 15px 0;"></div>
                <el-checkbox-group v-model="productIdStr.selection_product" @change="handleCheckedSelectionChange">
                  <el-checkbox v-for="item in selectionProductList" :label="item.product_id" :key="item.product_id">{{ item.product_name}}</el-checkbox>
                </el-checkbox-group>
              </el-tab-pane>
            </el-tabs>
          </el-form-item>
        </el-col>
      </el-row>
      <!-- 楼宇筛选 -->
      <div class="conditionType">
            <el-row><h4>楼宇筛选</h4></el-row>
            <div class="searchType" v-show="isActive">
                <el-row :gutter="10">
                 <el-form-item label="选择城市：">
                    <el-checkbox  @change="handleCheckAllChange" v-model="checkAllCity" :indeterminate="isIndeterminate" >全选</el-checkbox>
                    <el-checkbox-group  v-model="checkCityList" @change="handleCheckedCitiesChange">
                        <el-checkbox  v-for="city in cities" :label="city.mechanism_id" :key="city.mechanism_id">{{city.mechanism_name}}</el-checkbox>
                    </el-checkbox-group>
                  </el-form-item>
                </el-row>
                <el-row :gutter="10">
                     <el-form-item label="渠道类型：">
                        <el-checkbox  @change="handleCheckAllChannelChange" v-model="checkAllChannel" :indeterminate="isIndeterminateChannel" >全选</el-checkbox>
                        <el-checkbox-group v-model="checkChannelList" @change="handleCheckedChannelChange">
                            <el-checkbox v-for="channel in ChannelTypes" :label="channel.channel_id" :key="channel.channel_id">{{channel.channel_name}}</el-checkbox>
                        </el-checkbox-group>
                      </el-form-item>
                </el-row>
                <el-row :gutter="10">
                     <el-form-item label="设备类型：">
                     <el-checkbox  @change="handleCheckAllDeviceChange" v-model="checkAllDevice" :indeterminate="isIndeterminateDevice" >全选</el-checkbox>
                      <el-checkbox-group v-model="checkDevicelList" @change="handleCheckedDeviceChange">
                        <el-checkbox v-for="device in deviceTypes" :label="device.equip_type_id" :key="device.equip_type_id">{{device.equip_type_name}}</el-checkbox>
                      </el-checkbox-group>
                    </el-form-item>
                </el-row>
            </div>
            <div class="searchList" v-show="!isActive"> <!-- 搜索列表 -->
                   <el-row :gutter="10">
                     <el-form-item label="楼宇列表：">
                        <el-checkbox  @change="handleCheckAllBuildChange" v-model="checkAllBuild" :indeterminate="isIndeterminateBuild" >全选</el-checkbox>
                        <el-checkbox-group v-model="checkBuildList" @change="handleCheckedBuildChange">
                            <el-checkbox v-for="build in hasSearchBuildList" :label="build.build_id" :key="build.build_id">{{build.build_name}}</el-checkbox>
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
              <el-col :span="12">
                <el-form-item label="楼宇名称：">
                  <el-input v-model.trim="buildingName"></el-input>
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
            <div class="buildNameBox">
                <el-row v-for="item in searchData"  :key="item.build_id"  class="buildNameText" >
                <el-col :span="20">
                  <span>{{item.build_name}}</span>
                </el-col>
                <el-col :span="4">
                <i class="el-icon-delete" @click="deleteBuildList(item.build_id)"></i>
                </el-col>
              </el-row>
            </div>
        </div>
        <el-form-item size="medium" class="div-submit">
          <el-button @click="resetForm('form')">取消</el-button>
          <el-button type="primary" @click="submitForm('form')">提交</el-button>
        </el-form-item>
    </el-form>
  </div>
</template>
<script>
/* eslint-disable */
import axios from 'axios'
let onlineProductOptions = [];
let allProductOptions = [];
let selectionProductOptions = [];
export default {
  data() {
    return {
      voiceId:rootData.id,//活动ID
      form:{
        title:"",
        status:"1",
        cont:"",
        sceneList:[{scene_id:""}],
        launchTime:[],
      },
      // 选择场景
      sceneListData:rootData.scene_list,
      // 选择饮品
      productPoint:'1',
      onlineProductList:rootData.product_list.online_product,
      allProductList: rootData.product_list.outline_product,
      selectionProductList: rootData.product_list.selection_product,
      isIndeterminateOnline:true,
      isIndeterminateAll:true,
      isIndeterminateSelection:true,
      checkOnlineAll: false,
      checkAllAll: false,
      checkSelectionAll: false,
      isCheck: true,//标识场景是否全选
      // 楼宇筛选
      isActive:true,
      cities:rootData.mechanism_list,
      ChannelTypes:rootData.channel_list,
      deviceTypes:rootData.equip_type_list,
      checkCityList: [],//城市列表
      checkChannelList:[],//渠道类型列表
      checkDevicelList:[],//设备类型列表
      checkBuildList:[],//楼宇列表
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
      hasSearchBuildList:[],//已搜索楼宇列表
      // 楼宇列表
      buildingName:"",
      buildNameListData:[],//添加的楼宇
      buildNameList:[],//楼宇模板
      searchText:"",
      files:[],//文件列表
      uploadData: {// 上传楼宇txt文件参数，oldFilePath编辑时页面获取，提交时带上参数
        dataType: "1",
        oldFilePath:""
      },
      buildPath:"",
      uploadTxtCallbackInfo:"",
      uploadActionUrl:rootCoffeeStieUrl+"speech-control-api/verify-file.html",
      productIdStr: {
        online_product: [],
        outline_product: [],
        selection_product: []
      },
      productTabs: {
        onlineTabShow:true,
        allTabShow:true,
        selectionTabShow:true
      },
      rules: {
        title: [
          { required: true, message: '请输入语音名称', trigger: 'blur' }
        ],
        launchTime: [
          { required: true, message: '请选择上线时间', trigger: 'change' }
        ],
        cont: [
          { required: true, message: '请输入语音内容', trigger: 'blur' }
        ]
      },
      chooseScene:[{ required: true, message: '请选择场景', trigger: 'change'}]
    }
  },
  mounted() {
    this.init();
  },
  methods: {
    init(){
      //编辑
      if(rootData.id>0){
        let data=rootData;
        this.form.title=data.speech_control_title;//标题
        this.form.launchTime=[data.start_time,data.end_time];//时间
        this.form.status=data.status;//状态
        this.form.cont=data.speech_control_content;//语音内容
        this.productIdStr=data.check_product_list;//饮品列表
        //场景渲染
        this.form.sceneList=data.check_scene_list;
        //渲染楼宇列表及后台楼宇列表数据
        for(let i=0;i<data.check_build_list.length;i++){
          this.buildNameList.push({build_id:data.check_build_list[i].build_id,build_name:data.check_build_list[i].build_name});
          this.buildNameListData.push(data.check_build_list[i].build_id);
        }
        console.log("时间",this.form.launchTime)
        //渲染上传文件
        // let txtUrl = rootData.build_list_path||"";
        // let urlTxtName = txtUrl!=""?txtUrl.split("/").pop():"";
        // this.files = [{name:urlTxtName,url:txtUrl}];
        // this.uploadData.oldFilePath = txtUrl;
        // this.buildPath = txtUrl;
      }
    },
    // 选择饮品
    handleCheckAllOnline(val)
    {
      let onlineProductOptions=[];
      for(let i of this.onlineProductList){
        onlineProductOptions.push(i.product_id)
      }
      this.productIdStr.online_product = val ? onlineProductOptions : [];
      this.isIndeterminateOnline = false;
    },
    handleCheckedOnlineChange(value)
    {
      let checkedCount = value.length;
      this.checkOnlineAll = checkedCount === this.onlineProductList.length;
      this.isIndeterminateOnline = checkedCount > 0 && checkedCount < this.onlineProductList.length;
    },
    handleCheckAllOutline(val)
    {
      let allProductOptions=[];
      for(let i of this.allProductList){
        allProductOptions.push(i.product_id)
      }
      this.productIdStr.outline_product = val ? allProductOptions : [];
      this.isIndeterminateAll = false;
    },
    handleCheckedAllChange(value)
    {
      let checkedCount = value.length;
      this.checkAllAll = checkedCount === this.allProductList.length;
      this.isIndeterminateAll = checkedCount > 0 && checkedCount < this.allProductList.length;
    },
    handleCheckAllSelection(val)
    {
      let selectionProductOptions=[];
      for(let i of this.selectionProductList){
        selectionProductOptions.push(i.product_id)
      }
      this.productIdStr.selection_product = val ? selectionProductOptions : [];
      this.isIndeterminateSelection = false;
    },
    handleCheckedSelectionChange(value)
    {
      let checkedCount = value.length;
      this.checkSelectionAll = checkedCount === this.selectionProductList.length;
      this.isIndeterminateSelection = checkedCount > 0 && checkedCount < this.selectionProductList.length;
    },
    // 选择场景
    addSecne(){
      this.form.sceneList.push({scene_id:""})
    },
    delSecne(index){
      console.log(index)
      this.form.sceneList.splice(index,1)
    },
    checkScene(){
        console.log(this.form.sceneList)
    },
     //选择城市全选
      handleCheckedCitiesChange(value) {
        let checkedCount = value.length;
        let cityLength=Object.keys(this.cities).length;
        this.checkAllCity = checkedCount === cityLength;
        this.isIndeterminate = checkedCount > 0 && checkedCount < this.cities.length;
      },
      handleCheckAllChange(val){
        this.checkCitiesKey=[]
        for( let i of this.cities){
          this.checkCitiesKey.push(i.mechanism_id)
        }
        this.checkCityList = val ? this.checkCitiesKey : [];
        this.isIndeterminate =false
      },
      //渠道类型全选
      handleCheckedChannelChange(value) {
        let checkedCount = value.length;
        let channelLength=Object.keys(this.ChannelTypes).length;
        this.checkAllChannel = checkedCount === channelLength;
        this.isIndeterminateChannel = checkedCount > 0 && checkedCount < this.ChannelTypes.length;
      },
      handleCheckAllChannelChange(val){
        this.checkChannelKey=[];
        for( let i of this.ChannelTypes){
          this.checkChannelKey.push(i.channel_id)
        }
        this.checkChannelList = val ? this.checkChannelKey : [];
        this.isIndeterminateChannel =false
      },
      //设备类型全选
      handleCheckedDeviceChange(value) {
        let checkedCount = value.length;
        let deviceLength=Object.keys(this.deviceTypes).length;
        this.checkAllDevice = checkedCount === deviceLength;
        this.isIndeterminateDevice= checkedCount > 0 && checkedCount < this.deviceTypes.length;
      },
      handleCheckAllDeviceChange(val){
        this.checkDeviceKey=[];
        for( let i of this.deviceTypes){
          this.checkDeviceKey.push(i.equip_type_id)
        }
        this.checkDevicelList = val ? this.checkDeviceKey : [];
        this.isIndeterminateDevice =false;
      },
      //筛选楼宇全选
      handleCheckedBuildChange(value) {
        let checkedCount = value.length;
        let buildLength=this.hasSearchBuildList.length;
        this.checkAllBuild = checkedCount === buildLength;
        this.isIndeterminateBuild= checkedCount > 0 && checkedCount < this.hasSearchBuildList.length;
      },
      handleCheckAllBuildChange(val){
        this.checkBuildKey=[];
        for( let i of this.hasSearchBuildList){
          this.checkBuildKey.push(i.build_id);
        }
        console.log("checkkey",this.checkBuildKey)
        this.checkBuildList = val ? this.checkBuildKey : [];
        this.isIndeterminateBuild =false;
      },
    //筛选楼宇列表
      searchCitiesChange(){
        if(this.checkCityList.length==0&&this.checkChannelList.length==0&&this.checkDevicelList.length==0){
          this.alertMsg("请选择筛选条件");
          return;
        }else{
          let params={
            "mechanism_list":this.checkCityList,"channel_list":this.checkChannelList,"equip_type_list":this.checkDevicelList
          }
          console.log("筛选楼宇前传递的参数",params);
          axios.post('/speech-control/filter-build',params,{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'}).then((response)=>{
            console.log("筛选后后台传来的数据",response.data);
              if(JSON.stringify(response.data)!="{}"){
                this.isActive=false;
                this.hasSearchBuildList=response.data;
              }else{
                this.isActive=true;
                this.alertMsg("没有查询到对应楼宇");
              }
            })
            .catch((error)=>{
              this.alertMsg(error);
            });
          }
      },
    //继续筛选
    searchCitiesAgain(){
      this.isActive=true;
    },
    //将搜索的楼宇添加到列表中(并判重)
    addSearchBuildList(){
      console.log("搜索列表",this.hasSearchBuildList)
        let buildList=this.hasSearchBuildList.filter((item)=>{
          return this.checkBuildList.some((build)=>{
            return build==item.build_id
          })
        })
        console.log("格式化选中楼宇列表",buildList)
        console.log("默认选中的楼宇",this.checkBuildList);
        let len = this.buildNameList.length;
        for(let i=0; i<buildList.length; i++){
          let isNotRepeat=this.buildNameList.every((item)=>{
                return item.build_id!=buildList[i].build_id
            })
          console.log("是否没有重复",isNotRepeat)
          if(isNotRepeat){
             this.buildNameList.push({build_name:buildList[i].build_name,build_id:buildList[i].build_id});
              this.buildNameListData.push(buildList[i].build_id);
          }else{
            this.alertMsg("请勿重复添加列表");
          }
        }
        this.checkBuildList=[];//添加完后将搜索列表选中置空
        this.isIndeterminateBuild=true;
      },
    //加入列表
    addCityList(){
        let params={build_name:this.buildingName}
        axios.post('/speech-control/check-build',params
        ).then((response)=>{
          console.log("后台传来的数据楼宇编号",response.data.build_id);
          if(response.data.build_id!="0"){
            //判重
            console.log("楼宇列表",this.buildNameList)
            let isNotRepeat=this.buildNameList.every((item)=>{
                return item.build_id!=response.data.build_id
            })
            console.log("加入列表不重复",isNotRepeat)
            if(isNotRepeat){
              this.buildNameList.push({build_name:this.buildingName,build_id:response.data.build_id});
              this.buildNameListData.push(response.data.build_id);
              this.buildingName="";
            }else{
              this.alertMsg("请勿重复添加楼宇");
            }
          }else{
            this.alertMsg("添加楼宇失败");
          }
        })
        .catch(function (error) {
          this.alertMsg(error);
        });
      // 测试
    },
    // 删除对应楼宇列表
    deleteBuildList(id){
      let dIndex=this.buildNameList.findIndex((value,index)=>{
        return value.build_id==id
      })
      this.buildNameList.splice(dIndex,1);
      this.buildNameListData.splice(dIndex,1);
      console.log(this.buildNameListData)
    },
    //清空楼宇列表
    clearBuildNameList(){
      this.buildNameList=[];
      this.buildNameListData=[];
    },
    // 上传文件
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
      },
      // 上传错误
      uploadError (response, file, fileList)
      {
        this.alertMsg('上传失败，请重试！');
      },
    submitForm(formName) // 提交任务
    {
      this.$refs[formName].validate((valid,obj) => {
        if (valid) {
          if(this.productIdStr.online_product.length==0&&this.productIdStr.outline_product.length==0&&this.productIdStr.selection_product.length==0){
            this.alertMsg("请选择饮品");
            return false;
          }else if(this.buildNameListData.length=="0"&&this.buildPath==""){
            this.alertMsg("请选择楼宇");
            return false;
          }else{
            let sceneList=[];
            this.form.sceneList.filter((item)=>{
              sceneList.push(item.scene_id);
            });
            if(new Set(sceneList).size!=sceneList.length){
              this.alertMsg("场景不能重复");
            }else{
               this.submitFormAction();
            }
          }
        } else {
          for(let key in obj){
            if(this.rules[key][0].message){
              document.getElementById(key).scrollIntoView();
              this.alertMsg(this.rules[key][0].message);
            }
            break;
          }
          return false;
        }
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
    },
    submitFormAction() // 提交任务接口
    {
      let params = {
        id:this.voiceId,
        speech_control_title:this.form.title,
        speech_control_content:this.form.cont,
        start_time: this.form.launchTime[0],
        end_time: this.form.launchTime[1],
        status: this.form.status,
        scene_list:this.form.sceneList,
        product_list:this.productIdStr,
        build_list:this.buildNameListData,
        build_list_path:this.buildPath
      };
      console.log(params)
      const header = {
        'content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
      }
      axios.post("/speech-control/save-speech-control", params,header)
      .then((response)=> {
        console.log("后台返回的数据",response)
        let data = response.data;
        console.log(data)
        if(data.code=="0"){
           this.alertMsg(data.msg,'success');
           setTimeout(()=>{
            window.location.href="/speech-control/index";
           },2000);
        }else{
           this.alertMsg(data.msg);
        }
      })
      .catch((error)=> {
        this.alertMsg(error);
      });
    },
  },
  computed:{
  //搜索楼宇列表
   searchData: function() {
      let search = this.searchText;
      if (search) {
          return this.buildNameList.filter(function(product) {
            return String(product.build_name).toLowerCase().indexOf(search) > -1
          })
      }
      return this.buildNameList;
  }
}
}

</script>
<style>
.el-checkbox{
  margin-right:30px;
}
.el-checkbox+.el-checkbox{
  margin-left:0;
}
 .div_center {
    text-align: center;;
    width:100%;
    margin:0 auto;
    margin-bottom: 20px;
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
  .btnAdd,.upload-file{
    margin-left:15px;
  }
  .el-upload__tip{
    margin-left:5px;
  }
  .buildNameBox{
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
.el-tabs--border-card{
  box-shadow: none;
  -webkit-box-shadow:none;
}

.tip{
  margin-top:10px;
  color: red;
}
</style>
