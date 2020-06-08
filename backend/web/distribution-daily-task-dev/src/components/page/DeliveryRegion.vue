<template>
  <div class="content-body">
    <p class="line-title">配送区域详情</p>
    <div v-show="homeShow">
      <el-form :label-position="labelPosition" ref="regionForm" :model="regionForm" :rules="rules" size="small" label-width="120px">
        <el-row :gutter="10">
          <el-col :span="10">
            <el-form-item label="配送区域名称" prop="regionName">
              <el-input v-model="regionForm.regionName"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :span="10">
            <el-form-item label="营业状态" prop="businessStatus">
              <el-select  placeholder="请选择" clearable v-model="regionForm.businessStatus">
                  <el-option value="1" label="正常"></el-option>
                  <el-option value="2" label="暂停"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :span="8">
            <el-form-item label="营业时间" prop="businessTime">
              <el-time-picker
                is-range
                v-model="regionForm.businessTime"
                range-separator="至"
                start-placeholder="开始时间"
                end-placeholder="结束时间"
                placeholder="选择时间范围" format="HH:mm" value-format="HH:mm">
              </el-time-picker>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :span="10">
            <el-form-item label="起送价格(元)" prop="minConsum">
              <el-input v-model="regionForm.minConsum"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :span="3" style="width:120px;">
            <div class="label-txt">配送区域</div>
          </el-col>
          <el-col :span="3" v-if="addBtnShow">
            <el-button type="primary" size="small" @click="addMap">新增</el-button>
          </el-col>
          <el-col :span="6" v-if="editBtnShow">
            已添加配送区域 <el-button type="primary" size="small" @click="editMap">编辑</el-button>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :span="3" style="width:120px;">
            <div class="label-txt">配送员设置</div>
          </el-col>
          <el-col :span="6">
            <el-button type="primary" size="small" @click="addPerson">添加配送员</el-button>
          </el-col>
        </el-row>
        <div v-for="(item,index) in personList" :key="item.guid" class="pserson-list">
          配送员:
          <el-select v-model="item.id" filterable placeholder="请选择" style="width:100px;">
          <el-option v-for="item in personListData"
            :label="item.label"
            :value="item.value"
            :key="item.value">
          </el-option>
        </el-select>
        <el-button type="primary" size="small" @click="deletePerson(index)">删除</el-button>
        </div>
        <p class="bulid-list-title">配送区域包含点位</p>
        <div v-for="(item,index) in buildList" :key="index" class="bulid-list">点位名称: {{ item }}</div>
        <el-form-item size="medium" class="div-center">
          <el-button type="primary"  @click="submitForm" :disabled="submitBtnDisabled">保存</el-button>
        </el-form-item>
      </el-form>
    </div>
    <div class="delivery-region-map" v-show="regionMapShow">
      <v-delivery-region-map :set-region-data="setRegionData" :region-id="deliverRegionId" :region-data="coverageRange" :init-flag="mapInitFlag" @mapdata="getMapData"></v-delivery-region-map>
    </div>
  </div>
</template>
<script>
/* eslint-disable */
import axios  from  'axios'
import vDeliveryRegionMap from './DeliveryRegionMap'
export default {
  data() {
    var checkUnregularChar = (rule,value,callback) => {
      setTimeout(() => {
        let pattern = new RegExp("[`~!@#$^&*()=|{}':;',\\[\\].<>/?~！@#￥……&*（）——|{}【】‘；：”“'。，、？]");
        if (pattern.test(value)) {
          callback(new Error('不能有特殊字符'));
        } else {
          callback();
        }
      }, 200);
    };
    var checkNumber = (rule,value,callback) => {
      setTimeout(() => {
        if (isNaN(Number(value))) {
          callback(new Error('只允许输入数字'));
        } else {
          callback();
        }
      }, 200);
    };
    return {
      homeShow: true,
      regionMapShow: false,
      addBtnShow: false,
      editBtnShow: false,
      mapInitFlag: false,
      setRegionData: 0,
      deliverRegionId: '',
      labelPosition: 'left',
      regionForm: {
        regionName: '',
        businessStatus: '',
        businessTime: ['08:00', '18:00'],
        minConsum: ''
      },
      rules: {
        regionName: [
          { required: true, message: '请输入名称', trigger: 'blur' },
          { min: 2, max: 50, message: '长度在 2 到 50 个字符', trigger: 'blur' },
          { validator: checkUnregularChar, trigger: 'blur' }
        ],
        businessStatus: [
          { required: true, message: '请选择类型', trigger: 'change' }
        ],
        businessTime: [
          { required: true, message: '请选择时间', trigger: 'change' }
        ],
        minConsum: [
          { required: true, message: '请输入起送价格', trigger: 'blur' },
          { validator: checkNumber, trigger: 'blur' }
        ]
      },
      homeType: '',
      mapEditble: '',
      buildList: [],
      personList: [],
      personListData: [],
      // allBuildList: [],
      coverageRange: '',
      submitBtnDisabled: false
    }
  },
  computed: {
  },
  mounted() {
    this.init();
  },
  methods: {
    init() {
      // this.addMap();
      window.parent.onscroll = (e)=>{
        this.scrollMsg();
      }
      this.deliverRegionId = this.getUrlParam("id");
      this.mapInitFlag = true;
      if(this.deliverRegionId==null) {
        this.addBtnShow = true;
      } else {
        $.ajax({
          type: "POST",
          url: rootCoffeeUrl+"delivery-api/region-info.html",
          dataType: "json",
          data: {delivery_region_id: this.deliverRegionId},
          success: data=>{
              console.log("获取区域详情api..",data);
              if(data.status=="success"){
                  this.setRegionDetail(data.data);
              } else {
                  this.alertMsg(data.msg);
              }
          },
          error: (xhr,type)=>{
              console.log("接口错误",xhr);
          }
        });
      }
      $.ajax({
        type: "POST",
        url: rootCoffeeUrl+"delivery-api/get-person-list.html",
        dataType: "json",
        data: {delivery_region_id: this.deliverRegionId==null?'':this.deliverRegionId},
        success: data=>{
            console.log("获取所有配送员名单api..",data);
            this.personListData = data.map(item=>{
              return {label:item.person_name,value:item.person_id}
            })
            console.log(this.personListData)
        },
        error: (xhr,type)=>{
            console.log("接口错误",xhr);
        }
      });
    },
    setRegionDetail(data) {
      if(data.coverage_range.length>0) {
          this.editBtnShow = true;
          this.coverageRange = JSON.stringify(data.coverage_range);
          // console.log(this.coverageRange)
      }
      this.buildList = data.build_list.map(item=>{
        return item.name;
      });
      this.regionForm.regionName = data.region_name;
      this.regionForm.businessStatus = String(data.business_status);
      this.regionForm.businessTime = [data.start_time,data.end_time];
      this.regionForm.minConsum = data.min_consum;
      this.personList = data.person_list.map(item=>{
        return {id:item.person_id,guid:this.guid()}
      })
    },
    getMapData(data) {
      console.log(data);
      this.regionMapShow = false;
      this.homeShow = true;
      if(data.type=="save") {
        console.log("this.addBtnShow..",this.addBtnShow);
        if(this.addBtnShow){
          this.addBtnShow = false;
          this.editBtnShow = true;
        }
        this.coverageRange = data.region;
        this.buildList = data.build_list;
      }
    },
    addPerson() {
      this.personList.push({id:'',guid:this.guid()})
    },
    deletePerson(index) {
      this.personList.splice(index,1);
    },
    addMap() {
      this.regionMapShow = true;
      this.homeShow = false;
    },
    editMap() {
      this.regionMapShow = true;
      this.homeShow = false;
      this.setRegionData+=1;
    },
    submitForm() {
      // this.submitBtnDisabled
      this.$refs["regionForm"].validate((valid) => {
        if (valid) {
          if(this.coverageRange=='') {
            this.alertMsg("请添加配送区域");
          } else if(this.personList.length==0) {
            this.alertMsg("请添加配送员");
          } else if(this.personList.some(item=>item.id=='')) {
            this.alertMsg("请设置配送员");
          } else if(this.checkRepeatPerson()) {
            this.alertMsg("配送员有重复");
          } else {
            this.saveRegionData();
          }
        } else {
          this.alertMsg("请填写表单内容");
           return false;
        }
      });
    },
    saveRegionData() {
      this.submitBtnDisabled = true;
      let saveData = {
        delivery_region_id:this.deliverRegionId==null?'':this.deliverRegionId,
        region_name: this.regionForm.regionName,
        start_time: this.regionForm.businessTime[0],
        end_time: this.regionForm.businessTime[1],
        min_consum: this.regionForm.minConsum,
        business_status: this.regionForm.businessStatus,
        person_info: this.personList.map(item=>item.id),
        coverage_range: this.coverageRange
        // province: '北京',
        // city: '北京'
      };
      console.log("saveData..",saveData);
      $.ajax({
        type: "POST",
        url: rootCoffeeUrl+"delivery-api/region-create.html",
        dataType: "json",
        data: saveData,
        success: data=>{
            console.log("保存区域详情..",data);
            if(data.status=="success"){
                window.setTimeout(()=>{
                  window.location.href="/delivery-region/index";
                },1000);
            } else {
                this.alertMsg(data.msg);
                this.submitBtnDisabled = false;
            }
        },
        error: (xhr,type)=>{
            console.log("接口错误",xhr);
            this.submitBtnDisabled = false;
        }
      });
    },
    checkRepeatPerson(){
      let arrayRepeatFlag = false;
      let deliveryPersonList = this.personList.map(item=>item.id);
      // let deliveryPersonList = ["1","11","110"];
      let deliveryPersonListStr = JSON.stringify(deliveryPersonList);
      // let deliveryPersonListStr = '["1","11","110"]';
      console.log(deliveryPersonListStr)
      for(let i=0;i<deliveryPersonList.length;i++){
          if(deliveryPersonListStr.indexOf('"'+String(deliveryPersonList[i])+'"')!=deliveryPersonListStr.lastIndexOf('"'+String(deliveryPersonList[i])+'"')){
              arrayRepeatFlag = true;
              break;
          }
      }
      console.log(arrayRepeatFlag)
      return arrayRepeatFlag;
    },
    alertMsg(msg,type)
    {
      this.scrollMsg();
      let msgType = type?type:"error";
      this.$message({
        message: msg,
        duration:3600,
        type: msgType
      });
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
    //生成guid ,解决排序
    guid()
    {
      return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
          var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
          return v.toString(16);
      });
    },
    getUrlParam(name) {
      let reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
      let r = window.location.search.substr(1).match(reg);
      if (r != null) {
          return decodeURI(r[2])
      }
      return null
    }
  },
  components: {
    vDeliveryRegionMap
  }
}

</script>
<style scoped>
div {
    font-size: 14px;
    color: #666;
}
.delivery-region-map {
}
.label-txt {
  margin-top: 8px;
  margin-left: 12px;
  height: 30px;
}
.line-title{
  width: 100%;
  height: 50px;
  line-height: 50px;
  font-size: 22px;
}
.div-center {
  text-align: center;;
  width:100%;
  margin:0 auto;
  margin-top: 10px;
  margin-bottom: 20px;
}
.bulid-list-title {
  font-size: 15px;
  font-weight: bold;
  height: 24px;
  line-height: 24px;
  margin-top: 8px;
  margin-left: 12px;
}
.bulid-list {
  height: 24px;
  line-height: 24px;
  margin-left: 12px;
}
.pserson-list {
  height: 50px;
  line-height: 50px;
  margin-left: 12px;
}
</style>
