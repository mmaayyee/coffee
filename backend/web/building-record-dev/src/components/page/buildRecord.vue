<template>
  <div class="content-body">
    <el-form ref="form"  :model="form" :rules="rules"  label-width="150px" v-if="form.showInfo==1">
      <div class="line-title">公共信息</div>
      <el-row :gutter="10">
        <el-col :span="12" :offset="0">
         <el-form-item label="渠道：" prop="buildType" id="buildType">
            <el-select placeholder="请选择"  v-model="form.buildType" @change="changeBuildType">
               <el-option :label="item" :value="key" v-for="(item,key) in form.buildTypeList" :key="key"></el-option>
            </el-select>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row>
          <el-col :span="12" :offset="0" class="search-result" >
          <el-form-item label="楼宇建筑物名称：">
            <el-input v-model.trim="inputSearchVal" readonly @focus="searchMap"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="12" :offset="0" class="city">
          <el-form-item label="城市：">
            <el-input v-model="form.city" readonly></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="12" :offset="0" class="area">
          <el-form-item label="行政区：">
            <el-input v-model="form.area" readonly></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="12" :offset="0" class="address">
          <el-form-item label="地址：">
            <el-input v-model="form.address" readonly></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="12" :offset="0">
          <el-form-item label="商圈："  prop="bCircle" id="bCircle">
            <el-input v-model="form.bCircle"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="12" :offset="0">
          <el-form-item label="楼层："  prop="floor" id="floor">
            <el-input v-model="form.floor"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
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
      <div class="info_wrap">
        <el-row :gutter="10">
          <el-col :span="8" :offset="0">
           <el-form-item label="点位人流量：" prop="checkHumanTraffic" id="checkHumanTraffic">
              <el-select placeholder="请选择" v-model="form.checkHumanTraffic">
                <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.humanTraffic" :key="index"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="8" :offset="0">
           <el-form-item label="是否多个出入口分流：" prop="checkIsMoreEntrance" id="checkIsMoreEntrance">
              <el-select placeholder="请选择" v-model="form.checkIsMoreEntrance">
                <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.isMoreEntrance" :key="index"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="8" :offset="0">
           <el-form-item label="人群年龄层：" prop="checkPopulationAge" id="checkPopulationAge">
              <el-select placeholder="请选择" v-model="form.checkPopulationAge">
                <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.populationAge" :key="index"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
      </el-row>
      <el-row :gutter="10">
          <el-col :span="8" :offset="0">
           <el-form-item label="男女比例：" prop="checkScale" id="checkScale">
              <el-select placeholder="请选择" v-model="form.checkScale">
              <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.scale" :key="index"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="8" :offset="0">
           <el-form-item label="设备摆放位置：" prop="checkEquipmentLocation" id="checkEquipmentLocation">
              <el-select placeholder="请选择" v-model="form.checkEquipmentLocation">
                <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.equipmentLocation" :key="index"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="8" :offset="0">
           <el-form-item label="便利店（现磨）/咖啡厅：" prop="checkCoffeeshop" id="checkCoffeeshop">
              <el-select placeholder="请选择" v-model="form.checkCoffeeshop">
                <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.coffeeshop" :key="index"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
      </el-row>
      <el-row :gutter="10">
          <el-col :span="8" :offset="0">
           <el-form-item label="周边30米内商业：" prop="checkRoundBusiness" id="checkRoundBusiness">
              <el-select placeholder="请选择" v-model="form.checkRoundBusiness">
                <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.roundBusiness" :key="index"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="8" :offset="0">
           <el-form-item label="所在商圈：" prop="checkBusinessCircle" id="checkBusinessCircle">
              <el-select placeholder="请选择" v-model="form.checkBusinessCircle">
                <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.businessCircle" :key="index"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="8" :offset="0">
           <el-form-item label="是否有其他自助设备：" prop="checkHasOtherEquipment" id="checkHasOtherEquipment">
              <el-select placeholder="请选择" v-model="form.checkHasOtherEquipment">
                <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.hasOtherEquipment" :key="index"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
      </el-row>
      </div>
      <!-- 特殊信息 -->
      <div class="line-title">特殊信息</div>
      <div class="info_wrap">
        <!-- 写字楼 -->
        <div v-if="form.buildTypeValue=='写字楼'" class="officeBuilding">
          <el-row :gutter="10">
            <el-col :span="8" :offset="0">
             <el-form-item label="办公室租金 (天/平米)：" prop="checkOfficeRent" id="checkOfficeRent">
                <el-select placeholder="请选择" v-model="form.checkOfficeRent">
                 <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.officeRent" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="写字楼属性：" prop="checkOfficeProperty" id="checkOfficeProperty">
                <el-select placeholder="请选择" v-model="form.checkOfficeProperty">
                   <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.officeProperty" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="大堂面积：" prop="checkHallArea" id="checkHallArea">
                <el-select placeholder="请选择" v-model="form.checkHallArea">
                   <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.hallArea" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="10">
            <el-col :span="8" :offset="0">
             <el-form-item label="大堂挑高："  prop="checkLobbyHigh" id="checkLobbyHigh">
                <el-select placeholder="请选择" v-model="form.checkLobbyHigh">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.lobbyHigh" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="外立面材料：" prop="checkFacadeMaterial" id="checkFacadeMaterial">
                <el-select placeholder="请选择" v-model="form.checkFacadeMaterial">
                 <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.facadeMaterial" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="大堂地面：" prop="checkGroundFloor" id="checkGroundFloor">
                <el-select placeholder="请选择" v-model="form.checkGroundFloor">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.groundFloor" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="10">
            <el-col :span="8" :offset="0">
             <el-form-item label="空调：" prop="checkAirConditioner" id="checkAirConditioner">
                <el-select placeholder="请选择" v-model="form.checkAirConditioner">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.airConditioner" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="电梯数量：" prop="checkElevatorsNumber" id="checkElevatorsNumber">
                <el-select placeholder="请选择" v-model="form.checkElevatorsNumber">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.elevatorsNumber" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="公司规模：" prop="checkCompanySize" id="checkCompanySize">
                <el-select placeholder="请选择" v-model="form.checkCompanySize">
                   <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.companySize" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="10">
            <el-col :span="8" :offset="0">
              <el-form-item label="公司性质：" prop="checkCompanyNature" id="checkCompanyNature">
                <el-select placeholder="请选择" v-model="form.checkCompanyNature">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.companyNature" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="是否有公司超过三层：" prop="checkYesOrNotOverThree" id="checkYesOrNotOverThree">
                <el-select placeholder="请选择" v-model="form.checkYesOrNotOverThree">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.yesOrNotOverThree" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
        </div>
        <!-- 园区 -->
        <div v-else-if="form.buildTypeValue=='园区'" class="park">
          <el-row :gutter="10">
            <el-col :span="8" :offset="0">
             <el-form-item label="办公室租金 (天/平米)：" prop="checkParkOfficeRent" id="checkParkOfficeRent">
                <el-select placeholder="请选择" v-model="form.checkParkOfficeRent">
                 <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.park.officeRent" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="写字楼属性：" prop="checkParkOfficeProperty" id="checkParkOfficeProperty">
                <el-select placeholder="请选择" v-model="form.checkParkOfficeProperty">
                   <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.park.officeProperty" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="大堂面积：" prop="checkParkHallArea" id="checkParkHallArea">
                <el-select placeholder="请选择" v-model="form.checkParkHallArea">
                   <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.park.hallArea" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="10">
            <el-col :span="8" :offset="0">
             <el-form-item label="大堂挑高："  prop="checkParkLobbyHigh" id="checkParkLobbyHigh">
                <el-select placeholder="请选择" v-model="form.checkParkLobbyHigh">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.park.lobbyHigh" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="外立面材料：" prop="checkParkFacadeMaterial" id="checkParkFacadeMaterial">
                <el-select placeholder="请选择" v-model="form.checkParkFacadeMaterial">
                 <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.park.facadeMaterial" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="大堂地面：" prop="checkParkGroundFloor" id="checkParkGroundFloor">
                <el-select placeholder="请选择" v-model="form.checkParkGroundFloor">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.park.groundFloor" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="10">
            <el-col :span="8" :offset="0">
             <el-form-item label="空调：" prop="checkParkAirConditioner" id="checkParkAirConditioner">
                <el-select placeholder="请选择" v-model="form.checkParkAirConditioner">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.park.airConditioner" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="电梯数量：" prop="checkParkElevatorsNumber" id="checkParkElevatorsNumber">
                <el-select placeholder="请选择" v-model="form.checkParkElevatorsNumber">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.park.elevatorsNumber" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="公司规模：" prop="checkParkCompanySize" id="checkParkCompanySize">
                <el-select placeholder="请选择" v-model="form.checkParkCompanySize">
                   <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.park.companySize" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row :gutter="10">
            <el-col :span="8" :offset="0">
             <el-form-item label="公司性质：" prop="checkParkCompanyNature" id="checkParkCompanyNature">
                <el-select placeholder="请选择" v-model="form.checkParkCompanyNature">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.park.companyNature" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="是否有公司超过三层：" prop="checkParkYesOrNotOverThree" id="checkParkYesOrNotOverThree">
                <el-select placeholder="请选择" v-model="form.checkParkYesOrNotOverThree">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.park.yesOrNotOverThree" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
        </div>
        <!-- 学校 -->
        <div v-else-if="form.buildTypeValue=='学校'">
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="学校类型："  prop="checkSchoolTyle" id="checkSchoolTyle">
                <el-select placeholder="请选择" v-model="form.checkSchoolTyle">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.schoolTyle" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="学校人数："  prop="checkNumberOfSchool" id="checkNumberOfSchool">
                <el-select placeholder="请选择" v-model="form.checkNumberOfSchool">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.numberOfSchool" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="学校投放台数："  prop="checkSchoolPutinNumber" id="checkSchoolPutinNumber">
                <el-select placeholder="请选择" v-model="form.checkSchoolPutinNumber">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.schoolPutinNumber" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="学费收费标准:"  prop="checkChargingStandard" id="checkChargingStandard">
                <el-select placeholder="请选择" v-model="form.checkChargingStandard">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.chargingStandard" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="男女比例："  prop="checkSexRatio" id="checkSexRatio">
                <el-select placeholder="请选择" v-model="form.checkSexRatio">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.sexRatio" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="学生每月生活费:"  prop="checkLivingExpenses" id="checkLivingExpenses">
                <el-select placeholder="请选择" v-model="form.checkLivingExpenses">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.livingExpenses" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="校内商业："  prop="checkIntramuralCommerce" id="checkIntramuralCommerce">
                <el-select placeholder="请选择" v-model="form.checkIntramuralCommerce">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.intramuralCommerce" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="在校人数是否少于三千："  prop="checkLessThanThreeThousand" id="checkLessThanThreeThousand">
                <el-select placeholder="请选择" v-model="form.checkLessThanThreeThousand">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.lessThanThreeThousand" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="楼宇属性:"  prop="checkBuildingAttribute" id="checkBuildingAttribute">
                <el-select placeholder="请选择" v-model="form.checkBuildingAttribute">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.buildingAttribute" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="楼宇占地面积："  prop="checkBuildingArea" id="checkBuildingArea">
                <el-select placeholder="请选择" v-model="form.checkBuildingArea">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.buildingArea" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="楼宇层高："  prop="checkBuildingHeight" id="checkBuildingHeight">
                <el-select placeholder="请选择" v-model="form.checkBuildingHeight">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.buildingHeight" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
        </div>
        <!-- 公司 -->
        <div v-else-if="form.buildTypeValue=='公司'">
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="写字楼属性："  prop="checkCompanyOfficeProperty" id="checkCompanyOfficeProperty">
                <el-select placeholder="请选择" v-model="form.checkCompanyOfficeProperty">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.officeProperty" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="大堂面积："  prop="checkCompanyHallArea" id="checkCompanyHallArea">
                <el-select placeholder="请选择" v-model="form.checkCompanyHallArea">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.hallArea" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="大堂挑高："  prop="checkCompanyLobbyHigh" id="checkCompanyLobbyHigh">
                <el-select placeholder="请选择" v-model="form.checkCompanyLobbyHigh">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.lobbyHigh" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="外立面材料："  prop="checkCompanyFacadeMaterial" id="checkCompanyFacadeMaterial">
                <el-select placeholder="请选择" v-model="form.checkCompanyFacadeMaterial">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.facadeMaterial" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="大堂地面："  prop="checkCompanyGroundFloor" id="checkCompanyGroundFloor">
                <el-select placeholder="请选择" v-model="form.checkCompanyGroundFloor">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.groundFloor" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="空调："  prop="checkCompanyAirConditioner" id="checkCompanyAirConditioner">
                <el-select placeholder="请选择" v-model="form.checkCompanyAirConditioner">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.airConditioner" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="电梯数量："  prop="checkCompanyElevatorsNumber" id="checkCompanyElevatorsNumber">
                <el-select placeholder="请选择" v-model="form.checkCompanyElevatorsNumber">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.elevatorsNumber" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="公司是否在同层："  prop="checkCompanySameLayer" id="checkCompanySameLayer">
                <el-select placeholder="请选择" v-model="form.checkCompanySameLayer">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.sameLayer" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="公司性质："  prop="checkCompanyNature" id="checkCompanyNature">
                <el-select placeholder="请选择" v-model="form.checkCompanyNature">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.companyNature" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="办公室租金："  prop="checkCompanyOfficeRent" id="checkCompanyOfficeRent">
                <el-select placeholder="请选择" v-model="form.checkCompanyOfficeRent">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.officeRent" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="员工是否经常加班："  prop="checkCompanyOverTime" id="checkCompanyOverTime">
                <el-select placeholder="请选择" v-model="form.checkCompanyOverTime">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.overTime" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
                <el-form-item label="公司人数：" prop="numberOfCompanies" id="numberOfCompanies">
                  <el-input v-model="form.numberOfCompanies"></el-input>
                </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="是否提供咖啡："  prop="checkCompanyServingCoffee" id="checkCompanyServingCoffee">
                <el-select placeholder="请选择" v-model="form.checkCompanyServingCoffee">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.servingCoffee" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="是否提供下午茶："  prop="checkCompanyServingAfternoonTea" id="checkCompanyServingAfternoonTea">
                <el-select placeholder="请选择" v-model="form.checkCompanyServingAfternoonTea">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.servingAfternoonTea" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="是否有其他自助设备："  prop="checkCompanySelfServiceEquipment" id="checkCompanySelfServiceEquipment">
                <el-select placeholder="请选择" v-model="form.checkCompanySelfServiceEquipment">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.selfServiceEquipment" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="坐班人员是否占大多数："  prop="checkCompanyOnDutyMajority" id="checkCompanyOnDutyMajority">
                <el-select placeholder="请选择" v-model="form.checkCompanyOnDutyMajority">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.onDutyMajority" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
        </div>
        <!-- 医院 -->
        <div v-else-if="form.buildTypeValue=='医院'">
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="医院类型："  prop="checkHospitalType" id="checkHospitalType">
                <el-select placeholder="请选择" v-model="form.checkHospitalType">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.hospital.hospitalType" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="院内商业："  prop="checkHospitalBusiness" id="checkHospitalBusiness">
                <el-select placeholder="请选择" v-model="form.checkHospitalBusiness">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.hospital.hospitalBusiness" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="楼宇属性："  prop="checkHospitalBuildingAttribute" id="checkHospitalBuildingAttribute">
                <el-select placeholder="请选择" v-model="form.checkHospitalBuildingAttribute">
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.hospital.buildingAttribute" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
              <el-form-item label="楼宇层高："  prop="checkHospitalBuildingHeight" id="checkHospitalBuildingHeight">
              <el-select placeholder="请选择" v-model="form.checkHospitalBuildingHeight">
                <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.hospital.buildingHeight" :key="index"></el-option>
              </el-select>
            </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
              <el-form-item label="医护人员数量："  prop="medicalNum" id="medicalNum">
                <el-input v-model="form.medicalNum"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
              <el-form-item label="门诊接待量 ："  prop="receptionNum" id="receptionNum">
                <el-input v-model="form.receptionNum"></el-input>
              </el-form-item>
            </el-col>
          </el-row>
        </div>
        <div v-else>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="平层面积(m2)：" prop="checkFloorArea">
                <el-input v-model="form.checkFloorArea"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="人群穿着：" prop="checkClothing">
                <el-input v-model="form.checkClothing"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="覆盖人数：" prop="checkCoverPopulation">
                <el-input v-model="form.checkCoverPopulation"></el-input>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="楼层高度：" prop="checkFloorHeight">
                <el-input v-model="form.checkFloorHeight"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="12" :offset="0">
             <el-form-item label="50米内咖啡情况：" prop="checkFiftyCoffee">
                <el-input v-model="form.checkFiftyCoffee"></el-input>
              </el-form-item>
            </el-col>
          </el-row>
        </div>
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
      <!-- 特殊信息结束 -->
      <el-form-item size="medium" class="div-submit">
        <el-button @click="saveForm()">保存草稿</el-button>
        <el-button type="primary" @click="submitForm('form')">创建</el-button>
      </el-form-item>
      <div v-show="mapShow" class="map-container">
        <div class="map-bg">
          <el-row :gutter="10" class="build-name">
            <el-col :span="12" :offset="0" class="building-name" >
              <el-form-item label="楼宇名称搜索：">
                <el-input v-model.trim="inputSearchText"></el-input>
              </el-form-item>
            </el-col>
            <div class="search-address-list" v-show="showList">
              <div @click="chooseOneAddress(index)" v-for="(item,index) in searchAddressList" :key="index" class="choose-one-address">
                <p class="address1">{{item.title}}</p>
                <p class="address2">{{item.address}}</p>
              </div>
            </div>
          </el-row>
          <div id="allmap" v-show="showMap"></div>
          <div class="close-map"  @click="closeMap"><i class="el-icon-close"></i></div>
        </div>
      </div>
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
    var checkNumber=(rule,value,callback)=>{
      var checkReg=/^[1-9]\d*$/;
      if(!checkReg.test(value)){
         callback(new Error('请输入正整数'));
      }else{
        callback();
      }
    };
    return {
      mapShow: false,
      baseInfo:baseInfo,
      id:"",
      searchAddressList: [],
      showList: false,
      showMap:false,
      currentCity:'',
      inputSearchVal:'',//搜索结果
      inputSearchText:'',//搜索内容
      buildAppearPic:[],
      buildHallPic:[],
      form:{
        buildTypeValue:"",
        buildingName:"",
        buildType:"",
        buildTypeList:[],
        city:"",
        area:"",
        address:"",
        bCircle:"",//商圈
        buildLongitude:"116.4559021",//楼宇经度
        buildLatitude:"40.0205307",//楼宇纬度
        contactName:"",//联系人
        contactTel:"",//联系电话
        floor:"",//楼层
        checkHumanTraffic:"",//选中点位人流量
        checkScale:"",//选中男女比例
        checkRoundBusiness:"",//选中周边30米内商业
        checkIsMoreEntrance:"",//选中多个出入口
        checkEquipmentLocation:"",//选中设备摆放位置
        checkBusinessCircle:"",//选中所在商圈
        checkPopulationAge:"",//选中人群年龄层
        checkCoffeeshop:"",//咖啡厅
        checkHasOtherEquipment:"",//是否有其他设备
        //写字楼
        checkOfficeRent:"",//选中办公室租金
        checkOfficeProperty:"",//写字楼属性
        checkHallArea:"",//大堂面积
        checkLobbyHigh:"",//大堂挑高
        checkFacadeMaterial:"",//外立面材料
        checkGroundFloor:"",//大堂地面
        checkAirConditioner:"",//空调
        checkElevatorsNumber:"",//电梯数量
        checkCompanySize:"",//公司规模
        checkCompanyNature:"",//公司性质
        checkYesOrNotOverThree:"",//是否有公司超过三层
        //园区
        checkParkOfficeRent:"",//选中办公室租金
        checkParkOfficeProperty:"",//写字楼属性
        checkParkHallArea:"",//大堂面积
        checkParkLobbyHigh:"",//大堂挑高
        checkParkFacadeMaterial:"",//外立面材料
        checkParkGroundFloor:"",//大堂地面
        checkParkAirConditioner:"",//空调
        checkParkElevatorsNumber:"",//电梯数量
        checkParkCompanySize:"",//公司规模
        checkParkCompanyNature:"",//公司性质
        checkParkYesOrNotOverThree:"",//是否有公司超过三层
        // 学校
        checkSchoolTyle:"",//学校类型
        checkNumberOfSchool:"",//学校人数
        checkSchoolPutinNumber:"",//学校投放台数
        checkChargingStandard:"",//学费收费标准
        checkSexRatio:"",//男女比例
        checkLivingExpenses:"",//每个月生活费
        checkIntramuralCommerce:"",//校内商业
        checkLessThanThreeThousand:"",//在校人数是否少于三千
        checkBuildingAttribute:"",//楼宇属性
        checkBuildingArea:"",//楼宇占地面积
        checkBuildingHeight:"",//楼宇层高
        //公司
        checkCompanyOfficeProperty:"",//写字楼属性
        checkCompanyHallArea:"",//大堂面积
        checkCompanyLobbyHigh:"",//大堂挑高
        checkCompanyFacadeMaterial:"",//外立面材料
        checkCompanyGroundFloor:"",//大堂地面
        checkCompanyAirConditioner:"",//空调
        checkCompanyElevatorsNumber:"",//电梯数量
        checkCompanySameLayer:"",//公司是否在同层
        checkCompanyNature:"",//公司性质
        checkCompanyOfficeRent:"",//办公室租金
        checkCompanyOverTime:"",//员工是否经常加班
        checkCompanyOnDutyMajority:"",//坐班人员是否占大多数
        checkCompanyServingCoffee:"",//是否提供咖啡
        checkCompanyServingAfternoonTea:"",//是否提供下午茶
        checkCompanySelfServiceEquipment:"",//是否有其他自助设备
        numberOfCompanies:"",//公司人数
        //医院
        checkHospitalType:"",//医院类型
        checkHospitalBusiness:"",//院内商业
        checkHospitalBuildingAttribute:"",//楼宇属性
        checkHospitalBuildingHeight:"",//楼宇层高
        medicalNum:"",//医护人员数量
        receptionNum:"",//门诊接待量
        showInfo:"2",
        //其他
        checkFloorArea:"",//平层面积
        checkClothing:"",//人群穿着
        checkCoverPopulation:"",//覆盖人数
        checkFloorHeight:"",//楼层高度
        checkFiftyCoffee:""//50米内咖啡情况
      },
      creatorID:"",//创建人
      orgID:"",//所在公司
      submitFormUrl:rootCoffeeUrl+'building-record-api/save-building-record.html',
      rules:{
        buildType: [
          { required: true, message: '请选择渠道', trigger: 'change' }
        ],
        inputSearchVal: [
          { required: true, message: '请输入楼宇建筑名称', trigger: 'click' }
        ],
        city: [
          { required: true, message: '请输入城市', trigger: 'blur' }
        ],
        area: [
          { required: true, message: '请输入行政区', trigger: 'blur' }
        ],
        address: [
          { required: true, message: '请输入地址', trigger: 'blur' }
        ],
        bCircle: [
          { required: true, message: '请输入商圈', trigger: 'blur' }
        ],
        floor: [
          { required: true, message: '请输入楼层', trigger: 'blur' },
          {validator: checkNumber},
          { max: 5, message: '长度5个字符以内', trigger: 'blur' }
        ],
        checkHumanTraffic: [
          { required: true, message: '请选择点位人流量', trigger: 'change' }
        ],
        checkIsMoreEntrance: [
          { required: true, message: '请选择是否多个出入口分流', trigger: 'change' }
        ],
        checkPopulationAge: [
          { required: true, message: '请选择人群年龄层', trigger: 'change' }
        ],
        checkScale: [
          { required: true, message: '请选择男女比例', trigger: 'change' }
        ],
        checkEquipmentLocation: [
          { required: true, message: '请选择设备摆放位置', trigger: 'change' }
        ],
        checkCoffeeshop: [
          { required: true, message: '请选择便利店（现磨）/咖啡厅', trigger: 'change' }
        ],
        checkRoundBusiness: [
          { required: true, message: '请选择周边30米内商业', trigger: 'change' }
        ],
        checkBusinessCircle: [
          { required: true, message: '请选择所在商圈', trigger: 'change' }
        ],
        checkHasOtherEquipment: [
          { required: true, message: '请选择是否有其他自助设备', trigger: 'change' }
        ],
        //写字楼验证
        checkOfficeRent: [
          { required: true, message: '请选择办公室租金', trigger: 'change' }
        ],
        checkParkOfficeRent: [
          { required: true, message: '请选择办公室租金', trigger: 'change' }
        ],
        checkOfficeProperty: [
          { required: true, message: '请选择写字楼属性', trigger: 'change' }
        ],
        checkHallArea: [
          { required: true, message: '请选择大堂面积', trigger: 'change' }
        ],
        checkLobbyHigh: [
          { required: true, message: '请选择大堂挑高', trigger: 'change' }
        ],
        checkFacadeMaterial: [
          { required: true, message: '请选择外立面材料', trigger: 'change' }
        ],
        checkGroundFloor: [
          { required: true, message: '请选择大堂地面', trigger: 'change' }
        ],
        checkAirConditioner: [
          { required: true, message: '请选择空调', trigger: 'change' }
        ],
        checkElevatorsNumber: [
          { required: true, message: '请选择电梯数量', trigger: 'change' }
        ],
        checkCompanySize: [
          { required: true, message: '请选择公司规模', trigger: 'change' }
        ],
        checkCompanyNature: [
          { required: true, message: '请选择公司性质', trigger: 'change' }
        ],
        checkYesOrNotOverThree: [
          { required: true, message: '请选择是否有公司超过三层', trigger: 'change' }
        ],
        //园区验证
        checkParkOfficeRent: [
          { required: true, message: '请选择办公室租金', trigger: 'change' }
        ],
        checkParkOfficeRent: [
          { required: true, message: '请选择办公室租金', trigger: 'change' }
        ],
        checkParkOfficeProperty: [
          { required: true, message: '请选择写字楼属性', trigger: 'change' }
        ],
        checkParkHallArea: [
          { required: true, message: '请选择大堂面积', trigger: 'change' }
        ],
        checkParkLobbyHigh: [
          { required: true, message: '请选择大堂挑高', trigger: 'change' }
        ],
        checkParkFacadeMaterial: [
          { required: true, message: '请选择外立面材料', trigger: 'change' }
        ],
        checkParkGroundFloor: [
          { required: true, message: '请选择大堂地面', trigger: 'change' }
        ],
        checkParkAirConditioner: [
          { required: true, message: '请选择空调', trigger: 'change' }
        ],
        checkParkElevatorsNumber: [
          { required: true, message: '请选择电梯数量', trigger: 'change' }
        ],
        checkParkCompanySize: [
          { required: true, message: '请选择公司规模', trigger: 'change' }
        ],
        checkParkCompanyNature: [
          { required: true, message: '请选择公司性质', trigger: 'change' }
        ],
        checkParkYesOrNotOverThree: [
          { required: true, message: '请选择是否有公司超过三层', trigger: 'change' }
        ],
        //学校验证
        checkSchoolTyle: [
          { required: true, message: '请选择学校类型', trigger: 'change' }
        ],
        checkNumberOfSchool: [
          { required: true, message: '请选择学校人数', trigger: 'change' }
        ],
        checkSchoolPutinNumber: [
          { required: true, message: '请选择学校投放台数', trigger: 'change' }
        ],
        checkChargingStandard: [
          { required: true, message: '请选择学费收费标准', trigger: 'change' }
        ],
        checkSexRatio: [
          { required: true, message: '请选择男女比例', trigger: 'change' }
        ],
        checkLivingExpenses: [
          { required: true, message: '请选择学校人数', trigger: 'change' }
        ],
        checkSchoolPutinNumber: [
          { required: true, message: '请选择学生每月生活费', trigger: 'change' }
        ],
        checkIntramuralCommerce: [
          { required: true, message: '请选择校内商业', trigger: 'change' }
        ],
        checkLessThanThreeThousand: [
          { required: true, message: '请选择在校人数是否少于三千', trigger: 'change' }
        ],
        checkBuildingAttribute: [
          { required: true, message: '请选择楼宇属性', trigger: 'change' }
        ],
        checkBuildingArea: [
          { required: true, message: '请选择楼宇占地面积', trigger: 'change' }
        ],
        checkBuildingHeight: [
          { required: true, message: '请选择楼宇层高', trigger: 'change' }
        ],
        //公司验证
        checkCompanyOfficeProperty:[
          { required: true, message: '请选择写字楼属性', trigger: 'change' }
        ],
        checkCompanyHallArea:[
          { required: true, message: '请选择大堂面积', trigger: 'change' }
        ],
        checkCompanyLobbyHigh:[
          { required: true, message: '请选择大堂挑高', trigger: 'change' }
        ],
        checkCompanyFacadeMaterial:[
          { required: true, message: '请选择外立面材料', trigger: 'change' }
        ],
        checkCompanyGroundFloor:[
          { required: true, message: '请选择大堂地面', trigger: 'change' }
        ],
        checkCompanyAirConditioner:[
          { required: true, message: '请选择空调', trigger: 'change' }
        ],
        checkCompanyElevatorsNumber:[
          { required: true, message: '请选择电梯数量', trigger: 'change' }
        ],
        checkCompanySameLayer:[
          { required: true, message: '请选择公司是否在同层', trigger: 'change' }
        ],
        checkCompanyNature:[
          { required: true, message: '请选择公司性质', trigger: 'change' }
        ],
        checkCompanyOfficeRent:[
          { required: true, message: '请选择办公室租金', trigger: 'change' }
        ],
        checkCompanyOverTime:[
          { required: true, message: '请选择员工是否经常加班', trigger: 'change' }
        ],
        checkCompanyOnDutyMajority:[
          { required: true, message: '请选择坐班人员是否占大多数', trigger: 'change' }
        ],
        checkCompanyServingCoffee:[
          { required: true, message: '请选择是否提供咖啡', trigger: 'change' }
        ],
        checkCompanyServingAfternoonTea:[
          { required: true, message: '请选择是否提供下午茶', trigger: 'change' }
        ],
        checkCompanySelfServiceEquipment:[
          { required: true, message: '请选择是否有其他自助设备', trigger: 'change' }
        ],
        numberOfCompanies:[
          {required: true, message: '请输入公司人数', trigger: 'blur' },
          {validator: checkNumber},
          { max: 10, message: '长度10个字符以内', trigger: 'blur' }
        ],
        //医院验证
        checkHospitalType:[
          { required: true, message: '请选择医院类型', trigger: 'change' }
        ],
        checkHospitalBusiness:[
          { required: true, message: '请选择院内商业', trigger: 'change' }
        ],
        checkHospitalBuildingAttribute:[
          { required: true, message: '请选择楼宇属性', trigger: 'change' }
        ],
        checkHospitalBuildingHeight:[
          { required: true, message: '请选择楼宇层高', trigger: 'change' }
        ],
        medicalNum:[
           {required: true, message: '请输入医护人员数量', trigger: 'blur' },
           {validator: checkNumber},
           { max: 10, message: '长度10个字符以内', trigger: 'blur' }
        ],
        receptionNum:[
           {required: true, message: '请输入门诊接待量', trigger: 'blur' },
           {validator: checkNumber},
           { max: 10, message: '长度10个字符以内', trigger: 'blur' }
        ],
        contactTel:[
          {validator: checkPhone}
        ],
        checkFloorArea: [
          { required: true, message: '请输入平层面积', trigger: 'blur' },
          { validator: checkNumber },
          { max: 10, message: '长度10个字符以内', trigger: 'blur' }
        ],
        checkClothing: [
          { required: true, message: '请输入人群穿着', trigger: 'blur' },
          { min: 3, max: 20, message: '长度在 3 到 20 个字符', trigger: 'blur' }
        ],
        checkCoverPopulation: [
          { required: true, message: '请输入覆盖人数', trigger: 'blur' },
          { max: 10, message: '长度10个字符以内', trigger: 'blur' },
          { validator: checkNumber }
        ],
        checkFloorHeight: [
          { required: true, message: '请输入楼层高度', trigger: 'blur' },
          { max: 10, message: '长度10个字符以内', trigger: 'blur' },
          { validator: checkNumber }
        ],
        checkFiftyCoffee: [
          { required: true, message: '请输入50米内咖啡情况', trigger: 'blur' },
          { min: 3, max: 20, message: '长度在 3 到 20 个字符', trigger: 'blur' }
        ]
      }
    }
  },
  mounted(){
    this.checkUser();
  },
  watch:{
    inputSearchText:{
      handler:function(val){
        console.log(99);
        let data = {
          key: "RB5BZ-JSERU-SNBVG-4WPUM-5WWK5-O6FXZ",
          region: "全国",
          region_fix: 1,
          keyword: this.inputSearchText,
        };
        let url="https://apis.map.qq.com/ws/place/v1/suggestion";
        data.output="jsonp";
        $.ajax({
            type:"get",
            dataType:'jsonp',
            data:data,
            jsonp:"callback",
            jsonpCallback:"QQmap",
            url:url,
            success: searchData=> {
              console.log('mapdata',searchData);
              // console.log("searchData..",searchData);
              this.searchAddressList = searchData.data
              if(this.searchAddressList && this.searchAddressList.length>0){
                this.showList = true;
              }
              if(this.inputSearchText == ''){
                this.showList = false;
              }
            },
            error : err=> {
              this.showList = false;
              this.searchAddressList = [];
            }
        });
      },
      deep:true
    }
  },
  methods: {
    init(){
      window.parent.onscroll = (e)=>{
        this.scrollMsg();
      }
      this.id=this.$route.query.id;
      //根据ID判断是新建页还是编辑页
      if(this.id!=""){
        axios.get('/building-record/update?id='+this.id).then((res)=>{
          const initData =res.data;
          console.log("initData",initData);
          if(initData.error_code== 0){
              this.render(initData.data);
              this.form.showInfo=1;
          }else{
            this.alertMsg(initData.msg);
            return false;
          }
        }).catch((error)=>{
             console.log("error..",error);
        });
      }else{
        this.setDefaultType();
        this.form.showInfo=1;
      }
    },
    //进入页面权限检测
    checkUser(){
      axios.get('/building-record/create').then((res)=>{
        console.log("res",res);
        const initData =res.data;
        console.log("init",initData);
        if(initData.error_code== 0){
          this.form.buildTypeList=initData.data.buildTypeList;//渠道类型
          this.creatorID=initData.data.user_id;//创建人
          this.orgID=initData.data.org_id;//所在公司
          this.init();
        }else{
          this.alertMsg(initData.msg);
          return false;
        }
      }).catch((error)=>{
           console.log("error..",error);
      });
    },
    searchMap() {
      console.log("search")
      this.mapShow = true;
      this.inputSearchText = this.inputSearchVal;
    },
    closeMap() {
      this.mapShow = false;
    },
    //根据关键字索的结果点击事件
    chooseOneAddress(index) {
      this.showList = false;
      this.mapShow = false;
      console.log("showList",this.showList);
      this.inputSearchVal=this.searchAddressList[index].title;
      this.form.city=this.searchAddressList[index].city;
      this.form.area=this.searchAddressList[index].district;
      this.form.address=this.searchAddressList[index].address;
      this.form.buildLongitude=this.searchAddressList[index].location.lng;//楼宇经度
      this.form.buildLatitude=this.searchAddressList[index].location.lat;//楼宇纬度
      console.log("form", this.form.buildLongitude,this.form.buildLatitude);
      //选择完地址展示地图
      this.showMapPic(this.form.buildLatitude,this.form.buildLongitude);
    },
    setDefaultType(){
      for(let i in this.form.buildTypeList){
        if(this.form.buildTypeList[i]=="写字楼"){
          this.form.buildType=i;
          this.form.buildTypeValue="写字楼";
        }
      }
    },
    changeBuildType(key){
      for(let i in this.form.buildTypeList){
        if(i==key){
          this.form.buildTypeValue=this.form.buildTypeList[i];
          console.log("buildTypeValue",this.form.buildTypeValue)
        }
      }
    },
    //显示地图
    showMapPic(lat,lng){
      console.log(lat,lng);
      this.showMap=true;
       let map = new qq.maps.Map(document.getElementById('allmap'),{
        center: new qq.maps.LatLng(lat,lng),
        zoom: 15,
        disableDefaultUI: true
      });
      let marker = new qq.maps.Marker({
        map:map,
        draggable: false,
        position: new qq.maps.LatLng(lat,lng)
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
    render(buildingRecordList){
      //渲染数据
      for (const key in buildingRecordList.buildPublicInfo) {
        if (buildingRecordList.buildPublicInfo[key] == '请选择') {
          buildingRecordList.buildPublicInfo[key] = '';
        }
      }
      for (const key in buildingRecordList.buildSpecialInfo) {
        if (buildingRecordList.buildSpecialInfo[key] == '请选择') {
          buildingRecordList.buildSpecialInfo[key] = '';
        }
      }
      this.orgID = buildingRecordList.orgID;
      // console.log("record",buildingRecordList);
      this.id=buildingRecordList.id;
      this.inputSearchVal=buildingRecordList.buildingName;;
      // this.inputSearchText=buildingRecordList.buildingName;
      this.form.buildType=buildingRecordList.buildTypeID;
      this.form.city=buildingRecordList.city;
      this.form.area=buildingRecordList.area;
      this.form.address=buildingRecordList.address;
      this.form.buildLongitude=buildingRecordList.buildLongitude;
      this.form.buildLatitude=buildingRecordList.buildLatitude;
      window.setTimeout(()=>{
        this.showMapPic(this.form.buildLatitude,this.form.buildLongitude);
      },1000);
      this.form.contactName=buildingRecordList.contactName;
      this.form.contactTel=buildingRecordList.contactTel;
      let publicInfo=buildingRecordList.buildPublicInfo;
      this.form.floor=publicInfo.floor;
      this.form.bCircle=publicInfo.bCircle;
      this.form.checkHumanTraffic=publicInfo.humanTraffic;
      this.form.checkIsMoreEntrance=publicInfo.isMoreEntrance;
      this.form.checkPopulationAge=publicInfo.populationAge;
      this.form.checkScale=publicInfo.scale;
      this.form.checkEquipmentLocation=publicInfo.equipmentLocation;
      this.form.checkCoffeeshop=publicInfo.coffeeshop;
      this.form.checkRoundBusiness=publicInfo.roundBusiness;
      this.form.checkBusinessCircle=publicInfo.businessCircle;
      this.form.checkHasOtherEquipment=publicInfo.hasOtherEquipment;
      this.buildAppearPic = buildingRecordList.buildAppearPic;
      this.buildHallPic = buildingRecordList.buildHallPic;
      // 特殊信息
      let specialInfo=buildingRecordList.buildSpecialInfo;
      const typeName=this.form.buildTypeList[this.form.buildType];
      if(typeName=="写字楼"){
        this.form.buildTypeValue="写字楼";
        this.form.checkOfficeRent=specialInfo.officeRent;
        this.form.checkOfficeProperty=specialInfo.officeProperty;
        this.form.checkHallArea=specialInfo.hallArea;
        this.form.checkLobbyHigh=specialInfo.lobbyHigh;
        this.form.checkFacadeMaterial=specialInfo.facadeMaterial;
        this.form.checkGroundFloor=specialInfo.groundFloor;
        this.form.checkAirConditioner=specialInfo.airConditioner;
        this.form.checkElevatorsNumber=specialInfo.elevatorsNumber;
        this.form.checkCompanySize=specialInfo.companySize;
        this.form.checkCompanyNature=specialInfo.companyNature;
        this.form.checkYesOrNotOverThree=specialInfo.yesOrNotOverThree;
      }else if(typeName=="园区"){
        this.form.buildTypeValue="园区";
        this.form.checkParkOfficeRent=specialInfo.officeRent;
        this.form.checkParkOfficeProperty=specialInfo.officeProperty;
        this.form.checkParkHallArea=specialInfo.hallArea;
        this.form.checkParkLobbyHigh=specialInfo.lobbyHigh;
        this.form.checkParkFacadeMaterial=specialInfo.facadeMaterial;
        this.form.checkParkGroundFloor=specialInfo.groundFloor;
        this.form.checkParkAirConditioner=specialInfo.airConditioner;
        this.form.checkParkElevatorsNumber=specialInfo.elevatorsNumber;
        this.form.checkParkCompanySize=specialInfo.companySize;
        this.form.checkParkCompanyNature=specialInfo.companyNature;
        this.form.checkParkYesOrNotOverThree=specialInfo.yesOrNotOverThree;

      }else if(typeName=="学校"){
        this.form.buildTypeValue="学校";
        this.form.checkSchoolTyle=specialInfo.schoolTyle;
        this.form.checkNumberOfSchool=specialInfo.numberOfSchool;
        this.form.checkSchoolPutinNumber=specialInfo.schoolPutinNumber;
        this.form.checkChargingStandard=specialInfo.chargingStandard;
        this.form.checkSexRatio=specialInfo.sexRatio;
        this.form.checkLivingExpenses=specialInfo.livingExpenses;
        this.form.checkIntramuralCommerce=specialInfo.intramuralCommerce;
        this.form.checkLessThanThreeThousand=specialInfo.lessThanThreeThousand;
        this.form.checkBuildingAttribute=specialInfo.buildingAttribute;
        this.form.checkBuildingArea=specialInfo.buildingArea;
        this.form.checkBuildingHeight=specialInfo.buildingHeight;
      }else if(typeName=="公司"){
        this.form.buildTypeValue="公司";
        this.form.checkCompanyOfficeProperty=specialInfo.officeProperty;
        this.form.checkCompanyHallArea=specialInfo.hallArea;
        this.form.checkCompanyLobbyHigh=specialInfo.lobbyHigh;
        this.form.checkCompanyFacadeMaterial=specialInfo.facadeMaterial;
        this.form.checkCompanyGroundFloor=specialInfo.groundFloor;
        this.form.checkCompanyAirConditioner=specialInfo.airConditioner;
        this.form.checkCompanyElevatorsNumber=specialInfo.elevatorsNumber;
        this.form.checkCompanySameLayer=specialInfo.sameLayer;
        this.form.checkCompanyNature=specialInfo.companyNature;
        this.form.checkCompanyOfficeRent=specialInfo.officeRent;
        this.form.checkCompanyOverTime=specialInfo.overTime;
        this.form.checkCompanyOnDutyMajority=specialInfo.onDutyMajority;
        this.form.checkCompanyServingCoffee=specialInfo.servingCoffee;
        this.form.checkCompanyServingAfternoonTea=specialInfo.servingAfternoonTea;
        this.form.checkCompanySelfServiceEquipment=specialInfo.selfServiceEquipment;
        this.form.numberOfCompanies=specialInfo.numberOfCompanies;

      }else if(typeName=="医院"){
        this.form.buildTypeValue="医院";
        this.form.checkHospitalType=specialInfo.hospitalType;
        this.form.checkHospitalBusiness=specialInfo.hospitalBusiness;
        this.form.checkHospitalBuildingAttribute=specialInfo.buildingAttribute;
        this.form.checkHospitalBuildingHeight=specialInfo.buildingHeight;
        this.form.medicalNum=specialInfo.medicalNum;
        this.form.receptionNum=specialInfo.receptionNum;
      } else {
        this.form.checkFloorArea = specialInfo.checkFloorArea;
        this.form.checkClothing = specialInfo.checkClothing;
        this.form.checkCoverPopulation = specialInfo.checkCoverPopulation;
        this.form.checkFloorHeight = specialInfo.checkFloorHeight;
        this.form.checkFiftyCoffee = specialInfo.checkFiftyCoffee;
      }
    },
    saveForm(){//保存草稿
      console.log('name',this.inputSearchVal)
      if(this.inputSearchVal==""){
        this.alertMsg("请输入楼宇建筑名称");
        return;
      }else{
        this.submitAction("1");
      }
    },
    submitForm(formName) // 创建楼宇
    {
      this.$refs[formName].validate((valid,obj) => {
        console.log("checkHumanTraffic..",this.form.checkHumanTraffic);
        if (valid) {
          if (this.inputSearchVal==""){
            this.alertMsg("请输入楼宇建筑名称");
            return;
          } else if (this.form.city==""){
            this.alertMsg("请输入城市");
            return;
          } else if (this.form.area==""){
            this.alertMsg("请输入行政区");
            return;
          } else if (this.form.address==""){
            this.alertMsg("请输入地址");
            return;
          } else if (this.buildAppearPic.length<2||this.buildHallPic.length<2) {
            this.alertMsg("楼宇及大厅照片各需2张");
          } else {
            this.submitAction("2");
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
    submitAction(type){//type 1保存草稿、2创建
      const specialInfoBuild={//写字楼特殊信息
        officeRent:this.form.checkOfficeRent,
        officeProperty:this.form.checkOfficeProperty,
        hallArea:this.form.checkHallArea,
        lobbyHigh:this.form.checkLobbyHigh,
        facadeMaterial:this.form.checkFacadeMaterial,
        groundFloor:this.form.checkGroundFloor,
        airConditioner:this.form.checkAirConditioner,
        elevatorsNumber:this.form.checkElevatorsNumber,
        companySize:this.form.checkCompanySize,
        companyNature:this.form.checkCompanyNature,
        yesOrNotOverThree:this.form.checkYesOrNotOverThree
      }
      const specialInfoPark={//园区
        officeRent:this.form.checkParkOfficeRent,
        officeProperty:this.form.checkParkOfficeProperty,
        hallArea:this.form.checkParkHallArea,
        lobbyHigh:this.form.checkParkLobbyHigh,
        facadeMaterial:this.form.checkParkFacadeMaterial,
        groundFloor:this.form.checkParkGroundFloor,
        airConditioner:this.form.checkParkAirConditioner,
        elevatorsNumber:this.form.checkParkElevatorsNumber,
        companySize:this.form.checkParkCompanySize,
        companyNature:this.form.checkParkCompanyNature,
        yesOrNotOverThree:this.form.checkParkYesOrNotOverThree
      }
      const specialInfoSchool={//学校
        schoolTyle:this.form.checkSchoolTyle,
        numberOfSchool:this.form.checkNumberOfSchool,
        schoolPutinNumber:this.form.checkSchoolPutinNumber,
        chargingStandard:this.form.checkChargingStandard,
        sexRatio:this.form.checkSexRatio,
        livingExpenses:this.form.checkLivingExpenses,
        intramuralCommerce:this.form.checkIntramuralCommerce,
        lessThanThreeThousand:this.form.checkLessThanThreeThousand,
        buildingAttribute:this.form.checkBuildingAttribute,
        buildingArea:this.form.checkBuildingArea,
        buildingHeight:this.form.checkBuildingHeight
      }
      const specialInfoCompany={//公司
        officeProperty:this.form.checkCompanyOfficeProperty,
        hallArea: this.form.checkCompanyHallArea,
        lobbyHigh:this.form.checkCompanyLobbyHigh,
        facadeMaterial:this.form.checkCompanyFacadeMaterial,
        groundFloor:this.form.checkCompanyGroundFloor,
        airConditioner:this.form.checkCompanyAirConditioner,
        elevatorsNumber:this.form.checkCompanyElevatorsNumber,
        sameLayer:this.form.checkCompanySameLayer,
        companyNature:this.form.checkCompanyNature,
        officeRent:this.form.checkCompanyOfficeRent,
        overTime: this.form.checkCompanyOverTime,
        onDutyMajority:this.form.checkCompanyOnDutyMajority,
        servingCoffee:this.form.checkCompanyServingCoffee,
        servingAfternoonTea:this.form.checkCompanyServingAfternoonTea,
        selfServiceEquipment:this.form.checkCompanySelfServiceEquipment,
        numberOfCompanies:this.form.numberOfCompanies
      }
      const specialInfoHospital={//医院
        hospitalType:this.form.checkHospitalType,
        hospitalBusiness:this.form.checkHospitalBusiness,
        buildingAttribute:this.form.checkHospitalBuildingAttribute,
        buildingHeight:this.form.checkHospitalBuildingHeight,
        medicalNum:this.form.medicalNum,
        receptionNum:this.form.receptionNum
      }
      const specialInfoOthers={//其他
        checkFloorArea:this.form.checkFloorArea,
        checkClothing:this.form.checkClothing,
        checkCoverPopulation:this.form.checkCoverPopulation,
        checkFloorHeight:this.form.checkFloorHeight,
        checkFiftyCoffee:this.form.checkFiftyCoffee
      }

      let publicInfo={//公共信息json
        floor:this.form.floor,
        bCircle:this.form.bCircle,
        humanTraffic:this.form.checkHumanTraffic,
        scale:this.form.checkScale,
        roundBusiness:this.form.checkRoundBusiness,
        isMoreEntrance:this.form.checkIsMoreEntrance,
        equipmentLocation:this.form.checkEquipmentLocation,
        businessCircle:this.form.checkBusinessCircle,
        populationAge:this.form.checkPopulationAge,
        coffeeshop:this.form.checkCoffeeshop,
        hasOtherEquipment:this.form.checkHasOtherEquipment
      }
      let params={
        id:this.id,
        creatorID:this.creatorID,
        orgID:this.orgID,
        buildingName:this.inputSearchVal,
        buildingStatus:"2",//是创建还是保存草稿
        buildTypeID:this.form.buildType,//渠道类型
        city:this.form.city,
        area:this.form.area,
        address:this.form.address,
        buildLongitude:this.form.buildLongitude,
        buildLatitude:this.form.buildLatitude,
        contactName:this.form.contactName,
        contactTel:this.form.contactTel,
        buildPublicInfo:publicInfo,
        buildSpecialInfo:specialInfoBuild,//特殊信息json
        buildAppearPic:'',//pc端不传
        buildHallPic:''//pc端不传
      }
      if(type=="1"){
        params.buildingStatus="1";//保存草稿
      }
      switch(this.form.buildTypeValue){
          case "写字楼":
            params.buildSpecialInfo=specialInfoBuild;
            break;
          case "园区":
            params.buildSpecialInfo=specialInfoPark;
            break;
          case "学校":
            params.buildSpecialInfo=specialInfoSchool;
            break;
          case "公司":
            params.buildSpecialInfo=specialInfoCompany;
            break;
          case "医院":
            params.buildSpecialInfo=specialInfoHospital;
            break;
          default:
            params.buildSpecialInfo=specialInfoOthers;
            break;
      }
      console.log("params",params);
      axios.post(this.submitFormUrl,params)
      .then((response)=> {
        let data = response.data;
        console.log("data",data);
        if(data.error_code==0){
           if(type=="1"){
              this.alertMsg('保存草稿成功','success');
           }else{
              this.alertMsg('创建成功','success');
           }
           this.$router.push({name:"buildDetail",query:{id:data.data.id}});
        }else{
           this.alertMsg('操作失败','error');
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
.map-container {
  position: fixed;
  top: 0;
  background-color: rgba(0,0,0,0.5);
  width: 100%;
  height: 100%;
  padding: 30px 30px 0 20px;
  box-sizing: border-box;
}
.map-bg {
  position: relative;
  height: 450px;
  background-color: #fff;
  padding: 50px 20px 50px 20px;
}
.close-map {
  cursor: pointer;
  position: absolute;
  top: 15px;
  right: 25px;
}
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
li {list-style-type:none;}
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
.build-name{
  position: relative;
}
.search-address-list{
  height: 300px;
  width: 50%;
  background-color: #fff;
  position: absolute;
  top: 50px;
  left:150px;
  z-index:10;
  overflow: hidden;
  overflow-y: scroll;
  border:1px solid #dcdfe6;
  padding:10px;
  cursor:pointer;
 }
 .address1{
  color:#333;
  font-size:14px;
 }
 .address2{
  color:#999;
  font-size:12px;
 }
 #allmap{
  width:80%;
  height: 300px;
  margin:0 0 20px 150px;
 }
 .city,.area,.address,.building-name{
  position: relative;
 }
 .city:before,.area:before,.address:before{
  position: absolute;
  left:90px;
  top:11px;
  display:inline-block;
  content: "*";
  color:red;
 }
 .building-name:before{
  left:22px;
 }
 .area:before{
  left:75px;
 }
 .search-result:before{
  left:32px;
 }


</style>
