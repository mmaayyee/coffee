webpackJsonp([1],{"/Hv2":function(t,e){},CuRo:function(t,e){},NHnr:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});a("CuRo");var r=a("IvJb"),s=a("t+b9"),n=a.n(s),l=(a("/Hv2"),{render:function(){var t=this.$createElement,e=this._self._c||t;return e("div",{attrs:{id:"app"}},[e("router-view")],1)},staticRenderFns:[]}),o=a("C7Lr")(null,l,!1,null,null,null).exports,i=a("zO6J"),c=a("aozt"),h=a.n(c),u=["#328e55","#d66e49","#be384b","#a7aa9d","#354b5e"],p=["#54c7f1","#3ab2de","#289eca","#198db8","#0a7da7","#016b93"],d={data:function(){return{listUrl:"",getMonthlyUserListUrl:rootCoffeeStieUrl+"monthly-report-api/monthly-user-list.html",monthlyUserListData:[],labelPosition:"right",form:{searchDateStart:"",searchDateEnd:""},searchDatePickerOption:{disabledDate:function(t){return t.getTime()>Date.now()}},exportDate:"",exportUrl:"/monthly-users/export?",showDateSearch:""}},filters:{formatDate:function(t){return String(t).substr(0,10)}},computed:{},mounted:function(){this.init()},methods:{init:function(){var t=this;this.showDateSearch=String(rootLogin),window.parent.onscroll=function(e){t.scrollMsg()},this.getMonthlyUserList()},trimPercent:function(t){return"%"==String(t).charAt(String(t).length-1)?Number(String(t).substr(0,String(t).length-1)):Number(t)},searchDate:function(){var t=new Date(this.form.searchDateStart);new Date(this.form.searchDateEnd)>=t?this.getMonthlyUserList([this.form.searchDateStart,this.form.searchDateEnd]):this.alertMsg("结束月必须大于等于开始月，请重新选择。")},exportExcel:function(){},goBack:function(){},clearCharts:function(){this.monthlyUserListData=[],this.initCharts()},getMonthlyUserList:function(t){var e=this,a=t?this.getMonthlyUserListUrl+"?start="+t[0]+"&end="+t[1]:this.getMonthlyUserListUrl;this.exportDate=t?"start="+t[0]+"&end="+t[1]:"",h.a.get(a).then(function(t){t.data&&"200"==t.data.code?(0==t.data.data.length&&e.alertMsg("没有数据，请选择其他日期"),e.monthlyUserListData=[],window.setTimeout(function(){e.monthlyUserListData=t.data.data,e.initCharts()},300)):(e.clearCharts(),e.alertMsg("获取月报用户数据错误"))}).catch(function(t){e.clearCharts(),e.alertMsg("月报用户接口错误")})},initCharts:function(){this.chartUsersGrowth(),this.chartNewUsersTotal(),this.chartNewRegisteredUser(),this.chartActiveUser(),this.chartPayFreeActiveUser()},chartUsersGrowth:function(){var t=echarts.init(document.getElementById("usersGrowth"));t.clear();for(var e=[],a=[[],[],[]],r=this.monthlyUserListData,s=0,n=r.length;s<n;s++)e.push(r[s].month),a[0].push(r[s].guest_user),a[1].push(r[s].registered_user),a[2].push(r[s].users_total);var l={title:{text:"用户增长",textStyle:{fontSize:16}},tooltip:{},legend:{data:["非注册用户","注册用户","总用户数"]},calculable:!0,yAxis:[{type:"value"}],xAxis:[{type:"category",data:e}],series:[{name:"非注册用户",type:"bar",stack:"总量",barMaxWidth:20,itemStyle:{normal:{label:{show:!0,position:"insideRight"}}},data:a[0]},{name:"注册用户",type:"bar",stack:"总量",barMaxWidth:20,itemStyle:{normal:{label:{show:!0,position:"insideRight"}}},data:a[1]},{name:"总用户数",type:"bar",barMaxWidth:20,itemStyle:{normal:{label:{show:!0}}},data:a[2]}]};t.setOption(l)},chartNewUsersTotal:function(){this.initSingleChart("monthlyUserListData","month","newUsersTotal","new_users_total","新增用户")},chartNewRegisteredUser:function(){this.initSingleChart("monthlyUserListData","month","newRegisteredUser","new_registered_user","新增注册用户")},chartActiveUser:function(){this.initSingleChart("monthlyUserListData","month","activeUser","active_user","活跃用户数")},chartPayFreeActiveUser:function(){this.initDoubleChart("monthlyUserListData","month","payFreeActiveUser","pay_active_user","free_active_user","付费、免费活跃人数","付费活跃人数","免费活跃人数")},initSingleChart:function(t,e,a,r,s){var n=echarts.init(document.getElementById(a));n.clear();for(var l=[],o=[],i=this[t],c=0,h=i.length;c<h;c++){l.push(i[c][e]);var u=this.trimPercent(i[c][r]);o.push(u)}var d=o.length>0?o.reduce(function(t,e){return t>e?t:e}):0,m={color:p,backgroundColor:"#fff",title:{text:s,textStyle:{fontSize:16}},tooltip:{},legend:{data:[s]},xAxis:{data:l},yAxis:{},series:[{name:s,type:"bar",barMaxWidth:30,data:o,itemStyle:{normal:{label:{show:!0},color:function(t){var e=Number(t.value);e=Math.max(0,e);var a=d>0?Math.floor(5*e/d):0;return p[a]}}}}]};n.setOption(m)},initDoubleChart:function(t,e,a,r,s,n,l,o){var i=echarts.init(document.getElementById(a));i.clear();for(var c=[],h=[[],[]],p=this[t],d=0,m=p.length;d<m;d++)c.push(p[d][e]),h[0].push(p[d][r]),h[1].push(p[d][s]);var f={color:u,backgroundColor:"#fff",title:{text:n,textStyle:{fontSize:16}},tooltip:{},legend:{data:[l,o]},xAxis:{data:c},yAxis:{},series:[{name:l,type:"bar",barMaxWidth:20,data:h[0],itemStyle:{normal:{label:{show:!0}}}},{name:o,type:"bar",barMaxWidth:20,data:h[1],itemStyle:{normal:{label:{show:!0}}}}]};i.setOption(f)},alertMsg:function(t,e){this.scrollMsg();var a=e||"error";this.$message({message:t,duration:3600,type:a})},scrollMsg:function(){if(self!=top){var t=window.parent.document.documentElement.scrollTop||window.parent.document.body.scrollTop-50,e=document.getElementsByClassName("el-message")[0];e&&(e.style.cssText="top: "+t+"px;z-index:1000;")}}},components:{}},m={render:function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"content-body"},["0"!=t.showDateSearch?a("el-form",{ref:"form",attrs:{"label-position":t.labelPosition,model:t.form,size:"small","label-width":"120px"}},[a("el-row",{attrs:{gutter:10}},["1"!=t.showDateSearch?a("el-col",{staticStyle:{width:"350px"},attrs:{span:6}},[a("span",{staticClass:"demonstration"},[t._v("选择检索月")]),t._v(" "),a("el-date-picker",{attrs:{type:"month",placeholder:"开始月",format:"yyyy-MM","value-format":"yyyy-MM","picker-options":t.searchDatePickerOption},model:{value:t.form.searchDateStart,callback:function(e){t.$set(t.form,"searchDateStart",e)},expression:"form.searchDateStart"}}),t._v(" 至\n      ")],1):t._e(),t._v(" "),"1"!=t.showDateSearch?a("el-col",{staticStyle:{width:"250px"},attrs:{span:6}},[a("el-date-picker",{attrs:{type:"month",placeholder:"结束月",format:"yyyy-MM","value-format":"yyyy-MM","picker-options":t.searchDatePickerOption},model:{value:t.form.searchDateEnd,callback:function(e){t.$set(t.form,"searchDateEnd",e)},expression:"form.searchDateEnd"}})],1):t._e(),t._v(" "),"1"!=t.showDateSearch?a("el-col",{attrs:{span:3}},[a("el-button",{attrs:{type:"primary",plain:""},on:{click:t.searchDate}},[t._v("检索")])],1):t._e(),t._v(" "),a("el-col",{attrs:{span:5}},["2"!=t.showDateSearch?a("a",{attrs:{href:t.exportUrl+t.exportDate}},[a("el-button",{attrs:{type:"primary",plain:""}},[t._v("导出")])],1):t._e()])],1)],1):t._e(),t._v(" "),a("el-table",{staticClass:"el-table-common",attrs:{data:t.monthlyUserListData,border:""}},[a("el-table-column",{attrs:{prop:"year",label:"年份"}}),t._v(" "),a("el-table-column",{attrs:{prop:"month",label:"月份"}}),t._v(" "),a("el-table-column",{attrs:{prop:"users_total",label:"总用户数"}}),t._v(" "),a("el-table-column",{attrs:{prop:"new_users_total",label:"新增用户"}}),t._v(" "),a("el-table-column",{attrs:{prop:"new_users_growth",label:"用户增长率"}}),t._v(" "),a("el-table-column",{attrs:{prop:"registered_user",label:"注册用户"}}),t._v(" "),a("el-table-column",{attrs:{prop:"new_registered_user",label:"新增注册用户"}}),t._v(" "),a("el-table-column",{attrs:{prop:"new_registered_growth",label:"注册用户增长率"}}),t._v(" "),a("el-table-column",{attrs:{prop:"guest_user",label:"非注册用户"}}),t._v(" "),a("el-table-column",{attrs:{prop:"active_user",label:"活跃人数"}}),t._v(" "),a("el-table-column",{attrs:{prop:"active_user_growth",label:"活跃增长率"}}),t._v(" "),a("el-table-column",{attrs:{prop:"pay_active_user",label:"付费活跃用户"}}),t._v(" "),a("el-table-column",{attrs:{prop:"pay_user_growth",label:"付费活跃增长率"}}),t._v(" "),a("el-table-column",{attrs:{prop:"free_active_user",label:"免费活跃人数"}})],1),t._v(" "),t._m(0)],1)},staticRenderFns:[function(){var t=this.$createElement,e=this._self._c||t;return e("div",[e("div",{staticClass:"charts",attrs:{id:"usersGrowth"}}),this._v(" "),e("div",{staticClass:"charts",attrs:{id:"newUsersTotal"}}),this._v(" "),e("div",{staticClass:"charts",attrs:{id:"newRegisteredUser"}}),this._v(" "),e("div",{staticClass:"charts",attrs:{id:"activeUser"}}),this._v(" "),e("div",{staticClass:"charts",attrs:{id:"payFreeActiveUser"}})])}]};var f=a("C7Lr")(d,m,!1,function(t){a("QIXU")},"data-v-10775718",null).exports;r.default.use(i.a);var v=new i.a({routes:[{path:"/",redirect:"/userlist"},{path:"/userlist",name:"MonthlyUserList",component:f}]});r.default.use(n.a),r.default.config.productionTip=!1,new r.default({el:"#app",router:v,components:{App:o},template:"<App/>"})},QIXU:function(t,e){}},["NHnr"]);
//# sourceMappingURL=app.c6f9f058be3ce109cde0.js.map