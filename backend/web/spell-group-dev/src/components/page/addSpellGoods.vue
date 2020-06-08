<template>
<!-- eslint-disable -->
	<div class="content-body">
		<div class="line-title">添加社交游戏</div>
		<el-form ref="form" :model="form" label-width="140px" :rules="rules">
			<!-- 主标题 -->
			<el-row :gutter="10">
		        <el-col :span="12" :offset="0">
		            <el-form-item label="活动主标题：" prop="mainTitle" id="mainTitle">
                		 <el-input v-model.trim="form.mainTitle"></el-input>
              		</el-form-item>
		        </el-col>
      		</el-row>
      		<!-- 副标题 -->
      		<el-row :gutter="10">
		        <el-col :span="12" :offset="0">
		            <el-form-item label="活动副标题：" id="subTitle">
                		 <el-input v-model.trim="form.subTitle"></el-input>
              		</el-form-item>
		        </el-col>
      		</el-row>
      		<!-- 有效期 -->
      		<el-row :gutter="10">
		        <el-col :span="12" :offset="0">
		            <el-form-item label="有效期：" prop="expireTime" id="expireTime">
                		  <el-date-picker  v-model="form.expireTime" type="datetimerange" range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期" format="yyyy-MM-dd HH:mm:ss" value-format="yyyy-MM-dd HH:mm:ss">
            			  </el-date-picker>
              		</el-form-item>
		        </el-col>
      		</el-row>
      		<!-- 状态 -->
      		 <el-row :gutter="10">
		        <el-col :span="12">
		          <el-form-item label="状态：">
		            <el-radio v-model="form.status" label="1">上线</el-radio>
		            <el-radio v-model="form.status" label="0">下线</el-radio>
		          </el-form-item>
		        </el-col>
      		</el-row>
      		<!-- 类型 -->
      		<el-row :gutter="10">
		        <el-col :span="12">
		          <el-form-item label="类型：" prop="selectType" id="selectType">
		                <el-select v-model="form.selectType"  filterable placeholder="请选择" @change="selectType">
		                <!-- 新人团1,老带新2,全民参与3 -->
			                  <el-option
			                    v-for="item in typeList"
			                    :label="item.name"
			                    :value="item.value"
			                    :key="item.value"
			                    >
			                  </el-option>
                		</el-select>
		          </el-form-item>
		        </el-col>
      		</el-row>
      		<!-- 新手团 -->
      		<el-row :gutter="10" v-if="form.selectType==1">
      			<el-col :span="12" class="row-type">开团用户：新用户&nbsp;&nbsp;参团用户：新用户</el-col>
      			<el-col>
      				<el-form-item label="新用户类型：" v-model="form.newUserType">
			            <el-radio  v-model="form.newUserType" label="1">无购买用户</el-radio>
			            <el-radio  v-model="form.newUserType" label="2">无付费购买</el-radio>
          			</el-form-item>
      			</el-col>
      		</el-row>
      		<!-- 老带新团 -->
      		<el-row :gutter="10" v-if="form.selectType==2">
      			<el-col :span="12" class="row-type">开团用户：老用户&nbsp;&nbsp;参团用户：新用户</el-col>
      			<el-col>
      				<el-form-item label="新用户类型：">
			            <el-radio  v-model="form.newUserType" label="1">无购买用户</el-radio>
			            <el-radio  v-model="form.newUserType" label="2">无付费购买</el-radio>
          			</el-form-item>
      			</el-col>
      		</el-row>
      		<!-- 全民参与 -->
      		<el-row :gutter="10" v-if="form.selectType==3">
      			<el-col :span="12" class="row-type">开团用户：任何用户&nbsp;&nbsp;参团用户：任何用户</el-col>
      		</el-row>
      		<!-- 开团时长 -->
      		<el-row :gutter="10">
		        <el-col :span="12">
		          <el-form-item label="开团时长(小时)：" prop="conferenceTime" id="conferenceTime">
		            <el-input v-model.trim="form.conferenceTime"></el-input>
		          </el-form-item>
		        </el-col>
      		</el-row>
      		<!-- 活动梯度 -->
      		<el-row :gutter="10">
      			<el-col :span="2">
          			<div class="sub-title" style="width:120px; margin-right:20px">梯度管理：</div>
        		</el-col>
		        <el-col :span="20" style="padding-left:60px">
		            <div v-for="(item,index) in form.activityGradient" :key="index">
		              <div class="text activity" style="margin-bottom:20px">
		                 <el-col :span="9">
		                 	<el-form-item label="参团人数: " :prop="'activityGradient.' +index +'.tuxedo_num'" :rules="tuxedoNum" id="tuxedoNum">
			                 	<el-input v-model="item.tuxedo_num" type="number" size="small" style="width:80px;" min=1></el-input>
			                 </el-form-item>
		                 </el-col>
			             <el-col :span="9">
			             	<el-form-item label="拼团价格: " :prop="'activityGradient.' +index +'.group_price'" :rules="priceNumRules" id="priceNumRules">
			                 	<el-input v-model="item.group_price" size="small" style="width:80px;"></el-input>&nbsp;&nbsp;&nbsp;
			                 </el-form-item>
			             </el-col>
			             <el-col :span="3" class="btn-icon">
			             	<i class="el-icon-remove-outline" @click="deleteLadder(index)" v-if="index!=0" style="font-size:20px; position:relative; top:2px;cursor:pointer"></i>
		                	<i class="el-icon-circle-plus-outline" @click="addLadder" v-if="index==0" style="font-size:20px;position:relative; top:2px; cursor:pointer"></i>
			             </el-col>
		              </div>
		            </div>
		        </el-col>
      		</el-row>
			<!-- 商品设置 -->
			<div class="line-title">商品设置</div>
			<!-- 最多成团数 -->
			<el-row :gutter="10">
		        <el-col :span="12" :offset="0">
		            <el-form-item label="最多成团数：" prop="totalDrinks" id="totalDrinks">
                		 <el-input v-model.trim="form.totalDrinks" type="number" min=1></el-input>
              		</el-form-item>
		        </el-col>
      		</el-row>
      		<!-- 商品梯度 -->
      		<el-row :gutter="10">
      			<el-col :span="2">
          			<div class="sub-title" style="width:120px; margin-right:20px">选择商品：</div>
        		</el-col>
		        <el-col :span="18" style="padding-left:45px">
		            <div v-for="(item,index) in form.commodityGradient" :key="index">
		              <div class="text" style="margin-bottom:20px">
		                 <el-col :span="12">
		                 	<el-form-item label="选择商品：" :prop="'commodityGradient.' +index +'.cf_product_id'" :rules="selectProduct" id="selectProduct">
								<el-select v-model="item.cf_product_id"  filterable placeholder="请选择" >
				          			<!-- 新人团1,老带新2,全民参与3 -->
					                  <el-option
					                    v-for="item in productList"
					                    :label="item.cf_product_name"
					                    :value="item.cf_product_id"
					                    :key="item.cf_product_id"
					                    >
					                  </el-option>
		                		</el-select>
                			</el-form-item>
		                 </el-col>
		                 <el-col :span="9">
		                 	<el-form-item label="商品数量:" :prop="'commodityGradient.' +index +'.group_attached_num'" :rules="productNum" id="productNum">
		                 		<el-input v-model="item.group_attached_num" type="number" size="small" style="width:80px;" min=1></el-input>
		                 	</el-form-item>
		                 </el-col>
		                 <el-col :span="3" class="btn-icon">
		                 	<i class="el-icon-remove-outline" @click="deleteProductLadder(index)" v-if="index!=0" style="font-size:20px; position:relative; top:2px;cursor:pointer"></i>
		                	<i class="el-icon-circle-plus-outline" @click="addProductLadder" v-if="index==0" style="font-size:20px;position:relative; top:2px; cursor:pointer"></i>
		                 </el-col>
		              </div>
		            </div>
		        </el-col>
      		</el-row>
      		<!-- 商品原价 -->
			<!-- <el-row :gutter="10">
		        <el-col :span="12" :offset="0">
		            <el-form-item label="商品原价：" prop="originalPrice" id="originalPrice">
                		 <el-input v-model.trim="form.originalPrice"></el-input>
              		</el-form-item>
		        </el-col>
      		</el-row> -->
      		<!-- 商品图片 -->
      		<el-row :gutter="10">
		        <el-col :span="12">
		          <el-form-item label="商品图片："  id="uploadBannerPhoto">
		            <el-upload name="activity_img" :action="uploadActionUrl" :data="uploadBannerData" :on-error="uploadError" :on-success="uploadBannerSuccess" :before-upload="onBeforeUpload" :on-remove="onBannerRemove"  accept="image/jpeg,image/gif,image/png" :limit="1" :on-exceed="handleExceed" :file-list="filesBanner" list-type="picture">
		              <el-button size="small" type="success">上传图片</el-button>
		              <div slot="tip" class="el-upload__tip">请上传图片格式文件</div>
		            </el-upload>
		            <div class="uploadBannerCallback">{{ uploadBannerCallbackInfo }}</div>
		          </el-form-item>
		        </el-col>
           </el-row>
           <!-- 商品详情图片 -->
      		<el-row :gutter="10">
		        <el-col :span="12">
		          <el-form-item label="详情图片：">
		            <el-upload name="activity_details_img" :action="uploadActionUrl" :data="uploadProData" :on-error="uploadError" :on-success="uploadProSuccess" :before-upload="onBeforeUpload" :on-remove="onProRemove"  accept="image/jpeg,image/gif,image/png" :limit="10" :file-list="filesProduct" list-type="picture" :on-exceed="handleExceed">
		              <el-button size="small" type="success">上传图片</el-button>
		              <div slot="tip" class="el-upload__tip">请上传图片格式文件</div>
		            </el-upload>
		            <div class="uploadBannerCallback">{{ uploadProCallbackInfo }}</div>
		          </el-form-item>
		        </el-col>
           </el-row>
           <el-form-item size="medium" class="div-submit">
		        <el-button @click="resetForm('form')">返回</el-button>
		        <el-button type="primary" @click="submitForm('form')">保存</el-button>
      		</el-form-item>
		</el-form>
	</div>
</template>
<script>
import axios from 'axios'
/*eslint-disable*/
	export default {
		data(){
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
      		var checkTime = (rule,value,callback) => {
		        setTimeout(() => {
		        	var myreg=/^[0-9]*[1-9][0-9]*$/;
		        	if(value==""){
		        		callback(new Error('请输入开团时长(可输入一位小数)'));
		        	}else{
		        		var isNum=/([1-9]+[0-9]*|0)(\\.[\\d]+)?/;
			          	if(isNum.test(value)){
			          		if(value<=0){
			          			callback(new Error('请输入正数'));
			          		}else{
			          			if (!myreg.test(value)) {
						            if(value.toString().split('.').length>1){
						                if (value.toString().split('.')[1].length > 1) {
						                    callback(new Error('最多输入一位小数'));
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
      		var checkNum = (rule,value,callback) => {
		        setTimeout(() => {
		          var myreg=/^[0-9]*[1-9][0-9]*$/;
		          if(value!=""){
		          	if (!myreg.test(value)&&value!="") {
			            callback(new Error('请输入大于0的正整数'));
			          }
		          }else{
		          	 callback(new Error('请输入正整数'));
		          }
		           callback();
		        }, 200);
		      };
		     var checkTotalNum = (rule,value,callback) => {
		        setTimeout(() => {
		          var myreg=/^[0-9]*[1-9][0-9]*$/;
		          if(value!=""){
		          	if (!myreg.test(value)&&value!="") {
			            callback(new Error('请输入大于0的正整数'));
			          }
		          }
		           callback();
		        }, 200);
		      };
			return {
				form:{
					activityId:"",
					mainTitle:"",
					subTitle:"",
					expireTime:[],//有效期
					status:"1",
					selectType:"",
					conferenceTime:"",
					totalDrinks:"",//饮品总数
					originalPrice:"",//商品原价
					selectProduct:"",//选择商品
					newUserType:"1",
					activityGradient: [{tuxedo_num:"",group_price:""}],
					commodityGradient:[{cf_product_id:"",group_attached_num:""}],
				},
				typeList:[{name:"新手团",value:"1"},{name:"老带新",value:"2"},{name:"全民参与",value:"3"}],//参团类型
		        rules: {
			        mainTitle: [
			          { required: true, message: '请输入活动主标题', trigger: 'blur' }
			        ],
			        expireTime: [
			          { required: true, message: '请选择日期', trigger: 'change' }
			        ],
			        selectType: [
			          { required: true, message: '请选择类型', trigger: 'change' }
			        ],
			        // originalPrice: [
			        //   { required: true, message: '请输入价格', trigger: 'blur' },
			        //   {validator: checkPriceNum}
			        // ],
			        conferenceTime: [
			          { required: true, message: '请输入开团时长(可输入一位小数)', trigger: 'blur' },
			          {validator: checkTime}
			        ],
			        totalDrinks: [
			          {validator: checkTotalNum}
			        ]
				},
			 	priceNumRules:[
				     {required: true,message: '请输入价格',trigger: 'blur'},
		             {validator: checkPriceNum}
				],
				tuxedoNum:[
				 	{required: true,message: '请输入参团人数',trigger: 'blur'},
				 	{validator: checkNum}
				],
				productNum:[
					{required: true,message: '请输入商品数量',trigger: 'blur'},
				 	{validator: checkNum}
				],
				selectProduct:[
				     {required: true,message: '请选择商品'},
				],
				productList:rootData.data.coffee_product,
				uploadActionUrl: rootCoffeeStieUrl + "group-booking-api/upload-banner-files.html",
				submitUrl: rootCoffeeStieUrl + "group-booking-api/add-group.html", // 提交接口
				checkFormUrl: rootCoffeeStieUrl + "group-booking-api/get-title-repeat.html", // 检测标题
				uploadBannerData: {},//商品图片
     			uploadBannerCallbackInfo: '',//商品图片
     			filesBanner: [],//商品图片
     			bannerPhotoUrl:"",//商品图片上传路径
     			uploadProData: {},//详情图片
     			uploadProCallbackInfo: '',//商品图片
     			filesProduct: [],//商品图片
     			proPhotoUrl:[],//商品图片上传路径

			}
		},
		mounted(){
			this.init();
		},
		methods:{
			init(){
				//编辑数据
				if (JSON.stringify(rootData.data.details)!=="{}"){
        			this.render();
      			}
			},
			render(){
				let details=rootData.data.details;
				this.form.activityId=details.activity_id;
				this.form.mainTitle=details.main_title;
				this.form.subTitle=details.subhead;
				this.form.expireTime=[details.begin_time,details.end_time];
				this.form.status=details.status.toString();
				console.log(this.form.status);
				this.form.selectType=details.type.toString();
				let obj2 = {};
		        obj2 = this.typeList.find((item)=>{
		            return item.value === details.type;
		        });
				this.form.newUserType=details.new_type.toString();
				this.form.conferenceTime=details.duration;
				this.form.activityGradient=details.price_ladder;//活动梯度
				this.form.totalDrinks=details.drink_num;
				this.form.commodityGradient=details.drink_ladder;//商品梯度
				// this.form.originalPrice=details.original_cost;//原价
				//编辑图片
				this.bannerPhotoUrl=details.activity_img;
				let imgUrl = details.activity_img||"";
		        let urlImgName = imgUrl!=""?imgUrl.split("/").pop():"";
		        if(imgUrl!="") this.filesBanner = [{name:urlImgName,url:imgUrl}];

		        //编辑详情图片
		        //详情图片路径赋值
		        this.proPhotoUrl=details.activity_details_img;
		        console.log(this.proPhotoUrl);
		        // 页面渲染图片
		        var detailUrl = "";
		        var detailName="";
		        let activityDetailsImg=details.activity_details_img;
		        for (let item in activityDetailsImg) {
		        	let imgItem=activityDetailsImg[item];
		        	if(imgItem!=""){
            				detailName=imgItem.split("/").pop();
            				this.filesProduct.push({name:detailName,url:imgItem})
            			}
		        }
			},
			// 选择类型
			selectType(){
				if(this.form.selectType=="3"){
					this.form.newUserType=0;
				}
				console.log("选择类型",this.form.selectType);
			},
			// 活动梯度
			deleteLadder(index)
		    {
		      if(this.form.activityGradient.length>1){
		        this.form.activityGradient.splice(index,1);
		      }
		    },
		    addLadder()
		    {
		      this.form.activityGradient.push({tuxedo_num:"",group_price:""});
		    },
		    // 商品梯度
		    deleteProductLadder(index)
		    {
		      if(this.form.commodityGradient.length>1){
		        this.form.commodityGradient.splice(index,1);
		      }
		    },
		    addProductLadder()
		    {
		      this.form.commodityGradient.push({cf_product_id:"",group_attached_num:""});
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
		    // 商品图片上传
		    uploadBannerSuccess(response, file, fileList)
		    {
		      console.log("商品图片上传");
		      this.alertMsg('上传成功',"success");
		      let data = response.data || {};
		      console.log("商品图片接收数据",data);
		      console.log("商品图片上传路径",data.filePath);
		      let noExistsList = data.noExistsList || "";
		      let filePath = data.filePath || "";
		      this.bannerPhotoUrl = filePath;
		      this.uploadBannerCallbackInfo = noExistsList;
		      this.filesBanner.length=1;
		    },
		    uploadError(response, file, fileList) // 上传错误
		    {
		      this.alertMsg('上传失败，请重试！');
		    },
		    onBannerRemove(files, fileList) {
		      this.bannerPhotoUrl = "";
		      this.filesBanner=[];
    		},
    		onProRemove(files, fileList) {
		      this.proPhotoUrl =[];
		      for(let i in fileList){
		      	this.proPhotoUrl.push(fileList[i].url);
		      }
		      console.log(this.proPhotoUrl);
    		},
    		onBeforeUpload(file) {
		      const isIMG = file.type === 'image/jpeg' || 'image/gif' || 'image/png';
		      const isLt200K = file.size / 1024 / 200 < 1;
		      var aa=file.size/1024;
		      console.log("上传图片大小",aa);

		      if (!isIMG) {
		        this.alertMsg('上传文件只能是图片格式!');
		      }
		      if (!isLt200K) {
		        this.alertMsg('上传文件大小不能超过 200k!');
		      }
		      return isIMG && isLt200K;
		    },
		    handleExceed(files, fileList) {
      			this.alertMsg('图片已达上限请删除当前图片再上传新的图片','warning');
            },
            //商品详情上传图片
            uploadProSuccess(response, file, fileList)
		    {
		      console.log("商品详情上传");
		      this.alertMsg('上传成功！',"success");
		      let data = response.data || {};
		      console.log("接收的上传数据",data);
		      console.log("上传路径",data.filePath);
		      let noExistsList = data.noExistsList || "";
		      let filePath = data.filePath || "";
		      console.log("详情上传路径",filePath);
		      this.proPhotoUrl.push(filePath);
		      this.uploadProCallbackInfo = noExistsList;
		      this.filesProduct.length=1;
		    },
		   // 保存数据
		   submitForm(form)
    		{
    			this.$refs[form].validate((valid,obj) => {
    				if(valid){
    					console.log("验证通过");
    					if(this.filesBanner.length<=0) {
            				this.alertMsg('请上传商品图片');
            				return false;
          				}
          				if(this.proPhotoUrl.length>10){
          					this.alertMsg('详情图片超过上限');
            				return false;
          				}
          				if(JSON.stringify(rootData.data.details)!=="{}"){
          					if(this.form.mainTitle!==rootData.data.details.main_title){
          						console.log("编辑时修改了标题,检测标题");
			               		this.submitBefore();
          					}else{
          						console.log("编辑时未修改标题");
          						this.submitFormAction();
          					}
          				}else{
          					console.log("添加时检测标题");
          					this.submitBefore();
          				}
    				}else{
    					console.log("验证失败");
    					for(let key in obj){
    						if(key=="mainTitle"||key=="expireTime"||key=="selectType"||key=="conferenceTime"){
	    						if(this.rules[key][0].message){
					              document.getElementById(key).scrollIntoView();
					              this.alertMsg(this.rules[key][0].message);
					            }
    						}
				            break;
				          }
				          return false;
    				}
    			})
    		},
    		resetForm(formName){
    			 window.location.href="/group-activity/index";
    		},
    		//提交前检测标题是否重复
    		submitBefore(){
    			axios.get(this.checkFormUrl,{params:{'main_title':this.form.mainTitle}}).then((response)=> {
		        var data = response.data;
		        if(data){
		          this.alertMsg('套餐名称不能重复!');
		        }else{
		           this.submitFormAction();
		        }
		      }).catch((error)=> {
		        this.alertMsg(error);
		      });
    		},
    		//提交请求
    		submitFormAction(){
    		  let postUrl = this.submitUrl;
		      let params = {
		      	activity_id:this.form.activityId,
		      	main_title:this.form.mainTitle,
		      	subhead:this.form.subTitle,
		      	begin_time:this.form.expireTime[0],//开始时间
		      	end_time:this.form.expireTime[1],//结束时间
		      	status:this.form.status,
		      	type:this.form.selectType,//参团类型
		      	new_type:this.form.newUserType,//新用户类型
		      	duration:this.form.conferenceTime,//开团时间
		      	price_ladder:this.form.activityGradient,//活动梯度
		      	drink_num:this.form.totalDrinks,//饮品总数
		      	drink_ladder:this.form.commodityGradient,//商品梯度
		      	// original_cost:this.form.originalPrice,//商品原价
		      	activity_img:this.bannerPhotoUrl,//商品图片
		      	activity_details_img:this.proPhotoUrl//商品详情图片
		      };
		      axios.post(postUrl, params).then((response)=> {
		        let data = response.data;
		        if(data.state=="1"){
                    const type = this.form.activityId==""?"1":"2";
                    return axios.get('/activity-combin-package-assoc/create-activity-log?type='+type+'&moduleType=2');
		        }else{
		           this.alertMsg("保存失败");
		        }
		      })
              .then((res)=>{
                 this.alertMsg('保存成功',"success");
                 setTimeout(()=>{
                   window.location.href="/group-activity/index";
                 },2000);
              })
              .catch((error)=> {
		        this.alertMsg(error);
		      });
    		}

		},
		compontents:{

		}
	}
</script>
<style>
	.row-type{
		margin:0 0 20px 140px;
		color:#606266;
		font-size:14px;
	}
	.activity span{
		float:left;
	}
	.btn-icon{
		position: relative;
		top:6px;
	}
</style>