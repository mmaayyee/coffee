<template>
  <div class="content-body">
    <div class="line-title">发券任务添加</div>
    <el-main>
      <el-form :label-position="labelPosition" ref="form" :model="form" :rules="rules" size="small" label-width="120px">
        <el-row :gutter="10">
          <el-col :span="10">
            <el-form-item label="发券任务名称" prop="taskName">
              <el-input v-model="form.taskName"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="8" :offset="4">
            <el-form-item label="发送时间" prop="sendTime">
              <el-date-picker v-model="form.sendTime" format="yyyy-MM-dd HH:mm:ss" value-format="yyyy-MM-dd HH:mm:ss" type="datetime" placeholder="选择日期时间" :picker-options="sendTimePickerOption"></el-date-picker>
            </el-form-item>
          </el-col>
        </el-row>
        <div>
          <el-row :gutter="10">
            <el-col :span="12">
              <el-form-item label="优惠券类型" prop="couponType">
                <el-select v-model="form.couponType" clearable placeholder="请选择">
                  <el-option label="优惠券" value="1"></el-option>
                  <el-option label="优惠券套餐" value="2"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
        </div>
        <!-- 优惠券 -->
        <div v-for="(item,index) in form.couponGradient" :key="index"  v-if="form.couponType==1">
            <el-row :gutter="10">
              <el-col :span="10">
                <el-form-item label="优惠券" :prop="'couponGradient.' +index +'.id'" :rules="couponIds" id="couponIds">
                  <el-select v-model="item.id" clearable filterable placeholder="请选择">
                    <el-option v-for="item in couponGroupIdsOptions"
                      :label="item.label"
                      :value="item.value"
                      :key="item.value">
                    </el-option>
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :span="6">
                <el-form-item label="数量：">
                  <el-input type="number" min=1 value="1" v-model="item.num"></el-input>
                </el-form-item>
              </el-col>
              <el-col :span="6">
                <i class="el-icon-remove-outline iconButton" @click="delCouponGradient(index)" v-if="index!=0"></i>
                <i class="el-icon-circle-plus-outline iconButton" @click="addCouponGradient" v-if="index==0"></i>
              </el-col>
             </el-row>
          </div>
        <!-- 优惠券套餐 -->
        <el-row :gutter="10" v-if="form.couponType==2">
          <el-col :span="12">
            <el-form-item label="优惠券套餐" prop="couponGroupId">
              <el-select v-model="form.couponGroupId" clearable  filterable placeholder="请选择">
                <el-option v-for="item in couponGroupOptions"
                  :label="item.label"
                  :value="item.value"
                  :key="item.value">
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :span="12">
            <el-form-item label="用户筛选任务" prop="userSelectionTask">
              <el-select v-model="form.userSelectionTask" clearable filterable placeholder="请选择">
                <el-option v-for="item in userSelectTaskOptions" :label="item.label" :value="item.value" :key="item.value">
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :span="16">
            <el-row :gutter="10">
              <el-col :span="14">
                <el-form :label-position="labelPosition" ref="formUserPhone" :model="userPhoneForm" :rules="userPhoneRules" size="small" label-width="120px">
                  <el-form-item prop="importUserPhone" label="手机号">
                    <el-input v-model="userPhoneForm.importUserPhone" type="number"></el-input>
                  </el-form-item>
                </el-form>
              </el-col>
              <el-col :span="10">
                <el-button icon="el-icon-circle-plus-outline" @click="addTaskVerifyPhone">加入</el-button>
                <el-button icon="el-icon-delete" @click="clearUserPhone">清空</el-button>
              </el-col>
            </el-row>
            <!-- 手机号列表 -->
            <el-row>
              <el-col :span="20" :offset="4">
                <div class="phoneNameBox">
                  <el-row  v-for="(item,index) in form.phoneNumbersTextArea" :key="item.guid" class="phoneNameText">
                    <el-col :span="20">
                       <span>{{item.data}}</span>
                    </el-col>
                    <el-col :span="4">
                      <i class="el-icon-delete" @click="deletephoneNumbers(index)"></i>
                    </el-col>
                  </el-row>
                </div>
              </el-col>
            </el-row>
          </el-col>
          <el-col :span="6" :offset="1">
            <el-upload
              name="uploadPhoneTxt"
              :action="uploadActionUrl"
              :data="uploadData"
              :on-error="uploadError"
              :on-success="uploadSuccess"
              :on-remove="onRemoveTxt"
              :before-upload="onBeforeUpload"
              accept="text/plain"
              :limit="1"
              :on-exceed="handleExceed"
              :file-list="files">
              <el-button size="small" type="primary">点击上传</el-button>
              <div slot="tip" class="el-upload__tip">请上传txt格式文件，手机号码以空格或回车符或逗号","分隔</div>
            </el-upload>
            <div class="uploadTxtCallback">{{ uploadTxtCallbackInfo }}</div>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :span="16">
            <el-row :gutter="10">
              <el-col :span="14">
                <el-form :label-position="labelPosition" ref="formBlackPhone" :model="blackPhoneForm" :rules="blackPhoneRules" size="small" label-width="120px">
                  <el-form-item prop="sheildUserPhone" label="黑名单">
                    <el-input v-model="blackPhoneForm.sheildUserPhone" type="number"></el-input>
                  </el-form-item>
                </el-form>
              </el-col>
              <el-col :span="10">
                <el-button icon="el-icon-circle-plus-outline" @click="addBlackVerifyPhone">加入</el-button>
                <el-button icon="el-icon-delete" @click="clearBlackPhone">清空</el-button>
              </el-col>
            </el-row>
            <!-- 手机号列表 -->
            <el-row>
              <el-col :span="20" :offset="4">
                <div class="phoneNameBox">
                  <el-row  v-for="(item,index) in form.blacklistphoneNumTextArea" :key="item.guid" class="phoneNameText">
                    <el-col :span="20">
                       <span>{{item.data}}</span>
                    </el-col>
                    <el-col :span="4">
                      <i class="el-icon-delete" @click="deleteBlackNumbers(index)"></i>
                    </el-col>
                  </el-row>
                </div>
              </el-col>
            </el-row>
          </el-col>
          <el-col :span="6" :offset="1">
            <el-upload
              name="uploadBlackTxt"
              :action="uploadActionUrl"
              :data="uploadBlackData"
              :on-error="uploadError"
              :on-success="uploadSuccessBlack"
              :before-upload="onBeforeUpload"
              :on-remove="onRemoveBlack"
              accept="text/plain"
              :limit="1"
              :on-exceed="handleExceed"
              :file-list="filesBlack">
              <el-button size="small" type="primary">点击上传</el-button>
              <div slot="tip" class="el-upload__tip">请上传txt格式文件，手机号码以空格或回车符或逗号","分隔</div>
            </el-upload>
            <div class="uploadTxtCallback">{{ uploadBlackTxtCallbackInfo }}</div>
          </el-col>
        </el-row>
        <el-form-item size="medium" class="div_center">
          <el-button @click="backToList">取消任务</el-button>
          <!-- <el-button @click="resetForm('form')">重置表单</el-button> -->
          <el-button type="primary"  @click="submitForm('form')">提交任务</el-button>
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
    var checkPhoneNum = (rule,value,callback) => {
      if (!value) {
        return callback(new Error('手机号码不能为空'));
      }
      setTimeout(() => {
        let myreg=/^[1][3,4,5,6,7,8,9][0-9]{9}$/;
        if (!myreg.test(value)) {
          callback(new Error('手机号格式不对'));
        } else {
          callback();
        }
      }, 200);
    };
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
    return {
      myListData: {},
      taskId:"",//任务id 有则修改 无则添加
      files: [],
      filesBlack: [],
      checkStatus: 0,
      sendTimePickerOption: {//设置选取日期时间规则
        disabledDate(time){
          return time.getTime() < (Date.now()-3600*1000*24);
        }
      },
      uploadData: {// 上传手机号txt文件参数，oldFilePath编辑时页面获取，提交时带上参数
        dataType: "0",
        oldFilePath:""
      },
      uploadBlackData: {// 上传黑名单txt文件参数，oldFilePath编辑时页面获取，提交时带上参数
        dataType: "0",
        oldFilePath:""
      },
      uploadActionUrl: rootCoffeeStieUrl+"task-api/verify-file.html",// 上传txt文件接口
      submitFormUrl: rootCoffeeStieUrl+"task-api/coupon-send-task-create.html",//提交任务接口
      userSelectTaskUrl: rootCoffeeStieUrl+"task-api/get-selection-task-id-to-name-new-list.html",//获取用户筛选任务列表接口
      checkLegalUrl: '/user-selection-task/check-legal',// 验证手机号接口
      getCouponUrl:"/coupon-send-task/get-valid-coupon-group",// 获取优惠券套餐列表接口
      getCouponIdsUrl:"/coupon-send-task/get-valid-coupon",// 获取优惠券列表接口
      labelPosition: 'right',
      form: {
        taskName: '',// 任务名称
        sendTime: '',// 发送时间 new Date('2018-03-01 15:06:00')
        couponGroupId: '',// 优惠券套餐值下拉框
        couponType:"",//优惠券类型 0优惠券 1优惠券套餐
        couponGradient:[{id:"",num:"1"}],
        userSelectionTask: '',// 用户筛选任务 下拉框
        phoneNumbersTextArea: [],// 手机号列表文本框
        blacklistphoneNumTextArea: [],// 黑名单列表文本框
      },
      sendTimeFormat: '',//提交发送时间格式
      userPhoneForm: {
        importUserPhone: '',// 手机号
      },
      blackPhoneForm: {
        sheildUserPhone: ''// 黑名单手机号
      },
      couponGroupOptions: [],// 套餐 {value:"",label:"请选择"}
      couponGroupIdsOptions:[],//优惠券
      userSelectTaskOptions: [],// 用户筛选任务
      phoneListData: [],// 添加手机号列表 数组
      blackPhoneListData: [],// 黑名单手机号列表 数组
      uploadTxtCallbackInfo: '',// 上传手机列表返回提示信息
      uploadBlackTxtCallbackInfo: '',// 上传黑名单列表返回提示信息
      mobileUrl: '',
      blackMobileUrl: '',
      submitFlag:true,
      rules: {
        taskName: [
          { required: true, message: '请输入发券任务名称', trigger: 'blur' },
          { min: 2, max: 50, message: '长度在 2 到 50 个字符', trigger: 'blur' },
          { validator: checkUnregularChar, trigger: 'blur' }
        ],
        sendTime: [
          { required: true, message: '请选择日期', trigger: 'change' }
        ],
        couponGroupId: [
          { required: true, message: '请选择优惠券套餐', trigger: 'blur' }
        ],
        couponType:[
          { required: true, message: '请选择优惠券类型', trigger: 'blur' }
        ],
        /*userSelectionTask: [
          { required: true, message: '请选择用户筛选任务', trigger: 'change' }
        ]*/
      },
      couponIds:[{ required: true, message: '请选择优惠券', trigger: 'change' }],
      userPhoneRules: {
        importUserPhone: [
          { validator: checkPhoneNum, trigger: 'click' }
        ]
      },
      blackPhoneRules: {
        sheildUserPhone: [
          { validator: checkPhoneNum, trigger: 'click' }
        ]
      }
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
      this.myListData = rootList||{};
      this.taskId = rootTaskId||"";
      // 如果任务id不为空则进入编辑状态
      console.log("myListData..",this.myListData);
      if(this.taskId!=""){
        this.form.taskName = this.myListData.task_name;
        this.form.sendTime = this.myListData.send_time;
        this.phoneListData = this.myListData.mobileList;
        let txtUrl = this.myListData.mobile_file_url||"";
        let urlTxtName = txtUrl!=""?txtUrl.split("/").pop():"";
        let blackUrl = this.myListData.black_mobile_file_url||"";
        let blackTxtName = blackUrl!=""?blackUrl.split("/").pop():"";
        if(txtUrl!="") this.files = [{name:urlTxtName,url:rootCoffeeStieUrl+txtUrl}];
        if(blackUrl!="") this.filesBlack = [{name:blackTxtName,url:rootCoffeeStieUrl+blackUrl}];
        for(let i=0;i<this.phoneListData.length;i++){
          let myGuid = this.guid();
          let phoneNum = this.phoneListData[i];
          this.form.phoneNumbersTextArea.push({data:phoneNum,guid:myGuid});
        }
        this.blackPhoneListData = this.myListData.blackMobileList;
        for(let i=0;i<this.blackPhoneListData.length;i++){
          let myGuid = this.guid();
          let phoneNum = this.blackPhoneListData[i];
          this.form.blacklistphoneNumTextArea.push({data:phoneNum,guid:myGuid});
        }
        this.uploadData.oldFilePath = txtUrl;
        this.uploadBlackData.oldFilePath = blackUrl;
        this.mobileUrl = txtUrl;
        this.blackMobileUrl = blackUrl;
        //优惠券类型
        if(this.myListData.coupon_id_num_map.length==0&&this.myListData.coupon_group_id!=""){
          console.log("编辑时类型为优惠券套餐");
          this.form.couponType="2";
          this.form.couponGroupId=this.myListData.coupon_group_id;
        }else{
          console.log("编辑时类型为优惠券");
          this.form.couponType="1";
          this.form.couponGradient=this.myListData.coupon_id_num_map;
        }

      }
      //获取优惠券套餐数据
      axios.get(this.getCouponUrl).then((response)=> {
        const data = response.data;
        for(var key in data){
          if(!key=="") {
            this.couponGroupOptions.push({value:key,label:data[key]});
          }
        }
        // if(this.taskId!=""){
        //   if(this.couponGroupOptions.find((item)=>{
        //     return item.value == this.myListData.coupon_group_id;
        //   })){
        //     this.form.couponGroupId = this.myListData.coupon_group_id;
        //   }
        // }
      }).catch((error)=> {
        this.alertMsg(error);
      });
      //获取优惠券数据
      axios.get(this.getCouponIdsUrl).then((response)=> {
        const data = response.data;
        for(var key in data){
          if(!key=="") {
            this.couponGroupIdsOptions.push({value:key,label:data[key]});
          }
        }
      }).catch((error)=> {
        this.alertMsg(error);
      });
      // 获取用户筛选任务列表
      axios.get(this.userSelectTaskUrl).then((response)=> {
        const data = response.data;
        // console.log("userSelectTaskOptions data..",data);
        /*for(var key in data){
          // console.log("userSelectTaskOptions..",key,"...",data[key]);
          if(!key=="") {
            this.userSelectTaskOptions.push({value:key,label:data[key]});
          }
        }*/
        for(let i=0;i<data.length;i++){
          if(data[i].id!=""){
            this.userSelectTaskOptions.push({value:data[i].id,label:data[i].name});
          }
        }
        if(this.taskId!=""){
          if(this.userSelectTaskOptions.find((item)=>{
            return item.value == this.myListData.selection_task_id;
          })){
            this.form.userSelectionTask = this.myListData.selection_task_id;
          }
        }
      }).catch((error)=> {
        this.alertMsg(error);
      });
    },
    //添加优惠券梯度
    addCouponGradient(index){
      console.log(999)
      this.form.couponGradient.push({id:"",num:"1"});
      console.log(this.form.couponGradient)
    },
    //删除优惠券梯度
    delCouponGradient(index){
      this.form.couponGradient.splice(index,1)
    },
    submitForm(formName)// 提交任务
    {
      console.log("时间日期###.."+this.form.sendTime+"***");
      this.$refs[formName].validate((valid) => {
        if (valid) {
          if(this.form.sendTime < (Date.now()+60*1000*3)){
            this.alertMsg("发送时间不能早于当前时间");
          } else {
            if(this.phoneListData.length>0 || this.form.userSelectionTask!="" || this.mobileUrl!=""){
              this.submitFormAction();
            } else {
              this.alertMsg("用户筛选任务或手机号，至少需要有其中一项才可以提交任务");
            }
          }
        } else {
          // console.log("couponGroupId.."+this.form.couponGroupId);
          return false;
        }
      });
    },
    submitFormAction()// 提交任务接口
    {
      if(!this.submitFlag) return false;
      this.submitFlag = false;
      if(this.form.couponType==2){
        this.form.couponGradient=[{id:"",num:"1"}]//选择优惠券套餐类型，则将优惠券置空
      }else{
        this.form.couponGroupId="";//选择优惠券类型，则将套餐置空
      }
      console.log("id",this.form.couponGradient)
      var postUrl = this.submitFormUrl;
      var params={
        id:this.taskId,
        task_name:this.form.taskName,
        selection_task_id:this.form.userSelectionTask,
        coupon_group_id:this.form.couponGroupId,
        coupon_id_num_map:this.form.couponGradient,
        send_time:this.form.sendTime,
        mobileList:this.phoneListData,
        blackMobileList:this.blackPhoneListData,
        mobileUrl:this.mobileUrl,
        blackMobileUrl:this.blackMobileUrl
      }
      console.log("传到后台参数",params)
      axios.post(postUrl,params).then((response)=> {
        var data = response.data;
        console.log('data..'+data);
        if(data==0){
          this.alertMsg("提交失败");
          this.submitFlag = true;
        } else {
          const type = this.taskId==""?"1":"2";
          return axios.get('/activity-combin-package-assoc/create-activity-log?type='+type+'&moduleType=4');
        }
      })
      .then((response)=> {
        this.alertMsg("发券任务提交成功！","success");
        window.setTimeout(function(){
          window.location.href = "/index.php/coupon-send-task/index";
        },1000);
      })
      .catch((error)=> {
        this.alertMsg(error);
        this.submitFlag = true;
      });
    },
    resetForm(formName)// 重置表单
    {
      this.$refs[formName].resetFields();
      this.clearUserPhone();
      this.clearBlackPhone();
    },
    handleExceed(files, fileList)
    {
      this.alertMsg('请删除当前文件后再上传新的文件','warning');
    },
    onRemoveTxt(files, fileList)
    {
      console.log("remove txt");
      this.uploadData.oldFilePath = "";
      this.mobileUrl = "";
      this.uploadTxtCallbackInfo = "";
    },
    onRemoveBlack(files, fileList)
    {
      console.log("remove black");
      this.uploadBlackData.oldFilePath = "";
      this.blackMobileUrl = "";
      this.uploadBlackTxtCallbackInfo = "";
    },
    onBeforeUpload(file)
    {
      console.log("before");
      const isTXT = file.type === 'text/plain';
      const isLt1M = file.size / 1024 / 1024 < 1;

      if (!isTXT) {
        this.alertMsg('上传文件只能是txt格式!');
      }
      if (!isLt1M) {
        this.alertMsg('上传文件大小不能超过 1MB!');
      }
      return isTXT && isLt1M;
    },
    uploadSuccess (response, file, fileList)// 上传手机号成功后的回调
    {
      // console.log('response..', response);
      var data = response.data||{};
      // console.log('response.data..', response.data);
      var noExistsList = data.noExistsList||"";
      var filePath = data.filePath||"";
      // console.log('response.data.noExistsList..', response.data.noExistsList);
      this.uploadTxtCallbackInfo = noExistsList;
      this.mobileUrl = filePath;
    },
    uploadSuccessBlack (response, file, fileList)// 上传黑名单成功后的回调
    {
      // console.log('response..', response);
      var data = response.data||{};
      var noExistsList = data.noExistsList||"";
      var filePath = data.filePath||"";
      this.uploadBlackTxtCallbackInfo = noExistsList;
      this.blackMobileUrl = filePath;
    },
    uploadError (response, file, fileList)// 上传错误
    {
      console.log('上传失败，请重试！');
    },
    deletephoneNumbers(index){// 删除手机号码
      this.form.phoneNumbersTextArea.splice(index,1);
      this.phoneListData.splice(index,1);
    },
    deleteBlackNumbers(index){// 删除黑名单号码
      this.form.blacklistphoneNumTextArea.splice(index,1);
      this.blackPhoneListData.splice(index,1);
    },
    clearUserPhone()//清空手机号
    {
      this.userPhoneForm.importUserPhone = "";
      this.form.phoneNumbersTextArea = [];
      this.phoneListData = [];
    },
    clearBlackPhone()//清空黑名单
    {
      this.blackPhoneForm.sheildUserPhone = "";
      this.form.blacklistphoneNumTextArea = [];
      this.blackPhoneListData = [];
    },
    addTaskVerifyPhone ()//添加手机号
    {
      this.$refs["formUserPhone"].validate((valid) => {
        if (valid) {
          var phoneNum = this.userPhoneForm.importUserPhone;
          if(!this.phoneListData.find((num)=>num==phoneNum)){
            this.addTaskVerifyPhoneAction();
          } else {
            this.alertMsg("请勿重复添加手机号码");
          }
        } else {
          console.log("phone wrong..");
          return false;
        }
      });
    },
    addTaskVerifyPhoneAction()//添加手机号接口
    {
      var _this = this;
      var params = new URLSearchParams();
      params.append("check_type",1);
      params.append("mobile",this.userPhoneForm.importUserPhone);
      params.append("build_name","");
      params.append("company_name","");
      axios.post(this.checkLegalUrl,params
      ).then((response)=> {
        // console.log("inputVerifyPhoneNumber response..."+response);
        if(response.data){
          var myGuid = this.guid();
          var phoneNum = this.userPhoneForm.importUserPhone;
          this.form.phoneNumbersTextArea.push({data:phoneNum,guid:myGuid});
          this.phoneListData.push(phoneNum);
          this.userPhoneForm.importUserPhone = '';// 手机号
        } else {
          this.alertMsg("手机号不存在");
        }
      })
      .catch((error)=> {
        console.log("inputVerifyPhoneNumber error.."+error);
      });
    },
    addBlackVerifyPhone()//添加黑名单
    {
      this.$refs["formBlackPhone"].validate((valid) => {
        if (valid) {
          var phoneNum = this.blackPhoneForm.sheildUserPhone;
          if(!this.blackPhoneListData.find((num)=>num==phoneNum)){
            this.addTaskVerifyBlackPhoneAction();
          } else {
            this.alertMsg("请勿重复添加手机号码");
          }
        } else {
          console.log("phone wrong..");
          return false;
        }
      });
    },
    addTaskVerifyBlackPhoneAction()//添加黑名单接口
    {
      var _this = this;
      var params = new URLSearchParams();
      params.append("check_type",1);
      params.append("mobile",this.blackPhoneForm.sheildUserPhone);
      params.append("build_name","");
      params.append("company_name","");
      axios.post(this.checkLegalUrl,params
      ).then((response)=> {
        // console.log("inputVerifyPhoneNumber response..."+response);
        if(response.data){
          var myGuid = this.guid();
          var phoneNum = this.blackPhoneForm.sheildUserPhone;
          this.form.blacklistphoneNumTextArea.push({data:phoneNum,guid:myGuid});
          this.blackPhoneListData.push(phoneNum);
          this.blackPhoneForm.sheildUserPhone = "";
        } else {
          this.alertMsg("手机号不存在");
        }
      })
      .catch((error)=> {
        console.log("blackPhoneNumber error.."+error);
      });
    },
    backToList()
    {
      window.setTimeout(function(){
        window.location.href = "/index.php/coupon-send-task/index";
      },300);
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
    guid()//生成guid ,解决排序
    {
      return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
          var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
          return v.toString(16);
      });
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
    height: 70px;
    line-height: 70px;
    font-size: 22px;
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
  .phoneNameBox{
    width:100%;
    border:1px solid #b3d8ff;
    min-height: 90px;
    border-radius:5px;
  }
  .phoneNameText{
    padding:0 10px;
    height:35px;
    line-height: 36px;
    margin-bottom: 0px;
  }
  .phoneNameText+.phoneNameText{
    border-top:1px solid #b3d8ff;
  }
  .phoneNameText:nth-child(2n){
     background: #ecf5ff;
  }
  .uploadTxtCallback{
    font-size: 12px;
    line-height: 18px;
    margin-top: 10px;
  }
  .iconButton{
    font-size:24px;
    position: relative;
    top:3px;
    cursor:pointer;
  }
</style>