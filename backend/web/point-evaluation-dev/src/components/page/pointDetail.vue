<template>
  <div class="content-body">
    <div class="line-title">基础</div>
    <el-table
    :data="recordInfo"
    style="width: 95%" border class="table-line">
    <el-table-column   prop="buildTypeID" label="渠道" width="180"></el-table-column>
    <el-table-column  prop="buildingName" label="楼宇名称" width="180"></el-table-column>
     <el-table-column prop="orgID" label="分公司"> </el-table-column>
    <el-table-column prop="buildPublicInfo.bCircle" label="商圈"> </el-table-column>
    <el-table-column prop="contactName" label="联系人"></el-table-column>
    <el-table-column prop="contactTel" label="电话"></el-table-column>
  </el-table>
  <el-table
    style="width: 95%" border :data="pointInfo" class="table-line">
    <el-table-column  prop="cooperate" label="合作方式" width="180"></el-table-column>
    <el-table-column  prop="point_basic_info.rentWay" label="租金方式" width="180"></el-table-column>
    <el-table-column  prop="point_basic_info.electric" label="电费"> </el-table-column>
    <el-table-column  prop="point_basic_info.service" label="劳务费"></el-table-column>
    <el-table-column  prop="point_basic_info.total" label="费用总额"> </el-table-column>
    <!-- <el-table-column prop="point_applicant" label="创建人"></el-table-column> -->
    <el-table-column prop="approval_name" label="提交人"></el-table-column>
    <el-table-column prop="point_position" label="摆放位置"></el-table-column>
    <el-table-column prop="created_at" label="提交时间"></el-table-column>
  </el-table>
  <div v-if="buildTypeValue=='学校'">
    <el-table
    :data="pointInfo"
    style="width: 50%" border class="table-line">
    <el-table-column   prop="point_basic_info.area" label="楼宇占地面积" width="180"></el-table-column>
    <el-table-column   prop="point_basic_info.floor" label="楼宇层高" width="180"></el-table-column>
    <el-table-column   prop="point_basic_info.humanTraffic" label="点位人流量"> </el-table-column>
  </el-table>
  </div>
  <div v-else-if="buildTypeValue=='公司'">
    <el-table
    :data="pointInfo"
    style="width: 95%" border class="table-line">
    <el-table-column   prop="point_basic_info.companyType" label="公司类型" width="180"></el-table-column>
  </el-table>
  </div>
  <div v-else-if="buildTypeValue=='医院'">
    <el-table
    :data="pointInfo"
    style="width: 25%" border class="table-line">
    <el-table-column   prop="point_basic_info.hospitalLevel" label="医院等级"></el-table-column>
    <!-- <el-table-column  prop="point_basic_info.receptionNum" label="门诊接待量" width="180"></el-table-column> -->
    <!-- <el-table-column  prop="point_basic_info.medicalNum" label="医护人员数量"> </el-table-column> -->
    <!-- <el-table-column  prop="point_basic_info.hospitalHuman" label="点位人流量"></el-table-column> -->
  </el-table>
  </div>
  <div class="line-title">评分</div>
    <div v-if="buildTypeValue=='写字楼'">
      <div class="sub-title">条件1</div>
      <el-table
      :data="pointInfo"
      style="width: 95%" border class="table-line">
      <el-table-column   prop="point_score_info.floorArea" label="平层面积" width="180"></el-table-column>
      <el-table-column  prop="point_score_info.buildFloor" label="楼层" width="180"></el-table-column>
      <el-table-column  prop="point_score_info.lodgingRatio" label="入住率"> </el-table-column>
      <el-table-column  prop="point_score_info.useRatio" label="使用率"></el-table-column>
      <el-table-column prop="point_score_info.buildNum" label="楼宇人数"> </el-table-column>
    </el-table>
    <div class="sub-title">条件2</div>
    <el-table
      :data="buildDataList"
      style="width: 95%" border class="table-line">
      <el-table-column prop="businessCircle" label="所在商圈"></el-table-column>
      <el-table-column prop="officeRent" label="办公室租金"></el-table-column>
      <el-table-column prop="officeProperty" label="写字楼属性"></el-table-column>
      <el-table-column prop="hallArea" label="大堂面积"></el-table-column>
      <el-table-column prop="lobbyHigh" label="大堂挑高"></el-table-column>
      <el-table-column prop="facadeMaterial" label="外立面材料"></el-table-column>
      <el-table-column prop="groundFloor" label="大堂地面"></el-table-column>
      <el-table-column prop="airConditioner" label="空调"></el-table-column>
      <el-table-column prop="elevatorsNumber" label="电梯数量"></el-table-column>
    </el-table>
    <div class="sub-title">条件3</div>
    <el-table
      :data="buildDataList"
      style="width: 95%" border class="table-line">
      <el-table-column prop="populationAge" label="人群年龄层"></el-table-column>
      <el-table-column prop="scale" label="男女比例"></el-table-column>
      <el-table-column prop="companySize" label="公司规模"></el-table-column>
      <el-table-column prop="companyNature" label="公司性质"></el-table-column>
      <el-table-column prop="yesOrNotOverThree" label="是否有公司超过三层"></el-table-column>
      <el-table-column prop="isMoreEntrance" label="是否多个出入口分流"></el-table-column>
      <el-table-column prop="equipmentLocation" label="设备摆放位置"></el-table-column>
      <el-table-column prop="coffeeshop" label="便利店（现磨）/咖啡厅"></el-table-column>
      <el-table-column prop="roundBusiness" label="周边30米内商业"></el-table-column>
    </el-table>
    </div>
    <div v-else-if="buildTypeValue=='园区'">
      <div class="sub-title">条件1</div>
      <el-table
      :data="pointInfo"
      style="width: 95%" border class="table-line">
      <el-table-column   prop="point_score_info.floorParkArea" label="平层面积" width="180"></el-table-column>
      <el-table-column  prop="point_score_info.buildParkFloor" label="楼层" width="180"></el-table-column>
      <el-table-column  prop="point_score_info.lodgingParkRatio" label="入住率"> </el-table-column>
      <el-table-column  prop="point_score_info.useParkRatio" label="使用率"></el-table-column>
      <el-table-column prop="point_score_info.coverParkNum" label="楼宇人数"> </el-table-column>
      </el-table>
      <div class="sub-title">条件2</div>
    <el-table
      :data="parkDataList"
      style="width: 95%" border class="table-line">
      <el-table-column prop="businessCircle" label="所在商圈"></el-table-column>
      <el-table-column prop="officeRent" label="办公室租金"></el-table-column>
      <el-table-column prop="officeProperty" label="写字楼属性"></el-table-column>
      <el-table-column prop="hallArea" label="大堂面积"></el-table-column>
      <el-table-column prop="lobbyHigh" label="大堂挑高"></el-table-column>
      <el-table-column prop="facadeMaterial" label="外立面材料"></el-table-column>
      <el-table-column prop="groundFloor" label="大堂地面"></el-table-column>
      <el-table-column prop="airConditioner" label="空调"></el-table-column>
      <el-table-column prop="elevatorsNumber" label="电梯数量"></el-table-column>
    </el-table>
    <div class="sub-title">条件3</div>
    <el-table
      :data="parkDataList"
      style="width: 95%" border class="table-line">
      <el-table-column prop="populationAge" label="人群年龄层"></el-table-column>
      <el-table-column prop="scale" label="男女比例"></el-table-column>
      <el-table-column prop="companySize" label="公司规模"></el-table-column>
      <el-table-column prop="companyNature" label="公司性质"></el-table-column>
      <el-table-column prop="yesOrNotOverThree" label="是否有公司超过三层"></el-table-column>
      <el-table-column prop="isMoreEntrance" label="是否多个出入口分流"></el-table-column>
      <el-table-column prop="equipmentLocation" label="设备摆放位置"></el-table-column>
      <el-table-column prop="coffeeshop" label="便利店（现磨）/咖啡厅"></el-table-column>
      <el-table-column prop="roundBusiness" label="周边30米内商业"></el-table-column>
    </el-table>
    </div>
    <div v-else-if="buildTypeValue=='学校'">
      <div class="sub-title">学校情况</div>
      <el-table
      :data="schoolDataList"
      style="width: 95%" border class="table-line">
      <el-table-column   prop="schoolTyle" label="学校类型" width="180"></el-table-column>
      <el-table-column  prop="numberOfSchool" label="学校人数" width="180"></el-table-column>
      <el-table-column  prop="schoolPutinNumber" label="学校投放台数"> </el-table-column>
      <el-table-column  prop="chargingStandard" label="学费收费标准"></el-table-column>
      <el-table-column prop="sexRatio" label="男女比例"> </el-table-column>
      <el-table-column prop="livingExpenses" label="每个月生活费"></el-table-column>
      <el-table-column prop="intramuralCommerce" label="校内商业"></el-table-column>
      <el-table-column prop="lessThanThreeThousand" label="在校人数是否少于三千"></el-table-column>
    </el-table>
    <div class="sub-title">所在楼宇情况</div>
    <el-table
      :data="schoolDataList"
      style="width: 95%" border class="table-line">
      <el-table-column prop="buildingAttribute" label="楼宇属性"></el-table-column>
      <el-table-column prop="buildingArea" label="楼宇占地面积"></el-table-column>
      <el-table-column prop="buildingHeight" label="楼宇层高"></el-table-column>
      <el-table-column prop="humanTraffic" label="点位人流量"></el-table-column>
      <el-table-column prop="hasOtherEquipment" label="是否有其他自助设备"></el-table-column>
      <el-table-column prop="equipmentLocation" label="设备摆放位置"></el-table-column>
      <el-table-column prop="coffeeshop" label="便利店（现磨）／咖啡厅"></el-table-column>
      <el-table-column prop="isMoreEntrance" label="是否多个出入口分流"></el-table-column>
    </el-table>
    </div>
    <div v-else-if="buildTypeValue=='公司'">
      <div class="sub-title">所在楼宇情况</div>
      <el-table
      :data="companyDataList"
      style="width: 95%" border class="table-line">
      <el-table-column   prop="businessCircle" label="商圈" width="180"></el-table-column>
      <el-table-column   prop="officeProperty" label="写字楼属性" width="180"></el-table-column>
      <el-table-column  prop="hallArea" label="大堂面积" width="180"></el-table-column>
      <el-table-column  prop="lobbyHigh" label="大堂挑高"> </el-table-column>
      <el-table-column  prop="facadeMaterial" label="外立面材料"></el-table-column>
      <el-table-column prop="groundFloor" label="大堂地面"> </el-table-column>
      <el-table-column prop="airConditioner" label="空调"></el-table-column>
      <el-table-column prop="elevatorsNumber" label="电梯数量"></el-table-column>
      <el-table-column prop="coffeeshop" label="便利店（现磨）／
咖啡厅"></el-table-column>
      <el-table-column prop="roundBusiness" label="周边商业"></el-table-column>
      <el-table-column prop="sameLayer" label="公司是否在同层"></el-table-column>
    </el-table>
    <div class="sub-title">公司情况</div>
    <el-table
      :data="companyDataList"
      style="width: 95%" border class="table-line">
      <el-table-column prop="companyPersonNum" label="公司人数"></el-table-column>
      <el-table-column prop="companyNature" label="公司性质"></el-table-column>
      <el-table-column prop="officeRent" label="办公室租金"></el-table-column>
      <el-table-column prop="equipmentLocation" label="设备摆放位置"></el-table-column>
      <el-table-column prop="populationAge" label="人群年龄层"></el-table-column>
      <el-table-column prop="scale" label="男女比例"></el-table-column>
      <el-table-column prop="overTime" label="员工是否经常加班"></el-table-column>
      <el-table-column prop="onDutyMajority" label="坐班人员是否占大多数"></el-table-column>
      <el-table-column prop="servingCoffee" label="是否提供咖啡"></el-table-column>
      <el-table-column prop="servingAfternoonTea" label="是否提供下午茶"></el-table-column>
      <el-table-column prop="selfServiceEquipment" label="是否有其他自助设备"></el-table-column>
    </el-table>
    </div>
    <div v-else-if="buildTypeValue=='医院'">
      <div class="sub-title">医院情况</div>
      <el-table
      :data="hospitalDataList"
      style="width: 60%" border class="table-line">
        <el-table-column  prop="hospitalType" label="医院类型" width="180"></el-table-column>
        <el-table-column  prop="medicalNum" label="医护人员数量"> </el-table-column>
        <el-table-column  prop="receptionNum" label="门诊接待量"></el-table-column>
        <el-table-column  prop="hospitalBusiness" label="院内商业" width="180"></el-table-column>
        <el-table-column  prop="coffeeshop" label="便利店（现磨）／
  咖啡厅"> </el-table-column>
      </el-table>
      <div class="sub-title">所在楼宇情况</div>
      <el-table
      :data="hospitalDataList"
      style="width: 60%" border class="table-line">
        <el-table-column  prop="buildingAttribute" label="楼宇属性"> </el-table-column>
        <el-table-column  prop="buildingHeight" label="楼宇层高"></el-table-column>
        <el-table-column  prop="humanTraffic" label="点位人流量"></el-table-column>
        <el-table-column  prop="equipmentLocation" label="设备摆放位置"></el-table-column>
        <el-table-column  prop="hasOtherEquipment" label="是否有其他自助设备"></el-table-column>
      </el-table>
    </div>
    <div v-else>
      <el-table
      :data="othersDataList"
      style="width: 95%" border class="table-line">
        <el-table-column  prop="populationAge" label="人群年龄层"> </el-table-column>
        <el-table-column  prop="scale" label="男女比例"></el-table-column>
        <el-table-column  prop="roundBusiness" label="周边商业"></el-table-column>
        <el-table-column  prop="checkFloorArea" label="平层面积"></el-table-column>
        <el-table-column  prop="checkClothing" label="人群穿着"></el-table-column>
        <el-table-column  prop="checkCoverPopulation" label="覆盖人数"> </el-table-column>
        <el-table-column  prop="checkFloorHeight" label="楼层高度"></el-table-column>
        <el-table-column  prop="checkFiftyCoffee" label="50米内咖啡情况"> </el-table-column>
      </el-table>
    </div>
    <!-- 其他 -->
  <div class="line-title">其他</div>
  <el-table
    style="width: 95%" border :data="otherInfoData" class="table-line">
    <el-table-column  prop="role_name" label="手机信号" width="180">
      <template slot-scope="scope">
          <div v-if="scope.row.mobile!=''">
            <label>移动：</label><label>{{scope.row.mobile}}</label>
          </div>
          <div v-if="scope.row.unicorn!=''">
            <label>联通：</label><label>{{scope.row.unicorn}}</label>
          </div>
          <div v-if="scope.row.telecom!=''">
            <label>电信：</label><label>{{scope.row.telecom}}</label>
          </div>
      </template>
    </el-table-column>
    <el-table-column  prop="power" label="电源情况" width="180"></el-table-column>
    <el-table-column  prop="putEnvironment" label="投放环境"> </el-table-column>
    <el-table-column  prop="competingGoods" label="竞品情况"></el-table-column>
  </el-table>
  <!-- 初评建议 -->
  <div class="line-title">初评建议</div>
  <el-table
    style="width: 95%" border :data="buildRateData" class="table-line">
    <el-table-column  prop="role_name" label="职位名称" width="180"></el-table-column>
    <el-table-column  prop="rate_id" label="操作人" width="180"></el-table-column>
    <el-table-column  prop="rate_time" label="时间"> </el-table-column>
    <el-table-column  prop="rate_status" label="初评建议"></el-table-column>
    <el-table-column prop="rate_info" label="建议详情"> </el-table-column>
  </el-table>
  <!-- 照片 -->
  <div class="line-title">照片</div>
  <div class="sub-title">楼宇外观</div>
  <div class="pic-line" style="clear:both;">
      <img :src="item"   v-for="(item,index) in buildAppearPic" :key="index" class="pic">
  </div>
  <div class="sub-title">大厅全景</div>
  <div class="pic-line" style="clear:both;">
      <img :src="item"   v-for="(item,index) in buildHallPic" :key="index" class="pic">
  </div>
  <div class="sub-title">清晰的水牌</div>
  <div class="pic-line" style="clear:both;">
      <img :src="item"   v-for="(item,index) in pointLicencePic" :key="index" class="pic">
  </div>
  <div class="sub-title">具体投放位置</div>
  <div class="pic-line" style="clear:both;">
      <img :src="item"   v-for="(item,index) in pointPositionPic" :key="index" class="pic">
  </div>
  <div class="sub-title">楼上公司照片</div>
  <div class="pic-line" style="clear:both;">
      <img :src="item"   v-for="(item,index) in pointCompanyPic" :key="index" class="pic">
  </div>
  <div class="sub-title">平面图</div>
  <div class="pic-line" style="clear:both;">
      <img :src="item"   v-for="(item,index) in pointPlan" :key="index" class="pic">
  </div>
  <!-- 审批流程 -->
  <div class="line-title">审批流程</div>
  <el-table
    style="width: 95%" border :data="approvalList" class="table-line">
    <el-table-column  prop="role_name" label="职位名称" width="180"></el-table-column>
    <el-table-column  prop="approver" label="操作人" width="180"></el-table-column>
    <el-table-column  prop="created_at" label="时间"> </el-table-column>
    <el-table-column  prop="approver_status" label="审批流程"></el-table-column>
    <el-table-column prop="approval_level" label="审批级别"> </el-table-column>
    <el-table-column prop="approver_msg" label="意见"> </el-table-column>
  </el-table>
  <!-- 转交记录 -->
  <div class="line-title">转交记录</div>
  <el-table
    style="width: 95%" border :data="transferList" class="table-line">
    <el-table-column  prop="role_ame" label="职位名称" width="180"></el-table-column>
    <el-table-column  prop="transfer_id" label="操作人" width="180"></el-table-column>
    <el-table-column  prop="transfer_time" label="时间"> </el-table-column>
    <el-table-column  prop="original_creator_name" label="原操作人"></el-table-column>
    <el-table-column prop="new_creator_name" label="新负责人"> </el-table-column>
  </el-table>
  <div class="div-submit buttonSubmit" v-if="showButton">
        <el-button @click="showModalToast(3)">驳回</el-button>
        <el-button type="primary" @click="showModalToast(2)">通过</el-button>
  </div>
  <el-dialog title="建议" :visible.sync="dialogFormVisible">
  <el-form :model="form">
    <el-form-item label="级别:" :label-width="formLabelWidth" v-show="isAgree">
      <el-select placeholder="请选择"  v-model="form.checkLevel" style="width:80%">
          <el-option :label="item" :value="key" v-for="(item,key) in levelList" :key="key"></el-option>
      </el-select>
    </el-form-item>
    <el-form-item label="原因:" :label-width="formLabelWidth" v-show="isAgree">
      <el-input v-model.trim="form.reason" autocomplete="off" style="width:80%"></el-input>
    </el-form-item>
    <el-form-item label="驳回原因:" :label-width="formLabelWidth" v-show="!isAgree">
      <el-select placeholder="请选择"  v-model="form.rejectReason" style="width:80%">
          <el-option :label="item" :value="key" v-for="(item,key) in baseInfo.commonInfo.rejectReason" :key="key"></el-option>
      </el-select>
    </el-form-item>
    <el-form-item label="详细说明:" :label-width="formLabelWidth" v-show="!isAgree">
      <el-input v-model.trim="form.reason" autocomplete="off" style="width:80%"></el-input>
    </el-form-item>
  </el-form>
  <div slot="footer" class="dialog-footer">
    <el-button @click="dialogFormVisible = false">取 消</el-button>
    <el-button type="primary" @click="submitForm()">确 定</el-button>
  </div>
</el-dialog>
  </div>
</template>
<script>
// eslint-disable-next-line
/* eslint-disable */
import axios from 'axios'
export default {
  data() {
    return {
      baseInfo:baseInfo,
      id:"",//楼宇Id
      pointId:"",//点位ID
      submitFormUrl:'/point-evaluation/point-approval',
      recordInfo:[],
      pointInfo:[],//点位信息
      levelList:[],//级别列表
      buildTypeValue:"写字楼",//渠道类型中文
      commonInfo:baseInfo.commonInfo,//公共基础数据
      specialInfo:baseInfo.buildSpecialInfo,//所有特殊信息对象
      renderPublicInfoData:"",//公共信息串
      renderSpecialInfoData:"",//特殊信息串
      dialogFormVisible: false,
      buildAppearPic:[],//楼宇照片
      buildHallPic:[],//大厅照片
      pointLicencePic:[],//清晰水牌
      pointCompanyPic:[],//公司照片
      pointPlan:[],//平面图
      pointPositionPic:[],//投放位置
      buildTypeList:{},//渠道列表
      showButton:false,
      form: {
          reason:"",
          checkLevel:"",//选择的级别
          rejectReason: ""
        },
      formLabelWidth: '80px',
      isAgree:false,
      // 基础信息
      //学校
      baseDataSchoolList:[{
        area:'',//楼宇占地面积
        floor:'',//楼宇层高
        humanTraffic:'',//点位人流量
      }],
      //公司
      baseDataCompanyList:[{
        companyType:'',//公司类型
        companyNum:'',//公司人数
        scale:'',//男女比例
      }],
      //医院
      baseDataHospitalList:[{
        hospitalLevel:'',//医院等级
        receptionNum:'',//门诊接待量
        medicalNum:'',//医护人员数量
        hospitalHuman:'',//医院点位人流量
      }],
      // 条件
      //写字楼
      buildDataList:[{
        businessCircle:"",//商圈
        officeRent:"",//办公室租金
        officeProperty:"",//写字楼属性
        hallArea:"",//大堂面积
        lobbyHigh:"",//大堂挑高
        facadeMaterial:"",//外立面材料
        groundFloor:"",//大堂地面
        airConditioner:"",//空调
        elevatorsNumber:"",//电梯数量
        // 条件3
        populationAge:"",//人群年龄层
        scale:"",//男女比例
        companySize:"",//公司规模
        companyNature:"",//公司性质
        yesOrNotOverThree:"",//是否有公司超过三层
        equipmentLocation:"",//设备摆放位置
        isMoreEntrance:"",//多个出入口
        coffeeshop:"",//便利店
        roundBusiness:"",//周边30米
      }],
      //园区
      parkDataList:[{
        businessCircle:"",//商圈
        officeRent:"",//办公室租金
        officeProperty:"",//写字楼属性
        hallArea:"",//大堂面积
        lobbyHigh:"",//大堂挑高
        facadeMaterial:"",//外立面材料
        groundFloor:"",//大堂地面
        airConditioner:"",//空调
        elevatorsNumber:"",//电梯数量
        // 条件3
        populationAge:"",//人群年龄层
        scale:"",//男女比例
        companySize:"",//公司规模
        companyNature:"",//公司性质
        yesOrNotOverThree:"",//是否有公司超过三层
        equipmentLocation:"",//设备摆放位置
        isMoreEntrance:"",//多个出入口
        coffeeshop:"",//便利店
        roundBusiness:"",//周边30米
      }],
       //学校
      schoolDataList:[{
        //学校情况
        schoolTyle:"",//学校类型
        numberOfSchool:"",//学校人数
        schoolPutinNumber:"",//学校投放台数
        chargingStandard:"",//学费收费标准
        sexRatio:"",//男女比例
        livingExpenses:"",//每个月生活费
        intramuralCommerce:"",//校内商业
        lessThanThreeThousand:"",//在校人数是否少于三千
        //楼宇情况
        buildingAttribute:"",//楼宇属性
        buildingArea:"",//楼宇占地面积
        buildingHeight:"",//楼宇层高
        humanTraffic:"",//点位人流量
        hasOtherEquipment:"",//其他自助设备
        equipmentLocation:"",//设备摆放位置
        coffeeshop:"",//便利店
        isMoreEntrance:""//是否多个出入口
      }],
        //公司
      companyDataList:[{
        //所在楼宇情况
        businessCircle:"",//商圈
        officeProperty:"",//写字楼属性
        hallArea:"",//大堂面积
        lobbyHigh:"",//大堂挑高
        facadeMaterial:"",//外立面材料
        groundFloor:"",//大堂地面
        airConditioner:"",//空调
        elevatorsNumber:"",//电梯数量
        coffeeshop:"",//便利店
        roundBusiness:"",//周边商业
        sameLayer: "",//公司是否在同层
        //公司情况
        companyPersonNum:"",//公司人数
        companyNature:"",//公司性质
        officeRent: "",//办公室租金
        equipmentLocation:"",//设备摆放位置
        populationAge:"",//人群年龄层
        scale:"",//男女比例
        overTime:"",//员工是否经常加班
        onDutyMajority:"",//坐班人员是否占大多数
        servingCoffee:"",//是否提供咖啡
        servingAfternoonTea:"",//是否提供下午茶
        selfServiceEquipment:""//是否有其他自助设备

      }],
      //医院
      hospitalDataList:[{
        //医院情况
        hospitalType:"",//医院类型
        medicalNum:"",//医护数量
        receptionNum:"",//门诊接待量
        hospitalBusiness:"",//院内商业
        coffeeshop:"",//便利店
        //楼宇情况
        buildingAttribute:"",//楼宇属性
        buildingHeight:"",//楼宇层高
        humanTraffic:"",//点位人流量
        equipmentLocation:"",//设备摆放位置
        hasOtherEquipment:""//是否有其他设备
      }],
      othersDataList: [{
          roundBusiness:"",//周边商业
          populationAge:"",//人群年龄层
          scale:"",//男女比例
          checkFloorArea: "",
          checkClothing: "",
          checkCoverPopulation: "",
          checkFloorHeight: "",
          checkFiftyCoffee: ""
      }],
      buildRateData:[],//初评建议
      buildRate:{},
      transferList:[],//转交记录
      approvalList:[],//审批流程
      otherInfoData:[],//其他
      showModal:false
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
      // 获取ID
      this.pointId=this.$route.query.pointId;//点位Id
      if(this.$route.query.evaluate) {
        this.showButton = true;
      }
      // this.pointId=6;
      this.getDetailData();
    },
    render(){//渲染数据
      this.buildTypeValue=this.recordInfo[0].buildTypeID;
      //图片
      this.buildAppearPic=this.recordInfo[0].buildAppearPic;
      this.buildHallPic=this.recordInfo[0].buildHallPic;
      this.pointLicencePic=this.pointInfo[0].point_licence_pic;//清晰水牌
      this.pointCompanyPic=this.pointInfo[0].point_company_pic;//公司照片
      this.pointPlan=this.pointInfo[0].point_plan;//平面图
      this.pointPositionPic=this.pointInfo[0].point_position_pic;//投放位置
      //其他信息
      this.otherInfoData.push(this.pointInfo[0].point_other_info);
      //公共信息
      // this.commonDataList[0].humanTraffic=this.commonInfo.humanTraffic[this.commonInfoData.humanTraffic];//人流量
      // this.commonDataList[0].scale=this.commonInfo.scale[this.commonInfoData.scale];//男女比例
      // this.commonDataList[0].isMoreEntrance=this.commonInfo.isMoreEntrance[this.commonInfoData.isMoreEntrance];//是否多个出口
      // this.commonDataList[0].populationAge=this.commonInfo.populationAge[this.commonInfoData.populationAge];//人群年龄层
      // this.commonDataList[0].equipmentLocation=this.commonInfo.equipmentLocation[this.commonInfoData.equipmentLocation];//设备摆放位置
      // this.commonDataList[0].coffeeshop=this.commonInfo.coffeeshop[this.commonInfoData.coffeeshop];//便利店（现磨）/ 咖啡厅
      // this.commonDataList[0].roundBusiness=this.commonInfo.roundBusiness[this.commonInfoData.roundBusiness];//周边30米内商业
      // this.commonDataList[0].hasOtherEquipment=this.commonInfo.hasOtherEquipment[this.commonInfoData.hasOtherEquipment];////是否有其他自助设备
      // this.commonDataList[0].businessCircle=this.commonInfo.businessCircle[this.commonInfoData.businessCircle];////是否有其他自助设备
      //条件信息串
      //条件值
      this.renderSpecialInfoData=this.recordInfo[0].buildSpecialInfo;
      this.renderPublicInfoData=this.recordInfo[0].buildPublicInfo;
      const baseCommonInfo=this.commonInfo;//公共基础数据
      if(this.buildTypeValue=="写字楼"){
         // 写字楼
          const buildSpecialData=this.specialInfo.officeBuilding;//写字楼基础特殊数据
          this.buildDataList[0].businessCircle=baseCommonInfo.businessCircle[this.renderPublicInfoData.businessCircle];
          this.buildDataList[0].officeRent=buildSpecialData.officeRent[this.renderSpecialInfoData.officeRent];
          this.buildDataList[0].officeProperty=buildSpecialData.officeProperty[this.renderSpecialInfoData.officeProperty];
          this.buildDataList[0].hallArea=buildSpecialData.hallArea[this.renderSpecialInfoData.hallArea];
          this.buildDataList[0].lobbyHigh=buildSpecialData.lobbyHigh[this.renderSpecialInfoData.lobbyHigh];
          this.buildDataList[0].facadeMaterial=buildSpecialData.facadeMaterial[this.renderSpecialInfoData.facadeMaterial];
          this.buildDataList[0].groundFloor=buildSpecialData.groundFloor[this.renderSpecialInfoData.groundFloor];
          this.buildDataList[0].airConditioner=buildSpecialData.airConditioner[this.renderSpecialInfoData.airConditioner];
          this.buildDataList[0].elevatorsNumber=buildSpecialData.elevatorsNumber[this.renderSpecialInfoData.elevatorsNumber];
          //条件3
          this.buildDataList[0].populationAge=baseCommonInfo.populationAge[this.renderPublicInfoData.populationAge];
          this.buildDataList[0].scale=baseCommonInfo.scale[this.renderPublicInfoData.scale];
          this.buildDataList[0].companySize=buildSpecialData.companySize[this.renderSpecialInfoData.companySize];
          this.buildDataList[0].companyNature=buildSpecialData.companyNature[this.renderSpecialInfoData.companyNature];
          this.buildDataList[0].yesOrNotOverThree=buildSpecialData.yesOrNotOverThree[this.renderSpecialInfoData.yesOrNotOverThree];
          this.buildDataList[0].equipmentLocation=baseCommonInfo.equipmentLocation[this.renderPublicInfoData.equipmentLocation];
          this.buildDataList[0].isMoreEntrance=baseCommonInfo.isMoreEntrance[this.renderPublicInfoData.isMoreEntrance];
          this.buildDataList[0].coffeeshop=baseCommonInfo.coffeeshop[this.renderPublicInfoData.coffeeshop];
          this.buildDataList[0].roundBusiness=baseCommonInfo.roundBusiness[this.renderPublicInfoData.roundBusiness];

      }else if(this.buildTypeValue=="园区"){
          //园区基础特殊数据
          const parkSpecialData=this.specialInfo.park;
          this.parkDataList[0].businessCircle=baseCommonInfo.businessCircle[this.renderPublicInfoData.businessCircle];
          this.parkDataList[0].officeRent=parkSpecialData.officeRent[this.renderSpecialInfoData.officeRent];
          this.parkDataList[0].officeProperty=parkSpecialData.officeProperty[this.renderSpecialInfoData.officeProperty];
          this.parkDataList[0].hallArea=parkSpecialData.hallArea[this.renderSpecialInfoData.hallArea];
          this.parkDataList[0].lobbyHigh=parkSpecialData.lobbyHigh[this.renderSpecialInfoData.lobbyHigh];
          this.parkDataList[0].facadeMaterial=parkSpecialData.facadeMaterial[this.renderSpecialInfoData.facadeMaterial];
          this.parkDataList[0].groundFloor=parkSpecialData.groundFloor[this.renderSpecialInfoData.groundFloor];
          this.parkDataList[0].airConditioner=parkSpecialData.airConditioner[this.renderSpecialInfoData.airConditioner];
          this.parkDataList[0].elevatorsNumber=parkSpecialData.elevatorsNumber[this.renderSpecialInfoData.elevatorsNumber];
          //条件3
          this.parkDataList[0].populationAge=baseCommonInfo.populationAge[this.renderPublicInfoData.populationAge];
          this.parkDataList[0].scale=baseCommonInfo.scale[this.renderPublicInfoData.scale];
          this.parkDataList[0].companySize=parkSpecialData.companySize[this.renderSpecialInfoData.companySize];
          this.parkDataList[0].companyNature=parkSpecialData.companyNature[this.renderSpecialInfoData.companyNature];
          this.parkDataList[0].yesOrNotOverThree=parkSpecialData.yesOrNotOverThree[this.renderSpecialInfoData.yesOrNotOverThree];
          this.parkDataList[0].equipmentLocation=baseCommonInfo.equipmentLocation[this.renderPublicInfoData.equipmentLocation];
          this.parkDataList[0].isMoreEntrance=baseCommonInfo.isMoreEntrance[this.renderPublicInfoData.isMoreEntrance];
          this.parkDataList[0].coffeeshop=baseCommonInfo.coffeeshop[this.renderPublicInfoData.coffeeshop];
          this.parkDataList[0].roundBusiness=baseCommonInfo.roundBusiness[this.renderPublicInfoData.roundBusiness];
      }else if(this.buildTypeValue=="学校"){
          //学校特殊基础数据
          const schoolSpecialData=this.specialInfo.school;
          //学校情况
          this.schoolDataList[0].schoolTyle=schoolSpecialData.schoolTyle[this.renderSpecialInfoData.schoolTyle];
          this.schoolDataList[0].numberOfSchool=schoolSpecialData.numberOfSchool[this.renderSpecialInfoData.numberOfSchool];
          this.schoolDataList[0].schoolPutinNumber=schoolSpecialData.schoolPutinNumber[this.renderSpecialInfoData.schoolPutinNumber];
          this.schoolDataList[0].chargingStandard=schoolSpecialData.chargingStandard[this.renderSpecialInfoData.chargingStandard];
          this.schoolDataList[0].sexRatio=schoolSpecialData.sexRatio[this.renderSpecialInfoData.sexRatio];
          this.schoolDataList[0].livingExpenses=schoolSpecialData.livingExpenses[this.renderSpecialInfoData.livingExpenses];
          this.schoolDataList[0].intramuralCommerce=schoolSpecialData.intramuralCommerce[this.renderSpecialInfoData.intramuralCommerce];
          this.schoolDataList[0].lessThanThreeThousand=schoolSpecialData.lessThanThreeThousand[this.renderSpecialInfoData.lessThanThreeThousand];
          //楼宇情况
          this.schoolDataList[0].buildingAttribute=schoolSpecialData.buildingAttribute[this.renderSpecialInfoData.buildingAttribute];
          this.schoolDataList[0].buildingArea=schoolSpecialData.buildingArea[this.renderSpecialInfoData.buildingArea];
          this.schoolDataList[0].buildingHeight=schoolSpecialData.buildingHeight[this.renderSpecialInfoData.buildingHeight];
          this.schoolDataList[0].humanTraffic=baseCommonInfo.humanTraffic[this.renderPublicInfoData.humanTraffic];
          this.schoolDataList[0].hasOtherEquipment=baseCommonInfo.hasOtherEquipment[this.renderPublicInfoData.hasOtherEquipment];
          this.schoolDataList[0].equipmentLocation=baseCommonInfo.equipmentLocation[this.renderPublicInfoData.equipmentLocation];
          this.schoolDataList[0].coffeeshop=baseCommonInfo.coffeeshop[this.renderPublicInfoData.coffeeshop];
          this.schoolDataList[0].isMoreEntrance=baseCommonInfo.isMoreEntrance[this.renderPublicInfoData.isMoreEntrance];
      }else if(this.buildTypeValue=="公司"){
          //公司特殊基础数据
          const companySpecialData=this.specialInfo.company;
          //楼宇情况
          this.companyDataList[0].businessCircle=baseCommonInfo.businessCircle[this.renderPublicInfoData.businessCircle];
          this.companyDataList[0].officeProperty=companySpecialData.officeProperty[this.renderSpecialInfoData.officeProperty];
          this.companyDataList[0].hallArea=companySpecialData.hallArea[this.renderSpecialInfoData.hallArea];
          this.companyDataList[0].lobbyHigh=companySpecialData.lobbyHigh[this.renderSpecialInfoData.lobbyHigh];
          this.companyDataList[0].facadeMaterial=companySpecialData.facadeMaterial[this.renderSpecialInfoData.facadeMaterial];
          this.companyDataList[0].groundFloor=companySpecialData.groundFloor[this.renderSpecialInfoData.groundFloor];
          this.companyDataList[0].airConditioner=companySpecialData.airConditioner[this.renderSpecialInfoData.airConditioner];
          this.companyDataList[0].elevatorsNumber=companySpecialData.elevatorsNumber[this.renderSpecialInfoData.elevatorsNumber];
          this.companyDataList[0].coffeeshop=baseCommonInfo.coffeeshop[this.renderPublicInfoData.coffeeshop];
          this.companyDataList[0].roundBusiness=baseCommonInfo.roundBusiness[this.renderPublicInfoData.roundBusiness];
          this.companyDataList[0].sameLayer=companySpecialData.sameLayer[this.renderSpecialInfoData.sameLayer];
          //公司情况
          this.companyDataList[0].companyPersonNum=this.renderSpecialInfoData.numberOfCompanies;
          this.companyDataList[0].companyNature=companySpecialData.companyNature[this.renderSpecialInfoData.companyNature];
          this.companyDataList[0].officeRent=companySpecialData.officeRent[this.renderSpecialInfoData.officeRent];
          this.companyDataList[0].equipmentLocation=companySpecialData.equipmentLocation[this.renderPublicInfoData.equipmentLocation];
          this.companyDataList[0].populationAge=baseCommonInfo.populationAge[this.renderPublicInfoData.populationAge];
          this.companyDataList[0].scale=baseCommonInfo.scale[this.renderPublicInfoData.scale];
          this.companyDataList[0].overTime=companySpecialData.overTime[this.renderSpecialInfoData.overTime];
          this.companyDataList[0].onDutyMajority=companySpecialData.onDutyMajority[this.renderSpecialInfoData.onDutyMajority];
          this.companyDataList[0].servingCoffee=companySpecialData.servingCoffee[this.renderSpecialInfoData.servingCoffee];
          this.companyDataList[0].servingAfternoonTea=companySpecialData.servingAfternoonTea[this.renderSpecialInfoData.servingAfternoonTea];
          this.companyDataList[0].selfServiceEquipment=companySpecialData.selfServiceEquipment[this.renderSpecialInfoData.selfServiceEquipment];

      }else if(this.buildTypeValue=="医院"){
          //医院特殊基础数据
          const hospitalSpecialData=this.specialInfo.hospital;
          //医院情况
          this.hospitalDataList[0].hospitalType=hospitalSpecialData.hospitalType[this.renderSpecialInfoData.hospitalType];
          this.hospitalDataList[0].medicalNum=this.renderSpecialInfoData.medicalNum;
          this.hospitalDataList[0].receptionNum=this.renderSpecialInfoData.receptionNum;
          this.hospitalDataList[0].hospitalBusiness=hospitalSpecialData.hospitalBusiness[this.renderSpecialInfoData.hospitalBusiness];
          this.hospitalDataList[0].coffeeshop=baseCommonInfo.coffeeshop[this.renderPublicInfoData.coffeeshop];
          //楼宇情况
          this.hospitalDataList[0].buildingAttribute=hospitalSpecialData.buildingAttribute[this.renderSpecialInfoData.buildingAttribute];
          this.hospitalDataList[0].buildingHeight=hospitalSpecialData.buildingHeight[this.renderSpecialInfoData.buildingHeight];
          this.hospitalDataList[0].humanTraffic=baseCommonInfo.humanTraffic[this.renderPublicInfoData.humanTraffic];
          this.hospitalDataList[0].equipmentLocation=baseCommonInfo.equipmentLocation[this.renderPublicInfoData.equipmentLocation];
          this.hospitalDataList[0].hasOtherEquipment=baseCommonInfo.hasOtherEquipment[this.renderPublicInfoData.hasOtherEquipment];
      } else {
        this.othersDataList[0].roundBusiness = baseCommonInfo.roundBusiness[this.renderPublicInfoData.roundBusiness];
        this.othersDataList[0].populationAge = baseCommonInfo.populationAge[this.renderPublicInfoData.populationAge];
        this.othersDataList[0].scale = baseCommonInfo.scale[this.renderPublicInfoData.scale];
        this.othersDataList[0].checkFloorArea = this.renderSpecialInfoData.checkFloorArea;
        this.othersDataList[0].checkClothing = this.renderSpecialInfoData.checkClothing;
        this.othersDataList[0].checkCoverPopulation = this.renderSpecialInfoData.checkCoverPopulation;
        this.othersDataList[0].checkFloorHeight = this.renderSpecialInfoData.checkFloorHeight;
        this.othersDataList[0].checkFiftyCoffee = this.renderSpecialInfoData.checkFiftyCoffee;
      }
    },
    showModalToast(item){
      this.dialogFormVisible=true;
      if(item==2){
        this.isAgree=true;
        this.form.reason="";
        this.form.checkLevel="";
      }else{
        this.isAgree=false;
        this.form.reason="";
        this.form.checkLevel="";
      }
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
        let mycss2=document.getElementsByClassName("el-dialog")[0];
        if(mycss){
          mycss.style.cssText="top: "+scrollTop+"px;z-index:1000;";
        }
        if(mycss2){
          mycss2.style.cssText="top: "+(scrollTop+100)+"px;z-index:1000;";
        }
      }
    },
    getDetailData(){
      console.log("pointId",this.pointId);
      axios.get('/point-evaluation/view?id='+this.pointId).then((res)=>{
        console.log("res",res);
        const initData =res.data;
        if(initData.error_code!="0"){
          this.alertMsg(initData.msg);
          return;
        }else{
          this.recordInfo = [];
          this.pointInfo = [];
          this.buildRateData = [];
          this.recordInfo.push(initData.data.recordInfo);
          this.pointInfo.push(initData.data.pointInfo);
          this.levelList=initData.data.pointLevel;
          //初评建议
          this.buildRate=initData.data.buildRate;
          if(this.buildRate!="{}"){
            this.buildRateData.push(initData.data.buildRate);
          }
          this.transferList=initData.data.transferInfo;//转交记录
          this.approvalList=initData.data.approvalInfo//审核流程
          //是否显示操作按钮
          // if (initData.data.approvalRole=='0'){
          //     this.showButton=false;//false
          // }else{
          //     this.showButton=true;
          // }
          // console.log("info",this.recordInfo);
          this.render();
        }
      }).catch((error)=>{
           console.log("error..",error);
      });
    },
    submitForm() //提交建议
    {
      const reason=this.form.reason;
      if(reason==""){
        this.alertMsg("请输入原因","error");
      }else if(this.isAgree&&this.form.checkLevel===""){
        this.alertMsg("请选择级别","error");
      }else if(!this.isAgree&&this.form.rejectReason===""){
        this.alertMsg("请选择级别","error");
      }else{
        this.submitAction();
        this.dialogFormVisible=false;
      }
    },
    submitAction(){
      let params={
        approval_level:this.form.checkLevel,//选择级别
        point_id:this.pointId,//点位id
        approver_msg:this.form.reason,
        approver_status:2
      }
      if(!this.isAgree){
        params={
          approval_level:0,//选择级别
          point_id:this.pointId,//点位id
          approver_msg:baseInfo.commonInfo.rejectReason[this.form.rejectReason]+","+this.form.reason,
          approver_status:3//2通过、3驳回
        }
      }
      console.log('params',params);
      axios.post(this.submitFormUrl,params)
      .then((response)=> {
        let data = response.data;
        console.log('data',data);
        if(data.error_code==0){
          // this.getDetailData();
          this.alertMsg("操作成功","success");
          this.$router.push({name:"pointList"});
        }else{
          this.alertMsg(data.msg);
          return false;
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
<style>
.table-line{
  margin-bottom:20px;
  margin-left:30px;
}
.sub-title{
  font-size:14px;
}
.pic-line{
  margin-left:100px;
}
.pic{
  width:300px;
  height: auto;
  margin-bottom: 10px;
}
.pic+.pic{
  margin-left:10px !important;
}
.pic:nth-child(3n+1){
  margin-left:50px !important;
}
.buttonSubmit{
  margin:0;
}
.el-table td, .el-table th.is-leaf{
  text-align:center;
}

</style>
