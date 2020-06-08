<template>
  <div class="content-body">
    <div class="line-title">自组合套餐活动</div>
    <el-form ref="form" :model="form" :rules="rules" label-width="120px">
      <el-row :gutter="10">
        <el-col :span="12" :offset="0">
          <el-form-item label="活动类型：" prop="activityType" id="activityType">
          <el-select v-model="form.activityType" placeholder="请选择" @change="changeActivityType">
            <el-option label="自取" value="1"></el-option>
            <el-option label="外送" value="2"></el-option>
          </el-select>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10" v-if="form.activityType==2">
        <el-col :span="6" :offset="0">
          <el-form-item label="免配送金额：" prop="freeDelivery" id="freeDelivery">
            <el-input v-model.trim="form.freeDelivery"></el-input>
          </el-form-item>
        </el-col>
        <el-col :span="1" class="money">元</el-col>
        <el-col :span="10">
        <p class="tip">用户商品金额大于或等于该数均可免配送费</p>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="12" :offset="0">
          <el-form-item label="套餐名称：" prop="activityName" id="activityName">
            <el-input v-model.trim="form.activityName"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="24">
          <el-form-item label="上线时间：" prop="launchTime" id="launchTime">
            <el-date-picker v-model="form.launchTime" type="datetimerange" range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期" format="yyyy-MM-dd HH:mm:ss" value-format="yyyy-MM-dd HH:mm:ss" :editable="edit">
            </el-date-picker>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="12">
          <el-form-item label="状态：" prop="status">
            <el-radio v-model="form.status" label="2">上线</el-radio>
            <el-radio v-model="form.status" label="1">下线</el-radio>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="12">
          <el-form-item label="退款：" prop="isRefund">
            <el-radio v-model="form.isRefund" label="1">允许</el-radio>
            <el-radio v-model="form.isRefund" label="0">拒绝</el-radio>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="24">
          <el-form-item label="不参与机构：">
            <el-checkbox-group v-model="form.notPartCity">
              <el-checkbox v-for="city in cityList" :label="city.key" :key="city.key">{{ city.name }}</el-checkbox>
            </el-checkbox-group>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="12">
          <el-form-item label="banner图片："  prop="uploadBannerPhoto" id="uploadBannerPhoto">
            <el-upload name="banner_photo_url" :action="uploadActionUrl" :data="uploadBannerData" :on-error="uploadError" :on-success="uploadBannerSuccess" :before-upload="onBeforeUpload" :on-remove="onBannerRemove" :on-preview="handlePreview" accept="image/jpeg,image/gif,image/png" :limit="1" :on-exceed="handleExceed" :file-list="filesBanner" list-type="picture">
              <el-button size="small" type="success">上传图片</el-button>
              <div slot="tip" class="el-upload__tip">请上传图片格式文件(100k以内)</div>
            </el-upload>
            <div class="uploadBannerCallback">{{ uploadBannerCallbackInfo }}</div>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="24">
          <el-form-item label="点位类型：" prop="pointType" id="pointType">
            <el-checkbox-group v-model="form.pointType" @change="handlePointChange">
              <el-checkbox label="0">普通点位</el-checkbox>
              <el-checkbox label="1">臻选点位</el-checkbox>
            </el-checkbox-group>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="20">
          <el-form-item label="选择商品：">
            <el-tabs type="border-card" v-model="productPoint">
              <el-tab-pane label="上线普通单品" name="1" v-if="productTabs.onlineTabShow">
                <el-checkbox :indeterminate="isIndeterminateOnline" v-model="checkOnlineAll" @change="handleCheckAllOnline">全选</el-checkbox>
  <div style="margin: 15px 0;"></div>
                <el-checkbox-group v-model="productIdStr.online_product" @change="handleCheckedOnlineChange">
                  <el-checkbox v-for="item in onlineProductList" :label="item.key" :key="item.key">{{ item.name }}</el-checkbox>
                </el-checkbox-group>
              </el-tab-pane>
              <el-tab-pane label="其他普通单品" name="2" v-if="productTabs.allTabShow">
                <el-checkbox :indeterminate="isIndeterminateAll" v-model="checkAllAll" @change="handleCheckAllAll">全选</el-checkbox>
  <div style="margin: 15px 0;"></div>
                <el-checkbox-group v-model="productIdStr.all_product" @change="handleCheckedAllChange">
                  <el-checkbox v-for="item in allProductList" :label="item.key" :key="item.key">{{ item.name }}</el-checkbox>
                </el-checkbox-group>
              </el-tab-pane>
              <el-tab-pane label="臻选单品" name="3" v-if="productTabs.selectionTabShow">
                <el-checkbox :indeterminate="isIndeterminateSelection" v-model="checkSelectionAll" @change="handleCheckAllSelection">全选</el-checkbox>
  <div style="margin: 15px 0;"></div>
                <el-checkbox-group v-model="productIdStr.selection_product" @change="handleCheckedSelectionChange">
                  <el-checkbox v-for="item in selectionProductList" :label="item.key" :key="item.key">{{ item.name }}</el-checkbox>
                </el-checkbox-group>
              </el-tab-pane>
            </el-tabs>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="2" style="width:120px;">
          <div class="sub-title">活动介绍：</div>
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
      <!-- 选择类型 -->
      <el-row style="margin-top:20px">
        <el-col :span="10">
            <el-form-item label="类型：" prop="selectType" id="selectType">
               <el-select v-model="form.selectType" placeholder="请选择">
                  <el-option value="1" label="自由套餐"></el-option>
                  <el-option value="2" label="自由单品"></el-option>
                </el-select>
            </el-form-item>
        </el-col>
      </el-row>
      <div v-if="form.selectType==1">
          <el-row :gutter="10">
            <el-col :span="2" style="width:120px;">
              <div class="sub-title"></div>
            </el-col>
            <el-col :span="20">
                <div v-for="(item,index) in form.productInformationJson" :key="index">
                  <div class="text" style="width:100%; height:60px; overflow:hidden;">
                    <el-col :span="3">
                      <el-form-item label="梯度" label-width="20">
                      {{index+1 }}：
                      </el-form-item>
                    </el-col>
                    <el-col :span="8" style="position:relative; left:-15px;">
                      <el-form-item label="商品数量：" label-width="30" >
                        <el-input v-model="item.num" type="number" size="small" style="width:95px;" min=1></el-input>
                      </el-form-item>
                    </el-col>
                    <el-col :span="8">
                       <el-form-item label="优惠价格：" :prop="'productInformationJson.' +index +'.price'" :rules="priceRules" id="priceRules">
                          <el-input v-model="item.price"  size="small" style="width:95px;"></el-input>
                      </el-form-item>
                    </el-col>
                    <el-col :span="5">
                    <i class="el-icon-circle-plus-outline" @click="addLadder"></i>
                    <i class="el-icon-remove-outline" @click="deleteLadder(index)" v-if="index!=0"></i>
                    </el-col>
                    <div style="clear:both;"></div>
                  </div>
                  <div style="clear:both;"></div>
                  <div class="text" style="margin:10px 0 20px 80px;">
                    是否给与实物奖励
                    <el-radio v-model="item.is_real_prize" label="0">否</el-radio>
                    <el-radio v-model="item.is_real_prize" label="1">是</el-radio>
                  </div>
                  <div class="text" style="margin:10px 0 20px 80px;">
                    是否给优惠券奖励
                    <el-radio v-model="item.is_coupon" label="0">否</el-radio>
                    <el-radio v-model="item.is_coupon" label="1">是</el-radio>
                    <el-select v-model="item.couponSelectId" v-show="item.is_coupon==1">
                      <el-option label="优惠券" value="0"></el-option>
                      <el-option label="优惠券套餐" value="1"></el-option>
                    </el-select>
                    <el-select v-show="item.is_coupon==1&&item.couponSelectId==0" v-model="item.singleCouponID" clearable filterable placeholder="请选择">
                      <el-option v-for="item in singleCouponOptions"
                        :label="item.label"
                        :value="item.value"
                        :key="item.value">
                      </el-option>
                    </el-select>
                    <el-select v-show="item.is_coupon==1&&item.couponSelectId==1" v-model="item.packageCouponID" clearable filterable placeholder="请选择">
                      <el-option v-for="item in packageCouponOptions"
                        :label="item.label"
                        :value="item.value"
                        :key="item.value">
                      </el-option>
                    </el-select>
                  </div>
                </div>
            </el-col>
          </el-row>
      </div>
      <div v-if="form.selectType==2">
        <el-row :gutter="10">
            <el-col :span="2" style="width:80px;">
              <div class="sub-title"></div>
            </el-col>
            <el-col :span="20">
                <div v-for="(item,index) in form.itemPattern" :key="index">
                  <div class="text">
                     <el-row>
                        <el-col :span="8">
                          <el-form-item label="商品次序：">第{{ index+1 }}个</el-form-item>
                        </el-col>
                        <el-col :span="12">
                        <i class="el-icon-circle-plus-outline" @click="itemaddLadder"></i>
                          <i class="el-icon-remove-outline" @click="itemdeleteLadder(index)" v-if="index!=0"></i>
                        </el-col>
                      </el-row>
                     <el-row :gutter="10">
                        <el-col :span="20">
                          <el-form-item label="价格模式：">
                            <el-radio v-model="item.type" label="0">原价</el-radio>
                            <el-radio v-model="item.type" label="1" @change="clearText(index)">折扣</el-radio>
                            <el-radio v-model="item.type" label="2" @change="clearText(index)">固定价格</el-radio>
                            <el-radio v-model="item.type" label="3" @change="clearText(index)">固定减价</el-radio>
                          </el-form-item>
                        </el-col>
                      </el-row>
                      <el-row>
                        <el-col v-if="item.type==1" :span="6" style="margin-left:170px"><el-form-item label="打折比例：" :prop="'itemPattern.' +index +'.price'" :rules="discountRules" id="discountRules"><el-input v-model.trim="item.price" type="number" min="10" max="100"></el-input></el-form-item></el-col>
                        <el-col v-if="item.type==2" :span="6" style="margin-left:220px"><el-form-item label="价格：" :prop="'itemPattern.' +index +'.price'" :rules="priceNumRules" id="priceNumRules"><el-input v-model.trim="item.price"  min=0></el-input></el-form-item></el-col>
                        <el-col v-if="item.type==3" :span="6" style="margin-left:300px"><el-form-item label="减价：" :prop="'itemPattern.' +index +'.price'" :rules="priceNumRules" id="priceNumRules"><el-input v-model.trim="item.price"  min=0></el-input></el-form-item></el-col>
                      </el-row>
                  </div>
                </div>
            </el-col>
          </el-row>
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
    var checkPriceNum = (rule,value,callback) => {
            setTimeout(() => {
              var myreg=/^[0-9]*[1-9][0-9]*$/;
              if(value==""){
                callback(new Error('请输入价格'));
              }else{
                var isNum=/([1-9]+[0-9]*|0)(\\.[\\d]+)?/;
                if(isNum.test(value)){
                  if(value<=0){
                    callback(new Error('请输入正数'));
                  }else{
                    if (!myreg.test(value)) {
                      if(value.toString().split('.').length>1){
                          if (value.toString().split('.')[1].length > 2) {
                               callback(new Error('请保留两位小数'));
                          }
                      }
                    }
                  }
                }else{
                  callback(new Error('请输入数值型'));
              }
              }
              callback();
            }, 200);
          };
        var checkfreePrice=(rule,value,callback) => {
            setTimeout(() => {
              var myreg=/^[0-9]*[1-9][0-9]*$/;
              if(value!=""&&value!="0"){
                var isNum=/([1-9]+[0-9]*|0)(\\.[\\d]+)?/;
                if(isNum.test(value)){
                  if(value<=0){
                    callback(new Error('请输入正数'));
                  }else{
                    if (!myreg.test(value)) {
                      if(value.toString().split('.').length>1){
                          if (value.toString().split('.')[1].length > 2) {
                               callback(new Error('请保留两位小数'));
                          }else if(value.toString().split('.')[0].length > 3){
                          	   callback(new Error('整数位数不能超过三位'));
                          }
                      }
                    }else{
                    	if(value.toString().length>3){
                      		  callback(new Error('整数位数不能超过三位'));
                      	}
                    }
                  }
                }else{
                  callback(new Error('请输入数值型'));
              }
              }
              callback();
            }, 200);
          };
       var checkDiscount = (rule,value,callback) => {
            setTimeout(() => {
              var reg = /^\+?[1-9]\d*$/;
              if(value==""){
                callback(new Error('请输入价格'));
              }else{
                if(!reg.test(value)){
                  callback(new Error('请输入大于0整数'));
                }else{
                  if(value<10||value>100){
                    callback(new Error('请输入10-100间的整数'));
                  }
                }
              }
              callback();
            }, 200);
          };
    return {
      testVar:'robert man',
      checkOnlineAll: false,
      isIndeterminateOnline:true,
      checkAllAll: false,
      isIndeterminateAll:true,
      checkSelectionAll: false,
      isIndeterminateSelection:true,
      form: {
      	activityType:"1",
      	freeDelivery:'0',
        activityName: '',
        launchTime: [],
        status: '2',
        isRefund: '0',
        notPartCity: [],
        pointType: ["0"],
        activityDesc: '',
        selectType:'',
        itemPattern:[{type:"0",price:""}],
        productInformationJson: [{num:"",price:"",is_real_prize:"0",is_coupon:"0",couponSelectId:"0",singleCouponID:"",packageCouponID:""}],// is_coupon是否优惠券奖励 0否 1是，couponSelectId选择优惠券或优惠券套餐 0优惠券 1优惠券套餐 ，singleCouponID选中的优惠券，packageCouponID选中的优惠券套餐
      },
      activityId:'',//套餐id 修改时传入
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
      },
      productIdStr: {
        online_product: [],
        all_product: [],
        selection_product: []
      },
      productTabs: {
        onlineTabShow:false,
        allTabShow:false,
        selectionTabShow:false
      },
      // sendTimePickerOption: { //设置选取日期时间规则
      //   disabledDate(time) {
      //     return time.getTime() < (Date.now() - 3600 * 1000 * 24);
      //   }
      // },
      productPoint:'',
      singleCouponOptions: [],
      packageCouponOptions: [],
      cityList: [],
      onlineProductList: [],
      allProductList: [],
      selectionProductList: [],
      bannerPhotoUrl: '',
      uploadActionUrl: rootCoffeeStieUrl + "activity-api/upload-banner-files.html?key=coffee08&secret=d0bc0edd5ae98b5600f64c4728d5e4ce", // 上传图片接口
      submitFormUrl: rootCoffeeStieUrl + "activity-api/combin-package-assoc-save.html?key=coffee08&secret=d0bc0edd5ae98b5600f64c4728d5e4ce", // 提交表单接口
      checkFormUrl: rootCoffeeStieUrl + "activity-api/get-activity-name-unique.html?key=coffee08&secret=d0bc0edd5ae98b5600f64c4728d5e4ce", // 提交表单接口
      uploadBannerData: {},
      uploadBannerCallbackInfo: '',
      filesBanner: [],
      edit:false,
      rules: {
        activityName: [
          { required: true, message: '请输入套餐名称', trigger: 'blur' }
        ],
        activityType: [
          { required: true, message: '请选择活动类型', trigger: 'change' }
        ],
        freeDelivery:[{validator: checkfreePrice}],
        launchTime: [
          { required: true, message: '请选择日期', trigger: 'change' }
        ],
        pointType: [
          { required: true, message: '请选择点位类型', trigger: 'change' }
        ],
        selectType:[{ required: true, message: '请选择类型', trigger: 'change' }]
      },
      priceNumRules:[
          {required: true,message: '请输入价格',trigger: 'blur'},
          {validator: checkPriceNum}
      ],
      discountRules:[
          {required: true,message: '请输入价格',trigger: 'blur'},
          {validator: checkDiscount}
      ],
      priceRules:[
          {required: true,message: '请输入价格',trigger: 'blur'},
          {validator: checkPriceNum}
          ]
    }
  },
  computed: {
    editor() {
      return this.$refs.myQuillEditor.quill
    }
  },
  mounted() {
    this.init();
  },
  methods: {
    init() {
      window.parent.onscroll = (e)=>{
        this.scrollMsg();
      }
      //获取到对应模板数据后，显示初始值
      for (let i in rootCityList) {
        this.cityList.push({ 'key': i, 'name': rootCityList[i] });
      }
      for (let i in rootSingleCouponList) {
        if(i!=""){
          this.singleCouponOptions.push({'value': i, 'label': rootSingleCouponList[i]});
        }
      }
      this.singleCouponOptions.sort((a,b)=> {
        return Number(b.value) - Number(a.value);
      });
      rootPackageCouponList.forEach((item,i)=>{
        if(item.group_id!=""){
          this.packageCouponOptions.push({'value': item.group_id, 'label': item.group_name});
        }
      })
      // console.log(this.packageCouponOptions)
      const olProduct = rootPointProductList.online_product;
      olProduct.forEach((item,i)=>{
        this.onlineProductList.push({'key': item.product_id, 'name': item.product_name});
        onlineProductOptions.push(item.product_id);
      })
      const alProduct = rootPointProductList.all_product;
      alProduct.forEach((item,i)=>{
        this.allProductList.push({'key': item.product_id, 'name': item.product_name});
        allProductOptions.push(item.product_id);
      })
      const slProduct = rootPointProductList.selection_product;
      slProduct.forEach((item,i)=> {
        this.selectionProductList.push({ 'key': item.product_id, 'name': item.product_name });
        selectionProductOptions.push(item.product_id);
      })
      //渲染数据
      if (JSON.stringify(rootActivityInfo)!=="{}"){
        this.render();
      }
      this.setProductList();
    },
    render() {
      let data =rootActivityInfo;//接收页面的渲染数据
      this.activityId=data.activity_id;//编辑时ID赋值，添加时为空
      this.form.activityType=data.activity_type;//活动类型
      this.form.freeDelivery=data.free_delivery_cost;//免运费金额
      this.form.activityName=data.activity_name;//自组合名称
      this.form.launchTime=[data.start_time,data.end_time];//时间
      this.form.status=data.status;//上线状态
      this.form.isRefund=data.is_refund;//是否允许退款
      this.form.notPartCity=data.not_part_city==""?[]:data.not_part_city;//不参与机构
      //编辑图片
      let imgUrl = data.banner_photo_url||"";
      let urlImgName = imgUrl!=""?imgUrl.split("/").pop():"";
      if(imgUrl!="") this.filesBanner = [{name:urlImgName,url:imgUrl}];
      this.form.productInformationJson=data.product_information_json;//梯度管理
      this.form.pointType=data.point_type;//点位类型
      this.productIdStr.online_product=data.product_id_str.online_product;//上线产品
      this.productIdStr.all_product=data.product_id_str.all_product;//其他产品
      this.productIdStr.selection_product=data.product_id_str.selection_product;//臻选产品
      this.form.activityDesc=data.activity_desc;//活动介绍
      //渲染单品类型
      if(data.product_information_json.length==0&&data.free_single_json.length!=0){
        console.log("编辑时类型为自由单品")
        this.form.selectType="2"
        this.form.itemPattern=data.free_single_json;
        this.form.productInformationJson=[{num:"",price:"",is_real_prize:"0",is_coupon:"0",couponSelectId:"0",singleCouponID:"",packageCouponID:""}];
      }else{
        console.log("编辑时类型为单品套餐");
        this.form.selectType="1";
        this.form.productInformationJson=data.product_information_json;
        this.form.itemPattern=[{type:"0",price:""}];
      }
    },
    handlePointChange(value)
    {
      this.setProductList();
    },
    setProductList()
    {
      if(this.form.pointType.indexOf("1")==-1){
        this.productTabs.selectionTabShow = false;
        this.productIdStr.selection_product = [];
      } else {
        this.productTabs.selectionTabShow = true;
        this.productPoint = "3";
      }
      if(this.form.pointType.indexOf("0")==-1){
        this.productTabs.onlineTabShow = false;
        this.productTabs.allTabShow = false;
        this.productIdStr.online_product = [];
        this.productIdStr.all_product = [];
      } else {
        this.productTabs.onlineTabShow = true;
        this.productTabs.allTabShow = true;
        this.productPoint = "1";
      }
    },
    deleteLadder(index)
    {
      if(this.form.productInformationJson.length>1){
        this.form.productInformationJson.splice(index,1);
      }
    },
    addLadder()
    {
      this.form.productInformationJson.push({num:"",price:"",is_real_prize:"0",is_coupon:"0",couponSelectId:"0",singleCouponID:"",packageCouponID:""});
    },
    //修改活动类型
    changeActivityType(){
    	if(this.form.activityType=="1"){
    		this.form.freeDelivery="0"
    	}
    },
    //自有单品选择类型时清空值
    clearText(index){
      this.form.itemPattern[index].price=""
    },
    // 自由单品添加设置
    itemdeleteLadder(index)
    {
      if(this.form.itemPattern.length>1){
        this.form.itemPattern.splice(index,1);
      }
    },
    itemaddLadder()
    {
      this.form.itemPattern.push({type:"0",price:""});
    },
    handleCheckAllOnline(val)
    {
      this.productIdStr.online_product = val ? onlineProductOptions : [];
      this.isIndeterminateOnline = false;
    },
    handleCheckedOnlineChange(value)
    {
      let checkedCount = value.length;
      this.checkOnlineAll = checkedCount === this.onlineProductList.length;
      this.isIndeterminateOnline = checkedCount > 0 && checkedCount < this.onlineProductList.length;
    },
    handleCheckAllAll(val)
    {
      this.productIdStr.all_product = val ? allProductOptions : [];
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
      this.productIdStr.selection_product = val ? selectionProductOptions : [];
      this.isIndeterminateSelection = false;
    },
    handleCheckedSelectionChange(value)
    {
      let checkedCount = value.length;
      this.checkSelectionAll = checkedCount === this.selectionProductList.length;
      this.isIndeterminateSelection = checkedCount > 0 && checkedCount < this.selectionProductList.length;
    },
    submitForm(formName) // 提交任务
    {
      // console.log("this.productIdStr..",this.productIdStr)
      this.$refs[formName].validate((valid,obj) => {
        if (valid) {
          if(new Date(this.form.launchTime[1]) < new Date(this.form.launchTime[0])) {
            this.alertMsg("结束时间不能早于开始时间");
          } else if(new Date(this.form.launchTime[1]) < new Date(Date.now())) {
            this.alertMsg("结束时间不能早于当前时间");
          } else if(this.form.activityDesc==""){
            this.alertMsg("活动介绍内容更不能为空");
          } else if(!this.verifyProductInformationJson()&&this.form.selectType=="1") {
            this.alertMsg("请完善自由套餐梯度管理内容");
          } else if(this.productIdStr.online_product.length==0&&this.productIdStr.all_product.length==0&&this.productIdStr.selection_product.length==0) {
            this.alertMsg("请选择商品");
          } else if(this.filesBanner.length<=0) {
            this.alertMsg('请上传banner图片');
          } else {
            if(this.form.activityName!==rootActivityInfo.activity_name){
                this.submitBefore();
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
    submitBefore(){
      axios.get(this.checkFormUrl,{params:{'activityName':this.form.activityName}})
      .then((response)=> {
        var data = response.data;
        if(data){
          this.submitFormAction();
        }else{
          this.alertMsg('套餐名称不能重复!');
        }
      })
      .catch((error)=> {
        this.alertMsg(error);
      });
    },
    submitFormAction() // 提交任务接口
    {
      let postUrl = this.submitFormUrl;
      if(this.form.selectType=="1"){
        this.form.itemPattern=[]//选择自由套餐，则将单品置空
      }else{
        this.form.productInformationJson=[];//选择自由单品，则将自由套餐置空
      }
      let params = {
        activity_id:this.activityId,
        activity_type:this.form.activityType,
        free_delivery_cost:this.form.freeDelivery,
        activity_name:this.form.activityName,
        start_time: this.form.launchTime[0],
        end_time: this.form.launchTime[1],
        status: this.form.status,
        is_refund: this.form.isRefund,
        not_part_city: this.form.notPartCity,
        point_type: this.form.pointType,
        product_information_json: this.form.productInformationJson,//自由套餐
        free_single_json:this.form.itemPattern,//自由单品
        product_id_str: this.productIdStr,
        banner_photo_url:this.bannerPhotoUrl,
        activity_desc: this.form.activityDesc
      };
      console.log("传到后台参数",params)
      axios.post(postUrl, params)
      .then((response)=> {
        let data = response.data;
        if(data){
          const type = this.activityId==""?"1":"2";
          return axios.get('/activity-combin-package-assoc/create-activity-log?type='+type+'&moduleType=1');
        }else{
          this.alertMsg('保存失败');
          this.initTypeData();
        }
      })
      .then((response)=> {
        this.alertMsg('保存成功','success');
        setTimeout(()=>{
          window.location.href="/activity-combin-package-assoc/index";
        },2000);
      })
      .catch((error)=> {
        this.alertMsg(error);
        this.initTypeData();
      });
    },
    //保存失败时，根据当前选择类型，将类型初始化
    initTypeData(){
       if(this.form.selectType=="1"){
          this.form.itemPattern.push({type:"0",price:""});
        }else{
          this.form.productInformationJson.push({num:"",price:"",is_real_prize:"0",is_coupon:"0",couponSelectId:"0",singleCouponID:"",packageCouponID:""});
        }
    },
    verifyProductInformationJson()
    {
      for(let i in this.form.productInformationJson){
        if(this.form.productInformationJson[i].num=="" || this.form.productInformationJson[i].price==""|| (this.form.productInformationJson[i].is_coupon=="1"&&this.form.productInformationJson[i].singleCouponID==""&&this.form.productInformationJson[i].packageCouponID=="")){
          return false;
        }
      }
      return true;
    },
    resetForm(formName) // 重置表单
    {
      this.$refs[formName].resetFields();
      this.form.notPartCity = [];
      this.form.pointType = [];
      this.filesBanner = [];
      this.setProductList();
      // this.clearUserPhone();
    },
    handlePreview(file) {
      console.log(file);
    },
    handleExceed(files, fileList) {
      this.alertMsg('请删除当前图片后再上传新的图片','warning');
    },
    onBannerRemove(files, fileList) {
      this.bannerPhotoUrl = "";
      this.filesBanner=[];
    },
    onBeforeUpload(file) {
      console.log(file)
      const isIMG = file.type === 'image/jpeg' || 'image/gif' || 'image/png';
      const isLt200K = file.size / 1024 / 1024 < 0.1;
      for(let i in file){
        // console.log()
      }

      if (!isIMG) {
        this.alertMsg('上传文件只能是图片格式!');
      }
      if (!isLt200K) {
        this.alertMsg('上传文件大小不能超过 100k!');
      }
      return isIMG && isLt200K;
    },
    uploadBannerSuccess(response, file, fileList) // 上传手机号成功后的回调
    {
      this.$message.success('上传成功！');
      let data = response.data || {};
      let noExistsList = data.noExistsList || "";
      let filePath = data.filePath || "";
      this.bannerPhotoUrl = filePath;
      this.uploadBannerCallbackInfo = noExistsList;
      this.filesBanner.length=1;
    },
    uploadError(response, file, fileList) // 上传错误
    {
      this.alertMsg('上传失败，请重试！');
    }
  },
  components: {}
}

</script>
<style>
li {list-style-type:none;}
.uploadBannerCallback {
  font-size: 12px;
  line-height: 18px;
  margin-top: 10px;
}
.el-form-item {
  margin-bottom: 20px;
}

.el-upload-list--picture .el-upload-list__item-thumbnail {
  width: 163px;
  height: 71px;
}
.el-checkbox{
  margin-right:30px;
}
.el-checkbox+.el-checkbox{
  margin-left:0;
}
.ql-container{
  min-height: 250px;
}
.el-icon-remove-outline,.el-icon-circle-plus-outline{
  font-size:24px;position:relative; top:10px; cursor:pointer;
}
p.tip{
	margin-top:8px;
	color:#666;
	font-size:14px;

}
.money{
	line-height:40px;
}
</style>
