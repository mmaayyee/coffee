import Vue from 'vue'
import Router from 'vue-router'
import WeeklyUserList from '@/components/page/WeeklyUserList'

Vue.use(Router)
/* eslint-disable */
export default new Router({
  routes: [
    {
      path: '/',
      redirect: '/userlist'
    },
    {
      path: '/userlist',
      name: 'WeeklyUserList',
      component: WeeklyUserList
    }
  ]
})
