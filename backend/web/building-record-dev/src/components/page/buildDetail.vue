<template>
  <div class="content-body" v-if="showInfo==1">
    <div class="line-title">公共信息</div>
    <el-table
    :data="recordInfo"
    style="width: 95%" border class="table-line">
      <el-table-column prop="buildTypeValue" label="渠道" width="180"></el-table-column>
      <el-table-column prop="buildingName" label="楼宇(建筑物)名称" width="180"></el-table-column>
      <el-table-column prop="city" label="城市"> </el-table-column>
      <el-table-column prop="area" label="行政区"> </el-table-column>
      <el-table-column prop="buildPublicInfo.bCircle" label="商圈"> </el-table-column>
      <el-table-column prop="buildPublicInfo.floor" label="楼层"> </el-table-column>
      <el-table-column prop="contactName" label="联系人"></el-table-column>
      <el-table-column prop="contactTel" label="联系方式"></el-table-column>
    </el-table>
    <el-table
      style="width: 95%" border :data="commonDataList" class="table-line">
      <el-table-column  prop="humanTraffic" label="点位人流量" width="180"></el-table-column>
      <el-table-column  prop="isMoreEntrance" label="是否多个出入口分流" width="180"></el-table-column>
      <el-table-column  prop="populationAge" label="人群年龄层"> </el-table-column>
      <el-table-column  prop="scale" label="男女比例"></el-table-column>
      <el-table-column  prop="equipmentLocation" label="设备摆放位置"> </el-table-column>
      <el-table-column prop="coffeeshop" label="便利店（现磨）/ 咖啡厅"></el-table-column>
      <el-table-column prop="roundBusiness" label="周边30米内商业"></el-table-column>
      <el-table-column prop="businessCircle" label="所在商圈"></el-table-column>
      <el-table-column prop="hasOtherEquipment" label="是否有其他自助设备"></el-table-column>
    </el-table>
    <div class="line-title">特殊信息</div>
    <div v-if="buildTypeValue=='写字楼'">
      <el-table
      :data="specialDataBuildList"
      style="width: 95%" border class="table-line">
        <el-table-column   prop="officeRent" label="办公室租金" width="180"></el-table-column>
        <el-table-column   prop="officeProperty" label="写字楼属性" width="180"></el-table-column>
        <el-table-column   prop="hallArea" label="大堂面积"></el-table-column>
        <el-table-column   prop="lobbyHigh" label="大堂挑高"></el-table-column>
        <el-table-column   prop="facadeMaterial" label="外立面材料"></el-table-column>
        <el-table-column   prop="groundFloor" label="大堂地面"></el-table-column>
      </el-table>
      <el-table
      :data="specialDataBuildList"
      style="width: 95%" border class="table-line">
        <el-table-column   prop="airConditioner" label="空调"></el-table-column>
        <el-table-column   prop="elevatorsNumber" label="电梯数量"></el-table-column>
        <el-table-column   prop="companySize" label="公司规模"></el-table-column>
        <el-table-column   prop="companyNature" label="公司性质"></el-table-column>
        <el-table-column   prop="yesOrNotOverThree" label="是否有公司超过三层"></el-table-column>
      </el-table>
    </div>
    <div v-else-if="buildTypeValue=='园区'">
      <el-table
      :data="specialDataParkList"
      style="width: 95%" border class="table-line">
        <el-table-column   prop="officeRent" label="办公室租金" width="180"></el-table-column>
        <el-table-column   prop="officeProperty" label="写字楼属性" width="180"></el-table-column>
        <el-table-column   prop="hallArea" label="大堂面积"></el-table-column>
        <el-table-column   prop="lobbyHigh" label="大堂挑高"></el-table-column>
        <el-table-column   prop="facadeMaterial" label="外立面材料"></el-table-column>
        <el-table-column   prop="groundFloor" label="大堂地面"></el-table-column>
      </el-table>
      <el-table
      :data="specialDataParkList"
      style="width: 95%" border class="table-line">
        <el-table-column   prop="airConditioner" label="空调"></el-table-column>
        <el-table-column   prop="elevatorsNumber" label="电梯数量"></el-table-column>
        <el-table-column   prop="companySize" label="公司规模"></el-table-column>
        <el-table-column   prop="companyNature" label="公司性质"></el-table-column>
        <el-table-column   prop="yesOrNotOverThree" label="是否有公司超过三层"></el-table-column>
      </el-table>
    </div>
    <div v-else-if="buildTypeValue=='学校'">
      <el-table
      :data="specialDataSchoolList"
      style="width: 95%" border class="table-line">
        <el-table-column   prop="schoolTyle" label="学校类型" width="180"></el-table-column>
        <el-table-column   prop="numberOfSchool" label="学校人数" width="180"></el-table-column>
        <el-table-column   prop="schoolPutinNumber" label="学校投放台数"></el-table-column>
        <el-table-column   prop="chargingStandard" label="学费收费标准"></el-table-column>
        <el-table-column   prop="sexRatio" label="男女比例"></el-table-column>
        <el-table-column   prop="livingExpenses" label="每个月生活费"></el-table-column>
      </el-table>
      <el-table
      :data="specialDataSchoolList"
      style="width: 95%" border class="table-line">
        <el-table-column   prop="intramuralCommerce" label="校内商业"></el-table-column>
        <el-table-column   prop="lessThanThreeThousand" label="在校人数是否少于三千"></el-table-column>
        <el-table-column   prop="buildingAttribute" label="楼宇属性"></el-table-column>
        <el-table-column   prop="buildingArea" label="楼宇占地面积"></el-table-column>
        <el-table-column   prop="buildingHeight" label="楼宇层高"></el-table-column>
      </el-table>
    </div>
    <div v-else-if="buildTypeValue=='公司'">
      <el-table
      :data="specialDataCompanyList"
      style="width: 95%" border class="table-line">
        <el-table-column   prop="officeProperty" label="写字楼属性" width="180"></el-table-column>
        <el-table-column   prop="hallArea" label="大堂面积" width="180"></el-table-column>
        <el-table-column   prop="lobbyHigh" label="大堂挑高"></el-table-column>
        <el-table-column   prop="facadeMaterial" label="外立面材料"></el-table-column>
        <el-table-column   prop="groundFloor" label="大堂地面"></el-table-column>
        <el-table-column   prop="airConditioner" label="空调"></el-table-column>
        <el-table-column   prop="elevatorsNumber" label="电梯数量"></el-table-column>
        <el-table-column   prop="sameLayer" label="公司是否在同层"></el-table-column>
      </el-table>
      <el-table
      :data="specialDataCompanyList"
      style="width: 95%" border class="table-line">
        <el-table-column   prop="companyNature" label="公司性质"></el-table-column>
        <el-table-column   prop="officeRent" label="办公室租金"></el-table-column>
        <el-table-column   prop="overTime" label="员工是否经常加班"></el-table-column>
        <el-table-column   prop="onDutyMajority" label="坐班人员是否占大多数"></el-table-column>
        <el-table-column   prop="servingCoffee" label="是否提供咖啡"></el-table-column>
        <el-table-column   prop="servingAfternoonTea" label="是否提供下午茶"></el-table-column>
        <el-table-column   prop="selfServiceEquipment" label="是否有其他自助设备"></el-table-column>
        <el-table-column   prop="numberOfCompanies" label="公司人数"></el-table-column>
      </el-table>
    </div>
    <div v-else-if="buildTypeValue=='医院'">
      <el-table
      :data="specialDataHospitalList"
      style="width: 95%" border class="table-line">
        <el-table-column  prop="hospitalType" label="医院类型" width="180"></el-table-column>
        <el-table-column  prop="hospitalBusiness" label="院内商业" width="180"></el-table-column>
        <el-table-column  prop="buildingAttribute" label="楼宇属性"> </el-table-column>
        <el-table-column  prop="buildingHeight" label="楼宇层高"></el-table-column>
        <el-table-column  prop="medicalNum" label="医护人员数量"> </el-table-column>
        <el-table-column  prop="receptionNum" label="门诊接待量"></el-table-column>
      </el-table>
    </div>
    <div v-else>
      <el-table
      :data="specialOthersList"
      style="width: 95%" border class="table-line">
        <el-table-column  prop="checkFloorArea" label="平层面积"></el-table-column>
        <el-table-column  prop="checkClothing" label="人群穿着"></el-table-column>
        <el-table-column  prop="checkCoverPopulation" label="覆盖人数"> </el-table-column>
        <el-table-column  prop="checkFloorHeight" label="楼层高度"></el-table-column>
        <el-table-column  prop="checkFiftyCoffee" label="50米内咖啡情况"> </el-table-column>
      </el-table>
    </div>
    <!-- 照片 -->
    <div class="line-title">照片</div>
    <div class="sub-title">楼宇照片</div>
    <div class="pic-line" style="clear:both;">
        <img :src="item"   v-for="(item,index) in buildAppearPic" :key="index" class="pic">
    </div>
    <div class="sub-title">大厅照片</div>
    <div class="pic-line" style="clear:both;">
        <img :src="item"   v-for="(item,index) in buildHallPic" :key="index" class="pic">
    </div>
    <!-- 初评建议 -->
    <div class="line-title">初评建议</div>
    <el-table style="width: 95%" border :data="buildRateData" class="table-line">
      <el-table-column  prop="role_name" label="职位名称" width="180"></el-table-column>
      <el-table-column  prop="rate_id" label="操作人" width="180"></el-table-column>
      <el-table-column  prop="rate_time" label="时间"> </el-table-column>
      <el-table-column  prop="rate_status" label="初评建议"></el-table-column>
      <el-table-column prop="rate_info" label="建议详情"> </el-table-column>
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
      <el-button @click="showModalToast(1)">不通过</el-button>
      <el-button type="primary" @click="showModalToast(2)">通过</el-button>
    </div>
    <el-dialog title="建议" :visible.sync="dialogFormVisible">
      <el-form :model="form">
        <el-form-item label="原因:" :label-width="formLabelWidth">
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
      showInfo: 2,
      id: "",
      submitFormUrl: "/building-record/rate-building-record",
      recordInfo: [],
      buildTypeValue: "",
      commonInfo: baseInfo.commonInfo,
      specialInfo: baseInfo.buildSpecialInfo,
      commonInfoData: "",
      buildSpecialInfo: "",
      dialogFormVisible: false,
      buildAppearPic: [],
      buildHallPic: [],
      buildTypeList: {},
      showButton: false,
      form: {
          reason: ""
      },
      formLabelWidth: "100px",
      isAgree: false,
      commonDataList: [{
          humanTraffic: "",
          scale: "",
          roundBusiness: "",
          isMoreEntrance: "",
          equipmentLocation: "",
          businessCircle: "",
          populationAge: "",
          coffeeshop: "",
          hasOtherEquipment: ""
      }],
      specialDataBuildList: [{
          officeRent: "",
          officeProperty: "",
          hallArea: "",
          lobbyHigh: "",
          facadeMaterial: "",
          groundFloor: "",
          airConditioner: "",
          elevatorsNumber: "",
          companySize: "",
          companyNature: "",
          yesOrNotOverThree: ""
      }],
      specialDataParkList: [{
          officeRent: "",
          officeProperty: "",
          hallArea: "",
          lobbyHigh: "",
          facadeMaterial: "",
          groundFloor: "",
          airConditioner: "",
          elevatorsNumber: "",
          companySize: "",
          companyNature: "",
          yesOrNotOverThree: ""
      }],
      specialDataSchoolList: [{
          schoolTyle: "",
          numberOfSchool: "",
          schoolPutinNumber: "",
          chargingStandard: "",
          sexRatio: "",
          livingExpenses: "",
          intramuralCommerce: "",
          lessThanThreeThousand: "",
          buildingAttribute: "",
          buildingArea: "",
          buildingHeight: ""
      }],
      specialDataCompanyList: [{
          officeProperty: "",
          hallArea: "",
          lobbyHigh: "",
          facadeMaterial: "",
          groundFloor: "",
          airConditioner: "",
          elevatorsNumber: "",
          sameLayer: "",
          companyNature: "",
          officeRent: "",
          overTime: "",
          onDutyMajority: "",
          servingCoffee: "",
          servingAfternoonTea: "",
          selfServiceEquipment: "",
          numberOfCompanies: ""
      }],
      specialDataHospitalList: [{
          hospitalType: "",
          hospitalBusiness: "",
          buildingAttribute: "",
          buildingHeight: "",
          medicalNum: "",
          receptionNum: ""
      }],
      specialOthersList: [{
          checkFloorArea: "",
          checkClothing: "",
          checkCoverPopulation: "",
          checkFloorHeight: "",
          checkFiftyCoffee: ""
      }],
      buildRateData: [],
      buildRate: {},
      transferList: [],
      showModal: false
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
      this.id = this.$route.query.id;
      this.showButton = this.$route.query.evaluate;
      console.log("evaluate.",this.$route.query.evaluate);
      // this.id=6;
      this.getDetailData();
    },
    render(){
      // this.id = this.recordInfo[0].id;
      // console.log("render id.",this.recordInfo[0].id);
      this.commonInfoData = this.recordInfo[0].buildPublicInfo;
      this.buildSpecialInfo = this.recordInfo[0].buildSpecialInfo;
      this.buildAppearPic = this.recordInfo[0].buildAppearPic;
      this.buildHallPic = this.recordInfo[0].buildHallPic;
      this.buildTypeValue = this.buildTypeList[this.recordInfo[0].buildTypeID];
      // console.log("this.buildTypeList..",this.buildTypeList);
      // console.log("this.recordInfo[0].buildTypeID..",this.recordInfo[0].buildTypeID);
      // console.log("this.buildTypeValue..",this.buildTypeValue);
      this.recordInfo[0].buildTypeValue = this.buildTypeValue;
      this.commonDataList[0].humanTraffic = this.commonInfo.humanTraffic[this.commonInfoData.humanTraffic];
      this.commonDataList[0].scale = this.commonInfo.scale[this.commonInfoData.scale];
      this.commonDataList[0].isMoreEntrance = this.commonInfo.isMoreEntrance[this.commonInfoData.isMoreEntrance];
      this.commonDataList[0].populationAge = this.commonInfo.populationAge[this.commonInfoData.populationAge];
      this.commonDataList[0].equipmentLocation = this.commonInfo.equipmentLocation[this.commonInfoData.equipmentLocation];
      this.commonDataList[0].coffeeshop = this.commonInfo.coffeeshop[this.commonInfoData.coffeeshop];
      this.commonDataList[0].roundBusiness = this.commonInfo.roundBusiness[this.commonInfoData.roundBusiness];
      this.commonDataList[0].hasOtherEquipment = this.commonInfo.hasOtherEquipment[this.commonInfoData.hasOtherEquipment];
      this.commonDataList[0].businessCircle = this.commonInfo.businessCircle[this.commonInfoData.businessCircle];
      console.log("special",this.buildSpecialInfo);
      if("写字楼" == this.buildTypeValue){
        this.specialDataBuildList[0].officeRent = this.specialInfo.officeBuilding.officeRent[this.buildSpecialInfo.officeRent];
        this.specialDataBuildList[0].officeProperty = this.specialInfo.officeBuilding.officeProperty[this.buildSpecialInfo.officeProperty];
        this.specialDataBuildList[0].hallArea = this.specialInfo.officeBuilding.hallArea[this.buildSpecialInfo.hallArea];
        this.specialDataBuildList[0].lobbyHigh = this.specialInfo.officeBuilding.lobbyHigh[this.buildSpecialInfo.lobbyHigh];
        this.specialDataBuildList[0].facadeMaterial = this.specialInfo.officeBuilding.facadeMaterial[this.buildSpecialInfo.facadeMaterial];
        this.specialDataBuildList[0].groundFloor = this.specialInfo.officeBuilding.groundFloor[this.buildSpecialInfo.groundFloor];
        this.specialDataBuildList[0].airConditioner = this.specialInfo.officeBuilding.airConditioner[this.buildSpecialInfo.airConditioner];
        this.specialDataBuildList[0].elevatorsNumber = this.specialInfo.officeBuilding.elevatorsNumber[this.buildSpecialInfo.elevatorsNumber];
        this.specialDataBuildList[0].companySize = this.specialInfo.officeBuilding.companySize[this.buildSpecialInfo.companySize];
        this.specialDataBuildList[0].companyNature = this.specialInfo.officeBuilding.companyNature[this.buildSpecialInfo.companyNature];
        this.specialDataBuildList[0].yesOrNotOverThree = this.specialInfo.officeBuilding.yesOrNotOverThree[this.buildSpecialInfo.yesOrNotOverThree]
      } else if("园区" == this.buildTypeValue) {
        this.specialDataParkList[0].officeRent = this.specialInfo.park.officeRent[this.buildSpecialInfo.officeRent];
        this.specialDataParkList[0].officeProperty = this.specialInfo.park.officeProperty[this.buildSpecialInfo.officeProperty];
        this.specialDataParkList[0].hallArea = this.specialInfo.park.hallArea[this.buildSpecialInfo.hallArea];
        this.specialDataParkList[0].lobbyHigh = this.specialInfo.park.lobbyHigh[this.buildSpecialInfo.lobbyHigh];
        this.specialDataParkList[0].facadeMaterial = this.specialInfo.park.facadeMaterial[this.buildSpecialInfo.facadeMaterial];
        this.specialDataParkList[0].groundFloor = this.specialInfo.park.groundFloor[this.buildSpecialInfo.groundFloor];
        this.specialDataParkList[0].airConditioner = this.specialInfo.park.airConditioner[this.buildSpecialInfo.airConditioner];
        this.specialDataParkList[0].elevatorsNumber = this.specialInfo.park.elevatorsNumber[this.buildSpecialInfo.elevatorsNumber];
        this.specialDataParkList[0].companySize = this.specialInfo.park.companySize[this.buildSpecialInfo.companySize];
        this.specialDataParkList[0].companyNature = this.specialInfo.park.companyNature[this.buildSpecialInfo.companyNature];
        this.specialDataParkList[0].yesOrNotOverThree = this.specialInfo.park.yesOrNotOverThree[this.buildSpecialInfo.yesOrNotOverThree];
      } else if("学校" == this.buildTypeValue) {
        this.specialDataSchoolList[0].schoolTyle = this.specialInfo.school.schoolTyle[this.buildSpecialInfo.schoolTyle];
        this.specialDataSchoolList[0].numberOfSchool = this.specialInfo.school.numberOfSchool[this.buildSpecialInfo.numberOfSchool];
        this.specialDataSchoolList[0].schoolPutinNumber = this.specialInfo.school.schoolPutinNumber[this.buildSpecialInfo.schoolPutinNumber];
        this.specialDataSchoolList[0].chargingStandard = this.specialInfo.school.chargingStandard[this.buildSpecialInfo.chargingStandard];
        this.specialDataSchoolList[0].sexRatio = this.specialInfo.school.sexRatio[this.buildSpecialInfo.sexRatio];
        this.specialDataSchoolList[0].livingExpenses = this.specialInfo.school.livingExpenses[this.buildSpecialInfo.livingExpenses];
        this.specialDataSchoolList[0].intramuralCommerce = this.specialInfo.school.intramuralCommerce[this.buildSpecialInfo.intramuralCommerce];
        this.specialDataSchoolList[0].lessThanThreeThousand = this.specialInfo.school.lessThanThreeThousand[this.buildSpecialInfo.lessThanThreeThousand];
        this.specialDataSchoolList[0].buildingAttribute = this.specialInfo.school.buildingAttribute[this.buildSpecialInfo.buildingAttribute];
        this.specialDataSchoolList[0].buildingArea = this.specialInfo.school.buildingArea[this.buildSpecialInfo.buildingArea];
        this.specialDataSchoolList[0].buildingHeight = this.specialInfo.school.buildingHeight[this.buildSpecialInfo.buildingHeight];
      } else if("公司" == this.buildTypeValue) {
        this.specialDataCompanyList[0].officeProperty = this.specialInfo.company.officeProperty[this.buildSpecialInfo.officeProperty];
        this.specialDataCompanyList[0].hallArea = this.specialInfo.company.hallArea[this.buildSpecialInfo.hallArea];
        this.specialDataCompanyList[0].lobbyHigh = this.specialInfo.company.lobbyHigh[this.buildSpecialInfo.lobbyHigh];
        this.specialDataCompanyList[0].facadeMaterial = this.specialInfo.company.facadeMaterial[this.buildSpecialInfo.facadeMaterial];
        this.specialDataCompanyList[0].groundFloor = this.specialInfo.company.groundFloor[this.buildSpecialInfo.groundFloor];
        this.specialDataCompanyList[0].airConditioner = this.specialInfo.company.airConditioner[this.buildSpecialInfo.airConditioner];
        this.specialDataCompanyList[0].elevatorsNumber = this.specialInfo.company.elevatorsNumber[this.buildSpecialInfo.elevatorsNumber];
        this.specialDataCompanyList[0].sameLayer = this.specialInfo.company.sameLayer[this.buildSpecialInfo.sameLayer];
        this.specialDataCompanyList[0].companyNature = this.specialInfo.company.companyNature[this.buildSpecialInfo.companyNature];
        this.specialDataCompanyList[0].officeRent = this.specialInfo.company.officeRent[this.buildSpecialInfo.officeRent];
        this.specialDataCompanyList[0].overTime = this.specialInfo.company.overTime[this.buildSpecialInfo.overTime];
        this.specialDataCompanyList[0].onDutyMajority = this.specialInfo.company.onDutyMajority[this.buildSpecialInfo.onDutyMajority];
        this.specialDataCompanyList[0].servingCoffee = this.specialInfo.company.servingCoffee[this.buildSpecialInfo.servingCoffee];
        this.specialDataCompanyList[0].servingAfternoonTea = this.specialInfo.company.servingAfternoonTea[this.buildSpecialInfo.servingAfternoonTea];
        this.specialDataCompanyList[0].selfServiceEquipment = this.specialInfo.company.selfServiceEquipment[this.buildSpecialInfo.selfServiceEquipment];
        this.specialDataCompanyList[0].numberOfCompanies = this.buildSpecialInfo.numberOfCompanies;
      } else if("医院" == this.buildTypeValue) {
        this.specialDataHospitalList[0].hospitalType = this.specialInfo.hospital.hospitalType[this.buildSpecialInfo.hospitalType];
        this.specialDataHospitalList[0].hospitalBusiness = this.specialInfo.hospital.hospitalBusiness[this.buildSpecialInfo.hospitalBusiness];
        this.specialDataHospitalList[0].buildingAttribute = this.specialInfo.hospital.buildingAttribute[this.buildSpecialInfo.buildingAttribute];
        this.specialDataHospitalList[0].buildingHeight = this.specialInfo.hospital.buildingHeight[this.buildSpecialInfo.buildingHeight];
        this.specialDataHospitalList[0].medicalNum = this.buildSpecialInfo.medicalNum;
        this.specialDataHospitalList[0].receptionNum = this.buildSpecialInfo.receptionNum;
      } else {
        this.specialOthersList[0].checkFloorArea = this.buildSpecialInfo.checkFloorArea;
        this.specialOthersList[0].checkClothing = this.buildSpecialInfo.checkClothing;
        this.specialOthersList[0].checkCoverPopulation = this.buildSpecialInfo.checkCoverPopulation;
        this.specialOthersList[0].checkFloorHeight = this.buildSpecialInfo.checkFloorHeight;
        this.specialOthersList[0].checkFiftyCoffee = this.buildSpecialInfo.checkFiftyCoffee;
      }
    },
    showModalToast(item){
      this.dialogFormVisible=true;
      if(item==2){
        this.isAgree=true;
        this.form.reason="";
      }else{
        this.isAgree=false;
        this.form.reason="";
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
      axios.get('/building-record/view?id='+this.id).then((res)=>{
        console.log("res",res);
        const initData =res.data;
        if(initData.error_code!="0"){
          this.alertMsg(initData.msg);
          this.showInfo = 2;
          return;
        }else{
          this.showInfo = 1;
          this.recordInfo.push(initData.data.recordInfo);
          this.buildRate = initData.data.buildRate;
          if(this.buildRate!="{}"){
            this.buildRateData.push(initData.data.buildRate);
          }
          this.transferList = initData.data.transferList;
          this.buildTypeList = initData.data.buildTypeList;
          this.render();
          console.log("info",this.recordInfo);
        }
      }).catch((error)=>{
           console.log("error..",error);
      });
    },
    updateRateData(){
      axios.get('/building-record/view?id='+this.id).then((res)=>{
        console.log("updateRateData res",res);
        const initData =res.data;
        if(initData.error_code!="0"){
          this.alertMsg(initData.msg);
          return;
        }else{
          this.buildRate = initData.data.buildRate;
          if(this.buildRate!="{}"){
            this.buildRateData.push(initData.data.buildRate);
          }
        }
      }).catch((error)=>{
           console.log("error..",error);
      });
    },
    submitForm() //提交建议
    {
      let reason=this.form.reason;
      if(reason==""){
        this.alertMsg("请输入原因","error");
      } else {
        this.submitAction();
        this.dialogFormVisible=false;
      }
    },
    submitAction(){
      let params={
        build_record_id: this.id,
        rate_info: this.form.reason,
        rate_status: this.isAgree?"1":"2"
      }
      console.log('params',params);
      axios.post(this.submitFormUrl,params)
      .then((response)=> {
        let data = response.data;
        console.log('data',data);
        if(data.error_code==0){
          this.updateRateData();
          this.alertMsg("初评成功","success");
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
