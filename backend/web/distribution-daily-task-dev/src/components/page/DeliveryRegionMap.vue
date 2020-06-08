<template>
  <div class="content-body">
    <el-row :gutter="10">
      <el-col :span="18">
        <el-select v-model="currentCity" style="width:100px;">
          <el-option label="北京市" value="1"></el-option>
        </el-select>
        <el-select v-model="build" filterable placeholder="请选择" @change="mapMoveToBuilding">
          <el-option v-for="item in allBuildListData"
            :label="item.label"
            :value="item.value"
            :key="item.value">
          </el-option>
        </el-select>
        <el-button type="primary" size="small" @click="addRegion">添加配送区域</el-button>
        <el-button type="primary" size="small" @click="removeTheRegion">删除配送区域</el-button>
        <el-checkbox v-model="showHideMarkersCheck" @change="showHideMarkers">显示设备标记</el-checkbox>
      </el-col>
      <el-col :span="6">
        <el-button type="primary" size="small" @click="saveRegion" style="float:right;">保存</el-button>
        <el-button type="primary" size="small" @click="cancelRegion" style="float:right;margin-right:5px;">取消</el-button>
      </el-col>
    </el-row>
    <div id="container"></div>
  </div>
</template>
<script>
/* eslint-disable */
let map,polygon;
export default {
  name:'deliveryRegionMap',
  props:{
    setRegionData: {type:Number},
    regionId:{type:String},
    regionData: {type:String},
    initFlag: {type:Boolean}
  },
  data() {
    return {
      myRegionId: this.regionId,
      myRegionData: this.regionData,
      allBuildList: [],
      allBuildListData: [],
      build: '',
      currentBuildingId:'',
      preBuildingId:'',
      markersList: [],
      infoList: [],
      currentCity: '1',
      showHideMarkersCheck: true,
      city: '',
      province: ''
    }
  },
  computed: {
  },
  mounted() {
    this.init();
  },
  methods: {
    init() {
      window.parent.onscroll = (e)=>{
        this.scrollMsg();
      }
      let myLatLng = new qq.maps.LatLng(40.021759,116.453278);
      map = new qq.maps.Map(document.getElementById("container"),{
          center: myLatLng,
          zoom: 14
      });

      /*let location = "30.674446,103.986841";//30.725574,103.949955   30.731225,103.956421
      let myLatLng = new qq.maps.LatLng(location.split(",")[0],location.split(",")[1]);
      map = new qq.maps.Map(document.getElementById("container"),{
          center: myLatLng,
          zoom: 14
      });
        let marker = new qq.maps.Marker({
            position: myLatLng,
            map: map
        });
        let data = {
          location: location,
          key: 'GBWBZ-ILMWK-LMZJU-AJ642-J36CZ-ODBH5',
          get_poi: 1
        };
        let url='https://apis.map.qq.com/ws/geocoder/v1/?';
        data.output="jsonp";
        $.ajax({
            type:"get",
            dataType:'jsonp',
            data:data,
            jsonp:"callback",
            jsonpCallback:"QQmap",
            url:url,
            success: mapData=> {
              console.log("mapData..",mapData);
              let info = new qq.maps.InfoWindow({
                map: map,
                position: myLatLng,
                content: mapData.result.formatted_addresses.recommend
              });
              info.open();
            },
            error : err=> {
              console.log("腾讯地图服务器错误");
            }
        });*/
      this.getAllBuildingsList(this.currentCity);
    },
    getOtherRegions() {
        console.log("delivery_region_id..",this.myRegionId);
        let region_id = this.myRegionId==null?0:this.myRegionId;
        $.ajax({
            type: "POST",
            url: rootCoffeeUrl+"delivery-api/region-map.html",
            dataType: "json",
            data: {delivery_region_id: region_id},
            success: data=>{
                console.log("获取所有区域地图api..",data);
                if(data.status=="success"){
                    data.data.forEach((item,index)=>{
                        if(item.status==2) {
                            let path = [];
                            item.coverage_range.forEach((item2,index2)=>{
                                let lat = item2.lat;
                                let lng = item2.lng;
                                path.push(new qq.maps.LatLng(lat, lng))

                            });

                            let m1 = Math.floor(Math.random()*255);
                            let m2 = Math.floor(Math.random()*255);
                            let m3 = Math.floor(Math.random()*255);
                            let polygon = new qq.maps.Polygon({
                                fillColor: new qq.maps.Color(m1, m2, m3, 0.35),
                                map: map,
                                path: path,
                                strokeColor: '#666666',
                                strokeWeight: 1
                            });
                        }
                    });
                } else {
                    this.alertMsg(data.msg);
                }
            },
            error: (xhr,type)=>{
                console.log("接口错误",xhr);
            }
        });
    },
    getAllBuildingsList(city) {
        $.ajax({
            type: "POST",
            url: rootCoffeeUrl+"delivery-api/get-build-by-city.html",
            dataType: "json",
            data: {province: "北京市"},
            success: data=>{
                console.log("获取城市点位列表api..",data);
                if(data.status=="success"){
                    // console.log(data.data);
                    this.initBuildings(data.data);
                } else {
                    this.alertMsg(data.msg);
                }
            },
            error: (xhr,type)=>{
                console.log("接口错误,",xhr);
            }
        });
    },
    initBuildings(data) {
      //以下一行为测试
        // this.allBuildList = data.slice(0,50);
        this.allBuildList = data;
        this.allBuildListData = data.map((item,index)=>{
          return {value:index,label:item.name};
        })
        this.allBuildList.forEach((item,index)=>{
            let point = new qq.maps.LatLng(item.latitude, item.longitude)
            let marker = new qq.maps.Marker({
                position: point,
                map: map
            });
            let info = new qq.maps.InfoWindow({
                map: map,
                position: point,
                content: item.name
            });
            marker.fixed = false;
            this.markersList.push(marker);
            this.infoList.push(info);
            qq.maps.event.addListener(marker, 'mouseover', ()=> {
                info.open();
            });
            qq.maps.event.addListener(marker, 'mouseout', ()=> {
                if(!marker.fixed) {
                    info.close();
                }
            });
        });
    },
    showHideMarkers() {
      if(this.showHideMarkersCheck) {
        this.markersList.forEach(item=>{
            item.setVisible(true);
        });
      } else {
        this.markersList.forEach(item=>{
            item.setVisible(false);
        });
      }
    },
    setEditMpaData(data) {
        console.log("setEditMpaData");
        let polygonPath = JSON.parse(data);
        this.setPolygon(polygonPath);
    },
    getBuildList() {
        let build_list = [];
        let polygonPath = polygon.getPath().elems;
        let polygonPathLen = polygonPath.length;
        let polygonX = polygonPath.map(item=>{
            return item.lat;
        });
        let polygonX2 = polygonPath.map(item=>{
            return item.lat;
        });
        polygonX2.sort((a,b)=>{
            return a-b;
        });
        let polygonY = polygonPath.map(item=>{
            return item.lng;
        });
        let polygonY2 = polygonPath.map(item=>{
            return item.lng;
        });
        polygonY2.sort((a,b)=>{
            return a-b;
        });
        let minX = polygonX2[0],maxX = polygonX2[polygonPathLen-1],minY = polygonY2[0],maxY = polygonY2[polygonPathLen-1]
        this.allBuildList.forEach((item,index)=>{
            let pointX = Number(item.latitude);
            let pointY = Number(item.longitude);
            let containFlag;
            if(pointX<minX||pointX>maxX||pointY<minY||pointY>maxY) {
                containFlag = false;
            } else {
                containFlag = this.pnpoly(polygonPathLen,polygonX,polygonY,pointX,pointY);
            }
            if(containFlag) {
                build_list.push(item.name);
            }
        });
        // console.log("build_list..",build_list)
        return build_list;
    },
    pnpoly(nvert, vertx, verty, testx, testy) {
        let i,j,c=false;
        for (let i = 0, j = nvert-1; i < nvert; j = i++) {
            if(((verty[i]>testy)!=(verty[j]>testy))&&(testx<(vertx[j]-vertx[i])*(testy-verty[i])/(verty[j]-verty[i])+vertx[i])) {
                c = !c;
            }
        }
        return c;
    },
    saveRegion() {
        if(this.allBuildList.length==0) {
            this.alertMsg("没有获取到点位列表！");
            return false;
        }
        if(!polygon) {
            this.alertMsg("没有可保存的区域！");
            return false;
        }
        // console.log(this.getPolygonPath());
        // let allBuildList = this.data.allBuildList;
        let regionData = this.getPolygonPath();
        console.log("regionData.length..",JSON.parse(regionData).length);
        if(JSON.parse(regionData).length>200){
            this.alertMsg("区域连接点超过200个，无法保存！");
            return false;
        }
        let build_list = this.getBuildList();
        console.log("build_list..",build_list);
        if(build_list.length==0) {
            this.alertMsg("区域内没有包含点位!");
            return false;
        }
        if(polygon) {
            this.removeRegion();
        }
        this.$emit("mapdata",{type:"save",region:regionData,build_list: build_list});
    },
    cancelRegion() {
        if(polygon) {
            this.$confirm('如果退出，您更改的内容将会丢失。', '还未保存对配送区域的修改，确定退出吗?', {
              confirmButtonText: '确定',
              cancelButtonText: '取消',
              type: 'warning'
            }).then(() => {
              this.removeRegion();
              this.$emit("mapdata",{type:"cancel"});
            }).catch(() => {
              //
            });
        } else {
            this.$emit("mapdata",{type:"cancel"});
        }
    },
    removeTheRegion(){
        console.log("remove,",polygon);
        if(polygon) {
            this.$confirm('如果确定，您更改的内容将会丢失。', '当前配送区域未保存，确定要删除吗?', {
              confirmButtonText: '确定',
              cancelButtonText: '取消',
              type: 'warning'
            }).then(() => {
              this.removeRegion();
            }).catch(() => {
              //
            });
        } else {
            this.alertMsg("没有可删除的区域")
        }
    },
    removeRegion() {
        console.log("remove polygon");
        polygon.setMap(null);
        polygon = null;
    },
    addRegion() {
        if(polygon) {
            this.alertMsg("请勿重复添加区域，如需添加新区域，请删除当前区域。");
            return false;
        }
        console.log("this.build..",this.build)
        if(this.build==="") {
            this.alertMsg("请选择点位","warning");
            return false;
        }
        const lat = Number(this.allBuildList[Number(this.currentBuildingId)].latitude);
        const lng = Number(this.allBuildList[Number(this.currentBuildingId)].longitude);
        let pathArr = [
            {lat:lat-0.015, lng:lng-0.02},
            {lat:lat+0.015, lng:lng-0.02},
            {lat:lat+0.015, lng:lng+0.02},
            {lat:lat-0.015, lng:lng+0.02}
        ];

        this.setPolygon(pathArr);
    },
    setPolygon(pathArr) {
        let path = [];
        pathArr.forEach((item,index)=>{
            let lat = item.lat;
            let lng = item.lng;
            path.push(new qq.maps.LatLng(lat, lng))

        });
        setTimeout(()=>{
            let m1 = Math.floor(Math.random()*255);
            let m2 = Math.floor(Math.random()*255);
            let m3 = Math.floor(Math.random()*255);
            polygon = new qq.maps.Polygon({
                editable: true,
                fillColor: new qq.maps.Color(m1, m2, m3, 0.35),
                map: map,
                path: path,
                strokeColor: '#000000',
                strokeDashStyle: 'dash',
                strokeWeight: 1
            });
            let bounds = polygon.getBounds();
            map.fitBounds(bounds);
        },500);
    },
    mapMoveToBuilding() {
        if(this.build==="") {
            return false;
        }
        if(this.preBuildingId!==""){
            this.infoList[Number(this.preBuildingId)].close();
            this.markersList[Number(this.preBuildingId)].fixed = false;
        }
        this.currentBuildingId = this.build;
        const lat = this.allBuildList[Number(this.build)].latitude;
        const lng = this.allBuildList[Number(this.build)].longitude;
        map.panTo(new qq.maps.LatLng(lat,lng));
        this.infoList[Number(this.build)].open();
        this.markersList[Number(this.build)].fixed = true;
        this.preBuildingId = this.build;
        //
        /*let data = {
          location: location,
          key: this.global_.globalData.qqMapKey,
          get_poi: 0
        };
        let url=this.global_.globalData.qqMapUrl;
        data.output="jsonp";
        $.ajax({
            type:"get",
            dataType:'jsonp',
            data:data,
            jsonp:"callback",
            jsonpCallback:"QQmap",
            url:url,
            success: mapData=> {
              console.log("mapData..",mapData);
            },
            error : err=> {
            }
        });*/
    },
    getPolygonPath(){
        // console.log(polygon.getPath())
        return JSON.stringify(polygon.getPath().elems);
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
    }
  },
  watch: {
    setRegionData(val){
      if(val>0){
        this.setEditMpaData(this.myRegionData);
      }
    },
    regionId(val) {
      this.myRegionId = val;
    },
    regionData(val) {
      // console.log("myRegionData..",val)
      this.myRegionData = val;
    },
    initFlag(val) {
      if(val==true) {
        this.getOtherRegions();
      }
    }
  }
}

</script>
<style>
#container{
    min-width:600px;
    min-height:767px;
}
#info{
    width:603px;
    padding-top:3px;
    overflow:hidden;
}
</style>
