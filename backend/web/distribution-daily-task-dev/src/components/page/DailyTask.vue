<template>
  <div class="content-body">
    <div>{{nowDate}}</div>
    <table class="table table-bordered table-condensed">
      <thead>
        <tr class="text-center">
          <th>运维人员</th>
          <th v-for="(item,index) in listData.date" :key="index">
            <span>{{item}}</span>
            <span v-show="item == nowDate" class="all-issue"><img :src="require('../../assets/images/u1461.png')" title="下发任务"></span>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(value,key) in listData.dailyTaskList" :key="key">
          <td><a href="#">{{value.userName}}</a></td>
          <td class="list" style="padding:15px" v-for="(item,index) in listData.date" :key="index">
            <div v-if="key==item">
              {{value.buildNumber}}
            </div>
          </td>
        </tr>
      </tbody>
            <!--<tbody>
                {{# $.each(d.dailyTaskList,function(index,item){  }}
                <tr>
                        {{# for(var key in d.date){  }}
                    <td class="list" data-orgid="{{d.orgId}}" data-date="{{d.date[key]}}" data-userid ="{{index}}" style="padding:15px">
                        {{#if(item[d.date[key]]){ }}
                        <p>楼宇数量：{{item[d.date[key]].buildNumber }}台 </p>
                        <div class="operation-btn">
                            <p>未下发</p>
                            <p class="details-btn"><span class="glyphicon glyphicon-tasks" title="任务详情"></span></p>
                            {{# if(key == 0) { }}
                            <p class="issue"></p>
                            {{#  } }}
                        </div>
                        {{# if(item[d.date[key]].materialList) { }}
                        <div class="material-list">
                            <p>物料：</p>
                            {{# $.each(item[d.date[key]].materialList,function(indx, value){ }}
                                <p class="num">{{value.name}}:{{value.number}}</p>
                            {{#   }) }}
                        </div>
                        {{# } }}
                        <div class="task-list">
                            {{# $.each(item[d.date[key]].taskList,function(indx, value){ }}
                                <div>
                                    <p>{{value.buildName}}</p>
                                    <p class="color-red" data-taskType="{{value.type}}">{{value.typeName}}</p>
                                    <p class="icon icon-{{key}}" data-buildid="{{value.buildId}}">
                                      {{# if(key == 1&&urlType!=1|| key ==2&&urlType!=1) {  }}
                                      <span class="glyphicon glyphicon-arrow-left change-date-left" title="修改日期"></span>
                                      {{# } }}
                                      {{# if(key == 0&&urlType!=1) {  }}
                                      <span><img class="change-personnel" src="/images/u1554.png" alt="" title="修改人员"></span>
                                      {{# } }}
                                      {{# if(key == 0&&urlType!=1 || key == 1&&urlType!=1) { }}
                                      <span class="glyphicon glyphicon-arrow-right change-date-right" title="修改日期"></span>
                                      {{# } }}
                                    </p>
                                </div>
                            {{#  })  }}
                        </div>
                        {{# } }}
                    </td>
                    {{# } }}
                </tr>
               {{#  }) }}
            </tbody>-->
    </table>
  </div>
</template>
<script>
/* eslint-disable */
// import axios  from  'axios'
export default {
  data() {
    return {
      listData: '',
      //this.getNowFormatDate()
      nowDate: "2018-09-12"
    }
  },
  computed: {
  },
  mounted() {
    this.init();
  },
  methods: {
    init() {
      this.listData = rootInfoData;
    },
    saveRegionData() {
    },
    getNowFormatDate(){
      var date = new Date();
      var seperator1 = "-";
      var month = date.getMonth() + 1;
      var strDate = date.getDate();
      if (month >= 1 && month <= 9) {
          month = "0" + month;
      }
      if (strDate >= 0 && strDate <= 9) {
          strDate = "0" + strDate;
      }
      var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate;
      return currentdate;
    },
    //生成guid ,解决排序
    guid()
    {
      return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
          var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
          return v.toString(16);
      });
    },
    getUrlParam(name) {
      let reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
      let r = window.location.search.substr(1).match(reg);
      if (r != null) {
          return decodeURI(r[2])
      }
      return null
    }
  },
  components: {}
}

</script>
<style>
.content-body
{
  padding: 10px;
}
</style>
