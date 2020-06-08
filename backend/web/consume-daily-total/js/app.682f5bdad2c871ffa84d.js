webpackJsonp([1],{CuRo:function(t,a){},NHnr:function(t,a,e){"use strict";Object.defineProperty(a,"__esModule",{value:!0});e("CuRo");var s=e("MVMM"),i=e("ez6v"),r=e.n(i),l=(e("qvMO"),{render:function(){var t=this.$createElement,a=this._self._c||t;return a("div",{attrs:{id:"app"}},[a("router-view")],1)},staticRenderFns:[]}),n=e("vSla")(null,l,!1,null,null,null).exports,o=e("zO6J"),_=e("aozt"),c=e.n(_),v=["#328e55","#d66e49","#be384b","#a7aa9d","#354b5e"],u=["#54c7f1","#3ab2de","#289eca","#198db8","#0a7da7","#016b93"],d={data:function(){return{listUrl:"",consumeDailyTotalData:{list:[],MTD:{}},consumeDailyTotalUrl:rootCoffeeStieUrl+"consume-daily-total-api/get-consume-daily-total-list.html",userGrowthAndActivityData:{list:[],MTD:{}},userGrowthAndActivityUrl:rootCoffeeStieUrl+"consume-daily-total-api/get-user-growth-and-activity.html",labelPosition:"right",form:{searchDate:""},searchDatePickerOption:{disabledDate:function(t){return t.getTime()>Date.now()}},getUserSleepListData:[],getUserSleepListUrl:rootCoffeeStieUrl+"consume-daily-total-api/get-user-sleep-list.html",getUserRecallListData:[],getUserRecallListUrl:rootCoffeeStieUrl+"consume-daily-total-api/get-user-recall-list.html",getUserRetainListData:{head:[],head0:[],list:[]},getUserRetainListUrl:rootCoffeeStieUrl+"consume-daily-total-api/get-user-retain-list.html",exportDate:"",exportUrl:"/consume-daily-total/export?date=",showDateSearch:""}},filters:{formatDate:function(t){return String(t).substr(0,10)}},computed:{},mounted:function(){this.init()},methods:{init:function(){var t=this;this.showDateSearch=String(rootLogin),window.parent.onscroll=function(a){t.scrollMsg()},this.consumeDailyTotal()},searchDate:function(){this.consumeDailyTotal(this.form.searchDate)},exportExcel:function(){},goBack:function(){},clearCharts:function(t){this.consumeDailyTotalData={list:[],MTD:{}},this.initUserConsumeDailyTotalCharts(),this.userGrowthAndActivity(t),this.getUserSleepList(t)},consumeDailyTotal:function(t){var a=this,e=t?this.consumeDailyTotalUrl+"?date="+t:this.consumeDailyTotalUrl;this.exportDate=t||"",c.a.get(e).then(function(e){console.log(e.data),e.data&&"200"==e.data.code?(e.data.data.length==[]&&a.alertMsg("零售数据没有数据,请选择其他日期"),a.consumeDailyTotalData={list:[],MTD:{}},window.setTimeout(function(){e.data.data.length!=[]&&(a.consumeDailyTotalData=e.data.data),a.initUserConsumeDailyTotalCharts(),a.userGrowthAndActivity(t),a.getUserSleepList(t)},300)):(a.alertMsg("获取零售数据错误"),a.clearCharts(t))}).catch(function(e){a.alertMsg("零售数据接口错误"),a.clearCharts(t)})},initUserConsumeDailyTotalCharts:function(){this.chartConsumeTotalAmount(),this.chartConsumeTotalCups(),this.chartDrinkCups(),this.chartEquipmentsDailyAverage(),this.chartPayDailyAverage(),this.chartCupsDailyAverage(),this.chartPayCupsDailyAverage()},initSingleChart:function(t,a,e,s,i){var r=echarts.init(document.getElementById(e));r.clear();for(var l=[],n=[],o=this[t].list,_=0,c=o.length;_<c;_++){var v=Number(o[_][a].substr(5,2)),d=Number(o[_][a].substr(8,2));l.push(v+"月"+d+"日"),n.push(Number(o[_][s]))}var h=n.length>0?n.reduce(function(t,a){return t>a?t:a}):0,p={color:u,backgroundColor:"#fff",title:{text:i,textStyle:{fontSize:16}},tooltip:{},legend:{data:[i]},xAxis:{data:l},yAxis:{},series:[{name:i,type:"bar",barMaxWidth:30,data:n,itemStyle:{normal:{label:{show:!0},color:function(t){var a=Math.floor(5*t.value/h);return u[a]}}}}]};r.setOption(p)},initDoubleChart:function(t,a,e,s,i,r,l,n){var o=echarts.init(document.getElementById(e));o.clear();for(var _=[],c=[[],[]],u=this[t].list,d=0,h=u.length;d<h;d++){var p=Number(u[d][a].substr(5,2)),m=Number(u[d][a].substr(8,2));_.push(p+"月"+m+"日"),c[0].push(u[d][s]),c[1].push(u[d][i])}var y={color:v,backgroundColor:"#fff",title:{text:r,textStyle:{fontSize:16}},tooltip:{},legend:{data:[l,n]},xAxis:{data:_},yAxis:{},series:[{name:l,type:"bar",barMaxWidth:20,data:c[0],itemStyle:{normal:{label:{show:!0}}}},{name:n,type:"bar",barMaxWidth:20,data:c[1],itemStyle:{normal:{label:{show:!0}}}}]};o.setOption(y)},chartConsumeTotalAmount:function(){this.initSingleChart("consumeDailyTotalData","date","totalAmount","consume_total_amount","总销售额")},chartConsumeTotalCups:function(){this.initSingleChart("consumeDailyTotalData","date","totalCups","consume_total_cups","总杯数")},chartDrinkCups:function(){this.initDoubleChart("consumeDailyTotalData","date","drinkCups","consume_pay_cups","free_cups_daily_average","饮品杯数","免费杯数","付费杯数")},chartEquipmentsDailyAverage:function(){this.initSingleChart("consumeDailyTotalData","date","equipmentsDailyAverage","equipments_daily_average","总台日均杯数")},chartPayDailyAverage:function(){this.initSingleChart("consumeDailyTotalData","date","payDailyAverage","pay_daily_average","付费台日均杯数")},chartCupsDailyAverage:function(){this.initSingleChart("consumeDailyTotalData","date","cupsDailyAverage","cups_daily_average","总杯均价")},chartPayCupsDailyAverage:function(){this.initSingleChart("consumeDailyTotalData","date","payCupsDailyAverage","pay_cups_daily_average","付费杯均价")},clearCharts2:function(){this.userGrowthAndActivityData={list:[],MTD:{}},this.initUserGrowthAndActivityCharts()},userGrowthAndActivity:function(t){var a=this,e=t?this.userGrowthAndActivityUrl+"?date="+t:this.userGrowthAndActivityUrl;c.a.get(e).then(function(t){t.data&&"200"==t.data.code?(t.data.data.length!=[]?a.userGrowthAndActivityData=t.data.data:a.userGrowthAndActivityData={list:[],MTD:{}},a.initUserGrowthAndActivityCharts()):(a.clearCharts2(),a.alertMsg("获取用户增长及活跃数据错误"))}).catch(function(t){a.clearCharts2(),a.alertMsg("用户增长及活跃接口错误")})},initUserGrowthAndActivityCharts:function(){this.chartUsersTotalNumber(),this.chartNewTotalNumber(),this.chartUserActiveTotal(),this.chartUserPayActive(),this.chartPerCapitaPay()},chartUsersTotalNumber:function(){this.initSingleChart("userGrowthAndActivityData","created_at","usersTotalNumber","users_total_number","总用户数")},chartNewTotalNumber:function(){this.initSingleChart("userGrowthAndActivityData","created_at","newTotalNumber","new_total_number","(日)新增用户数")},chartUserActiveTotal:function(){this.initSingleChart("userGrowthAndActivityData","created_at","userActiveTotal","user_active_total","(日)活跃用户数")},chartUserPayActive:function(){this.initSingleChart("userGrowthAndActivityData","created_at","userPayActive","user_pay_active","(日)付费活跃用户数")},chartPerCapitaPay:function(){this.initSingleChart("userGrowthAndActivityData","created_at","perCapitaPay","per_capita_pay","人均付费购买频次")},getUserSleepList:function(t){var a=this,e=t?this.getUserSleepListUrl+"?date="+t:this.getUserSleepListUrl;c.a.get(e).then(function(e){e.data&&"200"==e.data.code?(a.getUserSleepListData=e.data.data,a.getUserRecallList(t)):(a.alertMsg("获取沉睡用户数据错误"),a.getUserRecallList(t))}).catch(function(e){a.alertMsg("沉睡用户接口错误"),a.getUserRecallList(t)})},getUserRecallList:function(t){var a=this,e=t?this.getUserRecallListUrl+"?date="+t:this.getUserRecallListUrl;c.a.get(e).then(function(e){e.data&&"200"==e.data.code?(a.getUserRecallListData=e.data.data,a.getUserRetainList(t)):(a.alertMsg("获取召回用户数据错误"),a.getUserRetainList(t))}).catch(function(e){a.alertMsg("召回用户接口错误"),a.getUserRetainList(t)})},getUserRetainList:function(t){var a=this,e=t?this.getUserRetainListUrl+"?date="+t:this.getUserRetainListUrl;c.a.get(e).then(function(t){if(t.data&&"200"==t.data.code){var e=t.data.data;a.getUserRetainListData.head0=["",""],a.getUserRetainListData.list=[],a.getUserRetainListData.head=[],a.getUserRetainListData.head[0]="星期",a.getUserRetainListData.head[1]="日期";for(var s=e.length,i=0;i<s;i++){a.getUserRetainListData.head0.push(e[i].retain_at),a.getUserRetainListData.head.push(e[i].retain_at.substr(5,5)),a.getUserRetainListData.list[i]=[];for(var r=0;r<s+2;r++)a.getUserRetainListData.list[i].push({});a.getUserRetainListData.list[i][0].content=e[i].retain_week,a.getUserRetainListData.list[i][1].content=e[i].retain_at.substr(5,5),a.getUserRetainListData.list[i][i+2].new=!0,a.getUserRetainListData.list[i][i+2].content=e[i].retain_number.today_new_register}for(var l=0;l<s;l++)for(var n=0;n<s;n++)e[l].retain_number[a.getUserRetainListData.head0[n+2]]&&(a.getUserRetainListData.list[l][n+2].content=e[l].retain_number[a.getUserRetainListData.head0[n+2]])}else a.alertMsg("获取留存用户数据错误")}).catch(function(t){a.alertMsg("留存用户接口错误")})},alertMsg:function(t,a){this.scrollMsg();var e=a||"error";this.$message({message:t,duration:3600,type:e})},scrollMsg:function(){if(self!=top){var t=window.parent.document.documentElement.scrollTop||window.parent.document.body.scrollTop-50,a=document.getElementsByClassName("el-message")[0];a&&(a.style.cssText="top: "+t+"px;z-index:1000;")}}},components:{}},h={render:function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("div",{staticClass:"content-body"},["0"!=t.showDateSearch?e("el-form",{ref:"form",attrs:{"label-position":t.labelPosition,model:t.form,size:"small","label-width":"120px"}},[e("el-row",{attrs:{gutter:10}},["1"!=t.showDateSearch?e("el-col",{staticStyle:{width:"350px"}},[e("el-form-item",{attrs:{label:"选择检索日期",prop:"sendTime"}},[e("el-date-picker",{attrs:{format:"yyyy-MM-dd","value-format":"yyyy-MM-dd",type:"date",placeholder:"选择日期","picker-options":t.searchDatePickerOption},model:{value:t.form.searchDate,callback:function(a){t.$set(t.form,"searchDate",a)},expression:"form.searchDate"}})],1)],1):t._e(),t._v(" "),"1"!=t.showDateSearch?e("el-col",{attrs:{span:10}},[e("el-button",{attrs:{type:"primary",plain:""},on:{click:t.searchDate}},[t._v("检索")])],1):t._e(),t._v(" "),e("el-col",{attrs:{span:4}},["2"!=t.showDateSearch?e("a",{attrs:{href:t.exportUrl+t.exportDate}},[e("el-button",{attrs:{type:"primary",plain:""}},[t._v("导出")])],1):t._e()])],1)],1):t._e(),t._v(" "),e("div",{staticClass:"sub-title",staticStyle:{"margin-top":"0"}},[t._v("零售数据--表单")]),t._v(" "),e("div",[e("table",{staticClass:"gridtable"},[t._m(0),t._v(" "),t._l(t.consumeDailyTotalData.list,function(a,s){return e("tr",{key:s},[e("td",[t._v(t._s(a.week))]),t._v(" "),e("td",[t._v(t._s(t._f("formatDate")(a.date)))]),t._v(" "),e("td",[t._v(t._s(a.equipments_number))]),t._v(" "),e("td",[t._v(t._s(a.equipments_number_count))]),t._v(" "),e("td",[t._v(t._s(a.consume_total_amount))]),t._v(" "),e("td",[t._v(t._s(a.consume_total_cups))]),t._v(" "),e("td",[t._v(t._s(a.consume_pay_cups))]),t._v(" "),e("td",[t._v(t._s(a.equipments_daily_average))]),t._v(" "),e("td",[Number(a.week_compare_daily_average)<0?e("span",[e("i",{staticClass:"el-icon-download",staticStyle:{color:"green"}})]):Number(a.week_compare_daily_average)>0?e("span",[e("i",{staticClass:"el-icon-upload2",staticStyle:{color:"red"}})]):t._e(),t._v(t._s(a.week_compare_daily_average))]),t._v(" "),e("td",[t._v(t._s(a.last_week_daily_average))]),t._v(" "),e("td",[t._v(t._s(a.pay_daily_average))]),t._v(" "),e("td",[Number(a.week_compare_pay)<0?e("span",[e("i",{staticClass:"el-icon-download",staticStyle:{color:"green"}})]):Number(a.week_compare_pay)>0?e("span",[e("i",{staticClass:"el-icon-upload2",staticStyle:{color:"red"}})]):t._e(),t._v(t._s(a.week_compare_pay))]),t._v(" "),e("td",[t._v(t._s(a.week_pay_daily_average))]),t._v(" "),e("td",[t._v(t._s(a.cups_daily_average))]),t._v(" "),e("td",[t._v(t._s(a.pay_cups_daily_average))]),t._v(" "),e("td",[t._v(t._s(a.free_cups_daily_average))])])}),t._v(" "),e("tr",[e("td",{staticStyle:{"text-align":"center"},attrs:{colspan:"2"}},[t._v("MTD")]),t._v(" "),e("td",[t._v(t._s(t.consumeDailyTotalData.MTD.equipments_number))]),t._v(" "),e("td",[t._v(t._s(t.consumeDailyTotalData.MTD.equipments_number_count))]),t._v(" "),e("td",[t._v(t._s(t.consumeDailyTotalData.MTD.consume_total_amount))]),t._v(" "),e("td",[t._v(t._s(t.consumeDailyTotalData.MTD.consume_total_cups))]),t._v(" "),e("td",[t._v(t._s(t.consumeDailyTotalData.MTD.consume_pay_cups))]),t._v(" "),e("td",[t._v(t._s(t.consumeDailyTotalData.MTD.equipments_daily_average))]),t._v(" "),e("td",[t._v(t._s(t.consumeDailyTotalData.MTD.week_compare_daily_average))]),t._v(" "),e("td",[t._v(t._s(t.consumeDailyTotalData.MTD.last_week_daily_average))]),t._v(" "),e("td",[t._v(t._s(t.consumeDailyTotalData.MTD.pay_daily_average))]),t._v(" "),e("td",[t._v(t._s(t.consumeDailyTotalData.MTD.week_compare_pay))]),t._v(" "),e("td",[t._v(t._s(t.consumeDailyTotalData.MTD.week_pay_daily_average))]),t._v(" "),e("td",[t._v(t._s(t.consumeDailyTotalData.MTD.cups_daily_average))]),t._v(" "),e("td",[t._v(t._s(t.consumeDailyTotalData.MTD.pay_cups_daily_average))]),t._v(" "),e("td",[t._v(t._s(t.consumeDailyTotalData.MTD.free_cups_daily_average))])])],2),t._v(" "),e("div",{staticClass:"sub-title",staticStyle:{"margin-bottom":"20px"}},[t._v("零售数据--图表")]),t._v(" "),e("div",{staticClass:"charts",attrs:{id:"totalAmount"}}),t._v(" "),e("div",{staticClass:"charts",attrs:{id:"totalCups"}}),t._v(" "),e("div",{staticClass:"charts",attrs:{id:"drinkCups"}}),t._v(" "),e("div",{staticClass:"charts",attrs:{id:"equipmentsDailyAverage"}}),t._v(" "),e("div",{staticClass:"charts",attrs:{id:"payDailyAverage"}}),t._v(" "),e("div",{staticClass:"charts",attrs:{id:"cupsDailyAverage"}}),t._v(" "),e("div",{staticClass:"charts",attrs:{id:"payCupsDailyAverage"}})]),t._v(" "),e("div",{staticClass:"sub-title",staticStyle:{"margin-top":"0"}},[t._v("用户增长及活跃--表单")]),t._v(" "),e("div",[e("table",{staticClass:"gridtable"},[t._m(1),t._v(" "),t._l(t.userGrowthAndActivityData.list,function(a,s){return e("tr",{key:s},[e("td",[t._v(t._s(a.create_week_day))]),t._v(" "),e("td",[t._v(t._s(t._f("formatDate")(a.created_at)))]),t._v(" "),e("td",[t._v(t._s(a.users_total_number))]),t._v(" "),e("td",[t._v(t._s(a.new_total_number))]),t._v(" "),e("td",[t._v(t._s(a.users_total_up))]),t._v(" "),e("td",[t._v(t._s(a.user_active_total))]),t._v(" "),e("td",[Number(a.week_active_total)<0?e("span",[e("i",{staticClass:"el-icon-download",staticStyle:{color:"green"}})]):Number(a.week_active_total)>0?e("span",[e("i",{staticClass:"el-icon-upload2",staticStyle:{color:"red"}})]):t._e(),t._v(t._s(a.week_active_total)+"\n        ")]),t._v(" "),e("td",[t._v(t._s(a.user_pay_active))]),t._v(" "),e("td",[Number(a.week_pay_active)<0?e("span",[e("i",{staticClass:"el-icon-download",staticStyle:{color:"green"}})]):Number(a.week_pay_active)>0?e("span",[e("i",{staticClass:"el-icon-upload2",staticStyle:{color:"red"}})]):t._e(),t._v(t._s(a.week_pay_active)+"\n        ")]),t._v(" "),e("td",[t._v(t._s(a.user_free_active))]),t._v(" "),e("td",[t._v(t._s(a.per_capita_pay))])])}),t._v(" "),e("tr",[e("td",{staticStyle:{"text-align":"center"},attrs:{colspan:"2"}},[t._v("MTD")]),t._v(" "),e("td",[t._v(t._s(t.userGrowthAndActivityData.MTD.users_total_number))]),t._v(" "),e("td",[t._v(t._s(t.userGrowthAndActivityData.MTD.new_total_number))]),t._v(" "),e("td",[t._v(t._s(t.userGrowthAndActivityData.MTD.users_total_up))]),t._v(" "),e("td",[t._v(t._s(t.userGrowthAndActivityData.MTD.user_active_total))]),t._v(" "),e("td",[t._v(t._s(t.userGrowthAndActivityData.MTD.week_active_total))]),t._v(" "),e("td",[t._v(t._s(t.userGrowthAndActivityData.MTD.user_pay_active))]),t._v(" "),e("td",[t._v(t._s(t.userGrowthAndActivityData.MTD.week_pay_active))]),t._v(" "),e("td",[t._v(t._s(t.userGrowthAndActivityData.MTD.user_free_active))]),t._v(" "),e("td",[t._v(t._s(t.userGrowthAndActivityData.MTD.per_capita_pay))])])],2),t._v(" "),e("div",{staticClass:"sub-title",staticStyle:{"margin-bottom":"20px"}},[t._v("用户增长及活跃--图表")]),t._v(" "),e("div",{staticClass:"charts",attrs:{id:"usersTotalNumber"}}),t._v(" "),e("div",{staticClass:"charts",attrs:{id:"newTotalNumber"}}),t._v(" "),e("div",{staticClass:"charts",attrs:{id:"userActiveTotal"}}),t._v(" "),e("div",{staticClass:"charts",attrs:{id:"userPayActive"}}),t._v(" "),e("div",{staticClass:"charts",attrs:{id:"perCapitaPay"}})]),t._v(" "),e("div",{staticClass:"sub-title"},[t._v("沉睡用户")]),t._v(" "),e("el-table",{staticClass:"el-table-common",attrs:{data:t.getUserSleepListData,border:""}},[e("el-table-column",{attrs:{prop:"create_week_day",label:"星期"}}),t._v(" "),e("el-table-column",{attrs:{label:"日期"},scopedSlots:t._u([{key:"default",fn:function(a){return[t._v("\n        "+t._s(t._f("formatDate")(a.row.created_at))+"\n      ")]}}])}),t._v(" "),e("el-table-column",{attrs:{prop:"user_sleep_total",label:"总沉睡用户"}}),t._v(" "),e("el-table-column",{attrs:{prop:"sleep_two_weeks",label:"沉睡2周-1个月"}}),t._v(" "),e("el-table-column",{attrs:{prop:"sleep_one_month",label:"沉睡1个月-2个月"}}),t._v(" "),e("el-table-column",{attrs:{prop:"sleep_two_month",label:"沉睡2个月-3个月"}}),t._v(" "),e("el-table-column",{attrs:{prop:"sleep_three_month",label:"沉睡3个月-4个月"}}),t._v(" "),e("el-table-column",{attrs:{prop:"sleep_four_month",label:"沉睡4个月-5个月"}}),t._v(" "),e("el-table-column",{attrs:{prop:"sleep_five_month",label:"沉睡5个月-6个月"}}),t._v(" "),e("el-table-column",{attrs:{prop:"sleep_six_month",label:"沉睡6个月以上"}})],1),t._v(" "),e("div",{staticClass:"sub-title"},[t._v("召回用户")]),t._v(" "),e("el-table",{staticClass:"el-table-common",attrs:{data:t.getUserRecallListData,border:""}},[e("el-table-column",{attrs:{prop:"create_week_day",label:"星期"}}),t._v(" "),e("el-table-column",{attrs:{label:"日期"},scopedSlots:t._u([{key:"default",fn:function(a){return[t._v("\n        "+t._s(t._f("formatDate")(a.row.created_at))+"\n      ")]}}])}),t._v(" "),e("el-table-column",{attrs:{prop:"user_recall_total",label:"总召回用户"}}),t._v(" "),e("el-table-column",{attrs:{prop:"recall_two_weeks",label:"召回2周-1个月"}}),t._v(" "),e("el-table-column",{attrs:{prop:"recall_one_month",label:"召回1个月-2个月"}}),t._v(" "),e("el-table-column",{attrs:{prop:"recall_two_month",label:"召回2个月-3个月"}}),t._v(" "),e("el-table-column",{attrs:{prop:"recall_three_month",label:"召回3个月-4个月"}}),t._v(" "),e("el-table-column",{attrs:{prop:"recall_four_month",label:"召回4个月-5个月"}}),t._v(" "),e("el-table-column",{attrs:{prop:"recall_five_month",label:"召回5个月-6个月"}}),t._v(" "),e("el-table-column",{attrs:{prop:"recall_six_month",label:"召回6个月以上"}})],1),t._v(" "),e("div",{staticClass:"sub-title"},[t._v("留存用户")]),t._v(" "),e("table",{staticClass:"gridtable"},[e("tr",t._l(t.getUserRetainListData.head,function(a,s){return e("th",{key:s,class:{week:0==s,"retain-day":1==s}},[t._v(t._s(a))])})),t._v(" "),t._l(t.getUserRetainListData.list,function(a,s){return e("tr",{key:s},t._l(t.getUserRetainListData.list[s],function(a,s){return e("td",{key:s,class:{newregister:a.new}},[t._v(t._s(a.content))])}))})],2),t._v(" "),e("div",{staticClass:"div-center"},[e("a",{attrs:{href:"#"}},[e("el-button",{attrs:{type:"primary",plain:""}},[t._v("返回顶部")])],1)])],1)},staticRenderFns:[function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("tr",[e("th",{attrs:{width:"60"}},[t._v("星期")]),t._v(" "),e("th",{attrs:{width:"95"}},[t._v("日期")]),t._v(" "),e("th",{attrs:{width:"50"}},[t._v("设备台数（除mini）")]),t._v(" "),e("th",{attrs:{width:"50"}},[t._v("设备总台数")]),t._v(" "),e("th",{attrs:{width:"60"}},[t._v("总销售额")]),t._v(" "),e("th",{attrs:{width:"60"}},[t._v("总杯数")]),t._v(" "),e("th",[t._v("付费杯数")]),t._v(" "),e("th",[t._v("总台日均（杯数）")]),t._v(" "),e("th",{attrs:{width:"80"}},[t._v("周同比")]),t._v(" "),e("th",[t._v("上周同期")]),t._v(" "),e("th",[t._v("付费台日均")]),t._v(" "),e("th",{attrs:{width:"80"}},[t._v("周同比")]),t._v(" "),e("th",[t._v("上周同期")]),t._v(" "),e("th",[t._v("总杯均价")]),t._v(" "),e("th",[t._v("付费杯均价")]),t._v(" "),e("th",[t._v("免费杯数")])])},function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("tr",[e("th",{attrs:{width:"60"}},[t._v("星期")]),t._v(" "),e("th",{attrs:{width:"95"}},[t._v("日期")]),t._v(" "),e("th",[t._v("总用户数")]),t._v(" "),e("th",[t._v("新增用户数")]),t._v(" "),e("th",[t._v("用户增长率")]),t._v(" "),e("th",[t._v("活跃用户数")]),t._v(" "),e("th",[t._v("上周同期对比")]),t._v(" "),e("th",[t._v("付费活跃用户数")]),t._v(" "),e("th",[t._v("上周同期对比")]),t._v(" "),e("th",[t._v("免费活跃用户")]),t._v(" "),e("th",[t._v("人均付费购买频次")])])}]};var p=e("vSla")(d,h,!1,function(t){e("fHsI")},"data-v-4e6914f1",null).exports;s.default.use(o.a);var m=new o.a({routes:[{path:"/",redirect:"/total"},{path:"/total",name:"ConsumeDailyTotal",component:p}]});s.default.use(r.a),s.default.config.productionTip=!1,new s.default({el:"#app",router:m,components:{App:n},template:"<App/>"})},fHsI:function(t,a){},qvMO:function(t,a){}},["NHnr"]);
//# sourceMappingURL=app.682f5bdad2c871ffa84d.js.map