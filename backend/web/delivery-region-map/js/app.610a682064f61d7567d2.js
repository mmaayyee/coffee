webpackJsonp([1],{"0AzE":function(e,t){},"4O/0":function(e,t){},CuRo:function(e,t){},NHnr:function(e,t,i){"use strict";Object.defineProperty(t,"__esModule",{value:!0});i("0AzE"),i("CuRo");var n=i("IvJb"),o=i("mcEN"),s=i.n(o),a=(i("qc+8"),{render:function(){var e=this.$createElement,t=this._self._c||e;return t("div",{attrs:{id:"app"}},[t("router-view")],1)},staticRenderFns:[]});var r=i("C7Lr")({name:"App"},a,!1,function(e){i("v/yU")},null,null).exports,l=i("zO6J"),u=i("3cXf"),c=i.n(u),d=(i("aozt"),void 0),g=void 0,m={name:"deliveryRegionMap",props:{setRegionData:{type:Number},regionId:{type:String},regionData:{type:String},initFlag:{type:Boolean}},data:function(){return{myRegionId:this.regionId,myRegionData:this.regionData,allBuildList:[],allBuildListData:[],build:"",currentBuildingId:"",preBuildingId:"",markersList:[],infoList:[],city:"1",showHideMarkersCheck:!0}},computed:{},mounted:function(){this.init()},methods:{init:function(){var e=this;window.parent.onscroll=function(t){e.scrollMsg()};var t=new qq.maps.LatLng(40.021759,116.453278);d=new qq.maps.Map(document.getElementById("container"),{center:t,zoom:14}),this.getAllBuildingsList(this.city)},getOtherRegions:function(){var e=this;console.log("delivery_region_id..",this.myRegionId);var t=null==this.myRegionId?0:this.myRegionId;$.ajax({type:"POST",url:rootCoffeeUrl+"delivery-api/region-map.html",dataType:"json",data:{delivery_region_id:t},success:function(t){console.log("获取所有区域地图api..",t),"success"==t.status?t.data.forEach(function(e,t){if(2==e.status){var i=[];e.coverage_range.forEach(function(e,t){var n=e.lat,o=e.lng;i.push(new qq.maps.LatLng(n,o))});var n=Math.floor(255*Math.random()),o=Math.floor(255*Math.random()),s=Math.floor(255*Math.random());new qq.maps.Polygon({fillColor:new qq.maps.Color(n,o,s,.35),map:d,path:i,strokeColor:"#666666",strokeWeight:1})}}):e.alertMsg(t.msg)},error:function(e,t){console.log("接口错误",e)}})},getAllBuildingsList:function(e){var t=this;$.ajax({type:"POST",url:rootCoffeeUrl+"delivery-api/get-build-by-city.html",dataType:"json",data:{province:"北京市"},success:function(e){console.log("获取城市点位列表api..",e),"success"==e.status?t.initBuildings(e.data):t.alertMsg(e.msg)},error:function(e,t){console.log("接口错误,",e)}})},initBuildings:function(e){var t=this;this.allBuildList=e,this.allBuildListData=e.map(function(e,t){return{value:t,label:e.name}}),this.allBuildList.forEach(function(e,i){var n=new qq.maps.LatLng(e.latitude,e.longitude),o=new qq.maps.Marker({position:n,map:d}),s=new qq.maps.InfoWindow({map:d,position:n,content:e.name});o.fixed=!1,t.markersList.push(o),t.infoList.push(s),qq.maps.event.addListener(o,"mouseover",function(){s.open()}),qq.maps.event.addListener(o,"mouseout",function(){o.fixed||s.close()})})},showHideMarkers:function(){this.showHideMarkersCheck?this.markersList.forEach(function(e){e.setVisible(!0)}):this.markersList.forEach(function(e){e.setVisible(!1)})},setEditMpaData:function(e){console.log("setEditMpaData");var t=JSON.parse(e);this.setPolygon(t)},getBuildList:function(){var e=this,t=[],i=g.getPath().elems,n=i.length,o=i.map(function(e){return e.lat}),s=i.map(function(e){return e.lat});s.sort(function(e,t){return e-t});var a=i.map(function(e){return e.lng}),r=i.map(function(e){return e.lng});r.sort(function(e,t){return e-t});var l=s[0],u=s[n-1],c=r[0],d=r[n-1];return this.allBuildList.forEach(function(i,s){var r=Number(i.latitude),g=Number(i.longitude);!(r<l||r>u||g<c||g>d)&&e.pnpoly(n,o,a,r,g)&&t.push(i.name)}),t},pnpoly:function(e,t,i,n,o){for(var s=!1,a=0,r=e-1;a<e;r=a++)i[a]>o!=i[r]>o&&n<(t[r]-t[a])*(o-i[a])/(i[r]-i[a])+t[a]&&(s=!s);return s},saveRegion:function(){if(0==this.allBuildList.length)return this.alertMsg("没有获取到点位列表！"),!1;if(!g)return this.alertMsg("没有可保存的区域！"),!1;var e=this.getPolygonPath();if(console.log("regionData.length..",JSON.parse(e).length),JSON.parse(e).length>200)return this.alertMsg("区域连接点超过200个，无法保存！"),!1;var t=this.getBuildList();if(console.log("build_list..",t),0==t.length)return this.alertMsg("区域内没有包含点位!"),!1;g&&this.removeRegion(),this.$emit("mapdata",{type:"save",region:e,build_list:t})},cancelRegion:function(){var e=this;g?this.$confirm("如果退出，您更改的内容将会丢失。","还未保存对配送区域的修改，确定退出吗?",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then(function(){e.removeRegion(),e.$emit("mapdata",{type:"cancel"})}).catch(function(){}):this.$emit("mapdata",{type:"cancel"})},removeTheRegion:function(){var e=this;console.log("remove,",g),g?this.$confirm("如果确定，您更改的内容将会丢失。","当前配送区域未保存，确定要删除吗?",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then(function(){e.removeRegion()}).catch(function(){}):this.alertMsg("没有可删除的区域")},removeRegion:function(){console.log("remove polygon"),g.setMap(null),g=null},addRegion:function(){if(g)return this.alertMsg("请勿重复添加区域，如需添加新区域，请删除当前区域。"),!1;if(console.log("this.build..",this.build),""===this.build)return this.alertMsg("请选择点位","warning"),!1;var e=Number(this.allBuildList[Number(this.currentBuildingId)].latitude),t=Number(this.allBuildList[Number(this.currentBuildingId)].longitude),i=[{lat:e-.015,lng:t-.02},{lat:e+.015,lng:t-.02},{lat:e+.015,lng:t+.02},{lat:e-.015,lng:t+.02}];this.setPolygon(i)},setPolygon:function(e){var t=[];e.forEach(function(e,i){var n=e.lat,o=e.lng;t.push(new qq.maps.LatLng(n,o))}),setTimeout(function(){var e=Math.floor(255*Math.random()),i=Math.floor(255*Math.random()),n=Math.floor(255*Math.random()),o=(g=new qq.maps.Polygon({editable:!0,fillColor:new qq.maps.Color(e,i,n,.35),map:d,path:t,strokeColor:"#000000",strokeDashStyle:"dash",strokeWeight:1})).getBounds();d.fitBounds(o)},500)},mapMoveToBuilding:function(){if(""===this.build)return!1;""!==this.preBuildingId&&(this.infoList[Number(this.preBuildingId)].close(),this.markersList[Number(this.preBuildingId)].fixed=!1),this.currentBuildingId=this.build;var e=this.allBuildList[Number(this.build)].latitude,t=this.allBuildList[Number(this.build)].longitude;d.panTo(new qq.maps.LatLng(e,t)),this.infoList[Number(this.build)].open(),this.markersList[Number(this.build)].fixed=!0,this.preBuildingId=this.build},getPolygonPath:function(){return c()(g.getPath().elems)},alertMsg:function(e,t){this.scrollMsg();var i=t||"error";this.$message({message:e,duration:3600,type:i})},scrollMsg:function(){if(self!=top){var e=window.parent.document.documentElement.scrollTop||window.parent.document.body.scrollTop-50,t=document.getElementsByClassName("el-message")[0];t&&(t.style.cssText="top: "+e+"px;z-index:1000;")}}},watch:{setRegionData:function(e){e>0&&this.setEditMpaData(this.myRegionData)},regionId:function(e){this.myRegionId=e},regionData:function(e){this.myRegionData=e},initFlag:function(e){1==e&&this.getOtherRegions()}}},h={render:function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("div",{staticClass:"content-body"},[i("el-row",{attrs:{gutter:10}},[i("el-col",{attrs:{span:18}},[i("el-select",{staticStyle:{width:"100px"},model:{value:e.city,callback:function(t){e.city=t},expression:"city"}},[i("el-option",{attrs:{label:"北京市",value:"1"}})],1),e._v(" "),i("el-select",{attrs:{filterable:"",placeholder:"请选择"},on:{change:e.mapMoveToBuilding},model:{value:e.build,callback:function(t){e.build=t},expression:"build"}},e._l(e.allBuildListData,function(e){return i("el-option",{key:e.value,attrs:{label:e.label,value:e.value}})})),e._v(" "),i("el-button",{attrs:{type:"primary",size:"small"},on:{click:e.addRegion}},[e._v("添加配送区域")]),e._v(" "),i("el-button",{attrs:{type:"primary",size:"small"},on:{click:e.removeTheRegion}},[e._v("删除配送区域")]),e._v(" "),i("el-checkbox",{on:{change:e.showHideMarkers},model:{value:e.showHideMarkersCheck,callback:function(t){e.showHideMarkersCheck=t},expression:"showHideMarkersCheck"}},[e._v("显示设备标记")])],1),e._v(" "),i("el-col",{attrs:{span:6}},[i("el-button",{staticStyle:{float:"right"},attrs:{type:"primary",size:"small"},on:{click:e.saveRegion}},[e._v("保存")]),e._v(" "),i("el-button",{staticStyle:{float:"right","margin-right":"5px"},attrs:{type:"primary",size:"small"},on:{click:e.cancelRegion}},[e._v("取消")])],1)],1),e._v(" "),i("div",{attrs:{id:"container"}})],1)},staticRenderFns:[]};var p={data:function(){return{homeShow:!0,regionMapShow:!1,addBtnShow:!1,editBtnShow:!1,mapInitFlag:!1,setRegionData:0,deliverRegionId:"",labelPosition:"left",regionForm:{regionName:"",businessStatus:"",businessTime:["08:00","18:00"],minConsum:""},rules:{regionName:[{required:!0,message:"请输入名称",trigger:"blur"},{min:2,max:50,message:"长度在 2 到 50 个字符",trigger:"blur"},{validator:function(e,t,i){setTimeout(function(){new RegExp("[`~!@#$^&*()=|{}':;',\\[\\].<>/?~！@#￥……&*（）——|{}【】‘；：”“'。，、？]").test(t)?i(new Error("不能有特殊字符")):i()},200)},trigger:"blur"}],businessStatus:[{required:!0,message:"请选择类型",trigger:"change"}],businessTime:[{required:!0,message:"请选择时间",trigger:"change"}],minConsum:[{required:!0,message:"请输入起送价格",trigger:"blur"},{validator:function(e,t,i){setTimeout(function(){isNaN(Number(t))?i(new Error("只允许输入数字")):i()},200)},trigger:"blur"}]},homeType:"",mapEditble:"",buildList:[],personList:[],personListData:[],coverageRange:"",submitBtnDisabled:!1}},computed:{},mounted:function(){this.init()},methods:{init:function(){var e=this;window.parent.onscroll=function(t){e.scrollMsg()},this.deliverRegionId=this.getUrlParam("id"),this.mapInitFlag=!0,null==this.deliverRegionId?this.addBtnShow=!0:$.ajax({type:"POST",url:rootCoffeeUrl+"delivery-api/region-info.html",dataType:"json",data:{delivery_region_id:this.deliverRegionId},success:function(t){console.log("获取区域详情api..",t),"success"==t.status?e.setRegionDetail(t.data):e.alertMsg(t.msg)},error:function(e,t){console.log("接口错误",e)}}),$.ajax({type:"POST",url:rootCoffeeUrl+"delivery-api/get-person-list.html",dataType:"json",data:{delivery_region_id:null==this.deliverRegionId?"":this.deliverRegionId},success:function(t){console.log("获取所有配送员名单api..",t),e.personListData=t.map(function(e){return{label:e.person_name,value:e.person_id}}),console.log(e.personListData)},error:function(e,t){console.log("接口错误",e)}})},setRegionDetail:function(e){var t=this;e.coverage_range.length>0&&(this.editBtnShow=!0,this.coverageRange=c()(e.coverage_range)),this.buildList=e.build_list.map(function(e){return e.name}),this.regionForm.regionName=e.region_name,this.regionForm.businessStatus=String(e.business_status),this.regionForm.businessTime=[e.start_time,e.end_time],this.regionForm.minConsum=e.min_consum,this.personList=e.person_list.map(function(e){return{id:e.person_id,guid:t.guid()}})},getMapData:function(e){console.log(e),this.regionMapShow=!1,this.homeShow=!0,"save"==e.type&&(console.log("this.addBtnShow..",this.addBtnShow),this.addBtnShow&&(this.addBtnShow=!1,this.editBtnShow=!0),this.coverageRange=e.region,this.buildList=e.build_list)},addPerson:function(){this.personList.push({id:"",guid:this.guid()})},deletePerson:function(e){this.personList.splice(e,1)},addMap:function(){this.regionMapShow=!0,this.homeShow=!1},editMap:function(){this.regionMapShow=!0,this.homeShow=!1,this.setRegionData+=1},submitForm:function(){var e=this;this.$refs.regionForm.validate(function(t){if(!t)return e.alertMsg("请填写表单内容"),!1;""==e.coverageRange?e.alertMsg("请添加配送区域"):0==e.personList.length?e.alertMsg("请添加配送员"):e.personList.some(function(e){return""==e.id})?e.alertMsg("请设置配送员"):e.checkRepeatPerson()?e.alertMsg("配送员有重复"):e.saveRegionData()})},saveRegionData:function(){var e=this;this.submitBtnDisabled=!0;var t={delivery_region_id:null==this.deliverRegionId?"":this.deliverRegionId,region_name:this.regionForm.regionName,start_time:this.regionForm.businessTime[0],end_time:this.regionForm.businessTime[1],min_consum:this.regionForm.minConsum,business_status:this.regionForm.businessStatus,person_info:this.personList.map(function(e){return e.id}),coverage_range:this.coverageRange};console.log("saveData..",t),$.ajax({type:"POST",url:rootCoffeeUrl+"delivery-api/region-create.html",dataType:"json",data:t,success:function(t){console.log("保存区域详情..",t),"success"==t.status?window.setTimeout(function(){window.location.href="/delivery-region/index"},1e3):(e.alertMsg(t.msg),e.submitBtnDisabled=!1)},error:function(t,i){console.log("接口错误",t),e.submitBtnDisabled=!1}})},checkRepeatPerson:function(){var e=!1,t=this.personList.map(function(e){return e.id}),i=c()(t);console.log(i);for(var n=0;n<t.length;n++)if(i.indexOf('"'+String(t[n])+'"')!=i.lastIndexOf('"'+String(t[n])+'"')){e=!0;break}return console.log(e),e},alertMsg:function(e,t){this.scrollMsg();var i=t||"error";this.$message({message:e,duration:3600,type:i})},scrollMsg:function(){if(self!=top){var e=window.parent.document.documentElement.scrollTop||window.parent.document.body.scrollTop-50,t=document.getElementsByClassName("el-message")[0];t&&(t.style.cssText="top: "+e+"px;z-index:1000;")}},guid:function(){return"xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g,function(e){var t=16*Math.random()|0;return("x"==e?t:3&t|8).toString(16)})},getUrlParam:function(e){var t=new RegExp("(^|&)"+e+"=([^&]*)(&|$)","i"),i=window.location.search.substr(1).match(t);return null!=i?decodeURI(i[2]):null}},components:{vDeliveryRegionMap:i("C7Lr")(m,h,!1,function(e){i("sWSt")},null,null).exports}},f={render:function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("div",{staticClass:"content-body"},[i("p",{staticClass:"line-title"},[e._v("配送区域详情")]),e._v(" "),i("div",{directives:[{name:"show",rawName:"v-show",value:e.homeShow,expression:"homeShow"}]},[i("el-form",{ref:"regionForm",attrs:{"label-position":e.labelPosition,model:e.regionForm,rules:e.rules,size:"small","label-width":"120px"}},[i("el-row",{attrs:{gutter:10}},[i("el-col",{attrs:{span:10}},[i("el-form-item",{attrs:{label:"配送区域名称",prop:"regionName"}},[i("el-input",{model:{value:e.regionForm.regionName,callback:function(t){e.$set(e.regionForm,"regionName",t)},expression:"regionForm.regionName"}})],1)],1)],1),e._v(" "),i("el-row",{attrs:{gutter:10}},[i("el-col",{attrs:{span:10}},[i("el-form-item",{attrs:{label:"营业状态",prop:"businessStatus"}},[i("el-select",{attrs:{placeholder:"请选择",clearable:""},model:{value:e.regionForm.businessStatus,callback:function(t){e.$set(e.regionForm,"businessStatus",t)},expression:"regionForm.businessStatus"}},[i("el-option",{attrs:{value:"1",label:"正常"}}),e._v(" "),i("el-option",{attrs:{value:"2",label:"暂停"}})],1)],1)],1)],1),e._v(" "),i("el-row",{attrs:{gutter:10}},[i("el-col",{attrs:{span:8}},[i("el-form-item",{attrs:{label:"营业时间",prop:"businessTime"}},[i("el-time-picker",{attrs:{"is-range":"","range-separator":"至","start-placeholder":"开始时间","end-placeholder":"结束时间",placeholder:"选择时间范围",format:"HH:mm","value-format":"HH:mm"},model:{value:e.regionForm.businessTime,callback:function(t){e.$set(e.regionForm,"businessTime",t)},expression:"regionForm.businessTime"}})],1)],1)],1),e._v(" "),i("el-row",{attrs:{gutter:10}},[i("el-col",{attrs:{span:10}},[i("el-form-item",{attrs:{label:"起送价格(元)",prop:"minConsum"}},[i("el-input",{model:{value:e.regionForm.minConsum,callback:function(t){e.$set(e.regionForm,"minConsum",t)},expression:"regionForm.minConsum"}})],1)],1)],1),e._v(" "),i("el-row",{attrs:{gutter:10}},[i("el-col",{staticStyle:{width:"120px"},attrs:{span:3}},[i("div",{staticClass:"label-txt"},[e._v("配送区域")])]),e._v(" "),e.addBtnShow?i("el-col",{attrs:{span:3}},[i("el-button",{attrs:{type:"primary",size:"small"},on:{click:e.addMap}},[e._v("新增")])],1):e._e(),e._v(" "),e.editBtnShow?i("el-col",{attrs:{span:6}},[e._v("\n          已添加配送区域 "),i("el-button",{attrs:{type:"primary",size:"small"},on:{click:e.editMap}},[e._v("编辑")])],1):e._e()],1),e._v(" "),i("el-row",{attrs:{gutter:10}},[i("el-col",{staticStyle:{width:"120px"},attrs:{span:3}},[i("div",{staticClass:"label-txt"},[e._v("配送员设置")])]),e._v(" "),i("el-col",{attrs:{span:6}},[i("el-button",{attrs:{type:"primary",size:"small"},on:{click:e.addPerson}},[e._v("添加配送员")])],1)],1),e._v(" "),e._l(e.personList,function(t,n){return i("div",{key:t.guid,staticClass:"pserson-list"},[e._v("\n        配送员:\n        "),i("el-select",{staticStyle:{width:"100px"},attrs:{filterable:"",placeholder:"请选择"},model:{value:t.id,callback:function(i){e.$set(t,"id",i)},expression:"item.id"}},e._l(e.personListData,function(e){return i("el-option",{key:e.value,attrs:{label:e.label,value:e.value}})})),e._v(" "),i("el-button",{attrs:{type:"primary",size:"small"},on:{click:function(t){e.deletePerson(n)}}},[e._v("删除")])],1)}),e._v(" "),i("p",{staticClass:"bulid-list-title"},[e._v("配送区域包含点位")]),e._v(" "),e._l(e.buildList,function(t,n){return i("div",{key:n,staticClass:"bulid-list"},[e._v("点位名称: "+e._s(t))])}),e._v(" "),i("el-form-item",{staticClass:"div-center",attrs:{size:"medium"}},[i("el-button",{attrs:{type:"primary",disabled:e.submitBtnDisabled},on:{click:e.submitForm}},[e._v("保存")])],1)],2)],1),e._v(" "),i("div",{directives:[{name:"show",rawName:"v-show",value:e.regionMapShow,expression:"regionMapShow"}],staticClass:"delivery-region-map"},[i("v-delivery-region-map",{attrs:{"set-region-data":e.setRegionData,"region-id":e.deliverRegionId,"region-data":e.coverageRange,"init-flag":e.mapInitFlag},on:{mapdata:e.getMapData}})],1)])},staticRenderFns:[]};var v=i("C7Lr")(p,f,!1,function(e){i("4O/0")},"data-v-29ec84df",null).exports;n.default.use(l.a);var b=new l.a({routes:[{path:"/",redirect:"/region"},{path:"/region",name:"DeliveryRegion",component:v}]});n.default.use(s.a),n.default.config.productionTip=!1,new n.default({el:"#app",router:b,components:{App:r},template:"<App/>"})},"qc+8":function(e,t){},sWSt:function(e,t){},"v/yU":function(e,t){}},["NHnr"]);