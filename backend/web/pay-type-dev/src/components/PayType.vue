<template>
  <div class="content-body">
    <div class="line-title">支付方式</div>
    <el-main>
      <el-form :label-position="labelPosition" ref="form" :model="form" :rules="rules" size="small" label-width="170px">
        <el-row :gutter="10">
          <el-col :span="12">
            <el-form-item label="支付方式">
              <el-input readonly v-model="payType" style="width:300px;"></el-input>
              <!-- <el-select v-model="id" disabled placeholder="请选择" @change="payTypeChange">
                <el-option v-for="item in payTypeList"
                  :label="item.label"
                  :value="item.value"
                  :key="item.value">
                </el-option>
              </el-select> -->
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :span="12">
            <el-form-item label="请设置支付方式图标">
              <el-upload
                action=""
                accept="image/png"
                :on-change="onIconUploadChange"
                :auto-upload="false"
                :show-file-list="false">
                <el-button slot="trigger" size="small" type="primary">点击上传</el-button>
                <div slot="tip" class="el-upload__tip">只能上传png文件,宽80px,高80px,且不能超过50k</div>
              </el-upload>
            </el-form-item>
          </el-col>
          <el-col :span="10">
            <div class="img-area" v-show="payTypeIconShow">
              <img class="img-pay-type-icon" :src="payTypeIconUrl">
              <img class="img-close" @click="closeIcon" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAMBAMAAACQIoDIAAAAD1BMVEUAAAAAAAAAAAAAAAAAAABPDueNAAAABXRSTlMAP+sf8L9zLYUAAAA3SURBVAjXYwACAyBmBmIgqQgkhIB8ECXAwKgIZgFpEA8ChASBglBhFwFMJkIBQhvCMIQVSBYDAOIcA3Udj1XDAAAAAElFTkSuQmCC">
            </div>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :span="12">
            <el-form-item label="请设置二维码中心图标">
              <el-upload
                action=""
                accept="image/jpeg,image/png"
                :on-change="onQrbgUploadChange"
                :auto-upload="false"
                :show-file-list="false">
                <el-button slot="trigger" size="small" type="primary">点击上传</el-button>
                <div slot="tip" class="el-upload__tip">只能上传jpg/png文件,宽40px,高40px,且不能超过20k</div>
              </el-upload>
            </el-form-item>
          </el-col>
          <el-col :span="10">
            <div class="img-area" v-show="qrBgShow">
              <img class="img-qr-icon" :src="qrBgUrl">
              <img class="img-close" @click="closeQrBg" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAMBAMAAACQIoDIAAAAD1BMVEUAAAAAAAAAAAAAAAAAAABPDueNAAAABXRSTlMAP+sf8L9zLYUAAAA3SURBVAjXYwACAyBmBmIgqQgkhIB8ECXAwKgIZgFpEA8ChASBglBhFwFMJkIBQhvCMIQVSBYDAOIcA3Udj1XDAAAAAElFTkSuQmCC">
            </div>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :span="12">
            <el-form-item label="是否支持优惠策略" prop="is_support_strategy">
              <el-select v-model="form.is_support_strategy" clearable :disabled="strategyDisabled" placeholder="请选择">
                <el-option label="是" value="1"></el-option>
                <el-option label="否" value="0"></el-option>
              </el-select>
            </el-form-item>
            <p v-show="strategyDisabled" class="strategy-tip">有设备在使用此支付方式的优惠策略</p>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :span="12">
            <el-form-item label="是否默认打开" prop="is_show">
              <el-select v-model="form.is_show" clearable placeholder="请选择" @change="isShowChange">
                <el-option label="是" value="1"></el-option>
                <el-option label="否" value="0"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="10" v-if="defaultStrategyShow">
          <el-col :span="12">
            <el-form-item label="默认优惠策略" prop="default_strategy">
              <el-select v-model="form.default_strategy" clearable  filterable placeholder="请选择">
                <el-option v-for="item in defaultStrategyList"
                  :label="item.label"
                  :value="item.value"
                  :key="item.value">
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="10" v-if="serialIdShow">
          <el-col :span="12">
            <el-form-item label="序号" prop="serial_id">
              <el-input v-model="form.serial_id" style="width:200px"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item size="medium" class="div_center">
          <!-- <el-button @click="backToList">取消任务</el-button> -->
          <!-- <el-button @click="resetForm('form')">重置表单</el-button> -->
          <el-button type="primary"  @click="submitForm('form')">提交</el-button>
        </el-form-item>
        <p v-show="submitTipShow" class="submit-tip">请检查所有必选项</p>
      </el-form>
    </el-main>
  </div>
</template>
<script>
/* eslint-disable */
import axios from 'axios'
export default {
  data (params) {
    var checkUnregularChar = (rule,value,callback) => {
      setTimeout(() => {
        let pattern = new RegExp("^[0-9]*$");
        if (!pattern.test(value)) {
          callback(new Error('请输入数字'));
        } else {
          callback();
        }
      }, 200);
    };
    return {
      payTypeData: {},
      labelPosition: 'right',
      id:"",
      form: {
        pay_type_icon:"",
        qr_bg:"",
        is_support_strategy:"",
        is_show:"",
        default_strategy:"",
        serial_id:"0"
      },
      strategyDisabled: false,
      submitTipShow: false,
      payType:"",
      payTypeIconUrl:"",
      qrBgUrl:"",
      originalQrBgUrl:"",
      submitFormUrl: "/pay-type/update-save",
      submitFlag:true,
      rules: {
        // id: [
        //   { required: true, message: '请选择支付方式', trigger: 'change' }
        // ],
        is_support_strategy:[
          { required: true, message: '请选择是否支持优惠策略', trigger: 'change' }
        ],
        is_show:[
          { required: true, message: '请选择是否默认打开', trigger: 'change' }
        ],
        default_strategy:[
          { required: false, message: '请选择默认优惠策略', trigger: 'change' }
        ],
        serial_id: [
          { required: true, message: '请输入序号', trigger: 'blur' },
          { validator: checkUnregularChar, trigger: 'blur' }
        ]
      },
      payTypeList: [],
      defaultStrategyList: []
    }
  },
  computed:{
    defaultStrategyShow: function() {
      if(this.form.is_support_strategy=="1"&&this.form.is_show=="1") {
        return true;
      }
      this.form.default_strategy="";
      return false;
    },
    payTypeIconShow: function() {
      if(this.payTypeIconUrl!="") {
        return true;
      }
      return false;
    },
    qrBgShow: function() {
      if(this.qrBgUrl!="") {
        return true;
      }
      return false;
    },
    serialIdShow: function() {
      if(this.form.is_show=="1") {
        return true;
      }
      this.form.serial_id = "0";
      return false;
    }
  },
  mounted()
  {
    this.init();
  },
  methods: {
    init()
    {
      window.parent.onscroll = (e)=>{
        this.scrollMsg();
      }
      this.payTypeData = rootPayTypeData||{};
      for(let item in this.payTypeData) {
        this.payTypeList.push({"value":item,"label":this.payTypeData[item].name,"strategy":this.payTypeData[item].discount_strategy});
      }
      console.log("payTypeData..",this.payTypeData);
      let id = this.getQueryString("id");
      console.log(id)
      if(id!=""){
        let payData = rootData||{};
        this.id = payData.id;
        this.changeStrategy(this.id);
        this.form.is_support_strategy = String(payData.is_support_strategy);
        this.form.is_show = String(payData.is_show);
        this.form.default_strategy = String(payData.default_strategy);
        this.form.serial_id = payData.serial_id;
        this.payTypeIconUrl = payData.pay_type_icon_url;
        this.qrBgUrl = payData.qr_bg_url;
        if(payData.is_use_build==1) {
          this.form.is_support_strategy = "1";
          this.strategyDisabled = true;
        }
        this.originalQrBgUrl = payData.qr_bg_url;
      }
      console.log("this.payTypeIconUrl..",this.payTypeIconUrl);
    },
    isShowChange(){
      if(this.form.is_show==1) {
        this.form.serial_id = "1";
      }
    },
    closeIcon(){
      this.form.pay_type_icon = "";
      this.payTypeIconUrl = "";
    },
    closeQrBg(){
      this.form.qr_bg = "";
      this.qrBgUrl = "";
    },
    payTypeChange(){
      this.changeStrategy(this.id);
    },
    changeStrategy(type){
      if(type=="") {
        return false;
      }
      this.defaultStrategyList = [];
      this.form.default_strategy = "";
      let tmpObj = this.payTypeList.find(item=>item.value==type);
      this.payType = tmpObj.label;
      // console.log("tmpObj..",tmpObj)
      for(let item in tmpObj.strategy) {
        this.defaultStrategyList.push({"value":item,"label":tmpObj.strategy[item]})
      }
    },
    onIconUploadChange(file) {
      console.log("file..",file)
        const isIMAGE = (file.raw.type === 'image/png');
        const isLt1M = file.size / 1024 / 50 < 1;
        if (!isIMAGE) {
          this.$message.error('只能上传png图片!');
          return false;
        }
        if (!isLt1M) {
          this.$message.error('上传文件大小不能超过 50k !');
          return false;
        }
        let reader = new FileReader();
        reader.readAsDataURL(file.raw);
        reader.onload = e=>{
          let data = e.target.result;
          let img = new Image();
          img.onload = ()=> {
            if(img.width!=80||img.height!=80){
              this.$message.error('图片宽高尺寸不对，请重新上传');
            } else {
              this.form.pay_type_icon = data;//图片的base64数据
              this.payTypeIconUrl = data;
            }
          }
          img.src= data;
        }
    },
    onQrbgUploadChange(file) {
        const isIMAGE = (file.raw.type === 'image/jpeg' || file.raw.type === 'image/png');
        const isLt1M = file.size / 1024 / 20 < 1;
        if (!isIMAGE) {
          this.$message.error('只能上传jpg/png图片!');
          return false;
        }
        if (!isLt1M) {
          this.$message.error('上传文件大小不能超过 20k !');
          return false;
        }
        let reader = new FileReader();
        reader.readAsDataURL(file.raw);
        reader.onload = e=>{
          let data = e.target.result;
          let img = new Image();
          img.onload = ()=> {
            if(img.width!=40||img.height!=40){
              this.$message.error('图片宽高尺寸不对，请重新上传');
            } else {
              this.form.qr_bg = data;//图片的base64数据
              this.qrBgUrl = data;
            }
          }
          img.src= data;
        }
    },
    submitForm(formName)// 提交任务
    {
      // console.log(this.id)
      this.$refs[formName].validate((valid) => {
        if (valid) {
          if(this.form.pay_type_icon==""&&this.payTypeIconUrl==""){
            this.alertMsg("请上传图标");
          } else {
            this.submitTipShow = false;
            this.submitFormAction();
          }
        } else {
          console.log("invalid");
          this.submitTipShow = true;
          return false;
        }
      });
    },
    submitFormAction()// 提交任务接口
    {
      if(!this.submitFlag) return false;
      this.submitFlag = false;
      if(this.originalQrBgUrl!=""&&this.qrBgUrl==""){
        this.form.qr_bg = "-1";
      }
      var postUrl = this.submitFormUrl+"?id="+this.id;
      var params = this.form;
      console.log("传到后台参数",params)
      axios.post(postUrl,params).then((response)=> {
        var data = response.data;
        console.log('data..'+data);
        if(data.error_code!=0){
          this.alertMsg("提交失败");
          this.submitFlag = true;
        } else {
          this.alertMsg("提交成功！","success");
          window.setTimeout(function(){
            window.location.href = "/pay-type/index";
          },1000);
        }
      }).catch((error)=> {
        this.alertMsg(error);
        this.submitFlag = true;
      });
    },
    backToList()
    {
      window.setTimeout(function(){
        // window.location.href = "";
      },300);
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
    guid()//生成guid ,解决排序
    {
      return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
          var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
          return v.toString(16);
      });
    },
    getQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) {
            return decodeURI(r[2])
        }
        return ''
    }
  }
}
</script>
<style>
  input[type="file"] {
    display: none;
  }
  .line-title {
    width: 100%;
    height: 50px;
    line-height: 50px;
    font-size: 22px;
  }
  .el-row {
    margin-bottom: 10px;
    &:last-child {
      margin-bottom: 0;
    }
  }
  .img-pay-type-icon {
    width: 80px;
    height: 80px;
  }
  .img-qr-icon{
    width: 40px;
    height: 40px;
  }
  .img-area .img-close {
    cursor: pointer;
    vertical-align: top;
  }
 .div_center {
    text-align: center;
    width:100%;
    margin:0 auto 20px -85px;
  }
  .strategy-tip {
    margin-left: 170px;
    margin-top: -10px;
    font-size: 12px;
    color: red;
    line-height: 15px;
  }
  .submit-tip {
    margin-top: -10px;
    font-size: 12px;
    color: red;
    text-align: center;
    line-height: 15px;
  }
</style>