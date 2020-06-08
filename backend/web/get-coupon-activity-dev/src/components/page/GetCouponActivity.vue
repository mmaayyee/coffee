<template>
  <div class="content-body">
    <div class="line-title">领券活动</div>
    <el-main>
      <el-form :label-position="labelPosition" ref="form" :model="form" :rules="rules" size="small" label-width="120px">
        <el-row :gutter="10">
          <el-col :span="10">
            <el-form-item label="活动名称" prop="activityName">
              <el-input v-model="form.activityName"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="活动起始日期" prop="activityTime">
              <el-date-picker type="datetimerange" range-separator="至" start-placeholder="开始日期"  end-placeholder="结束日期" format="yyyy-MM-dd HH:mm:ss" value-format="yyyy-MM-dd HH:mm:ss" v-model="form.activityTime">
              </el-date-picker>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :span="10">
            <el-form-item label="活动状态" prop="activityStatus">
              <el-select v-model="form.activityStatus" placeholder="请选择">
                <el-option v-for="(value,key) in data.activityStatus"
                  :label="value"
                  :value="String(key)"
                  :key="key">
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="10">
            <el-form-item label="是否验证关注" prop="isVerifySubscribe">
              <el-select v-model="form.isVerifySubscribe" placeholder="请选择">
                <el-option v-for="(value,key) in data.isVerifySubscribe"
                  :label="value"
                  :value="String(key)"
                  :key="key">
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :span="10">
            <el-form-item label="每人" prop="days">
                <el-input type="number" min=0 value="0" style="width:100px" v-model="form.days"></el-input>天可领取1次
            </el-form-item>
          </el-col>
          <el-col :span="10">
            <el-form-item label="绑定参数">
                <el-input type="text" value="" v-model="form.bindParams"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :span="2" style="width:120px;">
            <div class="sub-title"><span style="color:#F56C6C">*</span> 活动描述</div>
          </el-col>
          <el-col :span="20">
            <quill-editor
              v-model="form.activityDesc"
              ref="myQuillEditor"
              :options="editorOption"
              class="editor">
            </quill-editor>
          </el-col>
        </el-row>
        <div>
          <el-row :gutter="10">
            <el-col :span="12">
              <el-form-item label="优惠券类型" prop="couponType">
                <el-select v-model="form.couponType" clearable placeholder="请选择">
                  <el-option label="优惠券" value="2"></el-option>
                  <el-option label="优惠券套餐" value="1"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
        </div>
        <div v-for="(item,index) in form.couponGradient" :key="item.id" v-if="form.couponType==2">
          <el-row :gutter="10">
            <el-col :span="10">
              <el-form-item label="优惠券" :prop="'couponGradient.' +index +'.id'" :rules="couponIds">
                <el-select v-model="item.id" clearable filterable placeholder="请选择">
                  <el-option v-for="(value,key) in data.couponList"
                    :label="value"
                    :value="String(key)"
                    :key="key">
                  </el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="6">
              <el-form-item label="数量：" :prop="'couponGradient.' +index +'.num'" :rules="couponNum">
                <el-input type="number" min=1 value="1" v-model="item.num"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="6">
              <i class="el-icon-remove-outline iconButton" @click="delCouponGradient(index)" v-if="index!=0"></i>
              <i class="el-icon-circle-plus-outline iconButton" @click="addCouponGradient" v-if="index==0"></i>
            </el-col>
           </el-row>
        </div>
        <el-row :gutter="10" v-if="form.couponType==1">
          <el-col :span="12">
            <el-form-item label="优惠券套餐" prop="couponGroupId">
              <el-select v-model="form.couponGroupId" clearable  filterable placeholder="请选择">
                <el-option v-for="(value,key) in data.couponGroupList"
                  :label="value"
                  :value="String(key)"
                  :key="key">
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item size="medium" class="div_center">
          <el-button @click="resetForm" :disabled="submitDisabled">取消</el-button>
          <el-button type="primary" :disabled="submitDisabled"  @click="submitForm('form')">保存</el-button>
        </el-form-item>
      </el-form>
    </el-main>
  </div>
</template>
<script>
/* eslint-disable */
import axios from 'axios'
// axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded';
export default {
  data (params) {
    let checkUnregularChar = (rule,value,callback) => {
      setTimeout(() => {
        let pattern = new RegExp("[`~!@#$^&*()=|{}':;',\\[\\].<>/?~！@#￥……&*（）——|{}【】‘；：”“'。，、？]");
        if (pattern.test(value)) {
          callback(new Error('不能有特殊字符'));
        } else {
          callback();
        }
      }, 200);
    };
    let checkPositiveInteger = (rule,value,callback) => {
      setTimeout(() => {
        let pattern = new RegExp("^[0-9]*$");
        if (!pattern.test(value)||value<=0) {
          callback(new Error('请输入0以上整数'));
        } else {
          callback();
        }
      }, 200);
    };
    return {
      data: {},

      activityId: '',
      // submitFormUrl: '/get-coupon-activity/update',
      labelPosition: 'right',
      form: {
        activityName: '',
        activityStatus: '',
        activityDesc: '',
        activityTime: '',
        isVerifySubscribe:'',
        couponType: '',
        couponGroupId: '',
        days:0,
        bindParams:'',
        couponGradient:[{id:"",num:"1"}]
      },
      submitDisabled:false,
      rules: {
        activityName: [
          { required: true, message: '请输入活动名称', trigger: 'blur' },
          { min: 2, max: 50, message: '长度在 2 到 50 个字符', trigger: 'blur' },
          { validator: checkUnregularChar, trigger: 'blur' }
        ],
        activityTime: [
          { required: true, message: '请选择日期', trigger: 'change' }
        ],
        activityStatus:[
          { required: true, message: '请选择上线状态', trigger: 'blur' }
        ],
        isVerifySubscribe:[
          { required: true, message: '请选择是否验证关注', trigger: 'blur' }
        ],
        couponType:[
          { required: true, message: '请选择优惠券类型', trigger: 'blur' }
        ],
        couponGroupId: [
          { required: true, message: '请选择优惠券套餐', trigger: 'blur' }
        ]
      },
      couponIds:[{ required: true, message: '请选择优惠券', trigger: 'change' }],
      couponNum:[{ required: true, message: '请输入数量', trigger: 'blur' },
                 { validator: checkPositiveInteger, trigger: 'blur' }],
      editorOption: {
        placeholder: "请输入内容",
        modules: {
          toolbar: [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ 'header': 1 }, { 'header': 2 }],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            [{ 'script': 'sub' }, { 'script': 'super' }],
            [{ 'indent': '-1' }, { 'indent': '+1' }],
            [{ 'direction': 'rtl' }],
            [{ 'size': ['small', false, 'large', 'huge'] }],
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            // [{ 'font': [] }],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'align': [] }],
            // ['clean'],
            ['link']
            // ['link', 'image', 'video']
          ]
        }
      }
    }
  },
  computed: {
    editor() {
      return this.$refs.myQuillEditor.quill
    }
  },
  mounted()
  {
    this.init();
  },
  methods: {
    init()
    {
      window.parent.addEventListener('scroll',(e)=>{
        this.scrollMsg();
      },false);
      this.data = rootData;
      this.activityId = this.getQueryString("id");
      console.log("activityId..",this.activityId);
      if(this.activityId==null){
        this.activityId = "";
      }
      if(this.activityId!=""){
        this.form.activityName = this.data.activity.activityData.activity_name;
        this.form.activityStatus = String(this.data.activity.activityData.status);
        this.form.activityDesc = this.data.activity.activityData.activity_desc;
        this.form.activityTime = [this.data.activity.activityData.start_time,this.data.activity.activityData.end_time];
        this.form.isVerifySubscribe = String(this.data.activity.is_verify_subscribe);
        this.form.couponType = String(this.data.activity.couponData.type);
        this.form.days=this.data.activity.days;
        this.form.bindParams=this.data.activity.activityData.bind_params;
        //优惠券类型
        if(this.form.couponType==1){
          console.log("编辑时类型为优惠券套餐");
          this.form.couponGroupId=String(this.data.activity.couponData.coupon_group_id);
        }else{
          console.log("编辑时类型为优惠券");
          this.form.couponGradient=[];
          let sendCouponList = this.data.activity.couponData.sendCouponList;
          for(let key in sendCouponList){
            this.form.couponGradient.push({id : key, num : sendCouponList[key]});
          }
        }
      }
    },
    //添加优惠券梯度
    addCouponGradient(index){
      this.form.couponGradient.push({id:"",num:"1"});
      console.log(this.form.couponGradient)
    },
    //删除优惠券梯度
    delCouponGradient(index){
      this.form.couponGradient.splice(index,1)
    },
    submitForm(formName)// 提交任务
    {
      console.log("时间日期###.."+this.form.activityTime+"***");
      this.$refs[formName].validate((valid) => {
        if (valid) {
          if(this.form.activityDesc==""){
            this.alertMsg("活动说明不能为空");
          } else {
            this.submitFormAction();
          }
        } else {
          // console.log("couponGroupId.."+this.form.couponGroupId);
          return false;
        }
      });
    },
    submitFormAction()// 提交任务接口
    {
      console.log("提交");
      this.submitDisabled = true;
      let postUrl = this.activityId==""?'/get-coupon-activity/create':'/get-coupon-activity/update';
      let sendCouponList = {};
      if(this.form.couponType==2){
        this.form.couponGroupId = "";
        if (this.form.couponGradient<=0) {
          console.log('请选择优惠券');
          return false;
        }
        this.form.couponGradient.forEach(item=>{
          sendCouponList[item.id]=item.num;
        })
      }
      let params={
        "activityData": {
          "activity_id": this.activityId,
          "activity_name": this.form.activityName,
          "status": this.form.activityStatus,
          "activity_desc": this.form.activityDesc,
          "start_time": this.form.activityTime[0],
          "bind_params": this.form.bindParams,
          "end_time": this.form.activityTime[1]

        },
        "days": this.form.days,
        "is_verify_subscribe": this.form.isVerifySubscribe,
        "couponData": {
          "type": this.form.couponType,
          "coupon_group_id": this.form.couponGroupId,
          "sendCouponList": sendCouponList
        }
      };
      console.log("传到后台参数",params)
      const header = {
        'content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
      }
      axios.post(postUrl,params,header).then((response)=> {
        let data = response.data;
        console.log('data..',data);
        if(data.error_code==0){
          this.alertMsg("保存成功！","success");
          window.setTimeout(function(){
            window.location.href = "/index.php/get-coupon-activity/index";
          },1000);
        } else {
          this.alertMsg(data.msg);
          this.submitDisabled = false;
        }
      }).catch((error)=> {
        this.alertMsg(error);
        this.submitDisabled = false;
      });
    },
    resetForm()// 重置表单
    {
      this.form.activityDesc = "";
      this.$refs['form'].resetFields();
    },
    backToList()
    {
      window.setTimeout(function(){
        window.location.href = "/index.php/get-coupon-activity/index";
      },300);
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
        // console.log("scrollTop...",scrollTop);
        let mycss=document.getElementsByClassName("el-message")[0];
        if(mycss){
          mycss.style.cssText="top: "+scrollTop+"px;z-index:1000;";
        }
      }
    },
    guid()//生成guid ,解决排序
    {
      return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
          let r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
          return v.toString(16);
      });
    },
    getQueryString(name) {
        let reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        let r = window.location.search.substr(1).match(reg);
        if (r != null) {
            return decodeURI(r[2])
        }
        return null
    }
  }
}
</script>
<style>
  .line-title {
    width: 100%;
    height: 45px;
    line-height: 45px;
    font-size: 22px;
  }
  .sub-title {
      font-size: 14px;
      width: 100px;
      padding: 10px;
      text-align: right;
      color: #606266;
  }
  .el-row {
    margin-bottom: 10px;
    &:last-child {
      margin-bottom: 0;
    }
  }
  /* .el-form-item{
      margin-bottom: 25px;
  } */
 .div_center {
    text-align: center;
    width:100%;
    margin:0 auto;
    margin-bottom: 20px;
  }
  .iconButton{
    font-size:24px;
    position: relative;
    top:3px;
    cursor:pointer;
  }
</style>