webpackJsonp([1],{NHnr:function(e,t,r){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var o=r("IvJb"),a=r("9ab0"),s=r.n(a),i=(r("tqWA"),{render:function(){var e=this.$createElement,t=this._self._c||e;return t("div",{attrs:{id:"app"}},[t("router-view")],1)},staticRenderFns:[]});var n=r("C7Lr")({name:"App"},i,!1,function(e){r("gChc")},null,null).exports,l=r("zO6J"),u=r("aozt"),d=r.n(u),c={data:function(){return{groupForm:{groupInfoArr:{leader_id:"",group_id:""}},groupInfo:rootInitData.groupInfo,groupNumber:rootInitData.groupNumber,managers:rootInitData.managers,isShowManagers:!1,isdisable:!1,groupNumberArr:[],leaderIdRules:{required:!0,message:"请选择组长",trigger:"change"},groupIdRules:{required:!0,validator:function(e,t,r){"0"===t&&r(new Error("请选择分组")),r()},trigger:"change"},scheduleInfo:rootInitData.scheduleInfo,scheduleDate:rootInitData.date,isChange:rootInitData.isChange,schedulingForm:{date:""},weeksArr:[],scheduleArr:[],saveScheduleData:[],isScheduleDisabled:!1,isShowScheduleInfo:!0,beforeChangeVal:null,afterChangeVal:null,orgId:rootOrgId}},mounted:function(){this.init(rootInitData.groupInfo)},filters:{splitString:function(e){if(e){var t="";switch(e=Number(e.split("-")[1])){case 1:t="班";break;case 2:t="休";break;default:t="请假"}return t}}},methods:{init:function(e){var t=this;window.parent.onscroll=function(e){t.scrollMsg()};for(var r={},o=0;o<this.groupNumber.length;o++)r.num=this.groupNumber[o][0],r.isdisabled=!1,this.groupNumberArr.push(r),r=[];this.setGroup(e),this.scheduleInfo?(this.setWeek(this.scheduleDate.year,this.scheduleDate.month),this.schedulingForm.date=this.scheduleDate.year+"-"+this.scheduleDate.month,console.log("date",this.schedulingForm.date),this.isEdit()):this.isShowScheduleInfo=!1},getLeader:function(e,t){for(var r=this.groupInfo,o=[],a=0;a<r.length;a++)1!=e&&r[a].leader_id==t&&(r[a].leader_id=""),1==r[a].is_leader&&o.push(r[a]);this.managers=o},setGroup:function(e){for(var t=e,r={},o=[],a=0;a<t.length;a++){var s=t[a].group_id;if(o.push(t[a]),t[a+1]){if(t[a].group_id!=t[a+1].group_id)r[s]=o,o=[];else if(o.length>=6&&0!=t[a].group_id){var i=t[a].group_id;this.groupNumberArr[i].isdisabled=!0}}else r[s]=o}this.groupForm.groupInfoArr=r},getGroup:function(e,t,r){for(var o=this.groupInfo,a=[],s=[],i=0;i<o.length;i++)o[i].userid==r&&(o[i].leader_id=""),1==t&&o[i].leader_id==r&&(o[i].leader_id=""),1==o[i].is_leader&&a.push(o[i]),o[i].group_id==e&&s.push(o[i]);if(s.length>=7&&0!=e){this.alertMsg("添加成功，现在"+e+"组已满员");for(var n=0;n<this.groupNumberArr.length;n++)n==e&&(this.groupNumberArr[n].isdisabled=!0)}this.managers=a},setIsDisabled:function(e,t){if(!0===e?this.beforeChangeVal=t:this.afterChangeVal=t,this.beforeChangeVal!=this.afterChangeVal){for(var r=[],o=0;o<this.groupInfo.length;o++)this.beforeChangeVal&&this.groupInfo[o].group_id==this.beforeChangeVal&&r.push(this.groupInfo[o]);var a=[];r.length<7&&1==this.groupNumberArr[this.beforeChangeVal].isdisabled&&(this.groupNumberArr[this.beforeChangeVal].isdisabled=!1,a=this.groupNumberArr[this.beforeChangeVal],this.groupNumberArr.splice(this.beforeChangeVal,1,a))}},saveGroupData:function(){var e=this,t=[];for(var r in e.groupForm.groupInfoArr)for(var o in e.groupForm.groupInfoArr[r])t.push(e.groupForm.groupInfoArr[r][o]);d.a.post(rootErpUrl+"/distribution-user/update-group",{groupInfo:t,org_id:e.orgId}).then(function(t){t.data?(e.alertMsg("保存成功","success"),e.groupInfo=t.data.groupInfo,e.groupNumber=t.data.groupNumber,e.managers=t.data.managers,e.scheduleInfo=t.data.scheduleInfo,e.setGroup(e.groupInfo)):e.alertMsg("保存失败,请稍后重试","error")}).catch(function(t){e.alertMsg(t,"error")})},submitForm:function(e){var t=this;this.$refs[e].validate(function(e,r){if(!e){for(var o in r){document.getElementById(o).scrollIntoView();break}return!1}t.saveGroupData()})},getWeek:function(e,t,r){var o=new Date;o.setYear(e),o.setMonth(t-1),o.setDate(r);return["日","一","二","三","四","五","六"][o.getDay()]},setWeek:function(e,t){this.weeksArr=[];for(var r=0;r<this.scheduleDate.days;r++){var o=this.getWeek(e,t,r+1);this.weeksArr.push(o)}},getScheduleData:function(){var e=this,t=e.schedulingForm.date;d.a.get(rootErpUrl+"/distribution-user/get-schedule?date="+t+"&org_id="+e.orgId).then(function(t){t.data?t.data.scheduleInfo.length>0?(e.isShowScheduleInfo=!0,e.scheduleDate=t.data.date,e.setWeek(e.scheduleDate.year,e.scheduleDate.month),e.scheduleInfo=t.data.scheduleInfo,e.isChange=t.data.isChange,e.isEdit()):e.isShowScheduleInfo=!1:e.alertMsg("获取数据失败,请稍后重试","error")}).catch(function(t){e.alertMsg(t,"error")})},editScheduleStatus:function(e,t,r,o){var a=this.schedulingForm.date;a+=o+1<10?"-0"+(o+1):"-"+(o+1);var s=new Date(a).getTime(),i=(new Date).getTime();if(console.log("date",s),console.log("dateNow",i),i-s<0){this.isScheduleDisabled=!1;var n=r.schedule[o].split("-")[0],l=Number(r.schedule[o].split("-")[1]);switch(l){case 1:l=2;break;case 2:l=3;break;default:l=1}r.schedule[o]=n+"-"+l,this.scheduleInfo.splice(t,1,r)}},submit:function(e){var t=this,r={};r.date=this.schedulingForm.date,r.scheduleInfo=this.scheduleInfo,r.org_id=this.orgId,d.a.post(rootErpUrl+"/distribution-user/update-schedule",r,{"Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}).then(function(e){e.data?(t.alertMsg("保存成功","success"),t.scheduleInfo=e.data.scheduleInfo):t.alertMsg("保存失败,请稍后重试","error")}).catch(function(e){t.alertMsg(e,"error")})},alertMsg:function(e,t){this.$message({message:e,duration:3e3,type:t}),this.scrollMsg()},scrollMsg:function(){if(self!=top){var e=window.parent.document.documentElement.scrollTop||window.parent.document.body.scrollTop-50,t=document.getElementsByClassName("el-message")[0];t&&(t.style.cssText="top: "+e+"px;z-index:1000;")}},goBack:function(){window.location="/distribution-user/index"},isEdit:function(){this.isChange,this.isScheduleDisabled=!1}}},h={render:function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",{staticClass:"content-body"},[r("div",{staticClass:"group-form"},[r("el-form",{ref:"groupForm",attrs:{model:e.groupForm}},[r("div",{staticClass:"title"},[e._v("编组管理")]),e._v(" "),r("el-row",e._l(e.groupForm.groupInfoArr,function(t,o){return r("el-col",{key:t.index,attrs:{span:12}},[r("table",{staticClass:"el-table"},[r("thead",[r("tr",[r("th",{staticClass:"name"},[e._v("姓名 ")]),e._v(" "),r("th",[e._v("职位")]),e._v(" "),r("th",[e._v("组长")]),e._v(" "),r("th",[e._v("组别")])])]),e._v(" "),r("tbody",{staticClass:"el-table__body"},e._l(e.groupForm.groupInfoArr[o],function(t,a){return r("tr",{key:t.index,staticClass:"el-table__row"},[r("td",[e._v("\n                  "+e._s(t.name)+"\n                ")]),e._v(" "),r("td",[r("el-form-item",[r("el-select",{on:{change:function(r){e.getLeader(r,t.userid)}},model:{value:t.is_leader,callback:function(r){e.$set(t,"is_leader",r)},expression:"item.is_leader"}},[r("el-option",{attrs:{label:"运维专员",value:"2"}}),e._v(" "),r("el-option",{attrs:{label:"运维组长",value:"1"}})],1)],1)],1),e._v(" "),r("td",["1"==t.is_leader?r("el-form-item",{attrs:{prop:"groupInfoArr."+o+"."+a+".leader_id","show-message":!1}},[r("el-select",{attrs:{disabled:""},model:{value:t.userid,callback:function(r){e.$set(t,"userid",r)},expression:"item.userid"}},e._l(e.managers,function(o,a){return t.group_id==o.group_id?r("el-option",{key:a,attrs:{label:o.name,value:o.userid}}):e._e()}))],1):r("el-form-item",{attrs:{prop:"groupInfoArr."+o+"."+a+".leader_id",rules:e.leaderIdRules,id:"groupInfoArr."+o+"."+a+".leader_id"}},[r("el-select",{model:{value:t.leader_id,callback:function(r){e.$set(t,"leader_id",r)},expression:"item.leader_id"}},e._l(e.managers,function(o,a){return t.group_id==o.group_id?r("el-option",{key:a,attrs:{label:o.name,value:o.userid}}):e._e()}))],1)],1),e._v(" "),r("td",[r("el-form-item",{attrs:{prop:"groupInfoArr."+o+"."+a+".group_id",rules:e.groupIdRules,id:"groupInfoArr."+o+"."+a+".group_id"}},[r("el-select",{on:{change:function(r){e.getGroup(r,t.is_leader,t.userid)},"visible-change":function(r){e.setIsDisabled(r,t.group_id)}},model:{value:t.group_id,callback:function(r){e.$set(t,"group_id",r)},expression:"item.group_id"}},e._l(e.groupNumberArr,function(e,t){return r("el-option",{key:t,attrs:{disabled:e.isdisabled,label:e.num,value:t+""}})}))],1)],1)])}))])])})),e._v(" "),r("el-form-item",[r("el-button",{attrs:{type:"primary"},on:{click:function(t){e.submitForm("groupForm")}}},[e._v("编组生效")])],1)],1)],1),e._v(" "),r("div",{staticClass:"scheduling-form"},[r("el-row",[r("el-col",{attrs:{span:12}},[r("div",{staticClass:"title"},[e._v("排班管理")])]),e._v(" "),r("el-col",{attrs:{span:12}},[r("span",{staticClass:"demonstration"},[e._v("日期：")]),e._v(" "),r("el-date-picker",{attrs:{editable:!1,type:"month","value-format":"yyyy-MM",placeholder:"选择日期",clearable:!1},on:{change:e.getScheduleData},model:{value:e.schedulingForm.date,callback:function(t){e.$set(e.schedulingForm,"date",t)},expression:"schedulingForm.date"}})],1)],1),e._v(" "),r("el-form",{directives:[{name:"show",rawName:"v-show",value:e.isShowScheduleInfo,expression:"isShowScheduleInfo"}],attrs:{model:e.schedulingForm}},[r("el-table",{staticStyle:{width:"95%"},attrs:{data:e.scheduleInfo,"max-height":"400"}},[r("el-table-column",{attrs:{fixed:"",label:"组",prop:"group_id"}}),e._v(" "),r("el-table-column",{attrs:{fixed:"",label:"姓名",prop:"name"}}),e._v(" "),e._l(e.weeksArr,function(t,o){return r("el-table-column",{key:o+1,attrs:{label:1+o+""}},[r("el-table-column",{attrs:{label:""+t},scopedSlots:e._u([{key:"default",fn:function(t){return[r("el-button",{attrs:{type:"text",disabled:e.isScheduleDisabled},on:{click:function(r){e.editScheduleStatus(r,t.$index,t.row,o)}},model:{value:t.row.schedule[o],callback:function(r){e.$set(t.row.schedule,o,r)},expression:"scope.row.schedule[index]"}},[e._v(e._s(e._f("splitString")(t.row.schedule[o])))])]}}])})],1)})],2),e._v(" "),r("el-form-item",[r("el-button",{staticClass:"go-back",attrs:{type:"primary"},on:{click:e.goBack}},[e._v("返回")]),e._v(" "),r("el-button",{attrs:{type:"primary"},on:{click:function(t){e.submit("schedulingForm")}}},[e._v("排班生效")])],1)],1),e._v(" "),r("div",{directives:[{name:"show",rawName:"v-show",value:!e.isShowScheduleInfo,expression:"!isShowScheduleInfo"}],staticClass:"tip-txt"},[e._v("暂无排班数据")])],1)])},staticRenderFns:[]};var g=r("C7Lr")(c,h,!1,function(e){r("U/1x")},"data-v-59c9c857",null).exports;o.default.use(l.a);var p=new l.a({routes:[{path:"/",name:"management",component:g}]});o.default.use(s.a),o.default.prototype.$http=d.a,o.default.config.productionTip=!1,new o.default({el:"#app",router:p,components:{App:n},template:"<App/>"})},"U/1x":function(e,t){},gChc:function(e,t){},tqWA:function(e,t){}},["NHnr"]);