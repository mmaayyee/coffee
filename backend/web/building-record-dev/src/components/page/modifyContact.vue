<template>
  <div class="content-body">
    <div class="line-title">编辑信息</div>
    <el-form ref="form"  :model="form" :rules="rules"  label-width="150px">
      <el-row :gutter="10">
        <el-col :span="12" :offset="0">
          <el-form-item label="联系人：" >
            <el-input v-model="form.contactName"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="12" :offset="0">
          <el-form-item label="联系方式：" prop="contactTel" id="contactTel">
            <el-input v-model="form.contactTel"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-form-item size="medium" class="div-submit">
        <el-button type="primary" @click="submitForm('form')">修改</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>
<script>
// eslint-disable-next-line
/* eslint-disable */
import axios from 'axios'
export default {
  data() {
     var checkPhone = (rule,value,callback) => {
        if (value=="") {
          return callback();
        }else{
          var myReg=/^[1][3,4,5,6,7,8,9][0-9]{9}$/;//手机号验证
          var telReg=/^(\(\d{3,4}\)|\d{3,4}-|\s)?\d{7,14}$///座机号验证
          if (!myReg.test(value)&&!telReg.test(value)) {
             callback(new Error('号码格式不正确'));
          }else{
            callback();
          }
        }

      };
    return {
      submitFormUrl:"/building-record/update-contact-info",
      buildRecordId:"",//楼宇Id
      pointId:"",//点位Id
      form:{
        contactName:"",
        contactTel:"",
      },
      rules:{
        contactTel:[
          {validator: checkPhone}
        ]
      }
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
     this.buildRecordId=this.$route.query.id;//获取编辑页传来的楼宇Id
     this.pointId=this.$route.query.pointId;//获取编辑页传来的点位Id
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
    submitForm(formName){
      this.$refs[formName].validate((valid,obj) => {
        if(valid){
          this.submitAction();
        }
      })
    },
    submitAction(){
      let params={
        contactName:this.form.contactName,
        contactTel:this.form.contactTel,
        id:this.buildRecordId//楼宇ID
      }
       const header = {
        'content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
        }
        axios.post(this.submitFormUrl,params,header)
          .then((response)=> {
            let data = response.data;
            console.log("data",data);
            if(data.error_code==0){
               this.alertMsg('修改成功','success');
               this.$router.push({name:"pointDetail",query:{pointId:this.pointId}});
            }else{
               this.alertMsg('修改失败','error');
            }
          })
          .catch((error)=> {
            this.alertMsg(error);
          });
    }
  },
  components: {}
}
</script>
<style scoped>
.div-submit{
  width:70%;
  text-align:center;
}
</style>
