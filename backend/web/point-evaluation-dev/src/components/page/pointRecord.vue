<template>
  <div class="content-body">
    <!-- <input v-model="test"> -->
    <!-- <span>测试用：{{pointScore}} {{pointLevel}}</span> -->
    <el-form ref="form" :model="form" :rules="rules" label-width="150px">
      <div class="line-title">基础</div>
      <el-row :gutter="10">
        <el-col :span="12" :offset="0">
         <el-form-item label="渠道：" prop="buildType" id="buildType">
            <el-select placeholder="请选择" v-model="form.buildType" @change="changeBuildType">
               <el-option :label="item.type_name" :value="item.id" v-for="(item,key) in form.buildTypeList" :key="key"></el-option>
            </el-select>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="12" :offset="0">
         <el-form-item label="楼宇名称：" prop="buildRecordId">
            <el-select placeholder="请选择" v-model="form.buildRecordId" @change="getInfoByBuildingName">
               <el-option :label="item.buildNameStatus" :value="item.id" v-for="(item,key) in form.buildingNameList" :key="key"></el-option>
            </el-select>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="12" :offset="0" class="address">
          <el-form-item label="摆放位置：" prop="position" id="position">
            <el-input v-model.trim="form.position"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="12" :offset="0" class="address">
          <el-form-item label="点位地址：" prop="address" id="address">
            <el-input v-model.trim="form.address" readonly></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="12" :offset="0">
          <el-form-item label="商圈："  prop="bCircle" id="bCircle">
            <el-input v-model.trim="form.bCircle" readonly></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="12" :offset="0">
          <el-form-item  prop="contactName" label="联系人：" >
            <el-input v-model="form.contactName"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="12" :offset="0">
          <el-form-item label="电话：" prop="contactTel" id="contactTel">
            <el-input v-model="form.contactTel"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="12" :offset="0">
         <el-form-item label="合作方式：" prop="cooperation" id="cooperation">
            <el-select placeholder="请选择" v-model="form.cooperation">
               <el-option  value="直签" label="直签"></el-option>
               <el-option  value="第三方" label="第三方"></el-option>
            </el-select>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="12" :offset="0">
         <el-form-item label="租金方式：">
            <div class="charter-box">
                <el-radio v-model="form.rentWay" label="0" @change="changeRent">租金</el-radio><span v-show="form.rentWay=='0'"><el-input class="charter-money" v-model.trim="form.rentNum"></el-input>元/台/年</span>
            </div>
            <div class="charter-box">
              <el-radio v-model="form.rentWay" label="1" @change="changeRent">分成</el-radio><span v-show="form.rentWay=='1'"><el-input class="charter-money" v-model.trim="form.rentNum"></el-input>%/台/年</span>
            </div>
          </el-form-item>
        </el-col>
      </el-row>
      <el-row :gutter="10">
        <el-col :span="8" :offset="0">
          <el-form-item label="电费(元/度)：" prop="electric" id="electric">
            <el-input v-model.trim="form.electric"></el-input>
          </el-form-item>
        </el-col>
        <el-col :span="8" :offset="0">
          <el-form-item label="劳务费(元/年)：" prop="service" id="service">
            <el-input v-model.trim="form.service"></el-input>
          </el-form-item>
        </el-col>
        <el-col :span="8" :offset="0">
          <el-form-item label="费用总额(元/年)：" prop="total" id="total">
            <el-input v-model.trim="form.total"></el-input>
          </el-form-item>
        </el-col>
      </el-row>
      <!-- 以下基础信息会根据渠道不同,展示不同信息 -->
      <div>
        <!-- 学校 -->
        <div v-if="form.buildTypeValue=='学校'">
          <el-row :gutter="10">
            <el-col :span="8" :offset="0">
              <el-form-item label="投放楼宇占地面积：" prop="area" id="area">
                <el-input v-model.trim="form.area"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
              <el-form-item label="楼宇层高：" prop="floor" id="floor">
                <el-input v-model.trim="form.floor"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
              <el-form-item label="点位人流量：" prop="humanTraffic" id="humanTraffic">
                <el-input v-model.trim="form.humanTraffic"></el-input>
              </el-form-item>
            </el-col>
          </el-row>
        </div>
        <!-- 公司 -->
        <div v-else-if="form.buildTypeValue=='公司'">
          <el-row :gutter="10">
            <el-col :span="8" :offset="0">
              <el-form-item label="公司类型：" prop="companyType" id="companyType">
                <el-select placeholder="请选择" v-model="form.companyType">
                   <el-option  value="IT" label="IT"></el-option>
                   <el-option  value="互联网" label="互联网"></el-option>
                   <el-option  value="游戏" label="游戏"></el-option>
                   <el-option  value="投资金融类" label="投资金融类"></el-option>
                   <el-option  value="文化传媒" label="文化传媒"></el-option>
                   <el-option  value="事务所" label="事务所"></el-option>
                   <el-option  value="商业服务" label="商业服务"></el-option>
                   <el-option  value="其他" label="其他"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <!-- <el-col :span="8" :offset="0">
              <el-form-item label="公司人数：" prop="companyNum" id="companyNum">
                <el-input v-model.trim="form.companyNum"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
              <el-form-item label="男女比例：" prop="scale" id="scale">
                <el-input v-model.trim="form.scale"></el-input>
              </el-form-item>
            </el-col> -->
          </el-row>
        </div>
        <!-- 医院 -->
        <div v-else-if="form.buildTypeValue=='医院'">
          <el-row :gutter="10">
            <el-col :span="8" :offset="0">
              <el-form-item label="医院等级：" prop="hospitalLevel" id="hospitalLevel ">
                <el-select placeholder="请选择" v-model="form.hospitalLevel">
                   <el-option  value="一级甲等" label="一级甲等"></el-option>
                   <el-option  value="一级乙等" label="一级乙等"></el-option>
                   <el-option  value="一级丙等" label="一级丙等"></el-option>
                   <el-option  value="二级甲等" label="二级甲等"></el-option>
                   <el-option  value="二级乙等" label="二级乙等"></el-option>
                   <el-option  value="二级丙等" label="二级丙等"></el-option>
                   <el-option  value="三级甲等" label="三级甲等"></el-option>
                   <el-option  value="三级乙等" label="三级乙等"></el-option>
                   <el-option  value="三级丙等" label="三级丙等"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <!-- <el-col :span="8" :offset="0">
              <el-form-item label="门诊接待量：" prop="receptionNum" id="receptionNum ">
                <el-input v-model.trim="form.receptionNum"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
              <el-form-item label="医护人员数量：" prop="medicalNum" id="medicalNum ">
                <el-input v-model.trim="form.medicalNum"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0" >
              <el-form-item label="点位人流量：" prop="hospitalHuman" id="hospitalHuman ">
                <el-input v-model.trim="form.hospitalHuman"></el-input>
              </el-form-item>
            </el-col> -->
          </el-row>
        </div>
      </div>
      <!-- 基础信息结束 -->
      <!-- 评分 -->
      <div class="line-title">评分  {{pointScore}}</div>
      <div class="info_wrap">
        <!-- 写字楼 -->
      <div class="officeBuilding" v-if="form.buildTypeValue=='写字楼'">
        <div class="sub-title">条件1</div>
        <el-row>
            <el-col :span="8" :offset="0">
              <el-form-item label="平层面积(㎡)：" prop="floorArea" id="floorArea">
                <el-input v-model.trim="form.floorArea"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
              <el-form-item label="楼层(层)：" prop="buildFloor" id="buildFloor">
                <el-input v-model.trim="form.buildFloor"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
              <el-form-item label="入住率(%)：" prop="lodgingRatio" id="lodgingRatio">
                <el-input v-model.trim="form.lodgingRatio"></el-input>
              </el-form-item>
            </el-col>
        </el-row>
        <el-row>
            <el-col :span="8" :offset="0">
              <el-form-item label="写字楼使用率(%)：" prop="useRatio" id="useRatio">
                <el-input v-model.trim="form.useRatio"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
              <el-form-item label="楼宇人数：" prop="buildNum">
                <el-input v-model.trim="form.buildNum"></el-input>
              </el-form-item>
            </el-col>
        </el-row>
        <div class="sub-title">条件2</div>
        <el-row :gutter="10">
          <el-col :span="8" :offset="0">
           <el-form-item label="所在商圈：">
              <el-select placeholder="请选择" v-model="form.checkBusinessCircle" disabled>
               <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.businessCircle" :key="index"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="8" :offset="0">
           <el-form-item label="办公室租金 (天/平米)：">
              <el-select placeholder="请选择" v-model="form.checkOfficeRent" disabled>
               <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.officeRent" :key="index"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="8" :offset="0">
           <el-form-item label="写字楼属性：">
              <el-select placeholder="请选择" v-model="form.checkOfficeProperty" disabled>
                 <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.officeProperty" :key="index"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="10">
            <el-col :span="8" :offset="0">
             <el-form-item label="大堂面积：">
                <el-select placeholder="请选择" v-model="form.checkHallArea" disabled>
                   <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.hallArea" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="大堂挑高：">
                <el-select placeholder="请选择" v-model="form.checkLobbyHigh" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.lobbyHigh" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="外立面材料：">
                <el-select placeholder="请选择" v-model="form.checkFacadeMaterial" disabled>
                 <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.facadeMaterial" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
        </el-row>
        <el-row :gutter="10">
            <el-col :span="8" :offset="0">
             <el-form-item label="大堂地面：">
                <el-select placeholder="请选择" v-model="form.checkGroundFloor" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.groundFloor" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="空调：" prop="checkAirConditioner" id="checkAirConditioner">
                <el-select placeholder="请选择" v-model="form.checkAirConditioner" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.airConditioner" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="电梯数量：">
                <el-select placeholder="请选择" v-model="form.checkElevatorsNumber" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.elevatorsNumber" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
        </el-row>
        <div class="sub-title">条件3</div>
        <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="人群年龄层：">
                <el-select placeholder="请选择" v-model="form.checkPopulationAge" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.populationAge" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="男女比例：">
                <el-select placeholder="请选择" v-model="form.checkScale" disabled>
                <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.scale" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="公司规模：">
                <el-select placeholder="请选择" v-model="form.checkCompanySize" disabled>
                   <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.companySize" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
        </el-row>
        <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="公司性质：">
                <el-select placeholder="请选择" v-model="form.checkCompanyNature" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.companyNature" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="是否有公司超过三层：">
                <el-select placeholder="请选择" v-model="form.checkYesOrNotOverThree" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.yesOrNotOverThree" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="是否多个出入口分流：">
                <el-select placeholder="请选择" v-model="form.checkIsMoreEntrance" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.isMoreEntrance" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
        </el-row>
        <el-row :gutter="10">
            <el-col :span="8" :offset="0">
             <el-form-item label="设备摆放位置：">
                <el-select placeholder="请选择" v-model="form.checkEquipmentLocation" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.equipmentLocation" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="便利店（现磨）/咖啡厅：">
                <el-select placeholder="请选择" v-model="form.checkCoffeeshop" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.coffeeshop" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="周边30米内商业：">
                <el-select placeholder="请选择" v-model="form.checkRoundBusiness" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.roundBusiness" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
        </el-row>
      </div>
        <!-- 园区 -->
        <div class="park" v-else-if="form.buildTypeValue=='园区'">
            <div class="sub-title">条件1</div>
            <el-row>
                <el-col :span="8" :offset="0">
                  <el-form-item label="平层面积(㎡)：" prop="floorParkArea" id="floorParkArea">
                    <el-input v-model.trim="form.floorParkArea"></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="8" :offset="0">
                  <el-form-item label="楼层(层)：" prop="buildParkFloor" id="buildParkFloor">
                    <el-input v-model.trim="form.buildParkFloor"></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="8" :offset="0">
                  <el-form-item label="入住率(%)：" prop="lodgingParkRatio" id="lodgingParkRatio">
                    <el-input v-model.trim="form.lodgingParkRatio"></el-input>
                  </el-form-item>
                </el-col>
            </el-row>
            <el-row>
                <el-col :span="8" :offset="0">
                  <el-form-item label="20米内覆盖楼宇：" prop="useParkRatio" id="useParkRatio">
                    <el-input v-model.trim="form.useParkRatio"></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="8" :offset="0">
                  <el-form-item label="覆盖人数：" prop="coverParkNum" id="coverParkNum">
                    <el-input v-model.trim="form.coverParkNum"></el-input>
                  </el-form-item>
                </el-col>
            </el-row>
            <div class="sub-title">条件2</div>
            <el-row :gutter="10">
              <el-col :span="8" :offset="0">
               <el-form-item label="办公室租金 (天/平米)：">
                  <el-select placeholder="请选择" v-model="form.checkParkOfficeRent" disabled>
                   <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.park.officeRent" :key="index"></el-option>
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :span="8" :offset="0">
               <el-form-item label="写字楼属性：">
                  <el-select placeholder="请选择" v-model="form.checkParkOfficeProperty" disabled>
                     <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.park.officeProperty" :key="index"></el-option>
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :span="8" :offset="0">
               <el-form-item label="大堂面积：">
                  <el-select placeholder="请选择" v-model="form.checkParkHallArea" disabled>
                     <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.park.hallArea" :key="index"></el-option>
                  </el-select>
                </el-form-item>
              </el-col>
            </el-row>
            <el-row :gutter="10">
                <el-col :span="8" :offset="0">
                 <el-form-item label="大堂挑高：">
                    <el-select placeholder="请选择" v-model="form.checkParkLobbyHigh" disabled>
                      <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.park.lobbyHigh" :key="index"></el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
                <el-col :span="8" :offset="0">
                 <el-form-item label="外立面材料：">
                    <el-select placeholder="请选择" v-model="form.checkParkFacadeMaterial" disabled>
                     <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.park.facadeMaterial" :key="index"></el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
                <el-col :span="8" :offset="0">
                 <el-form-item label="大堂地面：">
                    <el-select placeholder="请选择" v-model="form.checkParkGroundFloor" disabled>
                      <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.park.groundFloor" :key="index"></el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
            </el-row>
            <el-row :gutter="10">
                <el-col :span="8" :offset="0">
                 <el-form-item label="空调：">
                    <el-select placeholder="请选择" v-model="form.checkParkAirConditioner" disabled>
                      <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.park.airConditioner" :key="index"></el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
                <el-col :span="8" :offset="0">
                 <el-form-item label="电梯数量：">
                    <el-select placeholder="请选择" v-model="form.checkParkElevatorsNumber" disabled>
                      <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.park.elevatorsNumber" :key="index"></el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
                <el-col :span="8" :offset="0">
                 <el-form-item label="所在商圈：">
                    <el-select placeholder="请选择" v-model="form.checkParkBusinessCircle" disabled>
                      <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.businessCircle" :key="index"></el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
            </el-row>
            <div class="sub-title">条件3</div>
            <el-row>
                <el-col :span="8" :offset="0">
                 <el-form-item label="人群年龄层：">
                    <el-select placeholder="请选择" v-model="form.checkParkPopulationAge" disabled>
                      <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.populationAge" :key="index"></el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
                <el-col :span="8" :offset="0">
                 <el-form-item label="男女比例：">
                    <el-select placeholder="请选择" v-model="form.checkParkScale" disabled>
                    <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.scale" :key="index"></el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
                <el-col :span="8" :offset="0">
                 <el-form-item label="公司规模：">
                    <el-select placeholder="请选择" v-model="form.checkParkCompanySize" disabled>
                       <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.companySize" :key="index"></el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
            </el-row>
            <el-row>
                <el-col :span="8" :offset="0">
                 <el-form-item label="公司性质：">
                    <el-select placeholder="请选择" v-model="form.checkParkCompanyNature" disabled>
                      <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.companyNature" :key="index"></el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
                <el-col :span="8" :offset="0">
                 <el-form-item label="是否有公司超过三层：">
                    <el-select placeholder="请选择" v-model="form.checkParkYesOrNotOverThree" disabled>
                      <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.officeBuilding.yesOrNotOverThree" :key="index"></el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
                <el-col :span="8" :offset="0">
                 <el-form-item label="是否多个出入口分流：">
                    <el-select placeholder="请选择" v-model="form.checkParkIsMoreEntrance" disabled>
                      <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.isMoreEntrance" :key="index"></el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
            </el-row>
            <el-row :gutter="10">
                <el-col :span="8" :offset="0">
                 <el-form-item label="设备摆放位置：">
                    <el-select placeholder="请选择" v-model="form.checkParkEquipmentLocation" disabled>
                      <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.equipmentLocation" :key="index"></el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
                <el-col :span="8" :offset="0">
                 <el-form-item label="便利店（现磨）/咖啡厅：">
                    <el-select placeholder="请选择" v-model="form.checkParkCoffeeshop" disabled>
                      <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.coffeeshop" :key="index"></el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
                <el-col :span="8" :offset="0">
                 <el-form-item label="周边30米内商业：">
                    <el-select placeholder="请选择" v-model="form.checkParkRoundBusiness" disabled>
                      <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.roundBusiness" :key="index"></el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
            </el-row>
        </div>
        <!-- 学校 -->
        <div v-else-if="form.buildTypeValue=='学校'">
          <div class="sub-title">学校情况</div>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="学校类型："  prop="checkSchoolTyle" id="checkSchoolTyle">
                <el-select placeholder="请选择" v-model="form.checkSchoolTyle" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.schoolTyle" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="学校人数："  prop="checkNumberOfSchool" id="checkNumberOfSchool">
                <el-select placeholder="请选择" v-model="form.checkNumberOfSchool" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.numberOfSchool" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="学校投放台数："  prop="checkSchoolPutinNumber" id="checkSchoolPutinNumber">
                <el-select placeholder="请选择" v-model="form.checkSchoolPutinNumber" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.schoolPutinNumber" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="学费收费标准:"  prop="checkChargingStandard" id="checkChargingStandard">
                <el-select placeholder="请选择" v-model="form.checkChargingStandard" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.chargingStandard" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="男女比例："  prop="checkSexRatio" id="checkSexRatio">
                <el-select placeholder="请选择" v-model="form.checkSexRatio" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.sexRatio" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="学生每月生活费:"  prop="checkLivingExpenses" id="checkLivingExpenses">
                <el-select placeholder="请选择" v-model="form.checkLivingExpenses" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.livingExpenses" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="校内商业："  prop="checkIntramuralCommerce" id="checkIntramuralCommerce">
                <el-select placeholder="请选择" v-model="form.checkIntramuralCommerce" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.intramuralCommerce" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="在校人数是否少于三千："  prop="checkLessThanThreeThousand" id="checkLessThanThreeThousand">
                <el-select placeholder="请选择" v-model="form.checkLessThanThreeThousand" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.lessThanThreeThousand" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <div class="sub-title">所在楼宇情况</div>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="楼宇属性:"  prop="checkBuildingAttribute" id="checkBuildingAttribute">
                <el-select placeholder="请选择" v-model="form.checkBuildingAttribute" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.buildingAttribute" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="楼宇占地面积："  prop="checkBuildingArea" id="checkBuildingArea">
                <el-select placeholder="请选择" v-model="form.checkBuildingArea" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.buildingArea" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="楼宇层高："  prop="checkBuildingHeight" id="checkBuildingHeight">
                <el-select placeholder="请选择" v-model="form.checkBuildingHeight" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.school.buildingHeight" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="点位人流量：">
                <el-select placeholder="请选择" v-model="form.checkSchoolHumanTraffic" disabled>
                  <el-option :label="item" :value="item" v-for="(item,index) in baseInfo.commonInfo.humanTraffic" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="是否有其他自助设备：">
                <el-select placeholder="请选择" v-model="form.checkSchoolHasOtherEquipment" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.hasOtherEquipment" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="设备摆放位置：">
                <el-select placeholder="请选择" v-model="form.checkSchoolEquipmentLocation" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.equipmentLocation" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="便利店（现磨）/咖啡厅：">
                <el-select placeholder="请选择" v-model="form.checkSchoolCoffeeshop" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.coffeeshop" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
               <el-form-item label="是否多个出入口分流：">
                  <el-select placeholder="请选择" v-model="form.checkSchoolIsMoreEntrance" disabled>
                    <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.isMoreEntrance" :key="index"></el-option>
                  </el-select>
                </el-form-item>
            </el-col>
          </el-row>
        </div>
        <!-- 公司 -->
        <div v-else-if="form.buildTypeValue=='公司'">
          <div class="sub-title">所在楼宇情况</div>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="所在商圈：">
                <el-select placeholder="请选择" v-model="form.checkCompanyBusinessCircle" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.businessCircle" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="写字楼属性：">
                <el-select placeholder="请选择" v-model="form.checkCompanyOfficeProperty" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.officeProperty" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="大堂面积：">
                <el-select placeholder="请选择" v-model="form.checkCompanyHallArea" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.hallArea" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="大堂挑高：">
                <el-select placeholder="请选择" v-model="form.checkCompanyLobbyHigh" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.lobbyHigh" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="外立面材料：">
                <el-select placeholder="请选择" v-model="form.checkCompanyFacadeMaterial" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.facadeMaterial" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="大堂地面：">
                <el-select placeholder="请选择" v-model="form.checkCompanyGroundFloor" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.groundFloor" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="空调：">
                <el-select placeholder="请选择" v-model="form.checkCompanyAirConditioner" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.airConditioner" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="电梯数量：">
                <el-select placeholder="请选择" v-model="form.checkCompanyElevatorsNumber" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.elevatorsNumber" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="便利店（现磨）/咖啡厅：">
                <el-select placeholder="请选择" v-model="form.checkCompanyCoffeeshop" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.coffeeshop" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
              <el-form-item label="周边30米内商业：">
                  <el-select placeholder="请选择" v-model="form.checkCompanyRoundBusiness" disabled>
                    <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.roundBusiness" :key="index"></el-option>
                  </el-select>
                </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="公司是否在同层：">
                <el-select placeholder="请选择" v-model="form.checkCompanySameLayer" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.sameLayer" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <div class="sub-title">公司情况</div>
          <el-row>
            <el-col :span="8" :offset="0">
              <el-form-item label="公司人数：">
                <el-input v-model="form.companyPersonNum" readonly></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="公司性质：">
                <el-select placeholder="请选择" v-model="form.checkCompanyNature" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.companyNature" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="办公室租金：">
                <el-select placeholder="请选择" v-model="form.checkCompanyOfficeRent" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.officeRent" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="设备摆放位置：">
                <el-select placeholder="请选择" v-model="form.checkCompanyEquipmentLocation" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.equipmentLocation" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
               <el-form-item label="人群年龄层：">
                  <el-select placeholder="请选择" v-model="form.checkCompanyPopulationAge" disabled>
                    <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.populationAge" :key="index"></el-option>
                  </el-select>
                </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
               <el-form-item label="男女比例：">
                  <el-select placeholder="请选择" v-model="form.checkCompanyScale" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.scale" :key="index"></el-option>
                  </el-select>
                </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="员工是否经常加班：">
                <el-select placeholder="请选择" v-model="form.checkCompanyOverTime" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.overTime" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="坐班人员是否占大多数：">
                <el-select placeholder="请选择" v-model="form.checkCompanyOnDutyMajority" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.onDutyMajority" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="是否提供咖啡：">
                <el-select placeholder="请选择" v-model="form.checkCompanyServingCoffee" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.servingCoffee" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="是否提供下午茶：">
                <el-select placeholder="请选择" v-model="form.checkCompanyServingAfternoonTea" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.servingAfternoonTea" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="是否有其他自助设备：">
                <el-select placeholder="请选择" v-model="form.checkCompanySelfServiceEquipment" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.company.selfServiceEquipment" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
        </div>
        <!-- 医院 -->
        <div v-else-if="form.buildTypeValue=='医院'">
          <div class="sub-title">医院情况</div>
            <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="医院类型：">
                <el-select placeholder="请选择" v-model="form.checkHospitalType" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.hospital.hospitalType" :key="index"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
                <el-form-item label="医护人员数量："  prop="medicalNum" id="medicalNum">
                  <el-input v-model="form.medicalNum" readonly></el-input>
                </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
              <el-form-item label="门诊接待量 ："  prop="receptionNum" id="receptionNum">
                <el-input v-model="form.receptionNum" readonly></el-input>
              </el-form-item>
            </el-col>
            </el-row>
            <el-row>
              <el-col :span="8" :offset="0">
                 <el-form-item label="院内商业：">
                    <el-select placeholder="请选择" v-model="form.checkHospitalBusiness" disabled>
                      <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.hospital.hospitalBusiness" :key="index"></el-option>
                    </el-select>
                  </el-form-item>
              </el-col>
              <el-col :span="8" :offset="0">
                 <el-form-item label="便利店（现磨）/咖啡厅：">
                    <el-select placeholder="请选择" v-model="form.checkHospitalCoffeeshop" disabled>
                      <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.coffeeshop" :key="index"></el-option>
                    </el-select>
                  </el-form-item>
              </el-col>
            </el-row>
            <div class="sub-title">所在楼宇情况</div>
            <el-row>
              <el-col :span="8" :offset="0">
               <el-form-item label="楼宇属性：">
                  <el-select placeholder="请选择" v-model="form.checkHospitalBuildingAttribute" disabled>
                    <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.hospital.buildingAttribute" :key="index"></el-option>
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :span="8" :offset="0">
                <el-form-item label="楼宇层高："  prop="checkHospitalBuildingHeight" id="checkHospitalBuildingHeight">
                <el-select placeholder="请选择" v-model="form.checkHospitalBuildingHeight" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.buildSpecialInfo.hospital.buildingHeight" :key="index"></el-option>
                </el-select>
              </el-form-item>
              </el-col>
              <el-col :span="8" :offset="0">
                 <el-form-item label="点位人流量：">
                    <el-select placeholder="请选择" v-model="form.checkHospitalHumanTraffic" disabled>
                      <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.humanTraffic" :key="index"></el-option>
                    </el-select>
                  </el-form-item>
                </el-col>
            </el-row>
            <el-row>
              <el-col :span="8" :offset="0">
                 <el-form-item label="设备摆放位置：">
                    <el-select placeholder="请选择" v-model="form.checkHospitalEquipmentLocation" disabled>
                      <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.equipmentLocation" :key="index"></el-option>
                    </el-select>
                  </el-form-item>
              </el-col>
              <el-col :span="8" :offset="0">
                 <el-form-item label="是否有其他自助设备：">
                    <el-select placeholder="请选择" v-model="form.checkHospitalHasOtherEquipment" disabled>
                      <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.hasOtherEquipment" :key="index"></el-option>
                    </el-select>
                  </el-form-item>
              </el-col>
            </el-row>
        </div>
        <!-- 其他 -->
        <div v-else>
          <el-row>
            <el-col :span="8" :offset="0">
              <el-form-item label="周边30米内商业：">
                  <el-select placeholder="请选择" v-model="form.checkCompanyRoundBusiness" disabled>
                    <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.roundBusiness" :key="index"></el-option>
                  </el-select>
                </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
               <el-form-item label="人群年龄层：">
                  <el-select placeholder="请选择" v-model="form.checkCompanyPopulationAge" disabled>
                    <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.populationAge" :key="index"></el-option>
                  </el-select>
                </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
               <el-form-item label="男女比例：">
                  <el-select placeholder="请选择" v-model="form.checkCompanyScale" disabled>
                  <el-option :label="item" :value="index" v-for="(item,index) in baseInfo.commonInfo.scale" :key="index"></el-option>
                  </el-select>
                </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="平层面积(m2)：" prop="checkFloorArea">
                <el-input v-model="form.checkFloorArea" readonly></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="人群穿着：" prop="checkClothing">
                <el-input v-model="form.checkClothing" readonly></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="8" :offset="0">
             <el-form-item label="覆盖人数：" prop="checkCoverPopulation">
                <el-input v-model="form.checkCoverPopulation" readonly></el-input>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="8" :offset="0">
             <el-form-item label="楼层高度：" prop="checkFloorHeight">
                <el-input v-model="form.checkFloorHeight" readonly></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="12" :offset="0">
             <el-form-item label="50米内咖啡情况：" prop="checkFiftyCoffee">
                <el-input v-model="form.checkFiftyCoffee" readonly></el-input>
              </el-form-item>
            </el-col>
          </el-row>
        </div>
      </div>
      <!-- 评分结束 -->
      <!-- 其他 -->
      <div class="line-title">其他</div>
      <el-row>
        <el-col :span="8" :offset="0">
           <el-form-item label="手机信号     联通：" class="mobile">
              <el-select placeholder="请选择" v-model="form.unicorn">
                <el-option label="0" value="0"></el-option>
                <el-option label="1" value="1"></el-option>
                <el-option label="2" value="2"></el-option>
                <el-option label="3" value="3"></el-option>
                <el-option label="4" value="4"></el-option>
                <el-option label="5" value="5"></el-option>
              </el-select>
            </el-form-item>
        </el-col>
        <el-col :span="8" :offset="0">
           <el-form-item label="移动：">
              <el-select placeholder="请选择" v-model="form.mobile">
                <el-option label="0" value="0"></el-option>
                <el-option label="1" value="1"></el-option>
                <el-option label="2" value="2"></el-option>
                <el-option label="3" value="3"></el-option>
                <el-option label="4" value="4"></el-option>
                <el-option label="5" value="5"></el-option>
              </el-select>
            </el-form-item>
        </el-col>
        <el-col :span="8" :offset="0">
           <el-form-item label="电信：">
              <el-select placeholder="请选择" v-model="form.telecom">
                <el-option label="0" value="0"></el-option>
                <el-option label="1" value="1"></el-option>
                <el-option label="2" value="2"></el-option>
                <el-option label="3" value="3"></el-option>
                <el-option label="4" value="4"></el-option>
                <el-option label="5" value="5"></el-option>
              </el-select>
            </el-form-item>
        </el-col>
      </el-row>
      <el-row>
        <el-col :span="12" :offset="0">
           <el-form-item label="电源情况：" prop="power" id="power">
              <el-select placeholder="请选择" v-model="form.power">
                <el-option value="电源不达标,需要接电" label="电源不达标,需要接电" ></el-option>
                <el-option value="电源达标,无需要接电" label="电源达标,无需要接电"></el-option>
              </el-select>
            </el-form-item>
        </el-col>
      </el-row>
      <el-row>
        <el-col :span="12" :offset="0">
           <el-form-item label="投放环境：" prop="putEnvironment" id="putEnvironment">
               <el-input type="textarea" v-model.trim="form.putEnvironment"></el-input>
            </el-form-item>
        </el-col>
      </el-row>
      <el-row>
        <el-col :span="12" :offset="0">
           <el-form-item label="竞品情况：" prop="competingGoods" id="competingGoods">
             <el-input type="textarea" v-model.trim="form.competingGoods"></el-input>
            </el-form-item>
        </el-col>
      </el-row>
      <!-- 楼宇初评建议 -->
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
    <el-form-item size="medium" class="div-submit">
        <el-button type="primary" @click="saveForm(0)">保存草稿</el-button>
        <el-button type="primary" @click="submitForm(1)">提交</el-button>
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
    var checkInt=(rule,value,callback)=>{
      var checkReg=/^[1-9]\d*$/;
      if(!checkReg.test(value)){
         callback(new Error('请输入正整数'));
      }else{
        callback();
      }
    };
    var checkNumber=(rule,value,callback)=>{
      var checkReg=/^\d+(?=\.{0,1}\d+$|$)/;
      if(!checkReg.test(value)){
         callback(new Error('请输入正确的数字格式'));
      }else{
        callback();
      }
    };
    return {
      baseInfo:baseInfo,
      id:"",//点位ID
      //初评建议
      buildRateData:[],
      buildAppearPic:["",""],//楼宇照片
      buildHallPic:["",""],//大厅照片
      pointPositionPic:["",""],//投放位置
      pointLicencePic:[""],//清晰水牌
      pointCompanyPic:["","","","",""],//公司照片
      pointPlan:[""],//平面图
      form:{
        // buildTypeID:"",
        buildTypeValue:"写字楼",//渠道中文名
        // buildingName:"",//楼宇名称
        buildRecordId:"",//楼宇ID
        buildType:"",
        buildTypeList:[],//渠道列表
        buildingNameList:[],//楼宇名称列表
        pointName:"",//点位名称（楼宇名称+摆放位置）
        position:"",//摆放位置
        address:"",
        bCircle:"",//商圈
        contactName:"",//联系人
        contactTel:"",//联系电话
        cooperation:"",//合作方式
        // pointLicencePic: "",//图片信息，PC新建编辑都可以传空
        // pointPositionPic: "",//摆放位置图片
        // pointCompanyPic: "",//公司照片
        // pointPlan: "",//平面图
        // 渠道不同，展示不同基础信息验证
        // 公共基础信息
        electric:'',//电费
        service:'',//服务费
        total:'',//费用总额
        rentWay:'0',//租金方式
        rentNum:'',//租金金额
        //学校基础信息
        area:'',//楼宇占地面积
        floor:'',//楼宇层高
        humanTraffic:'',//点位人流量
        //公司基础信息
        companyType:'',//公司类型
        // companyNum:'',//公司人数
        // scale:'',//男女比例
        //医院基础信息
        hospitalLevel:'',//医院等级
        receptionNum:'',//门诊接待量
        medicalNum:'',//医护人员数量
        hospitalHuman:'',//医院点位人流量
        //其他项
        unicorn:'',//联通
        mobile:'',//移动
        telecom:'',//电信
        power:'',//电源情况
        putEnvironment:'',//投放环境
        competingGoods:'',//竞品情况
        // 其他项结束
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
        checkHumanTraffic:"",//选中点位人流量
        checkScale:"",//选中男女比例
        checkRoundBusiness:"",//选中周边30米内商业
        checkIsMoreEntrance:"",//选中多个出入口
        checkEquipmentLocation:"",//选中设备摆放位置
        checkBusinessCircle:"",//选中所在商圈
        checkPopulationAge:"",//选中人群年龄层
        checkCoffeeshop:"",//咖啡厅
        checkHasOtherEquipment:"",//是否有其他设备
        //写字楼条件1
        floorArea:"",//平层面积
        buildFloor:"",//楼层
        lodgingRatio:"",//入住率
        useRatio:"",//使用率
        buildNum:"",//楼宇人数
        //园区
        checkParkBusinessCircle:"",//所在商圈
        checkParkOfficeRent:"",//选中办公室租金
        checkParkOfficeProperty:"",//写字楼属性
        checkParkHallArea:"",//大堂面积
        checkParkLobbyHigh:"",//大堂挑高
        checkParkFacadeMaterial:"",//外立面材料
        checkParkGroundFloor:"",//大堂地面
        checkParkAirConditioner:"",//空调
        checkParkElevatorsNumber:"",//电梯数量
        // 园区条件1
        floorParkArea:"",//平层面积
        buildParkFloor:"",//楼层
        lodgingParkRatio:"",//入住率
        useParkRatio:"",//覆盖楼宇
        coverParkNum:"",//覆盖人数
        // 园区条件3
        checkParkPopulationAge:"",//选中人群年龄层
        checkParkScale:"",//男女比例
        checkParkCompanySize:"",//公司规模
        checkParkCompanyNature:"",//公司性质
        checkParkYesOrNotOverThree:"",//是否有公司超过三层
        checkParkIsMoreEntrance:"",//选中多个出入口
        checkParkEquipmentLocation:"",//选中设备摆放位置
        checkParkRoundBusiness:"",//选中周边30米内商业
        checkParkCoffeeshop:"",//咖啡厅

        // 学校情况
        checkSchoolTyle:"",//学校类型
        checkNumberOfSchool:"",//学校人数
        checkSchoolPutinNumber:"",//学校投放台数
        checkChargingStandard:"",//学费收费标准
        checkSexRatio:"",//男女比例
        checkLivingExpenses:"",//每个月生活费
        checkIntramuralCommerce:"",//校内商业
        checkLessThanThreeThousand:"",//在校人数是否少于三千
        //学校所在楼宇情况
        checkBuildingAttribute:"",//楼宇属性
        checkBuildingArea:"",//楼宇占地面积
        checkBuildingHeight:"",//楼宇层高
        checkSchoolHumanTraffic:"",//点位人流量
        checkSchoolHasOtherEquipment:"",//其他自助设备
        checkSchoolEquipmentLocation:"",//设备摆放位置
        checkSchoolCoffeeshop:"",//便利店
        checkSchoolIsMoreEntrance:"",//是否多个出入口
        //公司所在楼宇情况
        checkCompanyBusinessCircle:"",//所在商圈
        checkCompanyOfficeProperty:"",//写字楼属性
        checkCompanyHallArea:"",//大堂面积
        checkCompanyLobbyHigh:"",//大堂挑高
        checkCompanyFacadeMaterial:"",//外立面材料
        checkCompanyGroundFloor:"",//大堂地面
        checkCompanyAirConditioner:"",//空调
        checkCompanyElevatorsNumber:"",//电梯数量
        checkCompanyCoffeeshop:"",//便利店
        checkCompanyRoundBusiness:"",//周边30米
        checkCompanySameLayer:"",//公司是否在同层
        //公司情况
        companyPersonNum:"",//公司人数
        checkCompanyNature:"",//公司性质
        checkCompanyOfficeRent:"",//办公室租金
        checkCompanyEquipmentLocation:"",//设备摆放位置
        checkCompanyPopulationAge:"",//人群年龄层
        checkCompanyScale:"",//男女比例
        checkCompanyOverTime:"",//员工是否经常加班
        checkCompanyOnDutyMajority:"",//坐班人员是否占大多数
        checkCompanyServingCoffee:"",//是否提供咖啡
        checkCompanyServingAfternoonTea:"",//是否提供下午茶
        checkCompanySelfServiceEquipment:"",//是否有其他自助设备

        //医院情况
        checkHospitalType:"",//医院类型
        medicalNum:"",//医护人员数量
        receptionNum:"",//门诊接待量
        checkHospitalBusiness:"",//院内商业
        checkHospitalCoffeeshop:"",//便利店
        //医院楼宇情况
        checkHospitalBuildingAttribute:"",//楼宇属性
        checkHospitalBuildingHeight:"",//楼宇层高
        checkHospitalHumanTraffic:"",//点位人流量
        checkHospitalEquipmentLocation:"",//设备摆放位置
        checkHospitalHasOtherEquipment:"",//是否有其他设备

        //其他
        checkFloorArea:"",//平层面积
        checkClothing:"",//人群穿着
        checkCoverPopulation:"",//覆盖人数
        checkFloorHeight:"",//楼层高度
        checkFiftyCoffee:""//50米内咖啡情况
      },
      submitFormUrl:'/point-evaluation/create',
      // test:10,
      pointLevel:"",
      rules:{
        buildType: [
          { required: true, message: '请选择渠道', trigger: 'change' }
        ],
        buildRecordId: [
          { required: true, message: '请选择楼宇名称', trigger: 'change' }
        ],
        position: [
          { required: true, message: '请输入摆放位置', trigger: 'blur' },
          { max: 20, message: '长度在20个字符以内', trigger: 'blur' }
        ],
        address:[
          { required: true, message: '请输入点位地址', trigger: 'blur' }
        ],//点位地址
        bCircle:[
          { required: true, message: '请输入商圈', trigger: 'blur' }
        ],//商圈
        contactName:[
          { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        ],//联系人
        contactTel:[
          { validator: checkPhone }
        ],//联系电话
        cooperation:[
          { required: true, message: '请选择合作方式', trigger: 'change' }
        ],//合作方式
        // 公共基础信息
        electric:[
          { required: true, message: '请输入电费', trigger: 'change' },
          { validator: checkNumber },
          { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        ],//电费
        service:[
          { required: true, message: '请输入服务费', trigger: 'change' },
          { validator: checkNumber },
          { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        ],//服务费
        total:[
          { required: true, message: '请输入费用总额', trigger: 'change' },
          { validator: checkNumber },
          { max: 20, message: '长度在20个字符以内', trigger: 'blur' }
        ],//费用总额
        rentNum:[
          { required: true, message: '请输入租金金额', trigger: 'blur' },
          { validator: checkNumber },
          { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        ],//租金金额
        //学校基础信息
        area:[
          { required: true, message: '请选择楼宇占地面积', trigger: 'change' },
          { validator: checkNumber },
          { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        ],//楼宇占地面积
        floor:[
          { required: true, message: '请输入楼宇层高', trigger: 'blur' },
          { validator: checkNumber },
          { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        ],//楼宇层高
        humanTraffic:[
          { required: true, message: '请输入点位人流量', trigger: 'blur' },
          { validator: checkNumber },
          { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        ],//点位人流量
        //公司基础信息
        companyType:[
          { required: true, message: '请选择公司类型', trigger: 'change' }
        ],//公司类型
        // companyNum:[
        //   { required: true, message: '请输入公司人数', trigger: 'blur' },
        //   { validator: checkNumber },
        //   { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        // ],//公司人数
        // scale:[
        //   { required: true, message: '请输入男女比例', trigger: 'blur' },
        //   { validator: checkNumber },
        //   { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        // ],//男女比例
        //医院基础信息
        hospitalLevel:[
          { required: true, message: '请选择医院等级', trigger: 'change' }
        ],//医院等级
        receptionNum:[
          { required: true, message: '请输入门诊接待量', trigger: 'blur' },
          { validator: checkNumber },
          { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        ],//门诊接待量
        medicalNum:[
          { required: true, message: '请输入医护人员数量', trigger: 'blur' },
          { validator: checkNumber },
          { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        ],//医护人员数量
        hospitalHuman:[
          { required: true, message: '请输入医院点位人流量', trigger: 'blur' },
          { validator: checkNumber },
          { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        ],//医院点位人流量
        //其他项
        unicorn:[
          { required: true, message: '请选择联通信号', trigger: 'change' }
        ],//联通
        mobile:[
          { required: true, message: '请选择移动信号', trigger: 'change' }
        ],//移动
        telecom:[
          { required: true, message: '请选择电信信号', trigger: 'change' }
        ],//电信
        power:[
          { required: true, message: '请选择电源情况', trigger: 'change' }
        ],//电源情况
        putEnvironment:[
          { required: true, message: '请输入投放环境', trigger: 'blur' },
          { max: 100, message: '长度在100个字符以内', trigger: 'blur' }
        ],//投放环境
        competingGoods:[
          { required: true, message: '请输入竞品情况', trigger: 'blur' },
          { max: 100, message: '长度在100个字符以内', trigger: 'blur' }
        ],//竞品情况
        //写字楼条件1
        floorArea:[
          { required: true, message: '请输入平层面积', trigger: 'blur' },
          { validator: checkNumber },
          { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        ],//平层面积
        buildFloor:[
          { required: true, message: '请输入楼层', trigger: 'blur' },
          { validator: checkNumber },
          { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        ],//楼层
        lodgingRatio:[
          { required: true, message: '请输入入住率', trigger: 'blur' },
          { validator: checkNumber },
          { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        ],//入住率
        useRatio:[
          { required: true, message: '请输入使用率', trigger: 'blur' },
          { validator: checkNumber },
          { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        ],//使用率
        buildNum:[
          { required: true, message: '请输入楼宇人数', trigger: 'blur' },
          { validator: checkNumber },
          { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        ],//楼宇人数
        // 园区条件1
        floorParkArea:[
          { required: true, message: '请输入平层面积', trigger: 'blur' },
          { validator: checkNumber },
          { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        ],//平层面积
        buildParkFloor:[
          { required: true, message: '请输入楼层', trigger: 'change' },
          { validator: checkNumber },
          { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        ],//楼层
        lodgingParkRatio:[
          { required: true, message: '请输入入住率', trigger: 'change' },
          { validator: checkNumber },
          { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        ],//入住率
        useParkRatio:[
          { required: true, message: '请输入覆盖楼宇', trigger: 'change' },
          { validator: checkNumber },
          { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        ],//覆盖楼宇
        coverParkNum:[
          { required: true, message: '请输入覆盖人数', trigger: 'change' },
          { validator: checkNumber },
          { max: 10, message: '长度在10个字符以内', trigger: 'blur' }
        ]//覆盖人数
      },
    }
  },
  computed:{
    pointScore:function(){
      let typeName=this.form.buildTypeValue;
      let score = 0;
      if(typeName=="写字楼"){
        // console.log("this.form.checkBusinessCircle..",this.form.checkBusinessCircle)
        //商圈
        let c10Arr = [10,8,6];
        let c10 = c10Arr[this.form.checkBusinessCircle];
        //办公室租金 (天/平米)
        let c11Arr = [10,9,8,6,3,1];
        let c11 = c11Arr[this.form.checkOfficeRent];
        //写字楼属性
        let c12Arr = [0,6,5,10,13,20];
        let c12 = c12Arr[this.form.checkOfficeProperty];
        //大堂面积
        let c13Arr = [-1,2,3,5,6];
        let c13 = c13Arr[this.form.checkHallArea];
        //大堂挑高
        let c14Arr = [1,3];
        let c14 = c14Arr[this.form.checkLobbyHigh];
        //外立面材料
        let c15Arr = [1,2,3];
        let c15 = c15Arr[this.form.checkFacadeMaterial];
        //大堂地面
        let c16Arr = [2,3];
        let c16 = c16Arr[this.form.checkGroundFloor];
        //空调
        let c17Arr = [0,1,3];
        let c17 = c17Arr[this.form.checkAirConditioner];
        //电梯数量
        let c18Arr = [5,4,3,1];
        let c18 = c18Arr[this.form.checkElevatorsNumber];

        //人群年龄层
        let f10Arr = [4,2,-1];
        let f10 = f10Arr[this.form.checkPopulationAge];
        //男女比例
        let f11Arr = [3,2,1];
        let f11 = f11Arr[this.form.checkScale];
        //公司规模
        let f12Arr = [2,3,-2];
        let f12 = f12Arr[this.form.checkCompanySize];
        //公司性质
        let f13Arr = [13,10,5];
        let f13 = f13Arr[this.form.checkCompanyNature];
        //是否有公司超过三层
        let f14Arr = [1,3];
        let f14 = f14Arr[this.form.checkYesOrNotOverThree];
        //多个出入口
        let f15Arr = [3,0,-3];
        let f15 = f15Arr[this.form.checkIsMoreEntrance];
        //设备摆放位置
        let f16Arr = [10,8,6,4,0];
        let f16 = f16Arr[this.form.checkEquipmentLocation];
        //便利店（现磨）/咖啡厅
        let f17Arr = [-5,0,3];
        let f17 = f17Arr[this.form.checkCoffeeshop];
        //周边30米内商业
        let f18Arr = [3,2,0];
        let f18 = f18Arr[this.form.checkRoundBusiness];

        //平层面积
        let b8 = this.form.floorArea>=5000?15:this.form.floorArea>=3000?12:this.form.floorArea>=2200?8:this.form.floorArea>=1500?6:this.form.floorArea>=800?4:this.form.floorArea>=600?2:0;
        //楼层
        let c8 = this.form.buildFloor>=42?25:this.form.buildFloor>=28?20:this.form.buildFloor>=20?14:this.form.buildFloor>=12?8:5;
        //入住率
        let d8 = this.form.lodgingRatio>=75?0:this.form.lodgingRatio>=60?-10:-20;
        //写字楼使用率
        let e8 = this.form.useRatio>80?0:this.form.useRatio>70?-5:this.form.useRatio>60?-10:-20;
        //楼宇人数
        let f8 = this.form.buildNum>=3500?20:this.form.buildNum>=2500?15:this.form.buildNum>=2000?13:this.form.buildNum>=1500?10:this.form.buildNum>=1200?8:this.form.buildNum>=800?5:this.form.buildNum>=600?0:-10;
        console.log("c10:"+c10+",c11:"+c11+",c12:"+c12+",c13:"+c13+",c14:"+c14+",c15:"+c15+",c16:"+c16+",c17:"+c17+",c18:"+c18+",f10:"+f10+",f11:"+f11+",f12:"+f12+",f13:"+f13+",f14:"+f14+",f15:"+f15+",f16:"+f16+",f17:"+f17+",f18:"+f18+",b8:"+b8+",c8:"+c8+",d8:"+d8+",e8:"+e8+",f8:"+f8);
        score = c10+c11+c12+c13+c14+c15+c16+c17+c18+f10+f11+f12+f13+f14+f15+f16+f17+f18+b8+c8+d8+e8+f8;
      }else if(typeName=="园区"){
        //商圈
        let c10Arr = [10,8,6];
        let c10 = c10Arr[this.form.checkParkBusinessCircle];
        //办公室租金 (天/平米)
        let c11Arr = [15,14,12,10,7,0];
        let c11 = c11Arr[this.form.checkParkOfficeRent];
        //写字楼属性
        let c12Arr = [5,10,13,20];
        let c12 = c12Arr[this.form.checkParkOfficeProperty];
        //大堂面积
        let c13Arr = [-1,2,3,5];
        let c13 = c13Arr[this.form.checkParkHallArea];
        //大堂挑高
        let c14Arr = [1,3];
        let c14 = c14Arr[this.form.checkParkLobbyHigh];
        //外立面材料
        let c15Arr = [1,2,3];
        let c15 = c15Arr[this.form.checkParkFacadeMaterial];
        //大堂地面
        let c16Arr = [2,3];
        let c16 = c16Arr[this.form.checkParkGroundFloor];
        //空调
        let c17Arr = [0,1,3];
        let c17 = c17Arr[this.form.checkParkAirConditioner];
        //电梯数量
        let c18Arr = [5,4,3,1];
        let c18 = c18Arr[this.form.checkParkElevatorsNumber];

        //人群年龄层
        let f10Arr = [4,2,-1];
        let f10 = f10Arr[this.form.checkParkPopulationAge];
        //男女比例
        let f11Arr = [3,2,1];
        let f11 = f11Arr[this.form.checkParkScale];
        //公司规模
        let f12Arr = [2,3,-2];
        let f12 = f12Arr[this.form.checkParkCompanySize];
        //公司性质
        let f13Arr = [13,10,5];
        let f13 = f13Arr[this.form.checkParkCompanyNature];
        //是否有公司超过三层
        let f14Arr = [1,3];
        let f14 = f14Arr[this.form.checkParkYesOrNotOverThree];
        //多个出入口
        let f15Arr = [3,0,-3];
        let f15 = f15Arr[this.form.checkParkIsMoreEntrance];
        //设备摆放位置
        let f16Arr = [10,8,6,4,0];
        let f16 = f16Arr[this.form.checkParkEquipmentLocation];
        //便利店（现磨）/咖啡厅
        let f17Arr = [-5,0,3];
        let f17 = f17Arr[this.form.checkParkCoffeeshop];
        //周边30米内商业
        let f18Arr = [3,2,0];
        let f18 = f18Arr[this.form.checkParkRoundBusiness];

        //平层面积
        let b8 = this.form.floorParkArea>=2500?10:this.form.floorParkArea>=2000?9:this.form.floorParkArea>=1200?8:this.form.floorParkArea>=800?6:this.form.floorParkArea>=500?4:0;
        //楼层
        let c8 = this.form.buildParkFloor>=15?15:this.form.buildParkFloor>=8?10:6;
        //入住率
        let d8 = this.form.lodgingParkRatio>=70?0:-20;
        //20米内覆盖楼宇
        let e8 = this.form.useParkRatio>1?5:0;
        //覆盖人数
        let f8 = this.form.coverParkNum>=3000?20:this.form.coverParkNum>=2200?12:this.form.coverParkNum>=1500?10:this.form.coverParkNum>=1000?8:this.form.coverParkNum>=600?5:3;
        console.log("c10:"+c10+",c11:"+c11+",c12:"+c12+",c13:"+c13+",c14:"+c14+",c15:"+c15+",c16:"+c16+",c17:"+c17+",c18:"+c18+",f10:"+f10+",f11:"+f11+",f12:"+f12+",f13:"+f13+",f14:"+f14+",f15:"+f15+",f16:"+f16+",f17:"+f17+",f18:"+f18+",b8:"+b8+",c8:"+c8+",d8:"+d8+",e8:"+e8+",f8:"+f8);
        score = c10+c11+c12+c13+c14+c15+c16+c17+c18+f10+f11+f12+f13+f14+f15+f16+f17+f18+b8+c8+d8+e8+f8;
      }else if(typeName=="学校"){
        //学校类型
        let c8Arr = [15,13,16,12,10];
        let c8 = c8Arr[this.form.checkSchoolTyle];
        //学校人数
        let c9Arr = [20,15,12,9,5,3];
        let c9 = c9Arr[this.form.checkNumberOfSchool];
        //学校投放台数
        let c10Arr = [8,6,3,1];
        let c10 = c10Arr[this.form.checkSchoolPutinNumber];
        //学费收费标准
        let c11Arr = [5,4,3,1];
        let c11 = c11Arr[this.form.checkChargingStandard];
        //男女比例
        let c12Arr = [5,3,1];
        let c12 = c12Arr[this.form.checkSexRatio];
        //每个月生活费
        let c13Arr = [0,2,4,5];
        let c13 = c13Arr[this.form.checkLivingExpenses];
        //校内商业
        let c14Arr = [3,1];
        let c14 = c14Arr[this.form.checkIntramuralCommerce];
        //在校人数是否少于三千
        let c15Arr = [-5,0];
        let c15 = c15Arr[this.form.checkLessThanThreeThousand];

        //楼宇属性
        let f8Arr = [12,5,15,5,8,3];
        let f8 = f8Arr[this.form.checkBuildingAttribute];
        //楼宇占地面积
        let f9Arr = [10,7,5,3];
        let f9 = f9Arr[this.form.checkBuildingArea];
        //楼宇层高
        let f10Arr = [5,3,1];
        let f10 = f10Arr[this.form.checkBuildingHeight];
        //点位人流量
        let f11Arr = [10,8,6,5];
        let f11 = f11Arr[this.form.checkSchoolHumanTraffic];
        //其他自助设备
        let f12Arr = [3,1];
        let f12 = f12Arr[this.form.checkSchoolHasOtherEquipment];
        //设备摆放位置
        let f13Arr = [10,8,6,4,0];
        let f13 = f13Arr[this.form.checkSchoolEquipmentLocation];
        //便利店
        let f14Arr = [-5,0,3];
        let f14 = f14Arr[this.form.checkSchoolCoffeeshop];
        //是否多个出入口
        let f15Arr = [3,0,0];
        let f15 = f15Arr[this.form.checkSchoolIsMoreEntrance];
        console.log("c8:"+c8+",c9:"+c9+",c10:"+c10+",c11:"+c11+",c12:"+c12+",c13:"+c13+",c14:"+c14+",c15:"+c15+",f8:"+f8+",f9:"+f9+",f10:"+f10+",f11:"+f11+",f12:"+f12+",f13:"+f13+",f14:"+f14+",f15:"+f15);
        score = c8+c9+c10+c11+c12+c13+c14+c15+f8+f9+f10+f11+f12+f13+f14+f15;
      }else if(typeName=="公司"){
        //所在商圈
        let c7Arr = [5,3,1];
        let c7 = c7Arr[this.form.checkCompanyBusinessCircle];
        //写字楼属性
        let c8Arr = [0,1,3,5,7,8];
        let c8 = c8Arr[this.form.checkCompanyOfficeProperty];
        //大堂面积
        let c9Arr = [1,2,3,4,5];
        let c9 = c9Arr[this.form.checkCompanyHallArea];
        //大堂挑高
        let c10Arr = [1,3];
        let c10 = c10Arr[this.form.checkCompanyLobbyHigh];
        //外立面材料
        let c11Arr = [1,2,3];
        let c11 = c11Arr[this.form.checkCompanyFacadeMaterial];
        //大堂地面
        let c12Arr = [2,3];
        let c12 = c12Arr[this.form.checkCompanyGroundFloor];
        //空调
        let c13Arr = [0,1,3];
        let c13 = c13Arr[this.form.checkCompanyAirConditioner];
        //电梯数量
        let c14Arr = [5,4,3,1];
        let c14 = c14Arr[this.form.checkCompanyElevatorsNumber];
        //便利店
        let c15Arr = [-5,0,3];
        let c15 = c15Arr[this.form.checkCompanyCoffeeshop];
        //周边30米
        let c16Arr = [3,2,0];
        let c16 = c16Arr[this.form.checkCompanyRoundBusiness];
        //公司是否在同层
        let c17Arr = [3,1];
        let c17 = c17Arr[this.form.checkCompanySameLayer];

        //公司人数
        let f7 = this.form.companyPersonNum>=500?42:this.form.companyPersonNum>=300?40:this.form.companyPersonNum>=200?30:this.form.companyPersonNum>=150?10:this.form.companyPersonNum>=100?0:-20;
        //公司性质
        let f8Arr = [30,20,25,25];
        let f8 = f8Arr[this.form.checkCompanyNature];
        //办公室租金
        let f9Arr = [5,4,3,1,-1];
        let f9 = f9Arr[this.form.checkCompanyOfficeRent];
        //设备摆放位置
        let f10Arr = [5,5,4,2,0];
        let f10 = f10Arr[this.form.checkCompanyEquipmentLocation];
        //人群年龄层
        let f11Arr = [3,1,0];
        let f11 = f11Arr[this.form.checkCompanyPopulationAge];
        //男女比例
        let f12Arr = [3,2,1];
        let f12 = f12Arr[this.form.checkCompanyScale];
        //员工是否经常加班
        let f13Arr = [1,-3];
        let f13 = f13Arr[this.form.checkCompanyOverTime];
        //坐班人员是否占大多数
        let f14Arr = [1,-3];
        let f14 = f14Arr[this.form.checkCompanyOnDutyMajority];
        //是否提供咖啡
        let f15Arr = [-5,3];
        let f15 = f15Arr[this.form.checkCompanyServingCoffee];
        //是否提供下午茶
        let f16Arr = [-1,3];
        let f16 = f16Arr[this.form.checkCompanyServingAfternoonTea];
        //是否有其他自助设备
        let f17Arr = [3,1];
        let f17 = f17Arr[this.form.checkCompanySelfServiceEquipment];

        console.log("c7:"+c7+",c8:"+c8+",c9:"+c9+",c10:"+c10+",c11:"+c11+",c12:"+c12+",c13:"+c13+",c14:"+c14+",c15:"+c15+",c16:"+c16+",c17:"+c17+",f7:"+f7+",f8:"+f8+",f9:"+f9+",f10:"+f10+",f11:"+f11+",f12:"+f12+",f13:"+f13+",f14:"+f14+",f15:"+f15+",f16:"+f16+",f17:"+f17);
        score = c7+c8+c9+c10+c11+c12+c13+c14+c15+c16+c17+f7+f8+f9+f10+f11+f12+f13+f14+f15+f16+f17;
      }else if(typeName=="医院"){
        //医院类型
        let c8Arr = [20,15,10];
        let c8 = c8Arr[this.form.checkHospitalType];
        //医护人员数量
        let c9 = this.form.medicalNum>=5000?30:this.form.medicalNum>=3000?25:this.form.medicalNum>=1800?20:this.form.medicalNum>=1000?10:5;
        //门诊接待量
        let c10 = this.form.receptionNum>=20000?25:this.form.receptionNum>=10000?20:this.form.receptionNum>=7000?15:this.form.receptionNum>=5000?12:this.form.receptionNum>=3000?9:5;
        //院内商业
        let c11Arr = [3,1];
        let c11 = c11Arr[this.form.checkHospitalBusiness];
        //便利店
        let c12Arr = [-5,0,3];
        let c12 = c12Arr[this.form.checkHospitalCoffeeshop];


        //楼宇属性
        let f8Arr = [15,12,5,5,8];
        let f8 = f8Arr[this.form.checkHospitalBuildingAttribute];
        //楼宇层高
        let f9Arr = [5,3,1];
        let f9 = f9Arr[this.form.checkHospitalBuildingHeight];
        //点位人流量
        let f10Arr = [10,8,5,0];
        let f10 = f10Arr[this.form.checkHospitalHumanTraffic];
        //设备摆放位置
        let f11Arr = [10,8,6,4,0];
        let f11 = f11Arr[this.form.checkHospitalEquipmentLocation];
        //是否有其他设备
        let f12Arr = [3,1];
        let f12 = f12Arr[this.form.checkHospitalHasOtherEquipment];

        console.log("c8:"+c8+",c9:"+c9+",c10:"+c10+",c11:"+c11+",c12:"+c12+",f8:"+f8+",f9:"+f9+",f10:"+f10+",f11:"+f11+",f12:"+f12);
        score = c8+c9+c10+c11+c12+f8+f9+f10+f11+f12;
      }else{
        score = 0;
      }
      if(isNaN(score)) {
        score = 0;
      }
      if(typeName=="写字楼"){
        this.pointLevel = score>=105?1:score>=88?2:score>=80?3:4;
      }else if(typeName=="园区"){
        this.pointLevel = score>=110?1:score>=85?2:score>=75?3:4;
      }else if(typeName=="学校"){
        this.pointLevel = score>=88?1:score>=82?2:score>=70?3:4;
      }else if(typeName=="公司"){
        this.pointLevel = score>=92?1:score>=82?2:score>=70?3:4;
      }else if(typeName=="医院"){
        this.pointLevel = score>=92?1:score>=82?2:score>=70?3:4;
      }else{
        this.pointLevel = score>=92?1:score>=82?2:score>=70?3:4;
      }
      return score;
    }
  },
  mounted(){
   this.init();
  },
  watch:{
  },
  methods: {
    init(){
      window.parent.addEventListener('scroll',(e)=>{
        this.scrollMsg();
      });
      this.id=this.$route.query.pointId;//点位Id
      console.log("this.id..",this.id)
      // this.id=6
      //根据ID判断是新建页还是编辑页
      if(this.id!=""){
        axios.get('/point-evaluation/update?point_id='+this.id).then((res)=>{
          const initData =res.data;
          console.log("init",initData);
          if(initData.error_code== 0){
              //编辑时会根据用户权限显示可以编辑的内容
              if(initData.data.isUpdate==0){
                this.$router.push({name:"modifyContact",query:{id:initData.data.recordInfo.id,pointId:this.id}});
              }else{
                this.render(initData.data);
              }
          }else{
            this.alertMsg(initData.msg);
            return false;
          }
        }).catch((error)=>{
             console.log("error..",error);
        });
      }else{
         this.getBuildTypeList();
      }
    },
    changeRent() {
      this.form.rentNum = "";
    },
    //初始化渠道列表
    getBuildTypeList(){
      axios.get('/point-evaluation/get-build-type-list').then((res)=>{
        const initData =res.data;
        if(initData.error_code== 0){
          this.form.buildTypeList=initData.data;//渠道类型列表
          for(let i of this.form.buildTypeList){
            if(i.id==this.form.buildType){
              this.form.buildTypeValue=i.type_name;//获取渠道名称name
              console.log("渠道名称",this.form.buildTypeValue);
            }
          }
        }else{
          this.alertMsg(initData.msg);
          return false;
        }
      }).catch((error)=>{
           console.log("error..",error);
      });
    },
    //根据渠道类型获取楼宇名称列表
    changeBuildType(){
      this.getBuildTypeNameById();
      axios.get('/point-evaluation/get-build-list?build_type_id='+this.form.buildType).then((res)=>{
        const initData =res.data;
        console.log("楼宇名称列表",initData);
        if(initData.error_code== 0){
          this.form.buildRecordId="";
          this.form.buildingNameList=initData.data;//楼宇名称列表
        }else{
          this.alertMsg(initData.msg);
          return false;
        }
      }).catch((error)=>{
           console.log("error..",error);
      });
    },
    changeBuildType2(){
      axios.get('/point-evaluation/get-build-list?build_type_id='+this.form.buildType).then((res)=>{
        const initData =res.data;
        console.log("楼宇名称列表...",initData);
        if(initData.error_code== 0){
          this.form.buildingNameList=initData.data;//楼宇名称列表
        }
      }).catch((error)=>{
           console.log("error..",error);
      });
    },
    //根据楼ID获取楼宇信息
    getInfoByBuildingName(){
       axios.get('/point-evaluation/get-build-record-info?record_id='+this.form.buildRecordId).then((res)=>{
        const initData =res.data;
        console.log("楼宇信息",initData);
        if(initData.error_code== 0){
          const data=initData.data;
          this.renderBuildingInfo(data);
        }else{
          this.alertMsg(initData.msg);
          return false;
        }
      }).catch((error)=>{
           console.log("error..",error);
      });
    },
    //根据楼宇ID获取楼宇名称
    getBuildNameById(){
      console.log("this.form.buildRecordId..",this.form.buildRecordId,"this.form.buildingNameList..",this.form.buildingNameList)
      for(let i of this.form.buildingNameList){
        console.log("id..",i.id);
        if(i.id==this.form.buildRecordId){
          this.form.pointName=i.buildNameStatus.split('+')[0];
          break;
        }
      }
    },
    //渲染指定楼宇信息
    renderBuildingInfo(data){
      this.buildAppearPic=data.recordInfo.buildAppearPic;
      this.buildHallPic=data.recordInfo.buildHallPic;
      // this.pointLicencePic=data.recordInfo.point_licence_pic;//清晰水牌
      // this.pointCompanyPic=data.recordInfo.point_company_pic;//公司照片
      // this.pointPlan=data.recordInfo.point_plan;//平面图
      // this.pointPositionPic=data.recordInfo.point_position_pic;//投放位置
      //基础信息
      console.log("楼宇data",data);
      this.form.buildRecordId=String(data.recordInfo.id);//楼宇Id
      this.form.address=data.recordInfo.address;
      this.form.bCircle=data.recordInfo.buildPublicInfo.bCircle;
      this.form.contactName=data.recordInfo.contactName;
      this.form.contactTel=data.recordInfo.contactTel;
      //楼宇条件信息
      let specialInfo=data.recordInfo.buildSpecialInfo;
      let publicInfo=data.recordInfo.buildPublicInfo;
      this.form.floor=publicInfo.floor;
      // this.form.bCircle=publicInfo.bCircle;
      // this.form.checkHumanTraffic=publicInfo.humanTraffic;
      // this.form.checkIsMoreEntrance=publicInfo.isMoreEntrance;
      // this.form.checkPopulationAge=publicInfo.populationAge;
      // this.form.checkScale=publicInfo.scale;
      // this.form.checkEquipmentLocation=publicInfo.equipmentLocation;
      // this.form.checkCoffeeshop=publicInfo.coffeeshop;
      // this.form.checkRoundBusiness=publicInfo.roundBusiness;
      // this.form.checkBusinessCircle=publicInfo.businessCircle;
      // this.form.checkHasOtherEquipment=publicInfo.hasOtherEquipment;
      let typeName=this.form.buildTypeValue;
      if(typeName=="写字楼"){
        // 条件2
        console.log(specialInfo.officeRent)
        this.form.checkBusinessCircle=publicInfo.businessCircle;//商圈
        this.form.checkOfficeRent=specialInfo.officeRent;//办公室租金
        this.form.checkOfficeProperty=specialInfo.officeProperty;//写字楼属性
        this.form.checkHallArea=specialInfo.hallArea;//大堂面积
        this.form.checkLobbyHigh=specialInfo.lobbyHigh;//大堂挑高
        this.form.checkFacadeMaterial=specialInfo.facadeMaterial;//外立面材料
        this.form.checkGroundFloor=specialInfo.groundFloor;//大堂地面
        this.form.checkAirConditioner=specialInfo.airConditioner;//空调
        this.form.checkElevatorsNumber=specialInfo.elevatorsNumber;//电梯数量
        // 条件3
        this.form.checkPopulationAge=publicInfo.populationAge;//人群年龄层
        this.form.checkScale=publicInfo.scale;//男女比例
        this.form.checkCompanySize=specialInfo.companySize;//公司规模
        this.form.checkCompanyNature=specialInfo.companyNature;//公司性质
        this.form.checkYesOrNotOverThree=specialInfo.yesOrNotOverThree;//是否有公司超过三层
        this.form.checkEquipmentLocation=publicInfo.equipmentLocation;//设备摆放位置
        this.form.checkIsMoreEntrance=publicInfo.isMoreEntrance;
        this.form.checkCoffeeshop=publicInfo.coffeeshop;//便利店
        this.form.checkRoundBusiness=publicInfo.roundBusiness;//周边商业

      }else if(typeName=="园区"){
        // 条件2
        this.form.checkParkBusinessCircle=publicInfo.businessCircle;//商圈
        this.form.checkParkOfficeRent=specialInfo.officeRent;//办公室租金
        this.form.checkParkOfficeProperty=specialInfo.officeProperty;//写字楼属性
        this.form.checkParkHallArea=specialInfo.hallArea;//大堂面积
        this.form.checkParkLobbyHigh=specialInfo.lobbyHigh;//大堂挑高
        this.form.checkParkFacadeMaterial=specialInfo.facadeMaterial;//外立面材料
        this.form.checkParkGroundFloor=specialInfo.groundFloor;//大堂地面
        this.form.checkParkAirConditioner=specialInfo.airConditioner;//空调
        this.form.checkParkElevatorsNumber=specialInfo.elevatorsNumber;//电梯数量
        // 条件3
        this.form.checkParkPopulationAge=publicInfo.populationAge;//人群年龄层
        this.form.checkParkScale=publicInfo.scale;//男女比例
        this.form.checkParkCompanySize=specialInfo.companySize;//公司规模
        this.form.checkParkCompanyNature=specialInfo.companyNature;//公司性质
        this.form.checkParkYesOrNotOverThree=specialInfo.yesOrNotOverThree;//是否有公司超过三层
        this.form.checkParkEquipmentLocation=publicInfo.equipmentLocation;//设备摆放位置
        this.form.checkParkIsMoreEntrance=publicInfo.isMoreEntrance;//多个出入口
        this.form.checkParkCoffeeshop=publicInfo.coffeeshop;//便利店
        this.form.checkParkRoundBusiness=publicInfo.roundBusiness;//周边商业

      }else if(typeName=="学校"){
        // 学校情况
        this.form.checkSchoolTyle=specialInfo.schoolTyle;//学校类型
        this.form.checkNumberOfSchool=specialInfo.numberOfSchool;//学校人数
        this.form.checkSchoolPutinNumber=specialInfo.schoolPutinNumber;//学校投放台数
        this.form.checkChargingStandard=specialInfo.chargingStandard;//学费收费标准
        this.form.checkSexRatio=specialInfo.sexRatio;//男女比例
        this.form.checkLivingExpenses=specialInfo.livingExpenses;//每个月生活费
        this.form.checkIntramuralCommerce=specialInfo.intramuralCommerce;//校内商业
        this.form.checkLessThanThreeThousand=specialInfo.lessThanThreeThousand;//在校人数是否少于三千
        //楼宇情况
        this.form.checkBuildingAttribute=specialInfo.buildingAttribute;//楼宇属性
        this.form.checkBuildingArea=specialInfo.buildingArea;//楼宇占地面积
        this.form.checkBuildingHeight=specialInfo.buildingHeight;//楼宇层高
        this.form.checkSchoolHumanTraffic=publicInfo.humanTraffic;//点位人流量
        this.form.checkSchoolHasOtherEquipment=publicInfo.hasOtherEquipment;//其他自助设备
        this.form.checkSchoolEquipmentLocation=publicInfo.equipmentLocation;//设备摆放位置
        this.form.checkSchoolCoffeeshop=publicInfo.coffeeshop;//便利店
        this.form.checkSchoolIsMoreEntrance=publicInfo.isMoreEntrance;//是否多个出入口
      }else if(typeName=="公司"){
        //公司所在楼宇情况
        this.form.checkCompanyBusinessCircle=publicInfo.businessCircle;//商圈
        this.form.checkCompanyOfficeProperty=specialInfo.officeProperty;//写字楼属性
        this.form.checkCompanyHallArea=specialInfo.hallArea;//大堂面积
        this.form.checkCompanyLobbyHigh=specialInfo.lobbyHigh;//大堂挑高
        this.form.checkCompanyFacadeMaterial=specialInfo.facadeMaterial;//外立面材料
        this.form.checkCompanyGroundFloor=specialInfo.groundFloor;//大堂地面
        this.form.checkCompanyAirConditioner=specialInfo.airConditioner;//空调
        this.form.checkCompanyElevatorsNumber=specialInfo.elevatorsNumber;//电梯数量
        this.form.checkCompanyCoffeeshop=publicInfo.coffeeshop;//便利店
        this.form.checkCompanyRoundBusiness=publicInfo.roundBusiness;//周边商业
        this.form.checkCompanySameLayer=specialInfo.sameLayer;//公司是否在同层
        //公司情况
        this.form.companyPersonNum=specialInfo.numberOfCompanies;//公司人数
        this.form.checkCompanyNature=specialInfo.companyNature;//公司性质
        this.form.checkCompanyOfficeRent=specialInfo.officeRent;//办公室租金
        this.form.checkCompanyEquipmentLocation=publicInfo.equipmentLocation;//设备摆放位置
        this.form.checkCompanyPopulationAge=publicInfo.populationAge;//人群年龄层
        this.form.checkCompanyScale=publicInfo.scale;//男女比例
        this.form.checkCompanyOverTime=specialInfo.overTime;//员工是否经常加班
        this.form.checkCompanyOnDutyMajority=specialInfo.onDutyMajority;//坐班人员是否占大多数
        this.form.checkCompanyServingCoffee=specialInfo.servingCoffee;//是否提供咖啡
        this.form.checkCompanyServingAfternoonTea=specialInfo.servingAfternoonTea;//是否提供下午茶
        this.form.checkCompanySelfServiceEquipment=specialInfo.selfServiceEquipment;//是否有其他自助设备
      }else if(typeName=="医院"){
        this.form.checkHospitalType=specialInfo.hospitalType;//医院类型
        this.form.medicalNum=specialInfo.medicalNum;//医护人员数量
        this.form.receptionNum=specialInfo.receptionNum;//门诊接待量
        this.form.checkHospitalBusiness=specialInfo.hospitalBusiness;//院内商业
        this.form.checkHospitalCoffeeshop=publicInfo.coffeeshop;//便利店
        this.form.checkHospitalBuildingAttribute=specialInfo.buildingAttribute;//楼宇属性
        this.form.checkHospitalBuildingHeight=specialInfo.buildingHeight;//楼宇层高
        this.form.checkHospitalHumanTraffic=publicInfo.humanTraffic;//点位人流量
        this.form.checkHospitalEquipmentLocation=publicInfo.equipmentLocation;//设备摆放位置
        this.form.checkHospitalHasOtherEquipment=publicInfo.hasOtherEquipment;//是否有其他设备
      } else {
        this.form.checkCompanyPopulationAge=publicInfo.populationAge;//人群年龄层
        this.form.checkCompanyScale=publicInfo.scale;//男女比例
        this.form.checkCompanyRoundBusiness=publicInfo.roundBusiness;//周边商业
        this.form.checkFloorArea = specialInfo.checkFloorArea;
        this.form.checkClothing = specialInfo.checkClothing;
        this.form.checkCoverPopulation = specialInfo.checkCoverPopulation;
        this.form.checkFloorHeight = specialInfo.checkFloorHeight;
        this.form.checkFiftyCoffee = specialInfo.checkFiftyCoffee;
      }
      //初评建议
      this.buildRateData = [];
      if(data.buildRate!="{}"){
        this.buildRateData.push(data.buildRate);
      }
      console.log("this.buildRateData[0]..",this.buildRateData[0])
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
    //根据渠道ID获取渠道名称
    getBuildTypeNameById(){
      for(let i of this.form.buildTypeList){
        if(i.id==this.form.buildType){
          this.form.buildTypeValue=i.type_name;
          console.log("buildTypeValue",this.form.buildTypeValue)
        }
      }
    },
    render(data){//渲染数据
      console.log("record",data);
      //点位信息
      this.form.buildTypeList=data.BuildTypeList;
      this.form.buildType=String(data.recordInfo.buildTypeID);
      this.getBuildTypeNameById();
      this.form.position=data.pointInfo.point_position;
      // this.form.buildingName=data.recordInfo.buildingName;
      this.form.cooperation=data.pointInfo.cooperate;
      // 基础信息
      this.form.rentWay=data.pointInfo.point_basic_info.rentWay;//租金方式
      this.form.rentNum=data.pointInfo.point_basic_info.rentNum//对应金额
      this.form.electric=data.pointInfo.point_basic_info.electric;//电费
      this.form.service=data.pointInfo.point_basic_info.service;//服务费
      this.form.total=data.pointInfo.point_basic_info.total;//费用总额
      if(this.form.buildTypeValue=="学校"){
        this.form.area=data.pointInfo.point_basic_info.area;//楼宇占地面积
        this.form.floor=data.pointInfo.point_basic_info.floor;//楼宇层高
        this.form.humanTraffic=data.pointInfo.point_basic_info.humanTraffic;//点位人流量
      }else if(this.form.buildTypeValue=="公司"){
        this.form.companyType=data.pointInfo.point_basic_info.companyType;//公司类型
        // this.form.companyNum=data.pointInfo.point_basic_info.companyNum;//公司人数
        // this.form.scale=data.pointInfo.point_basic_info.scale;//男女比例
      }else if(this.form.buildTypeValue=="医院"){
        //医院基础信息
        this.form.hospitalLevel=data.pointInfo.point_basic_info.hospitalLevel;//医院等级
        this.form.receptionNum=data.pointInfo.point_basic_info.receptionNum;//门诊接待量
        this.form.medicalNum=data.pointInfo.point_basic_info.medicalNum;//医护人员数量
        this.form.hospitalHuman=data.pointInfo.point_basic_info.medicalNum;//医院点位人流量

      }
      //条件信息
      if(this.form.buildTypeValue=="写字楼"){
        this.form.floorArea=data.pointInfo.point_score_info.floorArea;//平层面积
        this.form.buildFloor=data.pointInfo.point_score_info.buildFloor;//楼层
        this.form.lodgingRatio=data.pointInfo.point_score_info.lodgingRatio;//入住率
        this.form.useRatio=data.pointInfo.point_score_info.useRatio;//使用率
        this.form.buildNum=data.pointInfo.point_score_info.buildNum;//楼宇人数
      }else if(this.form.buildTypeValue=="园区"){
        this.form.floorParkArea=data.pointInfo.point_score_info.floorParkArea;//平层面积
        this.form.buildParkFloor=data.pointInfo.point_score_info.buildParkFloor;//楼层
        this.form.lodgingParkRatio=data.pointInfo.point_score_info.lodgingParkRatio;//入住率
        this.form.useParkRatio=data.pointInfo.point_score_info.useParkRatio;//覆盖楼宇
        this.form.coverParkNum=data.pointInfo.point_score_info.coverParkNum;//覆盖人数
      }
      //其他信息
      this.form.unicorn=data.pointInfo.point_other_info.unicorn,//联通
      this.form.mobile=data.pointInfo.point_other_info.mobile,//移动
      this.form.telecom=data.pointInfo.point_other_info.telecom,//电信
      this.form.power=data.pointInfo.point_other_info.power,//电源情况
      this.form.putEnvironment=data.pointInfo.point_other_info.putEnvironment,//投放环境
      this.form.competingGoods=data.pointInfo.point_other_info.competingGoods,//竞品情况
      //图片信息
      this.pointLicencePic=data.pointInfo.point_licence_pic;
      this.pointPositionPic=data.pointInfo.point_position_pic;//摆放位置图片
      this.pointCompanyPic=data.pointInfo.point_company_pic;//公司照片
      this.pointPlan=data.pointInfo.point_plan;//平面图

      //楼宇信息
      this.changeBuildType2();
      this.renderBuildingInfo(data);
    },
    checkPhotos(){
      let result = true;
      if(this.buildAppearPic.some(item=>item==="")) {
        return false;
      }
      if(this.buildHallPic.some(item=>item==="")) {
        return false;
      }
      if(this.pointLicencePic.some(item=>item==="")) {
        return false;
      }
      if(this.pointPositionPic.some(item=>item==="")) {
        return false;
      }
      if(this.pointCompanyPic.some(item=>item==="")) {
        return false;
      }
      //this.pointPlan.some(item=>item==="");
      return result;
    },
    submitForm(val){
      this.$refs['form'].validate((valid,obj) => {
        if (valid) {
          if (this.form.rentNum=="") {
            this.alertMsg("请填写租金方式");
            return;
          } else if (!/^\d+(?=\.{0,1}\d+$|$)/.test(this.form.rentNum)) {
            this.alertMsg("请正确填写租金方式");
            return;
          } else if(!this.checkPhotos()) {
            this.alertMsg("照片不能为空");
            return;
          } else {
            this.submitAction(val);
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
    saveForm(val){//保存草稿
      if(this.form.buildTypeID==""||this.form.buildRecordId==""||this.form.position==""){
        this.alertMsg("渠道、楼宇名称、摆放位置不能为空");
        return;
      } else {
        this.submitAction(val);
      }
    },
    submitAction(val){
      this.getBuildNameById();
      console.log("this.form.pointName...",this.form.pointName);
      // return false;
      // 条件
      const conditionInfoBuild={//写字楼
        floorArea:this.form.floorArea,//平层面积
        buildFloor:this.form.buildFloor,//楼层
        lodgingRatio:this.form.lodgingRatio,//入住率
        useRatio:this.form.useRatio,//使用率
        buildNum:this.form.buildNum//楼宇人数
      }
      const conditionInfoPark={//园区
        floorParkArea:this.form.floorParkArea,//平层面积
        buildParkFloor:this.form.buildParkFloor,//楼层
        lodgingParkRatio:this.form.lodgingParkRatio,//入住率
        useParkRatio:this.form.useParkRatio,//覆盖楼宇
        coverParkNum:this.form.coverParkNum//覆盖人数
      }
      const otherInfo={//其他信息
        unicorn:this.form.unicorn,//联通
        mobile:this.form.mobile,//移动
        telecom:this.form.telecom,//电信
        power:this.form.power,//电源情况
        putEnvironment:this.form.putEnvironment,//投放环境
        competingGoods:this.form.competingGoods//竞品情况
      }
      const baseInfo={//基础信息
        rentWay:this.form.rentWay,//租金方式
        rentNum:this.form.rentNum,//对应金额
        electric:this.form.electric,//电费
        service:this.form.service,//服务费
        total:this.form.total,//费用总额
      }
      let params={
        point_id:this.id,//点位Id
        point_name:this.form.pointName+this.form.position,
        point_position:this.form.position,
        point_level: this.pointLevel,//点位级别
        point_score: this.pointScore,//点位分数
        cooperate:this.form.cooperation,
        point_status:val,//0保存草稿 1创建提交
        build_type_id: this.form.buildType,//渠道类型,
        build_record_id:this.form.buildRecordId,
        point_basic_info: baseInfo,
        point_score_info:"",//条件信息
        point_other_info: otherInfo,
        point_licence_pic:[""],//清晰水牌
        point_position_pic:["",""],//摆放位置图片
        point_company_pic:["","","","",""],//公司照片
        point_plan:[""],//平面图
        contact_tel:this.form.contactTel,
        contact_name:this.form.contactName
      }
      if(this.form.buildTypeValue=="写字楼"){
        params.point_score_info=conditionInfoBuild;
      }else if(this.form.buildTypeValue=="园区"){
        params.point_score_info=conditionInfoPark;
      }else if(this.form.buildTypeValue=="学校"){
        baseInfo.area=this.form.area;//楼宇占地面积
        baseInfo.floor=this.form.floor;//楼宇层高
        baseInfo.humanTraffic=this.form.humanTraffic;//点位人流量
      }else if(this.form.buildTypeValue=="公司"){
        baseInfo.companyType=this.form.companyType;//公司类型
        // baseInfo.companyNum=this.form.companyNum;//公司人数
        // baseInfo.scale=this.form.scale;//男女比例
      }else if(this.form.buildTypeValue=="医院"){
        baseInfo.hospitalLevel=this.form.hospitalLevel;//医院等级
        baseInfo.receptionNum=this.form.receptionNum;//门诊接待量
        baseInfo.medicalNum=this.form.medicalNum;//医护人员数量
        baseInfo.hospitalHuman=this.form.hospitalHuman;//医院点位人流量
      }
      console.log("params",params);
      axios.post(this.submitFormUrl,params)
      .then((response)=> {
        let data = response.data;
        console.log("data",data);
        if(data.error_code==0){
          let msg = (val==0)?'保存草稿成功':'提交成功';
          this.alertMsg(msg,'success');
          this.$router.push({name:"pointList"});
        }else{
           this.alertMsg('操作失败',data.msg);
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
li {list-style-type:none;}
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
.sub-title{
  width:85px;
  font-size:14px;
  color:#333;
}
.el-form-item {
  margin-bottom: 20px;
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
.charter-money{
  width:60px;
  height: 20px;
  line-height: 20px;
  text-align:center;
  margin:0 10px;
}
.charter-box{
  float:left;
  margin-right:10px;
}
.charter-money .el-input__inner{
  height: 20px;
  line-height: 20px;
}
.table-line{
  margin-bottom:20px;
  margin-left:30px;
}
.mobile:before{
  position: absolute;
  left:27px;
  top:11px;
  display:inline-block;
  content: "*";
  color:red;
}

</style>
