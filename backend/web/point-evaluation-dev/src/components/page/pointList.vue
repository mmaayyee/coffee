<template>
  <div class="content-body">
    <div class="line-title">点位分级评分表</div>
     <el-form :model="form" ref="form"    label-width="150px">
        <el-row :gutter="10">
            <el-col :span="8" :offset="0" class="building-name" >
              <el-form-item label="点位名称：">
                 <el-autocomplete
                    class="inline-input"
                    v-model="pointName"
                    :fetch-suggestions="querySearch"
                    placeholder="请输入内容"
                ></el-autocomplete>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="渠道：">
                <el-select placeholder="请选择" v-model="buildType" clearable >
                   <el-option :label="item.type_name" :value="item.id" v-for="(item,key) in buildTypeList" :key="key"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="级别：">
                <el-select placeholder="请选择" v-model="pointLevel" clearable>
                   <el-option :label="item" :value="key" v-for="(item,key) in pointLevelList" :key="key"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
        </el-row>
         <el-row :gutter="10">
          <el-col :span="8" :offset="0">
             <el-form-item label="分公司：">
                <el-select placeholder="请选择" v-model="orgName" clearable>
                   <el-option :label="item.org_name" :value="item.org_id" v-for="(item,index) in orgList" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="提交人：">
                <el-select placeholder="请选择" v-model="submitPerson" clearable>
                    <el-option :label="item.username" :value="item.userid" v-for="(item,index) in submitPersonList" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <!-- <el-col :span="8" :offset="0">
             <el-form-item label="创建人：">
                 <el-select placeholder="请选择" v-model="creator" clearable>
                   <el-option :label="item" :value="item" v-for="(item,index) in creatorList" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col> -->
            <el-col :span="7" :offset="0">
             <el-form-item label="处理状态：">
                 <el-select placeholder="请选择" v-model="treatStatus" clearable>
                   <el-option :label="item.handleValue" :value="item.handleName" v-for="(item,index) in treatStatusList" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
        </el-row>
        <el-row>
            <el-col :span="10" :offset="0">
             <el-form-item label="提交时间：">
                 <el-date-picker
                    v-model="submitTime"
                    type="daterange"
                    range-separator="至"
                    start-placeholder="开始日期"
                    end-placeholder="结束日期">
                  </el-date-picker>
              </el-form-item>
            </el-col>
            <el-col :span="7" :offset="0">
             <el-form-item label="审批状态：">
                <el-select placeholder="请选择" v-model="approvalStatus" clearable>
                   <el-option :label="item" :value="key" v-for="(item,key) in approvalStatusList" :key="key"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
        </el-row>
        <el-form-item size="medium" class="div-submit">
          <el-button type="primary" @click="searchData(0)" v-if="isShowSearch">搜索</el-button>
          <el-button type="primary" @click="resetCondition" v-if="isShowSearch">重置</el-button>
        </el-form-item>
      <div class="btn-submit">
          <el-button type="primary" size="medium" @click="goRecord" v-if="isShowCreate">新建</el-button>
          <a :href="exportUrl"><el-button type="primary" size="medium" v-if="isShowExport">导出</el-button></a>
          <el-button type="primary" size="medium" @click="transferToastShow" v-if="isShowTransfer">转交</el-button>
      </div>
      <el-table
    :data="pointRecordList"
    style="width: 95%" border  class="table-line" @selection-change="handleSelectionChange">
          <el-table-column prop="" label="" width="50" type="selection"></el-table-column>
          <el-table-column prop="index" label="序号" width="50" align="center"></el-table-column>
          <el-table-column prop="point_name" label="点位名称" width="280" align="center"></el-table-column>
          <el-table-column prop="type_name" label="渠道" align="center"> </el-table-column>
          <el-table-column prop="point_level" label="级别" align="center"></el-table-column>
          <el-table-column prop="org_name" label="分公司" align="center"> </el-table-column>
          <!-- <el-table-column prop="point_applicant" label="创建人" align="center"> </el-table-column> -->
          <el-table-column prop="point_submitter" label="提交人" align="center"> </el-table-column>
          <el-table-column prop="point_status" label="审批状态" align="center"></el-table-column>
          <el-table-column prop="created_at" label="提交时间" align="center"></el-table-column>
          <el-table-column label="操作" width="90" align="center">
            <template slot-scope="scope">
              <i class="el-icon-edit" style="cursor: pointer" @click="edit(scope.row)" size="medium" title="修改" v-if="isShowUpdate"></i>
              <i class="el-icon-view" style="cursor: pointer" @click="detail(scope.row)" size="medium" title="查看" v-if="isShowView"></i>
              <i class="el-icon-check" style="cursor: pointer" @click="evaluate(scope.row)" size="medium" title="审批" v-if="scope.row.evaluate"></i>
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
      form:{

      },
      showInfo:"2",//1显示 2不显示
      pointRecordList:[],//点位记录列表
      buildTypeList:[],//渠道列表
      orgList:[],//分公司列表
      pointName:"",//点位名称
      approvalStatus:"",//审批状态
      approvalStatusList:[],//审批状态列表
      treatStatus:"",//处理状态
      treatStatusList:[],//处理状态列表
      submitTime:[],//提交时间
      submitPerson:"",//提交人
      submitPersonList:[],//提交人列表
      creator:"",//创建人
      creatorList:[],//创建人列表
      orgName:"",//选择的分公司
      buildType:"",//选择的渠道
      pointLevel:"",//级别
      pointLevelList:[],//级别列表
      dialogFormVisible: false,
      creatorChangeList:[],
      newCreator:"",//新创建人
      newCreatorList:[],//新负责人列表
      isShowSearch:true,//是否显示搜索
      isShowCreate:false,//是否显示新建
      isShowTransfer:false,//是否显示转交
      isShowExport:false,//是否显示导出
      isShowUpdate:false,//是否有编辑权限
      isShowView:false,//是否有查看权限
      // isShowEvaluate:false,
      multipleSelection:[],//复选框选项
      listCount:0,
      restaurants :[],//点位名称建议列表
      pageSize:20,
      exportUrl: '/point-evaluation/export-point?'
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
      //初始化数据
       axios.get('/point-evaluation/list').then((res)=>{
        console.log("res",res);
        const initData =res.data;
        if(initData.error_code== 0){
            this.pointRecordList=initData.data.pointList.pointArray;
            this.pointLevelList=initData.data.pointLevel;
            this.buildTypeList=initData.data.buildTypeList;
            this.restaurants=initData.data.pointNameList;//点位名称列表
            this.orgList=initData.data.orgList;
            this.submitPersonList=initData.data.approver;
            this.creatorList=initData.data.creatorList;
            this.approvalStatusList=initData.data.pointStatus;
            this.treatStatusList=initData.data.handle_status;
            this.newCreatorList=initData.data.newCreator;
            this.isShowTransfer=initData.data.roleList.transmit;
            this.isShowCreate=initData.data.roleList.create;
            this.isShowExport=initData.data.roleList.export;
            this.isShowUpdate=initData.data.roleList.update;
            this.isShowView=initData.data.roleList.view;
            // this.isShowEvaluate=initData.data.roleList.evaluate;
            this.listCount=parseInt(initData.data.pointList.totalCount);
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
      let data=this.pointRecordList.map((item,index)=>{
        item.index=index+1;
      })
    },
    changePageData(page){
      this.searchData(page-1)

    },
    //编辑
    edit(row){
      this.$router.push({name:"pointRecord",query:{pointId:row.id}});
    },
    //查看
    detail(row){
      this.$router.push({name:"pointDetail",query:{pointId:row.id}});
    },
    //
    evaluate(row){
      this.$router.push({name:"pointDetail",query:{pointId:row.id,evaluate:true}});
    },
    //导出
    exportRecord(){
      axios.post('/point-evaluation/export-point')
      .then((response)=> {
        let datas = response.data;
        console.log("导出...",datas);
        if(datas.error_code==0){
            this.alertMsg('导出成功','success');
        }else{
           this.alertMsg('网络异常','error');
        }
      })
      .catch((error)=> {
        this.alertMsg(error);
      });
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
          return (restaurant.value
.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
        };
      },
    //复选框选项发生改变
    handleSelectionChange(val) {
        this.multipleSelection = val;
        console.log("selection",this.multipleSelection);
    },
    //重置条件
    resetCondition(){
      this.submitTime=[];
      this.submitPerson="";
      this.orgName="";
      this.buildType="";
      this.pointName="";
      this.pointLevel="";
      this.approvalStatus="";
      this.treatStatus="";
    },
    //新建
    goRecord(){
       this.$router.push({name:"pointRecord",query:{pointId:""}});
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
          pointList.push({id:item.id,point_applicant:item.point_applicant});//point_applicant创建人
        }
        let transmitData={
          transferInfo:{
            new_creator_name:this.newCreator
          },
          transferPointList:pointList
        }
        console.log('tran',transmitData);
        axios.post('/point-evaluation/transfer',transmitData).then((res)=>{
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
        point_name:this.pointName,
        point_applicant:this.creator,//创建人
        point_submitter:this.submitPerson,//提交人
        point_level: this.pointLevel,
        point_status:this.approvalStatus,
        build_type_id:this.buildType,
        beginTime:this.submitTime[0],
        endTime:this.submitTime[1],
        handle_status:this.treatStatus,
        org_id:this.orgName,
        page:page
      }
      if(this.submitTime.length==0){
        params.beginTime="";
        params.endTime="";
      }
      console.log('params',params);
      axios.post('/point-evaluation/search-point',params)
      .then((response)=> {
        let datas = response.data;
        console.log("data",datas);
        if(datas.error_code==0){
            this.pointRecordList=datas.data.pointList.pointArray;
            this.listCount=parseInt(datas.data.pointList.totalCount);
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
