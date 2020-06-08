<template>
  <div class="content-body">
    <div class="line-title">楼宇</div>
     <el-form :model="form" ref="form"    label-width="150px">
        <el-row :gutter="10">
            <el-col :span="7" :offset="0" class="building-name" >
              <el-form-item label="楼宇名称：">
                 <el-autocomplete
                    class="inline-input"
                    v-model="buildName"
                    :fetch-suggestions="querySearch"
                    placeholder="请输入内容"
                ></el-autocomplete>
              </el-form-item>
            </el-col>
            <el-col :span="7" :offset="0">
             <el-form-item label="渠道：">
                <el-select placeholder="请选择" v-model="buildType" clearable >
                   <el-option :label="item.type_name" :value="item.id" v-for="(item,key) in buildTypeList" :key="key"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="10" :offset="0">
             <el-form-item label="分公司：">
                <el-select placeholder="请选择" v-model="orgName" clearable>
                   <el-option :label="item.org_name" :value="item.org_id" v-for="(item,index) in orgList" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
        </el-row>
         <el-row :gutter="10">
            <el-col :span="7" :offset="0">
             <el-form-item label="创建人：">
                 <el-select placeholder="请选择" v-model="creater" clearable>
                   <el-option :label="item" :value="item" v-for="(item,index) in creatorList" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="7" :offset="0">
             <el-form-item label="楼宇状态：">
                 <el-select placeholder="请选择" v-model="buildingStatus" clearable>
                   <el-option :label="item.label" :value="item.value" v-for="(item,index) in buildingStatusList" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="10" :offset="0">
             <el-form-item label="提交时间：">
                 <el-date-picker
                    v-model="createTime"
                    type="daterange"
                    range-separator="至"
                    start-placeholder="开始日期"
                    end-placeholder="结束日期">
                  </el-date-picker>
              </el-form-item>
            </el-col>
        </el-row>
        <el-form-item size="medium" class="div-submit">
          <el-button type="primary" @click="searchData(0)" v-if="isShowSearch">搜索</el-button>
          <el-button type="primary" @click="resetCondition" v-if="isShowSearch">重置</el-button>
        </el-form-item>
        <el-form-item size="medium" class="div-submit">
          <el-button type="primary" size="medium" @click="transferToastShow" v-if="isShowTransfer">转交</el-button>
          <el-button type="primary" size="medium" @click="goRecord" v-if="isShowCreate">新建</el-button>
        </el-form-item>
      <el-table
    :data="buildingRecordList"
    style="width: 95%" border  class="table-line" @selection-change="handleSelectionChange">
          <el-table-column   prop="" label="" width="50" type="selection"></el-table-column>
          <el-table-column   prop="index" label="序号" width="50" align="center"></el-table-column>
          <el-table-column  prop="building_name" label="楼宇名称" width="280" align="center"></el-table-column>
          <el-table-column  prop="type_name" label="渠道" align="center"> </el-table-column>
          <el-table-column prop="creator_id" label="创建人" align="center"> </el-table-column>
          <el-table-column prop="org_name" label="分公司" align="center"> </el-table-column>
          <el-table-column prop="rate_status" label="初评状态" align="center"> </el-table-column>
          <el-table-column prop="building_status" label="楼宇状态" align="center"></el-table-column>
          <el-table-column prop="created_at" label="创建时间" align="center"></el-table-column>
          <el-table-column  label="操作" width="90" align="center">
            <template slot-scope="scope">
              <i class="el-icon-edit" style="cursor: pointer" @click="edit(scope.row)" size="medium" title="修改" v-if="isShowUpdate"></i>
              <i class="el-icon-view" style="cursor: pointer" @click="detail(scope.row)" size="medium" title="查看" v-if="isShowView"></i>
              <i class="el-icon-check" style="cursor: pointer" @click="evaluate(scope.row)" size="medium" title="初评" v-if="isShowEvaluate"></i>
            </template>
          </el-table-column>
    </el-table>
    <el-dialog title="批量转移" :visible.sync="dialogFormVisible">
        <el-form :model="form">
          <el-form-item label="新负责人：">
            <el-select v-model="newCreator" placeholder="请选择">
              <el-option :label="item.username" :value="item.userid" v-for="(item,index) in newCreatorList" :key="index"></el-option>
            </el-select>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogFormVisible = false">取 消</el-button>
          <el-button type="primary" @click="transmitAction">确 定</el-button>
        </div>
  </el-dialog>
    <el-pagination
      background
      layout="prev, pager, next"
      :total="listCount" :page-size="pageSize" @current-change="changePageData">
    </el-pagination>
    </el-form>
  </div>
</template>
<script>
// eslint-disable-next-line
/* eslint-disable */
import axios from 'axios'
export default {
  data() {
    return {
      form:{},
      showInfo:"2",//1显示 2不显示
      buildingRecordList:[],//点位记录列表
      buildTypeList:[],//渠道列表
      orgList:[],//分公司列表
      buildName:"",//点位名称
      buildingStatus:"",//楼宇状态
      buildingStatusList:[{label:"保存草稿",value:"1"},{label:"创建",value:"2"}],//处理状态列表
      createTime:[],//提交时间
      creater:"",//创建人
      creatorList:[],//创建人列表
      orgName:"",//选择的分公司
      buildType:"",//选择的渠道
      dialogFormVisible: false,
      creatorChangeList:[],
      newCreator:"",//新创建人
      newCreatorList:[],//新负责人列表
      isShowSearch:true,//是否显示搜索
      isShowCreate:false,//是否显示新建
      isShowTransfer:false,//是否显示转交
      isShowUpdate:false,//是否有编辑权限
      isShowView:false,//是否有查看权限
      isShowEvaluate: false,//是否有初评权限
      multipleSelection:[],//复选框选项
      listCount:0,
      restaurants :[],//点位名称建议列表
      pageSize:20,
    }
  },
   mounted(){
    this.init();
  },
  methods: {
    init(){
      window.parent.onscroll = (e)=>{
        this.scrollMsg();
      }
      axios.get('/building-record/list').then((res)=>{
        console.log("building-record/list...",res);
        const initData =res.data;
        if(initData.error_code== 0){
           this.buildingRecordList = initData.data.recordList.buildingRecordList;
           this.buildTypeList = initData.data.buildTypeList;
           this.restaurants = initData.data.buildNameList;
           this.orgList = initData.data.orgList;
           this.creatorList = initData.data.creatorList;
           this.newCreatorList = initData.data.newCreator;
           this.isShowTransfer = initData.data.transmit;
           this.isShowCreate = initData.data.create;
           this.isShowView = initData.data.view;
           this.isShowEvaluate = initData.data.evaluate;
           this.isShowUpdate = initData.data.update;
           this.listCount = parseInt(initData.data.recordList.totalCount);
           this.formatData();
        }else{
          this.alertMsg(initData.msg);
          return false;
        }
      }).catch((error)=>{
           console.log("error..",error);
      });
    },
    formatData(){
      this.buildingRecordList.map((item,index)=> {
        item.index = index+1;
        item.building_status = item.building_status==1?"草稿":"已创建";
        if(item.rate_status==0) {
          item.rate_status = "未初评";
        } else if(item.rate_status==1) {
          item.rate_status = "已初评";
        } else {
          item.rate_status = "驳回";
        }
      })
    },
    changePageData(page){
      this.searchData(page-1);
    },
    //编辑
    edit(row){
      this.$router.push({name:"buildRecord",query:{id:row.id}});
    },
    //查看
    detail(row){
      this.$router.push({name:"buildDetail",query:{id:row.id}});
    },
    evaluate(row){
      this.$router.push({name:"buildDetail",query:{id:row.id,evaluate:true}});
    },
    //搜索建议
    querySearch(queryString, cb) {
      var restaurants = this.restaurants;
      var results = queryString ? restaurants.filter(this.createFilter(queryString)) : restaurants;
      console.log("results",cb(results));
      // 调用 callback 返回建议列表的数据
      cb(results);
    },
    createFilter(queryString) {
      return (restaurant) => {
        return (restaurant.value.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
      };
    },
    //复选框选项发生改变
    handleSelectionChange(val) {
        this.multipleSelection = val;
        console.log("selection",this.multipleSelection);
    },
    //重置条件
    resetCondition(){
      this.createTime=[];
      this.creater = "";
      this.orgName="";
      this.buildType="";
      this.buildName="";
      this.buildingStatus="";
    },
    //新建
    goRecord(){
       this.$router.push({name:"buildRecord",query:{id:""}});
    },
    //转交验证
    transferToastShow(){
      if(this.multipleSelection.length==0){
        this.alertMsg("请选择要转交的楼宇列表");
        return false;
      }else{
        this.dialogFormVisible=true;
      }
    },
    //转交
    transmitAction(){
      if(this.newCreator==""){
        this.alertMsg("请选择新负责人");
        return false;
      }else{
        let pointList=[];
        for(let item of this.multipleSelection){
          pointList.push({id:item.id,creator_id:item.creator_id});
        }
        let transmitData={
          transferInfo:{
            new_creator_name:this.newCreator
          },
          buildingIDList:pointList
        }
        console.log('tran',transmitData);
        axios.post('/building-record/transfer',transmitData).then((res)=>{
          const initData =res.data;
          if(initData.error_code== 0){
            this.alertMsg(initData.msg,'success');
            this.dialogFormVisible=false;
            this.init();
          }else{
            this.alertMsg(initData.msg);
            return false;
          }
        }).catch((error)=>{
             console.log("error..",error);
        });
      }

    },
    //搜索楼宇列表
    searchData(page){
      console.log("搜索的page",page);
      let params={
        BuildingRecordSearch: {
            org_id: this.orgName,
            building_name: this.buildName,
            build_type_id: this.buildType,
            creator_id: this.creater,
            building_status: this.buildingStatus,
            beginTime: this.createTime[0],
            endTime: this.createTime[1]
        },
        page:page
      }
      if(this.createTime.length==0){
        params.BuildingRecordSearch.beginTime="";
        params.BuildingRecordSearch.endTime="";
      }
      console.log('params',params);
      axios.post('/building-record/search-record',params)
      .then((response)=> {
        let datas = response.data;
        console.log("search-record data",datas);
        if(datas.error_code==0){
            this.buildingRecordList=datas.data.buildingRecordList;
            this.listCount=parseInt(datas.data.totalCount);
            this.formatData();
        }else{
           this.alertMsg('网络异常','error');
        }
      })
      .catch((error)=> {
        this.alertMsg(error);
      });
    },
    alertMsg(msg,type)
    {
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
  },
  components: {}
}
</script>
<style scoped>
.div-submit{
  text-align:right;
}
.btn-submit{
  margin:0 0 20px 60px;
}
.el-table{
  margin-left:60px;
}
.el-table td, .el-table th.is-leaf{
  text-align:center;
}
.el-pagination{
  margin:30px 0 0 60px;
}
.building-name:before{
  color:#fff;
}
.el-icon-edit-outline{
  margin-right:15px;
  cursor: pointer;
}
.el-icon-view{
  cursor: pointer;
}
</style>
