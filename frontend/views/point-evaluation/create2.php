<!DOCTYPE html>
<html>

<head>
  <meta charset=utf-8>
  <meta name=viewport content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
  <meta name=apple-mobile-web-app-capable content=yes>
  <meta name=apple-mobile-web-app-status-bar-style content=black>
  <meta name=format-detection content="telephone=no, email=no">
  <title>点位评分</title>
  <script type=text/javascript src=/js/rem.js></script>
  <script type=text/javascript src=/js/vconsole.min.js></script>
  <script>
    if(window.location.host.split(".")[0]!="erp") {
      var vConsole = new VConsole();
    }
  </script>
  <link href=/point-evaluation/css/app.36faf5fa02ff37995b9719acc697030f.css rel=stylesheet>
</head>

<body>
  <div id=app></div>
  <script src=https://res.wx.qq.com/open/js/jweixin-1.2.0.js></script>
  <script>
    var signPackage = <?php echo $signPackage; ?>;
    console.log("jsdk..",signPackage);
    wx.config({
      debug: false,
      appId: signPackage.appId, //企业微信的corpID
      timestamp: signPackage.timestamp, //生成签名的时间戳
      nonceStr: signPackage.nonceStr, //生成签名的随机串
      signature: signPackage.signature,
      jsApiList: [
        'chooseImage',
        'uploadImage',
      ]
    });
    var rootErpUrl= '';
    var rootCoffeeUrl= '';
    var baseInfo = {
      commonInfo: {
        humanTraffic: ['3000人及以上','1800-3000人','1000-1800人','1000人及以下'],//人流量
        scale: ['女性多','平均','男性多'],//男女比例
        roundBusiness: ['无餐饮店','5家以内餐饮店','5家以上餐饮店'],//周边30米内商业
        isMoreEntrance: ['1个出入口','2个出入口','3个及以上出入口'],//是否多个出入口分流
        equipmentLocation: ['电梯间门口','人群动线上','距离动线5m以内','距离动线5m以外','大堂隐蔽位置'],//设备摆放位置
        businessCircle: ['知名重点商圈','中心商圈','其他商圈'],//所在商圈
        populationAge: ['青年','中青年','中年'],//人群年龄层
        coffeeshop: ['所在楼下','周边楼下（50m内）','无咖啡厅便利店'],//便利店（现磨）厅
        hasOtherEquipment: ['Y','N'],//是否有其他自助设备
      },
      buildSpecialInfo: {
        officeBuilding: {//写字楼
          officeRent: ['15元以上','12.1元-15元','8.1元－12元','5.1元－8元','2.1元-5元','2元以下'],//办公室租金
          officeProperty: ['商住 商用率低于90%','商住 商用率高于90%','丙级写字楼','乙级写字楼','甲级写字楼','超甲级写字楼'],//写字楼属性
          hallArea: ['150平米以下','150-300平米','300-500平米','500-1000平米','1000平米以上'],//大堂面积
          lobbyHigh: ['1层','两层及以上'],//大堂挑高
          facadeMaterial: ['普通涂料','面砖／瓷砖／铝板','玻璃幕／大理石'],//外立面材料
          groundFloor: ['地砖／普通地毯','天然石材／高级地毯'],//大堂地面
          airConditioner: ['非中央空调','公共区域中央空调','全部中央空调'],//空调
          elevatorsNumber: ['6部及以上','4-5部','3部','2部及以下'],//电梯数量
          companySize: ['200人以上中大型公司','10-200人中小型公司','10人以下mini公司'],//公司规模
          companyNature: ['IT互联网／事务所','其他各类','建筑工程/服务类'],//公司性质
          YesOrNotOverThree: ['Y','N'],//是否有公司超过三层
        },
        park: {//园区
          officeRent: ['9元以上','6.6元－9元','4.5元－6.5元','2.5元－4.5元','1.1元-2.5元','1元以下'],//办公室租金
          officeProperty: ['丙级写字楼','乙级写字楼','甲级写字楼','超甲级写字楼'],//写字楼属性
          hallArea: ['50平米以下','50-300平米','300-500平米','500平米以上'],//大堂面积
          lobbyHigh: ['1层','两层及以上'],//大堂挑高
          facadeMaterial: ['普通涂料','面砖／瓷砖／铝板','玻璃幕／大理石'],//外立面材料
          groundFloor: ['地砖／普通地毯','天然石材／高级地毯'],//大堂地面
          airConditioner: ['非中央空调','公共区域中央空调','全部中央空调'],//空调
          elevatorsNumber: ['6部及以上','4-5部','3部','2部及以下'],//电梯数量
          companySize: ['200人以上中大型公司','10-200人中小型公司','10人以下mini公司'],//公司规模
          companyNature: ['IT互联网／事务所','其他各类','建筑工程/服务类'],//公司性质
          YesOrNotOverThree: ['Y','N'],//是否有公司超过三层
        },
        school: {//学校
          schoolTyle: ['重点大学','普通全日制大学','艺术体育类','三本院校','普通专科院校'],//学校类型
          numberOfSchool: ['5万人以上','3-5万人','1.5-3万人','0.8-1.5万人','0.5-0.8万人','0.5万人以下'],//学校人数
          schoolPutinNumber: ['9台及以上','5-8台','2-4台','1台'],//学校投放台数
          chargingStandard: ['1.5万元以上','0.8-1.5万元','0.5-0.8万元','0.5万元以下'],//学费收费标准
          sexRatio: ['女性多','平均','男性多'],//男女比例
          livingExpenses: ['1000元及以下','1000-2000元','2000-3000元','3000元及以上'],//每个月生活费
          intramuralCommerce: ['餐饮不便，商业萧条','商业发达，餐饮便利'],//校内商业
          lessThanThreeThousand: ['Y','N'],//在校人数是否少于三千
          buildingAttribute: ['主食堂','外包食堂/小食堂','主教学楼','辅助性教学楼','800人以上宿舍','800人以下宿舍'],//楼宇属性
          buildingArea: ['2200平米以上','1500-2200平米','800-1500平米','800平米以下'],//楼宇占地面积
          buildingHeight: ['9层级以上','5-8层','4层及以下'],//楼宇层高
        },
        company: {
          officeProperty: ['商住 商用率低于90%','商住 商用率高于90%','丙级写字楼','乙级写字楼','甲级写字楼','超甲级写字楼'],//写字楼属性
          hallArea: ['150平米以下','150-300平米','300-500平米','500-1000平米','1000平米以上'],//大堂面积
          lobbyHigh: ['1层','两层及以上'],//大堂挑高
          facadeMaterial: ['普通涂料','面砖／瓷砖／铝板','玻璃幕／大理石'],//外立面材料
          groundFloor: ['地砖／普通地毯','天然石材／高级地毯'],//大堂地面
          airConditioner: ['非中央空调','公共区域中央空调','全部中央空调'],//空调
          elevatorsNumber: ['6部及以上','4-5部','3部','2部及以下'],//电梯数量
          sameLayer: ['Y','N'],//公司是否在同层
          companyNature: ['IT互联网／事务所','其他各类','文化传媒类','贸易零售类'],//公司性质
          officeRent: ['12元以上','8.1元－12元','5.1元－8元','2.1元-5元','2元以下'],//办公室租金
          overTime: ['Y','N'],//员工是否经常加班
          onDutyMajority: ['Y','N'],//坐班人员是否占大多数
          servingCoffee: ['Y','N'],//是否提供咖啡
          servingAfternoonTea: ['Y','N'],//是否提供下午茶
          selfServiceEquipment: ['Y','N'],//是否有其他自助设备
        },
        hospital: {
          hospitalType: ['三级甲等','三级以上','二级以上'],//医院类型
          hospitalBusiness: ['餐饮不便，商业萧条','商业发达，餐饮便利'],//院内商业
          buildingAttribute: ['门诊大楼','住院部','行政楼','职工食堂','其他配套性楼宇'],//楼宇属性
          buildingHeight: ['9层级以上','5-8层','4层及以下'],//楼宇层高
        }
      }
    }
  </script>
  <script type=text/javascript src=/point-evaluation/js/manifest.9376812a435c03479edf.js></script>
  <script type=text/javascript src=/point-evaluation/js/vendor.39d746fcd22631cdbd59.js></script>
  <script type=text/javascript src=/point-evaluation/js/app.cc2dafe054b1bf0c6f56.js></script>
</body>
</html>
